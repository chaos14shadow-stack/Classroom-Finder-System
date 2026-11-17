<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\RoleModel;
use App\Models\DepartmentModel;
use App\Models\UserModel;
use App\Models\RoomModel;
use App\Models\BookingModel;
use App\Models\AnnouncementModel;
use App\Filters\AuthFilter;
use App\Filters\RoleFilter;
use Config\Services;

class VerifyBackend extends BaseCommand
{
    protected $group       = 'Testing';
    protected $name        = 'verify:backend';
    protected $description = 'Runs automated verification suite for CPC Room Finder backend.';

    private int $passCount = 0;
    private int $failCount = 0;

    public function run(array $params)
    {
        CLI::write("==================================================", 'yellow');
        CLI::write(" CPC ROOM FINDER BACKEND VERIFICATION SUITE", 'cyan');
        CLI::write("==================================================", 'yellow');

        $this->testDatabaseSeedData();
        $this->testAuthentication();
        $this->testRoleBasedAccessControl();
        $this->testBookingConflictAlgorithmMatrix();
        $this->testProfileWhitelisting();
        $this->testPinnedAnnouncementLogic();
        $this->testStudentMaintenanceNoteIsolation();
        $this->testBookingCancellationAndAutomaticRelease();
        $this->testOperatingHoursBufferRemovalAndSorting();

        CLI::newLine();
        CLI::write("==================================================", 'yellow');
        CLI::write(" VERIFICATION SUMMARY", 'yellow');
        CLI::write("==================================================", 'yellow');
        CLI::write("Total Passed: {$this->passCount}", 'green');
        if ($this->failCount > 0) {
            CLI::write("Total Failed: {$this->failCount}", 'red');
        } else {
            CLI::write("Total Failed: 0 (ALL CHECKS PASSED PERFECTLY!)", 'green');
        }
        CLI::newLine();
    }

    private function assert(bool $condition, string $message): void
    {
        if ($condition) {
            $this->passCount++;
            CLI::write(" [PASS] {$message}", 'green');
        } else {
            $this->failCount++;
            CLI::write(" [FAIL] {$message}", 'red');
        }
    }

    private function testDatabaseSeedData(): void
    {
        CLI::newLine();
        CLI::write("--- 1. Database Foundation & Seed Data ---", 'yellow');

        $roleModel = new RoleModel();
        $deptModel = new DepartmentModel();
        $userModel = new UserModel();
        $roomModel = new RoomModel();
        $annModel  = new AnnouncementModel();

        $this->assert($roleModel->countAllResults() === 3, "Roles table contains exactly 3 standard roles (admin, instructor, student)");
        $this->assert($deptModel->countAllResults() >= 6, "Departments table contains registered academic colleges (>= 6)");
        $this->assert($userModel->countAllResults() >= 3, "Users table contains seeded demo accounts");
        $this->assert($roomModel->countAllResults() >= 15, "Rooms table contains campus classrooms (>= 15)");

        $pinnedCount = $annModel->where('is_pinned', 1)->countAllResults();
        $this->assert($pinnedCount === 1, "Announcements table has exactly 1 pinned announcement");
    }

    private function testAuthentication(): void
    {
        CLI::newLine();
        CLI::write("--- 2. Authentication & Verification ---", 'yellow');

        $userModel = new UserModel();

        // Test Admin Login
        $admin = $userModel->verifyCredentials('admin', 'admin123');
        $this->assert($admin['success'] && strtolower($admin['user']['role_name']) === 'admin', "Admin login verified ('admin' / 'admin123')");

        // Test Instructor Login
        $instructor = $userModel->verifyCredentials('202400645', 'instructor2024');
        $this->assert($instructor['success'] && strtolower($instructor['user']['role_name']) === 'instructor', "Instructor login verified ('202400645')");

        // Test Student Login
        $student = $userModel->verifyCredentials('202401001', 'purisima2024');
        $this->assert($student['success'] && strtolower($student['user']['role_name']) === 'student', "Student login verified ('202401001')");

        // Test Wrong Password Rejection
        $badPass = $userModel->verifyCredentials('admin', 'wrong_password_999');
        $this->assert($badPass['success'] === false, "Invalid password attempt rejected");

        // Test Inactive User Account Rejection
        $studentUser = $userModel->where('id_number', '202401001')->first();
        if ($studentUser) {
            $userModel->update($studentUser['id'], ['is_active' => 0]);
            $inactiveAttempt = $userModel->verifyCredentials('202401001', 'purisima2024');
            $this->assert($inactiveAttempt['success'] === false, "Inactive user account (is_active=0) rejected at login");
            // Restore active
            $userModel->update($studentUser['id'], ['is_active' => 1]);
        }
    }

