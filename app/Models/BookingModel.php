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
        'actual_start_time',
        'actual_end_time',
        'vacated_at',
        'buffer_until',
        'requested_end_time',
        'is_delayed',
        'reassigned_from_room_id',
        'purpose',
        'status',
        'completed_at',
        'cancelled_at',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Check if a time interval conflicts with any existing active, ongoing, overtime, or buffered booking.
     * Overlap condition:
     *   new_start < existing_effective_end AND new_end > existing_start
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
            ->whereIn('status', ['pending', 'approved', 'ongoing', 'overtime', 'extension_requested', 'vacated']);

        if ($excludeBookingId !== null) {
            $builder->where('id !=', $excludeBookingId);
        }

        // Standard interval overlap against scheduled time or extension
        $builder->groupStart()
            ->where('start_time <', $endTime)
            ->groupStart()
                ->where('end_time >', $startTime)
                ->orWhere('requested_end_time >', $startTime)
            ->groupEnd()
        ->groupEnd();

        $activeConflicts = $builder->countAllResults();
        return $activeConflicts > 0;
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
            $sf = strtolower(trim($statusFilter));
            if ($sf === 'ongoing') {
                $builder->whereIn('bookings.status', ['ongoing', 'vacated']);
            } elseif ($sf === 'cancelled') {
                $builder->whereIn('bookings.status', ['cancelled', 'rejected']);
            } else {
                $builder->where('bookings.status', $sf);
            }
        }

        return $builder->orderBy('bookings.created_at', 'DESC')
            ->orderBy('bookings.id', 'DESC')
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
     * Retrieve all bookings for Admin moderation
     */
    public function getAllForAdmin(?string $statusFilter = null, ?string $search = null): array
    {
        $builder = $this->select('bookings.*, rooms.room_name, rooms.capacity, departments.code AS department_code, users.first_name, users.last_name, users.id_number, users.email')
            ->join('rooms', 'rooms.id = bookings.room_id', 'left')
            ->join('departments', 'departments.id = rooms.department_id', 'left')
            ->join('users', 'users.id = bookings.user_id', 'left');

        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $sf = strtolower(trim($statusFilter));
            if ($sf === 'ongoing') {
                $builder->whereIn('bookings.status', ['ongoing', 'vacated']);
            } elseif ($sf === 'cancelled') {
                $builder->whereIn('bookings.status', ['cancelled', 'rejected']);
            } else {
                $builder->where('bookings.status', $sf);
            }
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

        return $builder->orderBy('bookings.created_at', 'DESC')
            ->orderBy('bookings.id', 'DESC')
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
            ->orderBy('bookings.id', 'DESC')
            ->findAll($limit);
    }

    /**
     * Count bookings scheduled for today
     */
    public function getBookingsTodayCount(): int
    {
        return $this->where('booking_date', date('Y-m-d'))
            ->whereIn('status', ['pending', 'approved', 'ongoing', 'overtime', 'extension_requested'])
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
     * Count overtime or extension-requested bookings
     */
    public function getOvertimeCount(): int
    {
        return $this->whereIn('status', ['overtime', 'extension_requested'])->countAllResults();
    }

    /**
     * Instructor or Admin confirms room vacated.
     * Records room vacation timestamp, activates room transition buffer, and keeps status as 'ongoing'
     * while the buffer is active. Once the buffer ends, it completes.
     */
    public function confirmRoomVacated(int $bookingId, int $userId, bool $isAdmin = false): array
    {
        $this->db->transStart();

        $booking = $this->find($bookingId);
        if (!$booking) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Booking request not found.'];
        }

        if (!$isAdmin && (int) $booking['user_id'] !== $userId) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Unauthorized: You can only confirm vacancy for your own bookings.'];
        }

        $allowedStatuses = ['approved', 'ongoing', 'overtime', 'extension_requested'];
        if (!in_array($booking['status'], $allowedStatuses, true)) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Booking cannot be vacated in status: ' . $booking['status']];
        }

        $nowTime = date('H:i:s');
        $nowDateTime = date('Y-m-d H:i:s');

        $updateData = [
            'status'          => 'ongoing',
            'actual_end_time' => $nowTime,
            'vacated_at'      => $nowDateTime,
        ];

        $this->update($bookingId, $updateData);

        // Audit log
        $logModel = new BookingLogModel();
        $logModel->log(
            $bookingId,
            'room_vacated',
            $userId,
            "Room confirmed vacated at {$nowTime}. Booking is now awaiting room return finalization."
        );

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Database error while confirming room vacancy.'];
        }

        return [
            'success' => true,
            'message' => "Room confirmed vacated at " . date('g:i A') . ". Booking is now awaiting room return finalization.",
        ];
    }

    /**
     * Complete Booking Return / Final Turnover.
     * Transitions status from 'vacated' to 'completed', moving it out of Ongoing into Completed.
     */
    public function completeBookingReturn(int $bookingId, int $userId): array
    {
        $this->db->transStart();

        $booking = $this->find($bookingId);
        if (!$booking) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Booking record not found.'];
        }

        if (!in_array($booking['status'], ['vacated', 'ongoing', 'overtime'], true)) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Only active or vacated bookings can be marked as completed / returned.'];
        }

        $nowDateTime = date('Y-m-d H:i:s');
        $nowTime = date('H:i:s');

        $updateData = [
            'status'       => 'completed',
            'completed_at' => $nowDateTime,
        ];

        if (empty($booking['vacated_at'])) {
            $updateData['vacated_at'] = $nowDateTime;
            $updateData['actual_end_time'] = $nowTime;
        }

        $this->update($bookingId, $updateData);

        $logModel = new BookingLogModel();
        $logModel->log(
            $bookingId,
            'room_returned_completed',
            $userId,
            "Room return finalized and booking marked as completed at {$nowTime}."
        );

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return ['success' => false, 'message' => 'Database error while finalizing booking return.'];
        }

        return [
            'success' => true,
            'message' => 'Room returned successfully. Booking is now completed and moved out of Ongoing.',
        ];
    }

    /**
     * Automatic Overtime Detection.
     * Transitions ongoing/approved bookings that have passed their end time to 'overtime'
     * and flags upcoming conflicting bookings. Never automatically marks them completed!
     */
    public function detectOvertimeBookings(): array
    {
        $currentDate = date('Y-m-d');
        $currentTime = date('H:i:s');
        $nowDateTime = date('Y-m-d H:i:s');

        $this->db->transStart();

        // 2. Transition today's approved bookings that have started to 'ongoing'
        $ongoingCandidates = $this->builder()
            ->whereIn('status', ['approved'])
            ->where('booking_date', $currentDate)
            ->where('start_time <=', $currentTime)
            ->where('end_time >', $currentTime)
            ->get()
            ->getResultArray();

        foreach ($ongoingCandidates as $cand) {
            $this->update($cand['id'], [
                'status'            => 'ongoing',
                'actual_start_time' => $cand['actual_start_time'] ?? $cand['start_time'],
            ]);
        }

        // 3. Identify approved or ongoing bookings past their end time (or past requested extension) without vacation
        $overtimeBookings = $this->builder()
            ->whereIn('status', ['approved', 'ongoing'])
            ->where('vacated_at IS NULL')
            ->groupStart()
                ->where('booking_date <', $currentDate)
                ->orGroupStart()
                    ->where('booking_date', $currentDate)
                    ->where('end_time <', $currentTime)
                ->groupEnd()
            ->groupEnd()
            ->get()
            ->getResultArray();

        $logModel = new BookingLogModel();
        $flaggedCount = 0;

        foreach ($overtimeBookings as $ot) {
            $this->update($ot['id'], ['status' => 'overtime']);
            $logModel->log((int)$ot['id'], 'overtime_detected', null, "Scheduled end time ({$ot['end_time']}) passed without vacancy confirmation.");

            // Flag upcoming bookings for this room today as potentially delayed
            $affected = $this->builder()
                ->where('room_id', $ot['room_id'])
                ->where('booking_date', $ot['booking_date'])
                ->whereIn('status', ['pending', 'approved'])
                ->where('start_time <=', date('H:i:s', strtotime('+30 minutes', strtotime($currentTime))))
                ->where('id !=', $ot['id'])
                ->get()
                ->getResultArray();

            foreach ($affected as $aff) {
                $this->update($aff['id'], ['is_delayed' => 1]);
                $flaggedCount++;
            }
        }

        $this->db->transComplete();

        return [
            'overtime_count' => count($overtimeBookings),
            'delayed_count'  => $flaggedCount,
        ];
    }

    /**
     * Instructor requests a time extension for an active/overtime booking
     */
    public function requestExtension(int $bookingId, int $userId, string $requestedEndTime, ?string $reason = null): array
    {
        $booking = $this->find($bookingId);
        if (!$booking) {
            return ['success' => false, 'message' => 'Booking record not found.'];
        }

        if ((int) $booking['user_id'] !== $userId) {
            return ['success' => false, 'message' => 'Unauthorized: You can only request extensions for your own bookings.'];
        }

        if (!in_array($booking['status'], ['approved', 'ongoing', 'overtime'], true)) {
            return ['success' => false, 'message' => 'Extensions can only be requested for approved, ongoing, or overtime bookings.'];
        }

        if ($requestedEndTime <= $booking['end_time']) {
            return ['success' => false, 'message' => 'Requested extension time must be later than the scheduled end time (' . date('g:i A', strtotime($booking['end_time'])) . ').'];
        }

        // Operating hours constraint: extension cannot exceed 9:00 PM
        if ($requestedEndTime > '21:00:00' || $requestedEndTime <= '07:00:00') {
            return [
                'success' => false,
                'message' => "Extension cannot exceed 9:00 PM. Classes cannot extend beyond the school's operating hours (7:00 AM – 9:00 PM).",
            ];
        }

        // Check if there is an overlapping booking in the requested window
        $conflict = $this->hasConflict(
            (int) $booking['room_id'],
            $booking['booking_date'],
            $booking['end_time'],
            $requestedEndTime,
            $bookingId
        );

        $this->update($bookingId, [
            'status'             => 'extension_requested',
            'requested_end_time' => $requestedEndTime,
        ]);

        $logModel = new BookingLogModel();
        $conflictNote = $conflict ? ' (Warning: Conflicts with upcoming reservation)' : ' (No immediate conflicts)';
        $logModel->log(
            $bookingId,
            'extension_requested',
            $userId,
            "Requested extension until " . date('g:i A', strtotime($requestedEndTime)) . ". Reason: " . ($reason ?? 'None provided') . $conflictNote
        );

        $msg = $conflict
            ? 'Extension requested until ' . date('g:i A', strtotime($requestedEndTime)) . '. Note: An upcoming reservation is affected; administrator review required.'
            : 'Extension requested until ' . date('g:i A', strtotime($requestedEndTime)) . '. Awaiting administrator confirmation.';

        return ['success' => true, 'message' => $msg, 'has_conflict' => $conflict];
    }

    /**
     * Admin approves an extension request
     */
    public function approveExtension(int $bookingId, int $adminId): array
    {
        $this->db->transStart();

        $booking = $this->find($bookingId);
        if (!$booking || $booking['status'] !== 'extension_requested') {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Booking does not have an active extension request.'];
        }

        $newEndTime = $booking['requested_end_time'];

        $this->update($bookingId, [
            'status'             => 'ongoing',
            'end_time'           => $newEndTime,
            'requested_end_time' => null,
        ]);

        $logModel = new BookingLogModel();
        $logModel->log(
            $bookingId,
            'extension_approved',
            $adminId,
            "Extension approved by administrator. New scheduled end time is " . date('g:i A', strtotime($newEndTime))
        );

        $this->db->transComplete();

        return ['success' => true, 'message' => 'Extension request approved. Scheduled end time updated to ' . date('g:i A', strtotime($newEndTime))];
    }

    /**
     * Admin rejects an extension request
     */
    public function rejectExtension(int $bookingId, int $adminId, ?string $reason = null): array
    {
        $booking = $this->find($bookingId);
        if (!$booking || $booking['status'] !== 'extension_requested') {
            return ['success' => false, 'message' => 'Booking does not have an active extension request.'];
        }

        // Revert status to overtime or ongoing based on current clock
        $currentTime = date('H:i:s');
        $newStatus = ($booking['booking_date'] === date('Y-m-d') && $currentTime < $booking['end_time']) ? 'ongoing' : 'overtime';

        $this->update($bookingId, [
            'status'             => $newStatus,
            'requested_end_time' => null,
        ]);

        $logModel = new BookingLogModel();
        $logModel->log($bookingId, 'extension_rejected', $adminId, "Extension rejected. Reason: " . ($reason ?? 'Classroom required for next session'));

        return ['success' => true, 'message' => 'Extension request has been rejected.'];
    }

    /**
     * Admin reassigns an affected booking to an alternative room
     */
    public function reassignRoom(int $bookingId, int $newRoomId, int $adminId): array
    {
        $this->db->transStart();

        $booking = $this->find($bookingId);
        if (!$booking) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Booking not found.'];
        }

        $targetRoom = $this->db->table('rooms')->where('id', $newRoomId)->get()->getRowArray();
        if (!$targetRoom || $targetRoom['status'] === 'Maintenance') {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Target room is not available or under maintenance.'];
        }

        // Check conflicts in new room
        if ($this->hasConflict($newRoomId, $booking['booking_date'], $booking['start_time'], $booking['end_time'], $bookingId)) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'Target room has a conflicting booking during that time slot.'];
        }

        $oldRoomId = $booking['room_id'];
        $this->update($bookingId, [
            'room_id'                 => $newRoomId,
            'reassigned_from_room_id' => $oldRoomId,
            'is_delayed'              => 0,
        ]);

        $logModel = new BookingLogModel();
        $logModel->log(
            $bookingId,
            'room_reassigned',
            $adminId,
            "Reassigned from room #{$oldRoomId} to {$targetRoom['room_name']} by administrator."
        );

        $this->db->transComplete();

        return ['success' => true, 'message' => "Booking successfully reassigned to {$targetRoom['room_name']}."];
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

        $currentStatus = strtolower($booking['status']);

        if (in_array($currentStatus, ['cancelled', 'rejected', 'completed'], true)) {
            return ['success' => false, 'message' => 'This booking has already been ' . $booking['status'] . ' and cannot be cancelled.'];
        }

        $updateData = [
            'status'       => 'cancelled',
            'cancelled_at' => date('Y-m-d H:i:s'),
        ];

        $this->update($bookingId, $updateData);

        $logModel = new BookingLogModel();
        $logModel->log($bookingId, 'booking_cancelled', $userId, 'Booking cancelled by instructor.');

        return ['success' => true, 'message' => 'Booking successfully cancelled and room released.'];
    }

    /**
     * Admin approves a booking
     */
    public function approveBooking(int $bookingId, ?int $adminId = null): array
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

        if ($this->hasConflict($booking['room_id'], $booking['booking_date'], $booking['start_time'], $booking['end_time'], $bookingId)) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'This time slot conflicts with an existing active booking. Cannot approve.'];
        }

        $this->update($bookingId, ['status' => 'approved']);

        $logModel = new BookingLogModel();
        $logModel->log($bookingId, 'booking_approved', $adminId, 'Booking approved by administrator.');

        $this->db->transComplete();

        return ['success' => true, 'message' => 'Booking request approved successfully.'];
    }

    /**
     * Admin rejects a booking
     */
    public function rejectBooking(int $bookingId, ?int $adminId = null): array
    {
        $booking = $this->find($bookingId);
        if (!$booking) {
            return ['success' => false, 'message' => 'Booking record not found.'];
        }

        $this->update($bookingId, ['status' => 'rejected']);

        $logModel = new BookingLogModel();
        $logModel->log($bookingId, 'booking_rejected', $adminId, 'Booking rejected by administrator.');

        return ['success' => true, 'message' => 'Booking request rejected.'];
    }
}
