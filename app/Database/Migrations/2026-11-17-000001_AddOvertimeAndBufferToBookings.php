<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOvertimeAndBufferToBookings extends Migration
{
    public function up(): void
    {
        // 1. Expand status ENUM to support the full room booking lifecycle
        $this->forge->modifyColumn('bookings', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'pending',
                    'approved',
                    'ongoing',
                    'overtime',
                    'extension_requested',
                    'vacated',
                    'completed',
                    'cancelled',
                    'rejected',
                ],
                'default'    => 'pending',
            ],
        ]);

        // 2. Add tracking columns for overtime, room transition buffer, and extensions
        $this->forge->addColumn('bookings', [
            'actual_start_time' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'end_time',
            ],
            'actual_end_time' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'actual_start_time',
            ],
            'vacated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'actual_end_time',
            ],
            'buffer_until' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'vacated_at',
            ],
            'requested_end_time' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'buffer_until',
            ],
            'is_delayed' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'requested_end_time',
            ],
            'reassigned_from_room_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'is_delayed',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('bookings', [
            'actual_start_time',
            'actual_end_time',
            'vacated_at',
            'buffer_until',
            'requested_end_time',
            'is_delayed',
            'reassigned_from_room_id',
        ]);

        $this->forge->modifyColumn('bookings', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'cancelled', 'rejected', 'completed'],
                'default'    => 'pending',
            ],
        ]);
    }
}