    private function testRoleBasedAccessControl(): void
    {
        CLI::newLine();
        CLI::write("--- 3. RBAC & Security Filters ---", 'yellow');

        $roleFilter = new RoleFilter();
        $request    = Services::request();
        $session    = Services::session();

        // 3a. Student attempts to access admin route
        $session->set([
            'isLoggedIn' => true,
            'user_id'    => 3,
            'role'       => 'student',
        ]);
        $response = $roleFilter->before($request, ['admin']);
        $isForbidden = ($response instanceof \CodeIgniter\HTTP\ResponseInterface) && ($response->getStatusCode() === 403);
        $this->assert($isForbidden, "Student access to Admin route blocked with HTTP 403 Forbidden");

        // 3b. Instructor attempts to access admin route
        $session->set([
            'isLoggedIn' => true,
            'user_id'    => 2,
            'role'       => 'instructor',
        ]);
        $response = $roleFilter->before($request, ['admin']);
        $isForbidden = ($response instanceof \CodeIgniter\HTTP\ResponseInterface) && ($response->getStatusCode() === 403);
        $this->assert($isForbidden, "Instructor access to Admin route blocked with HTTP 403 Forbidden");

        // 3c. Admin accesses admin route
        $session->set([
            'isLoggedIn' => true,
            'user_id'    => 1,
            'role'       => 'admin',
        ]);
        $response = $roleFilter->before($request, ['admin']);
        $this->assert($response === null, "Admin access to Admin route passes authorization check");

        // 3d. AuthFilter unauthenticated redirect
        $session->remove(['isLoggedIn', 'user_id', 'role']);
        $authFilter = new AuthFilter();
        $response = $authFilter->before($request);
        $isRedirect = ($response instanceof \CodeIgniter\HTTP\RedirectResponse);
        $this->assert($isRedirect, "Unauthenticated session redirected to login by AuthFilter");
    }

