<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialDataSeeder extends Seeder
{
    public function run(): void
    {
        $db = $this->db;
        $now = date('Y-m-d H:i:s');

        // 1. Roles
        $rolesData = [
            ['name' => 'admin', 'description' => 'System Administrator with full access'],
            ['name' => 'instructor', 'description' => 'Faculty member who can request and manage room bookings'],
            ['name' => 'student', 'description' => 'Student who can search classrooms, view schedules, and read announcements'],
        ];

        foreach ($rolesData as $role) {
            $exists = $db->table('roles')->where('name', $role['name'])->countAllResults();
            if ($exists === 0) {
                $db->table('roles')->insert([
                    'name'        => $role['name'],
                    'description' => $role['description'],
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }

        $roles = [];
        foreach ($db->table('roles')->get()->getResultArray() as $r) {
            $roles[$r['name']] = (int) $r['id'];
        }

        // 2. Departments
        $deptsData = [
            ['code' => 'CET', 'name' => 'College of Engineering and Technology'],
            ['code' => 'CASE', 'name' => 'College of Arts and Sciences Education'],
            ['code' => 'CBA', 'name' => 'College of Business Administration'],
            ['code' => 'Marine', 'name' => 'College of Maritime Education'],
            ['code' => 'Psychology', 'name' => 'Psychology Department'],
            ['code' => 'IT', 'name' => 'Information Technology'],
        ];

        foreach ($deptsData as $dept) {
            $exists = $db->table('departments')->where('code', $dept['code'])->countAllResults();
            if ($exists === 0) {
                $db->table('departments')->insert([
                    'code'       => $dept['code'],
                    'name'       => $dept['name'],
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        $departments = [];
        foreach ($db->table('departments')->get()->getResultArray() as $d) {
            $departments[$d['code']] = (int) $d['id'];
        }

        // 3. Test Users
        $usersData = [
            [
                'id_number'      => 'admin',
                'password_hash'  => password_hash('admin123', PASSWORD_DEFAULT),
                'first_name'     => 'System',
                'last_name'      => 'Administrator',
                'email'          => 'admin@purisima.edu.ph',
                'contact_number' => '09170000001',
                'role_id'        => $roles['admin'],
                'department_id'  => $departments['IT'] ?? null,
                'course'         => null,
                'year_level'     => null,
                'is_active'      => 1,
            ],
            [
                'id_number'      => '202400645',
                'password_hash'  => password_hash('instructor2024', PASSWORD_DEFAULT),
                'first_name'     => 'Juan',
                'last_name'      => 'Dela Cruz',
                'email'          => 'jdelacruz@purisima.edu.ph',
                'contact_number' => '09123456789',
                'role_id'        => $roles['instructor'],
                'department_id'  => $departments['CET'] ?? null,
                'course'         => null,
                'year_level'     => null,
                'is_active'      => 1,
            ],
            [
                'id_number'      => '202401001',
                'password_hash'  => password_hash('purisima2024', PASSWORD_DEFAULT),
                'first_name'     => 'John',
                'last_name'      => 'Doe',
                'email'          => 'jdoe@purisima.edu.ph',
                'contact_number' => '09987654321',
                'role_id'        => $roles['student'],
                'department_id'  => null,
                'course'         => 'BSIT',
                'year_level'     => '3rd Year',
                'is_active'      => 1,
            ],
        ];

        foreach ($usersData as $usr) {
            $exists = $db->table('users')->where('id_number', $usr['id_number'])->countAllResults();
            if ($exists === 0) {
                $usr['created_at'] = $now;
                $usr['updated_at'] = $now;
                $db->table('users')->insert($usr);
            }
        }

        $adminUser = $db->table('users')->where('id_number', 'admin')->get()->getRowArray();
        $instructorUser = $db->table('users')->where('id_number', '202400645')->get()->getRowArray();

        // 4. Sample Classrooms (~15 rooms)
        $roomsData = [
            ['room_name' => 'Room 224', 'dept' => 'CET', 'capacity' => 24, 'status' => 'Available', 'maintenance_note' => null],
            ['room_name' => 'Room 101', 'dept' => 'Psychology', 'capacity' => 36, 'status' => 'Occupied', 'maintenance_note' => null],
            ['room_name' => 'DABBA', 'dept' => 'Marine', 'capacity' => 80, 'status' => 'Maintenance', 'maintenance_note' => 'Simulation projection unit maintenance in progress'],
            ['room_name' => 'Lab D', 'dept' => 'IT', 'capacity' => 20, 'status' => 'Available', 'maintenance_note' => null],
            ['room_name' => 'Room 102', 'dept' => 'CASE', 'capacity' => 40, 'status' => 'Available', 'maintenance_note' => null],
            ['room_name' => 'Room 205', 'dept' => 'CBA', 'capacity' => 45, 'status' => 'Available', 'maintenance_note' => null],
            ['room_name' => 'Room 301', 'dept' => 'CET', 'capacity' => 30, 'status' => 'Available', 'maintenance_note' => null],
            ['room_name' => 'Lab A', 'dept' => 'IT', 'capacity' => 25, 'status' => 'Occupied', 'maintenance_note' => null],
            ['room_name' => 'Lab B', 'dept' => 'IT', 'capacity' => 30, 'status' => 'Available', 'maintenance_note' => null],
            ['room_name' => 'Room 210', 'dept' => 'CASE', 'capacity' => 35, 'status' => 'Available', 'maintenance_note' => null],
            ['room_name' => 'Room 105', 'dept' => 'Psychology', 'capacity' => 40, 'status' => 'Available', 'maintenance_note' => null],
            ['room_name' => 'Auditorium', 'dept' => 'CASE', 'capacity' => 150, 'status' => 'Available', 'maintenance_note' => null],
            ['room_name' => 'Room 304', 'dept' => 'CBA', 'capacity' => 50, 'status' => 'Maintenance', 'maintenance_note' => 'Air conditioning compressor maintenance'],
            ['room_name' => 'Bridge Sim', 'dept' => 'Marine', 'capacity' => 30, 'status' => 'Occupied', 'maintenance_note' => null],
            ['room_name' => 'Room 220', 'dept' => 'CET', 'capacity' => 35, 'status' => 'Available', 'maintenance_note' => null],
        ];

        foreach ($roomsData as $room) {
            $exists = $db->table('rooms')->where('room_name', $room['room_name'])->countAllResults();
            if ($exists === 0 && isset($departments[$room['dept']])) {
                $db->table('rooms')->insert([
                    'room_name'        => $room['room_name'],
                    'department_id'    => $departments[$room['dept']],
                    'capacity'         => $room['capacity'],
                    'status'           => $room['status'],
                    'maintenance_note' => $room['maintenance_note'],
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            }
        }

        // 5. Announcements (exactly 1 pinned)
        $adminId = $adminUser ? (int) $adminUser['id'] : 1;
        $announcementsData = [
            [
                'title'     => 'Campus Classroom Policy & Booking Guidelines',
                'message'   => 'All instructors and student representatives are reminded that classroom bookings must be finalized at least 24 hours in advance. Please ensure all air conditioning and projector equipment are turned off after class sessions.',
                'is_pinned' => 1,
                'author_id' => $adminId,
            ],
            [
                'title'     => 'CET Building Maintenance Schedule',
                'message'   => 'Routine electrical maintenance will be conducted in the Engineering Hall this Saturday. Classrooms on the 2nd floor will be temporarily unavailable from 8:00 AM to 12:00 PM.',
                'is_pinned' => 0,
                'author_id' => $adminId,
            ],
            [
                'title'     => 'Midterm Examination Room Assignments',
                'message'   => 'Midterm examination room schedules for the 1st semester have been published. Please check your assigned room details and schedules through the CPC RoomFinder portal.',
                'is_pinned' => 0,
                'author_id' => $adminId,
            ],
        ];

        foreach ($announcementsData as $ann) {
            $exists = $db->table('announcements')->where('title', $ann['title'])->countAllResults();
            if ($exists === 0) {
                $ann['created_at'] = $now;
                $ann['updated_at'] = $now;
                $db->table('announcements')->insert($ann);
            }
        }

        // 6. Sample Initial Bookings for Demo
        if ($instructorUser) {
            $room101 = $db->table('rooms')->where('room_name', 'Room 101')->get()->getRowArray();
            $room224 = $db->table('rooms')->where('room_name', 'Room 224')->get()->getRowArray();
            $today = date('Y-m-d');
            $tomorrow = date('Y-m-d', strtotime('+1 day'));

            if ($room101) {
                $exists = $db->table('bookings')
                    ->where('room_id', $room101['id'])
                    ->where('booking_date', $today)
                    ->countAllResults();
                if ($exists === 0) {
                    $db->table('bookings')->insert([
                        'room_id'      => $room101['id'],
                        'user_id'      => $instructorUser['id'],
                        'booking_date' => $today,
                        'start_time'   => '08:00:00',
                        'end_time'     => '18:00:00',
                        'purpose'      => 'Psychology 101 - General Psychology Lecture',
                        'status'       => 'approved',
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }
            }

            if ($room224) {
                $exists = $db->table('bookings')
                    ->where('room_id', $room224['id'])
                    ->where('booking_date', $tomorrow)
                    ->countAllResults();
                if ($exists === 0) {
                    $db->table('bookings')->insert([
                        'room_id'      => $room224['id'],
                        'user_id'      => $instructorUser['id'],
                        'booking_date' => $tomorrow,
                        'start_time'   => '10:00:00',
                        'end_time'     => '12:00:00',
                        'purpose'      => 'CET 312 - Thermodynamics Laboratory Practice',
                        'status'       => 'pending',
                        'created_at'   => $now,
                        'updated_at'   => $now,
                    ]);
                }
            }
        }
    }
}

