<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use App\Models\BookingModel;
use App\Models\DepartmentModel;
use App\Models\RoleModel;
use App\Models\RoomModel;
use App\Models\UserModel;

class Admin extends BaseController
{
    protected RoomModel $roomModel;
    protected UserModel $userModel;
    protected BookingModel $bookingModel;
    protected AnnouncementModel $announcementModel;
    protected DepartmentModel $departmentModel;
    protected RoleModel $roleModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->userModel = new UserModel();
        $this->bookingModel = new BookingModel();
        $this->announcementModel = new AnnouncementModel();
        $this->departmentModel = new DepartmentModel();
        $this->roleModel = new RoleModel();
    }

    /**
     * Admin Dashboard Overview (GET /admin/overview)
     */
    public function overview(): string
    {
        $totalRooms = $this->roomModel->countAllResults();
        $totalUsers = $this->userModel->where('is_active', 1)->countAllResults();
        $bookingsToday = $this->bookingModel->getBookingsTodayCount();
        $pendingApprovals = $this->bookingModel->getPendingCount();

        $pinnedAnnouncement = $this->announcementModel->getPinnedAnnouncement();
        $recentBookings = $this->bookingModel->getRecentBookings(5);

        return view('admin/overview', [
            'title'              => 'Overview - CPC RoomFinder | Admin Portal',
            'activeNav'          => 'overview',
            'totalRooms'         => $totalRooms,
            'totalUsers'         => $totalUsers,
            'bookingsToday'      => $bookingsToday,
            'pendingApprovals'   => $pendingApprovals,
            'pinnedAnnouncement' => $pinnedAnnouncement,
            'recentBookings'     => $recentBookings,
        ]);
    }

    /**
     * Classroom Management (GET /admin/rooms)
     */
    public function rooms(): string
    {
        $keyword = $this->request->getGet('q');
        $dept = $this->request->getGet('department');
        $capacity = $this->request->getGet('capacity') ? (int) $this->request->getGet('capacity') : null;
        $status = $this->request->getGet('status');

        $rooms = $this->roomModel->getRoomsWithFilters($keyword, $dept, $capacity, $status, true);

        // Attach live occupancy data for each room
        foreach ($rooms as &$r) {
            $r['occupancy'] = $this->roomModel->getActiveOccupancy((int) $r['id']);
        }
        unset($r);

        $departments = $this->departmentModel->orderBy('name', 'ASC')->findAll();

        return view('admin/rooms', [
            'title'       => 'Manage Rooms - CPC RoomFinder | Admin Portal',
            'activeNav'   => 'rooms',
            'rooms'       => $rooms,
            'departments' => $departments,
            'filters'     => [
                'q'          => $keyword,
                'department' => $dept,
                'capacity'   => $capacity,
                'status'     => $status,
            ],
        ]);
    }

    /**
     * Create classroom (POST /admin/rooms)
     */
    public function createRoom()
    {
        $rules = [
            'room_name'     => 'required|max_length[100]|is_unique[rooms.room_name]',
            'department_id' => 'required|is_natural_no_zero',
            'capacity'      => 'required|is_natural_no_zero',
            'status'        => 'required|in_list[Available,Occupied,Maintenance]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'room_name'        => trim((string) $this->request->getPost('room_name')),
            'department_id'    => (int) $this->request->getPost('department_id'),
            'capacity'         => (int) $this->request->getPost('capacity'),
            'status'           => (string) $this->request->getPost('status'),
            'maintenance_note' => $this->request->getPost('maintenance_note') ? trim((string) $this->request->getPost('maintenance_note')) : null,
        ];

        $this->roomModel->insert($data);

        return redirect()->to(base_url('admin/rooms'))->with('success', 'Classroom "' . esc($data['room_name']) . '" created successfully.');
    }

    /**
     * Update classroom (POST /admin/rooms/update/(:num))
     */
    public function updateRoom(int $id)
    {
        $room = $this->roomModel->find($id);
        if (!$room) {
            return redirect()->to(base_url('admin/rooms'))->with('error', 'Classroom not found.');
        }

        $rules = [
            'room_name'     => "required|max_length[100]|is_unique[rooms.room_name,id,{$id}]",
            'department_id' => 'required|is_natural_no_zero',
            'capacity'      => 'required|is_natural_no_zero',
            'status'        => 'required|in_list[Available,Occupied,Maintenance]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'room_name'        => trim((string) $this->request->getPost('room_name')),
            'department_id'    => (int) $this->request->getPost('department_id'),
            'capacity'         => (int) $this->request->getPost('capacity'),
            'status'           => (string) $this->request->getPost('status'),
            'maintenance_note' => $this->request->getPost('maintenance_note') ? trim((string) $this->request->getPost('maintenance_note')) : null,
        ];

        $this->roomModel->update($id, $data);

        return redirect()->to(base_url('admin/rooms'))->with('success', 'Classroom "' . esc($data['room_name']) . '" updated successfully.');
    }

    /**
     * Delete classroom (POST /admin/rooms/delete/(:num))
     */
    public function deleteRoom(int $id)
    {
        $room = $this->roomModel->find($id);
        if (!$room) {
            return redirect()->to(base_url('admin/rooms'))->with('error', 'Classroom not found.');
        }

        // Prevent deletion when active bookings exist
        $activeBookings = $this->bookingModel->where('room_id', $id)
            ->whereIn('status', ['pending', 'approved'])
            ->countAllResults();

        if ($activeBookings > 0) {
            return redirect()->to(base_url('admin/rooms'))
                ->with('error', 'Cannot delete classroom "' . esc($room['room_name']) . '" because it has active pending or approved bookings.');
        }

        $this->roomModel->delete($id);

        return redirect()->to(base_url('admin/rooms'))->with('success', 'Classroom "' . esc($room['room_name']) . '" deleted successfully.');
    }

    /**
     * User Management (GET /admin/users)
     */
    public function users(): string
    {
        $roleFilter = $this->request->getGet('role');
        $search = $this->request->getGet('q');

        $users = $this->userModel->getAllWithDetails($roleFilter, $search);
        $roles = $this->roleModel->findAll();
        $departments = $this->departmentModel->orderBy('name', 'ASC')->findAll();

        return view('admin/users', [
            'title'       => 'Manage Users - CPC RoomFinder | Admin Portal',
            'activeNav'   => 'users',
            'users'       => $users,
            'roles'       => $roles,
            'departments' => $departments,
            'roleFilter'  => $roleFilter,
            'search'      => $search,
        ]);
    }

    /**
     * Toggle active/inactive user status (POST /admin/users/toggle/(:num))
     */
    public function toggleUserStatus(int $id)
    {
        if ($id === (int) session()->get('user_id')) {
            return redirect()->to(base_url('admin/users'))->with('error', 'You cannot deactivate your own administrative account.');
        }

        $newStatus = $this->userModel->toggleActiveStatus($id);

        if ($newStatus === null) {
            return redirect()->to(base_url('admin/users'))->with('error', 'User account not found.');
        }

        $statusText = $newStatus === 1 ? 'activated' : 'deactivated';
        return redirect()->to(base_url('admin/users'))->with('success', "User account has been {$statusText}.");
    }

    /**
     * Change user role (POST /admin/users/change-role/(:num))
     */
    public function changeUserRole(int $id)
    {
        $roleId = (int) $this->request->getPost('role_id');
        $role = $this->roleModel->find($roleId);

        if (!$role) {
            return redirect()->to(base_url('admin/users'))->with('error', 'Invalid role selected.');
        }

        if ($id === (int) session()->get('user_id') && strtolower($role['name']) !== 'admin') {
            return redirect()->to(base_url('admin/users'))->with('error', 'You cannot remove your own administrative role.');
        }

        $this->userModel->update($id, ['role_id' => $roleId]);

        return redirect()->to(base_url('admin/users'))->with('success', 'User role updated successfully.');
    }

    /**
     * Booking Moderation (GET /admin/bookings)
     */
    public function bookings(): string
    {
        $statusFilter = $this->request->getGet('status');
        $search = $this->request->getGet('q');

        $bookings = $this->bookingModel->getAllForAdmin($statusFilter, $search);

        return view('admin/bookings', [
            'title'        => 'Manage Bookings - CPC RoomFinder | Admin Portal',
            'activeNav'    => 'bookings',
            'bookings'     => $bookings,
            'statusFilter' => $statusFilter,
            'search'       => $search,
        ]);
    }

    /**
     * Approve Booking (POST /admin/bookings/approve/(:num))
     */
    public function approveBooking(int $id)
    {
        $result = $this->bookingModel->approveBooking($id);

        if ($result['success']) {
            return redirect()->to(base_url('admin/bookings'))->with('success', $result['message']);
        }

        return redirect()->to(base_url('admin/bookings'))->with('error', $result['message']);
    }

    /**
     * Reject Booking (POST /admin/bookings/reject/(:num))
     */
    public function rejectBooking(int $id)
    {
        $result = $this->bookingModel->rejectBooking($id);

        if ($result['success']) {
            return redirect()->to(base_url('admin/bookings'))->with('success', $result['message']);
        }

        return redirect()->to(base_url('admin/bookings'))->with('error', $result['message']);
    }

    /**
     * Announcement Management (GET /admin/announcements)
     */
    public function announcements(): string
    {
        $announcements = $this->announcementModel->getPublishedAnnouncements();
        $pinnedAnnouncement = $this->announcementModel->getPinnedAnnouncement();

        return view('admin/announcements', [
            'title'              => 'Announcements - CPC RoomFinder | Admin Portal',
            'activeNav'          => 'announcements',
            'announcements'      => $announcements,
            'pinnedAnnouncement' => $pinnedAnnouncement,
        ]);
    }

    /**
     * Create Announcement (POST /admin/announcements)
     */
    public function storeAnnouncement()
    {
        $rules = [
            'title'   => 'required|max_length[255]',
            'message' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $authorId = (int) session()->get('user_id');
        $isPinned = $this->request->getPost('is_pinned') ? 1 : 0;

        $data = [
            'title'     => trim((string) $this->request->getPost('title')),
            'message'   => trim((string) $this->request->getPost('message')),
            'is_pinned' => $isPinned,
            'author_id' => $authorId,
        ];

        $this->announcementModel->saveWithPinManagement($data);

        return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement published successfully.');
    }

    /**
     * Update Announcement (POST /admin/announcements/update/(:num))
     */
    public function updateAnnouncement(int $id)
    {
        $ann = $this->announcementModel->find($id);
        if (!$ann) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        $rules = [
            'title'   => 'required|max_length[255]',
            'message' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $isPinned = $this->request->getPost('is_pinned') ? 1 : 0;

        $data = [
            'title'     => trim((string) $this->request->getPost('title')),
            'message'   => trim((string) $this->request->getPost('message')),
            'is_pinned' => $isPinned,
        ];

        $this->announcementModel->saveWithPinManagement($data, $id);

        return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement updated successfully.');
    }

    /**
     * Delete Announcement (POST /admin/announcements/delete/(:num))
     */
    public function deleteAnnouncement(int $id)
    {
        $ann = $this->announcementModel->find($id);
        if (!$ann) {
            return redirect()->to(base_url('admin/announcements'))->with('error', 'Announcement not found.');
        }

        $this->announcementModel->delete($id);

        return redirect()->to(base_url('admin/announcements'))->with('success', 'Announcement deleted successfully.');
    }

    /**
     * Admin Profile page
     */
    public function profile(): string
    {
        return view('profile/edit', [
            'title'     => 'Admin Profile - CPC RoomFinder',
            'userRole'  => 'admin',
            'activeNav' => 'profile',
        ]);
    }
}