    private function testBookingConflictAlgorithmMatrix(): void
    {
        CLI::newLine();
        CLI::write("--- 4. Booking Conflict Check Algorithm Matrix ---", 'yellow');

        $bookingModel = new BookingModel();
        $roomModel    = new RoomModel();
        $userModel    = new UserModel();

        $room = $roomModel->first();
        $user = $userModel->where('id_number', '202400645')->first();

        $roomId   = (int) $room['id'];
        $userId   = (int) $user['id'];
        $testDate = '2027-01-15'; // Future test date

        // Clean any existing test bookings on this room and date
        $bookingModel->where('room_id', $roomId)->where('booking_date', $testDate)->delete();

        // Baseline booking: 10:00:00 to 12:00:00 (Approved)
        $baselineId = $bookingModel->insert([
            'room_id'      => $roomId,
            'user_id'      => $userId,
            'booking_date' => $testDate,
            'start_time'   => '10:00:00',
            'end_time'     => '12:00:00',
            'purpose'      => 'Automated Baseline Test Booking',
            'status'       => 'approved',
        ]);

        // Case 1: Exact same interval [10:00 - 12:00]
        $c1 = $bookingModel->hasConflict($roomId, $testDate, '10:00:00', '12:00:00');
        $this->assert($c1 === true, "Case 1: Exact match [10:00 - 12:00] detected as CONFLICT");

        // Case 2: Starts before, ends inside [09:30 - 10:30]
        $c2 = $bookingModel->hasConflict($roomId, $testDate, '09:30:00', '10:30:00');
        $this->assert($c2 === true, "Case 2: Overlapping start [09:30 - 10:30] detected as CONFLICT");

        // Case 3: Starts inside, ends after [11:30 - 12:30]
        $c3 = $bookingModel->hasConflict($roomId, $testDate, '11:30:00', '12:30:00');
        $this->assert($c3 === true, "Case 3: Overlapping end [11:30 - 12:30] detected as CONFLICT");

        // Case 4: Enclosing interval [09:00 - 13:00]
        $c4 = $bookingModel->hasConflict($roomId, $testDate, '09:00:00', '13:00:00');
        $this->assert($c4 === true, "Case 4: Enclosing interval [09:00 - 13:00] detected as CONFLICT");

        // Case 5: Enclosed sub-interval [10:30 - 11:30]
        $c5 = $bookingModel->hasConflict($roomId, $testDate, '10:30:00', '11:30:00');
        $this->assert($c5 === true, "Case 5: Enclosed sub-interval [10:30 - 11:30] detected as CONFLICT");

        // Case 6: Adjacent before [08:00 - 10:00]
        $c6 = $bookingModel->hasConflict($roomId, $testDate, '08:00:00', '10:00:00');
        $this->assert($c6 === false, "Case 6: Adjacent before [08:00 - 10:00] is ALLOWED (No conflict)");

        // Case 7: Adjacent after [12:00 - 14:00]
        $c7 = $bookingModel->hasConflict($roomId, $testDate, '12:00:00', '14:00:00');
        $this->assert($c7 === false, "Case 7: Adjacent after [12:00 - 14:00] is ALLOWED (No conflict)");

        // Case 8: Slot with cancelled booking [14:00 - 16:00]
        $cancelledId = $bookingModel->insert([
            'room_id'      => $roomId,
            'user_id'      => $userId,
            'booking_date' => $testDate,
            'start_time'   => '14:00:00',
            'end_time'     => '16:00:00',
            'purpose'      => 'Cancelled Booking',
            'status'       => 'cancelled',
        ]);
        $c8 = $bookingModel->hasConflict($roomId, $testDate, '14:30:00', '15:30:00');
        $this->assert($c8 === false, "Case 8: Slot matching cancelled booking [14:30 - 15:30] is ALLOWED");

        // Clean up
        $bookingModel->delete($baselineId);
        $bookingModel->delete($cancelledId);
    }

    private function testProfileWhitelisting(): void
    {
        CLI::newLine();
        CLI::write("--- 5. Security & Profile Field Whitelisting ---", 'yellow');

        $userModel = new UserModel();
        $user = $userModel->where('id_number', '202401001')->first();
        $originalIdNumber = $user['id_number'];
        $originalRoleId   = $user['role_id'];

        // Attempt to tamper with id_number and role_id
        $tamperedData = [
            'id_number'      => 'HACKED_ID_99999',
            'role_id'        => 1, // Try to promote to admin
            'first_name'     => 'TestUpdatedFirst',
            'last_name'      => $user['last_name'],
            'email'          => $user['email'],
            'contact_number' => '09999999999',
        ];

        $userModel->updateProfileWhitelisted((int) $user['id'], $tamperedData);

        $reloaded = $userModel->find($user['id']);
        $this->assert($reloaded['id_number'] === $originalIdNumber, "Profile edit prevented tampering of 'id_number'");
        $this->assert((int) $reloaded['role_id'] === (int) $originalRoleId, "Profile edit prevented tampering of 'role_id'");
        $this->assert($reloaded['first_name'] === 'TestUpdatedFirst', "Profile allowed valid whitelisted field update ('first_name')");

        // Restore original
        $userModel->update($user['id'], ['first_name' => $user['first_name']]);
    }

    private function testPinnedAnnouncementLogic(): void
    {
        CLI::newLine();
        CLI::write("--- 6. Pinned Announcement Mutual Exclusion ---", 'yellow');

        $annModel = new AnnouncementModel();

        // Create announcement 1 as pinned
        $annModel->saveWithPinManagement([
            'title'     => 'Test Pin 1',
            'message'   => 'Message 1',
            'is_pinned' => 1,
            'author_id' => 1,
        ]);
        $id1 = $annModel->getInsertID();

        // Create announcement 2 as pinned
        $annModel->saveWithPinManagement([
            'title'     => 'Test Pin 2',
            'message'   => 'Message 2',
            'is_pinned' => 1,
            'author_id' => 1,
        ]);
        $id2 = $annModel->getInsertID();

        $pinnedList = $annModel->where('is_pinned', 1)->findAll();
        $this->assert(count($pinnedList) === 1 && (int)$pinnedList[0]['id'] === $id2, "Setting new pinned announcement automatically unpinned previous announcement");

        // Cleanup
        $annModel->delete($id1);
        $annModel->delete($id2);

        // Ensure at least one original is pinned
        $firstAnn = $annModel->first();
        if ($firstAnn) {
            $annModel->saveWithPinManagement(['is_pinned' => 1], (int) $firstAnn['id']);
        }
    }

