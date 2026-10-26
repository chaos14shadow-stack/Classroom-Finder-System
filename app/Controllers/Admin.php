<?php

namespace App\Controllers;

class Admin extends BaseController
{
    public function overview(): string {return view('admin/overview');}

    public function rooms(): string {return view('admin/rooms');}

    public function users(): string {return view('admin/users');}

    public function bookings(): string {return view('admin/bookings');}

    public function announcements(): string {return view('admin/announcements');}
}
