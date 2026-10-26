<?php

namespace App\Controllers;

class Instructor extends BaseController
{
    public function dashboard(): string
    {
        return view('instructor/bookings');
    }

    public function bookings(): string
    {
        return view('instructor/bookings');
    }

    /**
     * Room directory list for booking (Middle Panel in screenshot)
     */
    public function bookSearch(): string
    {
        // If query param form=1 is passed, show form directly
        if ($this->request->getGet('form') === '1') {
            return $this->bookRoom();
        }
        return view('instructor/book_search');
    }

    /**
     * Room Booking request form (Bottom Panel in screenshot)
     */
    public function bookRoom(): string
    {
        $room = $this->request->getGet('room') ?? 'Room 224';
        return view('instructor/book_room', ['selectedRoom' => $room]);
    }

    public function announcements(): string
    {
        return view('instructor/announcements');
    }
}
