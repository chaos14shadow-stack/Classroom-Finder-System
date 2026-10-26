<?php

namespace App\Controllers;

class Student extends BaseController
{
    public function dashboard(): string { return view('student/dashboard');}
    public function rooms(): string { return view('student/rooms');}
    public function announcements(): string {return view('student/announcements');}
}