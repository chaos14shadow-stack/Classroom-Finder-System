<?php

namespace App\Models;

use CodeIgniter\Model;

class RoomModel extends Model
{
    protected $table            = 'rooms';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'room_name',
        'department_id',
        'capacity',
        'status',
        'maintenance_note',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Retrieve classrooms with department details and query filters
     */
    public function getRoomsWithFilters(
        ?string $keyword = null,
        ?string $deptFilter = null,
        ?int $minCapacity = null,
        ?string $statusFilter = null,
        bool $includeMaintenanceNote = false
    ): array {
        $builder = $this->select('rooms.*, departments.name AS department_name, departments.code AS department_code')
            ->join('departments', 'departments.id = rooms.department_id', 'left');

        if (!empty($keyword)) {
            $builder->like('rooms.room_name', trim($keyword));
        }

        if (!empty($deptFilter) && $deptFilter !== 'all') {
            if (is_numeric($deptFilter)) {
                $builder->where('rooms.department_id', (int) $deptFilter);
            } else {
                $builder->where('departments.code', trim($deptFilter));
            }
        }

        if (!empty($minCapacity) && $minCapacity > 0) {
            $builder->where('rooms.capacity >=', $minCapacity);
        }

        if (!empty($statusFilter) && $statusFilter !== 'all') {
            $builder->where('rooms.status', trim($statusFilter));
        }

        $results = $builder->orderBy('rooms.room_name', 'ASC')->findAll();

        if (!$includeMaintenanceNote) {
            foreach ($results as &$r) {
                unset($r['maintenance_note']);
            }
            unset($r);
        }

        return $results;
    }

    /**
     * Retrieve a single room by ID or Room Name with department details
     */
    public function getRoomWithDetails($idOrName, bool $includeMaintenanceNote = false): ?array
    {
        $builder = $this->select('rooms.*, departments.name AS department_name, departments.code AS department_code')
            ->join('departments', 'departments.id = rooms.department_id', 'left');

        if (is_numeric($idOrName)) {
            $builder->where('rooms.id', (int) $idOrName);
        } else {
            $builder->where('rooms.room_name', trim((string) $idOrName));
        }

        $room = $builder->first();

        if ($room && !$includeMaintenanceNote) {
            unset($room['maintenance_note']);
        }

        return $room;
    }

    /**
     * Get real-time active occupancy badge data for a classroom
     * Returns: ['is_occupied' => bool, 'instructor' => ?string, 'until' => ?string, 'purpose' => ?string]
     */
    public function getActiveOccupancy(int $roomId): array
    {
        $nowDate = date('Y-m-d');
        $nowTime = date('H:i:s');

        $activeBooking = $this->db->table('bookings')
            ->select('bookings.*, users.first_name, users.last_name')
            ->join('users', 'users.id = bookings.user_id', 'left')
            ->where('bookings.room_id', $roomId)
            ->where('bookings.booking_date', $nowDate)
            ->where('bookings.start_time <=', $nowTime)
            ->where('bookings.end_time >', $nowTime)
            ->where('bookings.status', 'approved')
            ->get()
            ->getRowArray();

        if ($activeBooking) {
            $instructorName = trim($activeBooking['first_name'] . ' ' . $activeBooking['last_name']);
            return [
                'is_occupied'     => true,
                'instructor_name' => $instructorName,
                'until'           => date('g:i A', strtotime($activeBooking['end_time'])),
                'purpose'         => $activeBooking['purpose'],
                'badge_text'      => 'Booked by ' . $instructorName . ' until ' . date('g:i A', strtotime($activeBooking['end_time'])),
            ];
        }

        return [
            'is_occupied'     => false,
            'instructor_name' => null,
            'until'           => null,
            'purpose'         => null,
            'badge_text'      => null,
        ];
    }

    /**
     * Get approved schedule for a room on a given date (chronological order)
     */
    public function getApprovedSchedule(int $roomId, ?string $date = null): array
    {
        $date = $date ?? date('Y-m-d');

        return $this->db->table('bookings')
            ->select('bookings.*, users.first_name, users.last_name')
            ->join('users', 'users.id = bookings.user_id', 'left')
            ->where('bookings.room_id', $roomId)
            ->where('bookings.booking_date', $date)
            ->where('bookings.status', 'approved')
            ->orderBy('bookings.start_time', 'ASC')
            ->get()
            ->getResultArray();
    }
}