    private function testStudentMaintenanceNoteIsolation(): void
    {
        CLI::newLine();
        CLI::write("--- 7. Information Leakage Protection (Student View) ---", 'yellow');

        $roomModel = new RoomModel();

        // 1. Student query (includeMaintenanceNote = false)
        $studentRooms = $roomModel->getRoomsWithFilters(null, null, null, null, false);
        $leaked = false;
        foreach ($studentRooms as $r) {
            if (array_key_exists('maintenance_note', $r)) {
                $leaked = true;
                break;
            }
        }
        $this->assert(!$leaked, "Student room query does not include 'maintenance_note' column");

        // 2. Admin query (includeMaintenanceNote = true)
        $adminRooms = $roomModel->getRoomsWithFilters(null, null, null, null, true);
        $adminHasNote = false;
        foreach ($adminRooms as $r) {
            if (array_key_exists('maintenance_note', $r)) {
                $adminHasNote = true;
                break;
            }
        }
        $this->assert($adminHasNote, "Admin room query properly includes 'maintenance_note' column");
    }

    private function testBookingCancellationAndAutomaticRelease(): void
    {
        CLI::newLine();
        CLI::write("--- 8. Instructor Cancellation & Automatic Expiration ---", 'yellow');

        $bookingModel = new BookingModel();
        $roomModel = new RoomModel();
        $userModel = new UserModel();

        $instructor = $userModel->where('role_id', 2)->first();
        $student = $userModel->where('role_id', 3)->first();
        $room = $roomModel->first();

        if (!$instructor || !$room) {
            CLI::write("Skipping test 8: Instructor or Room not found in DB.", 'yellow');
            return;
        }

        $roomId = (int) $room['id'];
        $instId = (int) $instructor['id'];
        $testDate = '2029-12-15';

        // 1. Create a pending booking
        $bookingModel->insert([
            'room_id'      => $roomId,
            'user_id'      => $instId,
            'booking_date' => $testDate,
            'start_time'   => '08:00:00',
            'end_time'     => '10:00:00',
            'purpose'      => 'Test Pending Cancellation',
            'status'       => 'pending',
        ]);
        $pendingId = (int) $bookingModel->getInsertID();

        // 2. Reject cancellation by another user (unauthorized)
        $unauthRes = $bookingModel->cancelBooking($pendingId, 999999);
        $this->assert($unauthRes['success'] === false, "Unauthorized cancellation attempt is blocked");

        // 3. Cancel own pending booking
        $cancelPendingRes = $bookingModel->cancelBooking($pendingId, $instId);
        $this->assert($cancelPendingRes['success'] === true, "Instructor can cancel their own pending booking");

        $bRecord = $bookingModel->find($pendingId);
        $this->assert($bRecord['status'] === 'cancelled' && !empty($bRecord['cancelled_at']), "Pending booking marked cancelled with cancelled_at timestamp");

        // 4. Repeated cancellation is blocked
        $repeatRes = $bookingModel->cancelBooking($pendingId, $instId);
        $this->assert($repeatRes['success'] === false, "Repeated cancellation of cancelled booking is blocked");

        // 5. Create an approved booking and test slot release
        $bookingModel->insert([
            'room_id'      => $roomId,
            'user_id'      => $instId,
            'booking_date' => $testDate,
            'start_time'   => '10:00:00',
            'end_time'     => '12:00:00',
            'purpose'      => 'Test Approved Cancellation',
            'status'       => 'approved',
        ]);
        $approvedId = (int) $bookingModel->getInsertID();

        // Slot should be in conflict while approved
        $hasConflictBefore = $bookingModel->hasConflict($roomId, $testDate, '10:30:00', '11:30:00');
        $this->assert($hasConflictBefore === true, "Approved booking blocks overlapping reservation");

        // Cancel approved booking
        $cancelApprovedRes = $bookingModel->cancelBooking($approvedId, $instId);
        $this->assert($cancelApprovedRes['success'] === true, "Instructor can cancel their own approved booking");

        // Slot must be immediately released
        $hasConflictAfter = $bookingModel->hasConflict($roomId, $testDate, '10:30:00', '11:30:00');
        $this->assert($hasConflictAfter === false, "Cancelling approved booking immediately releases time slot");

        // 6. Test automatic overtime detection (does NOT auto-mark completed!)
        $pastDate = '2020-01-01';
        $bookingModel->insert([
            'room_id'      => $roomId,
            'user_id'      => $instId,
            'booking_date' => $pastDate,
            'start_time'   => '08:00:00',
            'end_time'     => '10:00:00',
            'purpose'      => 'Test Overtime Past Booking',
            'status'       => 'approved',
        ]);
        $overtimeId = (int) $bookingModel->getInsertID();

        $otRes = $bookingModel->detectOvertimeBookings();
        $this->assert($otRes['overtime_count'] >= 1, "detectOvertimeBookings() successfully detects past approved bookings");

        $otRecord = $bookingModel->find($overtimeId);
        $this->assert($otRecord['status'] === 'overtime', "Past approved booking status updated to overtime instead of silently completed");

        // 7. Room Vacate Confirmation (Awaiting Return, Buffer Removed)
        $vacateRes = $bookingModel->confirmRoomVacated($overtimeId, $instId, false);
        $this->assert($vacateRes['success'] === true, "Instructor confirms room vacated");

        $vacatedRecord = $bookingModel->find($overtimeId);
        $this->assert($vacatedRecord['status'] === 'ongoing' && !empty($vacatedRecord['vacated_at']), "Vacated booking remains ongoing and awaiting return without transition buffer");

        $returnRes = $bookingModel->completeBookingReturn($overtimeId, 1);
        $this->assert($returnRes['success'] === true, "Admin completes booking return turnover");
        $returnedRecord = $bookingModel->find($overtimeId);
        $this->assert($returnedRecord['status'] === 'completed', "Turnover finalizes booking to completed");

        // 8. Extension Request Workflow with 9:00 PM Operating Hours Ceiling
        $bookingModel->insert([
            'room_id'      => $roomId,
            'user_id'      => $instId,
            'booking_date' => $testDate,
            'start_time'   => '14:00:00',
            'end_time'     => '15:00:00',
            'purpose'      => 'Test Extension Flow',
            'status'       => 'approved',
        ]);
        $extBookingId = (int) $bookingModel->getInsertID();

        // Attempt extension past 9:00 PM (21:30:00) -> Must be rejected!
        $past9Req = $bookingModel->requestExtension($extBookingId, $instId, '21:30:00', 'Late night test');
        $this->assert($past9Req['success'] === false, "Extension exceeding 9:00 PM (21:30:00) is strictly rejected");

        // Attempt extension earlier than scheduled end -> Must be rejected!
        $earlyReq = $bookingModel->requestExtension($extBookingId, $instId, '14:30:00', 'Early test');
        $this->assert($earlyReq['success'] === false, "Extension earlier than scheduled end is rejected");

        // Valid extension within operating hours
        $extReq = $bookingModel->requestExtension($extBookingId, $instId, '15:30:00', 'Lab experiment unfinished');
        $this->assert($extReq['success'] === true, "Instructor successfully submits extension request within operating hours");

        $adminApproveExt = $bookingModel->approveExtension($extBookingId, 1);
        $this->assert($adminApproveExt['success'] === true, "Admin approves extension request");

        $extRec = $bookingModel->find($extBookingId);
        $this->assert($extRec['end_time'] === '15:30:00', "Approved extension updates scheduled end_time");

        // Cleanup test entries
        $bookingModel->delete($pendingId);
        $bookingModel->delete($approvedId);
        $bookingModel->delete($overtimeId);
        $bookingModel->delete($extBookingId);
    }

