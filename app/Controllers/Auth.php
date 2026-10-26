<?php

namespace App\Controllers;

class Auth extends BaseController
{
    /**
     * Display the Login page
     */
    public function login(): string
    {
        return view('auth/login');
    }

    /**
     * Display the Registration page (multi-step wizard)
     */
    public function register(): string
    {
        $role = $this->request->getGet('role') ?? 'student';
        return view('auth/register', ['initialRole' => strtolower($role)]);
    }

    /**
     * Direct alias for Student Registration
     */
    public function studentRegister(): string
    {
        return view('auth/register', ['initialRole' => 'student']);
    }

    /**
     * Direct alias for Instructor Registration
     */
    public function instructorRegister(): string
    {
        return view('auth/register', ['initialRole' => 'instructor']);
    }
}
