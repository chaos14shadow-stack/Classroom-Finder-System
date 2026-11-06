<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    protected $table            = 'bookings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'room_id',
        'user_id',
        'booking_date',
        'start_time',
        'end_time',
        'purpose',
        'status',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Check if a time interval conflicts with any existing pending or approved booking
     * Conflict condition: new_start < existing_end AND new_end > existing_start
     */
    public function hasConflict(
        int $roomId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeBookingId = null
    ): bool {
        $builder = $this->builder();
        $builder->where('room_id', $roomId)
            ->where('booking_date', $date)
            ->whereIn('status', ['pending', 'approved'])
            ->where('start_time <', $endTime)
            ->where('end_time >', $startTime);

        if ($excludeBookingId !== null) {
            $builder->where('id !=', $excludeBookingId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Retrieve bookings requested by a specific user with full room and department details
     */
    public function getBookingsByUser(int $userId, ?string $statusFilter = null): array
    {
        $builder = $this->select('bookings.*, rooms.room_name, rooms.capacity, departments.code AS department_code, departments.name AS department_name')
            ->join('rooms', 'rooms.id = bookings.room_id', 'left')
            ->join('departments', 'departments.id = rooms.department_id', 'left')
            ->where('bookings.user_id', $userId);

        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $builder->where('bookings.status', strtolower(trim($statusFilter)));
        }

        return $builder->orderBy('bookings.booking_date', 'DESC')
            ->orderBy('bookings.start_time', 'DESC')
            ->findAll();
    }

    /**
     * Retrieve a single booking with room, user, and department details
     */
    public function getBookingWithDetails(int $id): ?array
    {
        return $this->select('bookings.*, rooms.room_name, rooms.capacity, rooms.status AS room_current_status, departments.name AS department_name, departments.code AS department_code, users.first_name, users.last_name, users.email, users.id_number')
            ->join('rooms', 'rooms.id = bookings.room_id', 'left')
            ->join('departments', 'departments.id = rooms.department_id', 'left')
            ->join('users', 'users.id = bookings.user_id', 'left')
            ->where('bookings.id', $id)
            ->first();
    }

    /**
     * Retrieve all bookings for Admin moderation (pending bookings sorted first)
     */
    public function getAllForAdmin(?string $statusFilter = null, ?string $search = null): array
    {
        $builder = $this->select('bookings.*, rooms.room_name, rooms.capacity, departments.code AS department_code, users.first_name, users.last_name, users.id_number, users.email')
            ->join('rooms', 'rooms.id = bookings.room_id', 'left')
            ->join('departments', 'departments.id = rooms.department_id', 'left')
            ->join('users', 'users.id = bookings.user_id', 'left');

        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $builder->where('bookings.status', strtolower(trim($statusFilter)));
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('rooms.room_name', $search)
                ->orLike('users.first_name', $search)
                ->orLike('users.last_name', $search)
                ->orLike('users.id_number', $search)
                ->orLike('bookings.purpose', $search)
                ->groupEnd();
        }

        return $builder->orderBy("CASE WHEN bookings.status = 'pending' THEN 0 ELSE 1 END", 'ASC', false)
            ->orderBy('bookings.booking_date', 'DESC')
            ->orderBy('bookings.start_time', 'DESC')
            ->findAll();
    }

    /**
     * Get 5 most recent booking requests for Admin Overview dashboard
     */
    public function getRecentBookings(int $limit = 5): array
    {
        return $this->select('bookings.*, rooms.room_name, users.first_name, users.last_name, users.id_number, departments.code AS department_code')
            ->join('rooms', 'rooms.id = bookings.room_id', 'left')
            ->join('departments', 'departments.id = rooms.department_id', 'left')
            ->join('users', 'users.id = bookings.user_id', 'left')
            ->orderBy('bookings.created_at', 'DESC')
            ->findAll($limit);
    }

    /**
     * Count bookings scheduled for today
     */
    public function getBookingsTodayCount(): int
    {
        return $this->where('booking_date', date('Y-m-d'))
            ->whereIn('status', ['pending', 'approved'])
            ->countAllResults();
    }

    /**
     * Count pending bookings awaiting approval
     */
    public function getPendingCount(): int
    {
        return $this->where('status', 'pending')->countAllResults();
    }

    /**
     * Instructor cancels their own booking
     */
    public function cancelBooking(int $bookingId, int $userId): array
    {
        $booking = $this->find($bookingId);
        if (!$booking) {
            return ['success' => false, 'message' => 'Booking request not found.'];
        }

        if ((int) $booking['user_id'] !== $userId) {
            return ['success' => false, 'message' => 'Unauthorized: You can only cancel your own bookings.'];
        }

        if (in_array($booking['status'], ['cancelled', 'rejected'], true)) {
            return ['success' => false, 'message' => 'This booking has already been ' . $booking['status'] . '.'];
        }

        $this->update($bookingId, ['status' => 'cancelled']);
        return ['success' => true, 'message' => 'Booking successfully cancelled.'];
    }

    /**
     * Admin approves a booking (with transactional conflict check)
     */
    public function approveBooking(int $bookingId): array
    {
        $this->db->transStart();

        $booking = $this->find($bookingId);
        if (!$booking) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Booking record not found.'];
        }

        if ($booking['status'] !== 'pending') {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Only pending bookings can be approved. Current status: ' . $booking['status']];
        }

        // Re-check conflict to prevent concurrent overlapping approvals
        if ($this->hasConflict($booking['room_id'], $booking['booking_date'], $booking['start_time'], $booking['end_time'], $bookingId)) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'This time slot conflicts with an existing booking. Cannot approve.'];
        }

        $this->update($bookingId, ['status' => 'approved']);
        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Database error while approving booking.'];
        }

        return ['success' => true, 'message' => 'Booking request approved successfully.'];
    }

    /**
     * Admin rejects a booking
     */
    public function rejectBooking(int $bookingId): array
    {
        $booking = $this->find($bookingId);
        if (!$booking) {
            return ['success' => false, 'message' => 'Booking record not found.'];
        }

        if ($booking['status'] !== 'pending') {
            return ['success' => false, 'message' => 'Only pending bookings can be rejected. Current status: ' . $booking['status']];
        }

        $this->update($bookingId, ['status' => 'rejected']);
        return ['success' => true, 'message' => 'Booking request rejected.'];
    }
}

