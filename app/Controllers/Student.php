<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use App\Models\DepartmentModel;
use App\Models\RoomModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Student extends BaseController
{
    protected RoomModel $roomModel;
    protected AnnouncementModel $announcementModel;
    protected DepartmentModel $departmentModel;

    public function __construct()
    {
        $this->roomModel = new RoomModel();
        $this->announcementModel = new AnnouncementModel();
        $this->departmentModel = new DepartmentModel();
    }

    /**
     * Student Portal Dashboard
     */
    public function dashboard(): string
    {
        $pinnedAnnouncement = $this->announcementModel->getPinnedAnnouncement();
        $recentAnnouncements = $this->announcementModel->getPublishedAnnouncements(3);
        $totalRooms = $this->roomModel->countAllResults();
        $availableRooms = $this->roomModel->where('status', 'Available')->countAllResults();

        return view('student/dashboard', [
            'title'               => 'Student Dashboard - CPC RoomFinder',
            'activeNav'           => 'home',
            'pinnedAnnouncement'  => $pinnedAnnouncement,
            'recentAnnouncements' => $recentAnnouncements,
            'totalRooms'          => $totalRooms,
            'availableRooms'      => $availableRooms,
        ]);
    }

    /**
     * Classroom directory with search and filter parameters (GET /student/rooms)
     */
    public function rooms(): string
    {
        $keyword = $this->request->getGet('q');
        $dept = $this->request->getGet('department');
        $capacity = $this->request->getGet('capacity') ? (int) $this->request->getGet('capacity') : null;
        $status = $this->request->getGet('status');

        // Strictly omit maintenance_note for student viewers
        $rooms = $this->roomModel->getRoomsWithFilters($keyword, $dept, $capacity, $status, false);

        // Attach live occupancy info
        foreach ($rooms as &$r) {
            $r['occupancy'] = $this->roomModel->getActiveOccupancy((int) $r['id']);
        }
        unset($r);

        $departments = $this->departmentModel->orderBy('name', 'ASC')->findAll();

        return view('student/rooms', [
            'title'       => 'Find Rooms - CPC RoomFinder',
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
     * Read-only classroom detail and today's schedule (GET /student/rooms/(:segment))
     */
    public function roomDetail(string $idOrName): string
    {
        $idOrName = urldecode($idOrName);

        // Retrieve room details with maintenance note excluded
        $room = $this->roomModel->getRoomWithDetails($idOrName, false);

        if (!$room) {
            throw PageNotFoundException::forPageNotFound('Classroom "' . esc($idOrName) . '" was not found.');
        }

        // Fetch chronological approved bookings for today
        $schedule = $this->roomModel->getApprovedSchedule((int) $room['id'], date('Y-m-d'));
        $occupancy = $this->roomModel->getActiveOccupancy((int) $room['id']);

        return view('student/room_detail', [
            'title'     => esc($room['room_name']) . ' Schedule - CPC RoomFinder',
            'activeNav' => 'rooms',
            'room'      => $room,
            'schedule'  => $schedule,
            'occupancy' => $occupancy,
        ]);
    }

    /**
     * Campus announcements for students (GET /student/announcements)
     */
    public function announcements(): string
    {
        $announcements = $this->announcementModel->getPublishedAnnouncements();
        $pinnedAnnouncement = $this->announcementModel->getPinnedAnnouncement();

        return view('student/announcements', [
            'title'              => 'Campus Announcements - CPC RoomFinder',
            'activeNav'          => 'announcements',
            'announcements'      => $announcements,
            'pinnedAnnouncement' => $pinnedAnnouncement,
        ]);
    }

    /**
     * Student Profile Page
     */
    public function profile(): string
    {
        return view('profile/edit', [
            'title'     => 'My Profile - CPC RoomFinder',
            'userRole'  => 'student',
            'activeNav' => 'profile',
        ]);
    }
}