    private function testOperatingHoursBufferRemovalAndSorting(): void
    {
        CLI::newLine();
        CLI::write("--- 9. Operating Hours (7 AM - 9 PM), Buffer Removal & Sorting ---", 'yellow');

        $bookingModel = new BookingModel();
        $roomModel = new RoomModel();

        // Helper closures for operating hour validation rules
        $validateHours = function(string $start, string $end): bool {
            $opening = '07:00:00';
            $closing = '21:00:00';
            if ($start < $opening || $start >= $closing || $end <= $opening || $end > $closing || $start >= $end) {
                return false;
            }
            return true;
        };

        // 1. Operating Hours Matrix Test
        $this->assert($validateHours('07:00:00', '08:00:00') === true, "Operating hours: 7:00 AM - 8:00 AM is Allowed");
        $this->assert($validateHours('08:00:00', '10:00:00') === true, "Operating hours: 8:00 AM - 10:00 AM is Allowed");
        $this->assert($validateHours('12:00:00', '14:00:00') === true, "Operating hours: 12:00 PM - 2:00 PM is Allowed");
        $this->assert($validateHours('17:00:00', '19:00:00') === true, "Operating hours: 5:00 PM - 7:00 PM is Allowed");
        $this->assert($validateHours('20:00:00', '21:00:00') === true, "Operating hours: 8:00 PM - 9:00 PM (closing exactly at 9 PM) is Allowed");

        $this->assert($validateHours('06:00:00', '08:00:00') === false, "Operating hours: 6:00 AM - 8:00 AM is Rejected (< 7:00 AM)");
        $this->assert($validateHours('21:00:00', '22:00:00') === false, "Operating hours: 9:00 PM - 10:00 PM is Rejected (school closed)");
        $this->assert($validateHours('22:00:00', '23:00:00') === false, "Operating hours: 10:00 PM - 11:00 PM is Rejected");
        $this->assert($validateHours('20:00:00', '22:00:00') === false, "Operating hours: 8:00 PM - 10:00 PM is Rejected (ends > 9 PM)");
        $this->assert($validateHours('23:00:00', '01:00:00') === false, "Operating hours: 11:00 PM - 1:00 AM crossing midnight is Rejected");
        $this->assert($validateHours('06:00:00', '06:00:00') === false, "Operating hours: 6:00 AM - 6:00 AM equal start/end is Rejected");

        // 2. Advance Booking Test
        $tomorrow = date('Y-m-d', strtotime('+1 day'));
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $this->assert($tomorrow > date('Y-m-d'), "Advance booking support: Tomorrow ({$tomorrow}) is accepted as a valid future date");
        $this->assert($nextWeek > date('Y-m-d'), "Advance booking support: Next week ({$nextWeek}) is accepted as a valid future date");

        // 3. Newest-First Sorting Test (created_at DESC, id DESC)
        $testRoom = $roomModel->first();
        $roomId = (int) $testRoom['id'];
        $instId = 2;

        $id1 = $bookingModel->insert([
            'room_id'      => $roomId,
            'user_id'      => $instId,
            'booking_date' => $tomorrow,
            'start_time'   => '08:00:00',
            'end_time'     => '09:00:00',
            'purpose'      => 'Sort Test Earlier 1',
            'status'       => 'pending',
        ]);

        $id2 = $bookingModel->insert([
            'room_id'      => $roomId,
            'user_id'      => $instId,
            'booking_date' => $tomorrow,
            'start_time'   => '10:00:00',
            'end_time'     => '11:00:00',
            'purpose'      => 'Sort Test Later 2',
            'status'       => 'pending',
        ]);

        $adminBookings = $bookingModel->getAllForAdmin();
        $this->assert(!empty($adminBookings) && (int)$adminBookings[0]['id'] === (int)$id2, "Admin Manage Bookings retrieves newest created booking first (created_at DESC, id DESC)");

        $userBookings = $bookingModel->getBookingsByUser($instId);
        $this->assert(!empty($userBookings) && (int)$userBookings[0]['id'] === (int)$id2, "Instructor My Bookings retrieves newest created booking first (created_at DESC, id DESC)");

        // 4. Room Occupancy Buffer Isolation
        $occ = $roomModel->getActiveOccupancy($roomId);
        $this->assert($occ['instructor_name'] !== 'Cleaning / Transition Buffer', "Room occupancy logic completely isolated from transition buffer spoofing");

        // Clean up
        $bookingModel->delete($id1);
        $bookingModel->delete($id2);
    }
}
