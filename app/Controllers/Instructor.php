<?php

namespace App\Controllers;

use App\Models\AnnouncementModel;
use App\Models\BookingModel;
use App\Models\DepartmentModel;
use App\Models\RoomModel;

class Instructor extends BaseController
{
    protected BookingModel $bookingModel;
    protected RoomModel $roomModel;
    protected AnnouncementModel $announcementModel;
    protected DepartmentModel $departmentModel;

    public function __construct()
    {
        $this->bookingModel = new BookingModel();
        $this->roomModel = new RoomModel();
        $this->announcementModel = new AnnouncementModel();
        $this->departmentModel = new DepartmentModel();
    }

    public function dashboard()
    {
        return $this->bookings();
    }

    /**
     * Instructor My Bookings list (GET /instructor/bookings)
     */
    public function bookings(): string
    {
        $userId = (int) session()->get('user_id');
        $statusFilter = $this->request->getGet('status') ?? 'all';

        $bookings = $this->bookingModel->getBookingsByUser($userId, $statusFilter);

        return view('instructor/bookings', [
            'title'        => 'My Bookings - CPC RoomFinder',
            'activeNav'    => 'bookings',
            'bookings'     => $bookings,
            'statusFilter' => $statusFilter,
        ]);
    }

    /**
     * Room directory list for selecting a classroom to book
     */
    public function bookSearch(): string
    {
        if ($this->request->getGet('form') === '1') {
            return $this->bookRoom();
        }

        $keyword = $this->request->getGet('q');
        $dept = $this->request->getGet('department');
        $capacity = $this->request->getGet('capacity') ? (int) $this->request->getGet('capacity') : null;

        $rooms = $this->roomModel->getRoomsWithFilters($keyword, $dept, $capacity, 'all', true);
        $departments = $this->departmentModel->orderBy('name', 'ASC')->findAll();

        return view('instructor/book_search', [
            'title'       => 'Book a Room - CPC RoomFinder',
            'activeNav'   => 'book',
            'rooms'       => $rooms,
            'departments' => $departments,
            'filters'     => [
                'q'          => $keyword,
                'department' => $dept,
                'capacity'   => $capacity,
            ],
        ]);
    }

    /**
     * Room Booking request form
     */
    public function bookRoom(): string
    {
        $roomParam = $this->request->getGet('room');
        $roomIdParam = $this->request->getGet('room_id');

        $selectedRoom = null;
        if (!empty($roomIdParam)) {
            $selectedRoom = $this->roomModel->getRoomWithDetails($roomIdParam, true);
        } elseif (!empty($roomParam)) {
            $selectedRoom = $this->roomModel->getRoomWithDetails($roomParam, true);
        }

        $availableRooms = $this->roomModel->where('status !=', 'Maintenance')->orderBy('room_name', 'ASC')->findAll();

        return view('instructor/book_room', [
            'title'          => 'Book a Room - CPC RoomFinder',
            'activeNav'      => 'book',
            'selectedRoom'   => $selectedRoom['room_name'] ?? ($roomParam ?? 'Room 224'),
            'roomDetails'    => $selectedRoom,
            'availableRooms' => $availableRooms,
        ]);
    }

    /**
     * Submit new booking request with concurrency-safe conflict validation (POST /instructor/book)
     */
    public function storeBooking()
    {
        $userId = (int) session()->get('user_id');

        $rules = [
            'room_id'      => 'required',
            'booking_date' => 'required|valid_date',
            'start_time'   => 'required',
            'end_time'     => 'required',
            'purpose'      => 'required|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roomInput = $this->request->getPost('room_id');
        $room = is_numeric($roomInput)
            ? $this->roomModel->find((int) $roomInput)
            : $this->roomModel->where('room_name', trim((string) $roomInput))->first();

        if (!$room) {
            return redirect()->back()->withInput()->with('error', 'Selected classroom does not exist.');
        }

        if ($room['status'] === 'Maintenance') {
            return redirect()->back()->withInput()->with('error', 'This classroom is currently under maintenance and cannot be booked.');
        }

        $bookingDate = trim((string) $this->request->getPost('booking_date'));
        $startTime = date('H:i:s', strtotime((string) $this->request->getPost('start_time')));
        $endTime = date('H:i:s', strtotime((string) $this->request->getPost('end_time')));

        // Rule: booking date cannot be in the past
        if ($bookingDate < date('Y-m-d')) {
            return redirect()->back()->withInput()->with('error', 'Booking date cannot be in the past.');
        }

        // Rule: start_time must be strictly earlier than end_time
        if ($startTime >= $endTime) {
            return redirect()->back()->withInput()->with('error', 'End time must be after the start time.');
        }

        // CONFLICT CHECK ALGORITHM:
        // Reject if existing pending or approved booking: new_start < existing_end AND new_end > existing_start
        if ($this->bookingModel->hasConflict((int) $room['id'], $bookingDate, $startTime, $endTime)) {
            return redirect()->back()->withInput()->with('error', 'This time slot conflicts with an existing booking.');
        }

        $newBooking = [
            'room_id'      => (int) $room['id'],
            'user_id'      => $userId,
            'booking_date' => $bookingDate,
            'start_time'   => $startTime,
            'end_time'     => $endTime,
            'purpose'      => trim((string) $this->request->getPost('purpose')),
            'status'       => 'pending',
        ];

        $this->bookingModel->insert($newBooking);

        return redirect()->to(base_url('instructor/bookings'))
            ->with('success', 'Booking request submitted successfully! Awaiting administrator approval.');
    }

    /**
     * Cancel personal booking request (POST /instructor/bookings/cancel/(:num))
     */
    public function cancelBooking(int $id)
    {
        $userId = (int) session()->get('user_id');
        $result = $this->bookingModel->cancelBooking($id, $userId);

        if ($result['success']) {
            return redirect()->to(base_url('instructor/bookings'))->with('success', $result['message']);
        }

        return redirect()->to(base_url('instructor/bookings'))->with('error', $result['message']);
    }

    /**
     * Instructor Announcements page
     */
    public function announcements(): string
    {
        $announcements = $this->announcementModel->getPublishedAnnouncements();
        $pinnedAnnouncement = $this->announcementModel->getPinnedAnnouncement();

        return view('instructor/announcements', [
            'title'              => 'Announcements - CPC RoomFinder',
            'activeNav'          => 'announcements',
            'announcements'      => $announcements,
            'pinnedAnnouncement' => $pinnedAnnouncement,
        ]);
    }

    /**
     * Instructor Profile page
     */
    public function profile(): string
    {
        return view('profile/edit', [
            'title'     => 'My Profile - CPC RoomFinder',
            'userRole'  => 'instructor',
            'activeNav' => 'profile',
        ]);
    }
}
