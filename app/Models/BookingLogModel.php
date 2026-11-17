<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingLogModel extends Model
{
    protected $table            = 'booking_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'booking_id',
        'user_id',
        'action',
        'details',
        'created_at',
    ];

    protected $useTimestamps = false;

    /**
     * Record an audit log for a booking event
     */
    public function log(int $bookingId, string $action, ?int $userId = null, $details = null): bool
    {
        $data = [
            'booking_id' => $bookingId,
            'user_id'    => $userId,
            'action'     => $action,
            'details'    => is_array($details) ? json_encode($details) : (string) $details,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        return (bool) $this->insert($data);
    }

    /**
     * Get all logs for a specific booking
     */
    public function getLogsByBooking(int $bookingId): array
    {
        return $this->select('booking_logs.*, users.first_name, users.last_name')
            ->join('users', 'users.id = booking_logs.user_id', 'left')
            ->where('booking_id', $bookingId)
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
}
