<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompletedStatusAndTimestampsToBookings extends Migration
{
    public function up(): void
    {
        // 1. Modify status column to include 'completed' in ENUM
        $this->forge->modifyColumn('bookings', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'cancelled', 'rejected', 'completed'],
                'default'    => 'pending',
            ],
        ]);

        // 2. Add completed_at and cancelled_at timestamp columns
        $this->forge->addColumn('bookings', [
            'completed_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status',
            ],
            'cancelled_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'completed_at',
            ],
        ]);

        // 3. Add composite index for high-performance expiration & conflict queries
        $this->db->query('CREATE INDEX idx_bookings_status_date_time ON bookings (status, booking_date, end_time)');
    }

    public function down(): void
    {
        $this->db->query('DROP INDEX idx_bookings_status_date_time ON bookings');

        $this->forge->dropColumn('bookings', ['completed_at', 'cancelled_at']);

        $this->forge->modifyColumn('bookings', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'cancelled', 'rejected'],
                'default'    => 'pending',
            ],
        ]);
    }
}

