<?php

namespace App\Controllers;

use App\Models\DepartmentModel;
use App\Models\RoleModel;
use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;
    protected RoleModel $roleModel;
    protected DepartmentModel $departmentModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->departmentModel = new DepartmentModel();
    }

    /**
     * Display the Login page
     */
    public function login()
    {
        return view('auth/login', [
            'title' => 'Log In - CPC RoomFinder',
        ]);
    }

    /**
     * Process authentication submission (POST /login)
     */
    public function attemptLogin()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please provide both your ID Number and Password.');
        }

        $idNumber = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        try {
            $result = $this->userModel->verifyCredentials($idNumber, $password);
        } catch (\Throwable $e) {
            log_message('error', 'Login DB Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Database connection error. Please ensure MySQL is running in XAMPP.');
        }

        if (!$result['success']) {
            return redirect()->back()->withInput()->with('error', $result['error']);
        }

        $user = $result['user'];

        // Prevent session fixation attack
        session()->regenerate();

        $sessionData = [
            'user_id'         => (int) $user['id'],
            'id_number'       => $user['id_number'],
            'role'            => strtolower((string) $user['role_name']),
            'name'            => trim($user['first_name'] . ' ' . $user['last_name']),
            'first_name'      => $user['first_name'],
            'last_name'       => $user['last_name'],
            'email'           => $user['email'],
            'contact_number'  => $user['contact_number'],
            'department_id'   => $user['department_id'] ? (int) $user['department_id'] : null,
            'department_name' => $user['department_name'] ?? null,
            'department_code' => $user['department_code'] ?? null,
            'course'          => $user['course'],
            'year_level'      => $user['year_level'],
            'isLoggedIn'      => true,
        ];

        session()->set($sessionData);
        session()->setFlashdata('success', 'Welcome back, ' . $user['first_name'] . '!');

        return $this->redirectBasedOnRole($sessionData['role']);
    }

    /**
     * Display the Registration page (multi-step wizard)
     */
    public function register()
    {
        $role = $this->request->getGet('role') ?? 'student';
        try {
            $departments = $this->departmentModel->orderBy('name', 'ASC')->findAll();
        } catch (\Throwable $e) {
            $departments = [];
        }

        return view('auth/register', [
            'title'       => 'Create Account - CPC RoomFinder',
            'initialRole' => strtolower($role),
            'departments' => $departments,
        ]);
    }

    /**
     * Process registration submission (POST /register)
     */
    public function attemptRegister()
    {
        $requestedRole = strtolower(trim((string) $this->request->getPost('role')));
        if (!in_array($requestedRole, ['student', 'instructor'], true)) {
            $requestedRole = 'student';
        }

        $rules = [
            'firstname'        => 'required|min_length[2]|max_length[50]',
            'lastname'         => 'required|min_length[2]|max_length[50]',
            'email'            => 'required|valid_email|is_unique[users.email]',
            'role'             => 'required|in_list[student,instructor]',
            'password'         => 'required|min_length[6]',
            'password_confirm' => 'required|matches[password]',
        ];

        $idNumber = '';
        if ($requestedRole === 'student') {
            $rules['student_id'] = 'required|min_length[3]|max_length[50]|is_unique[users.id_number]';
            $rules['course']     = 'required';
            $rules['year_level'] = 'required';
            $idNumber = trim((string) $this->request->getPost('student_id'));
        } else {
            $rules['employee_id'] = 'required|min_length[3]|max_length[50]|is_unique[users.id_number]';
            $rules['department']  = 'required';
            $idNumber = trim((string) $this->request->getPost('employee_id'));
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleRecord = $this->roleModel->getByName($requestedRole);
        if (!$roleRecord) {
            return redirect()->back()->withInput()->with('error', 'Invalid role selected.');
        }

        $departmentId = null;
        if ($requestedRole === 'instructor') {
            $deptInput = $this->request->getPost('department');
            if (is_numeric($deptInput)) {
                $departmentId = (int) $deptInput;
            } else {
                $deptRecord = $this->departmentModel->getByCode((string) $deptInput);
                $departmentId = $deptRecord ? (int) $deptRecord['id'] : null;
            }
        }

        $userData = [
            'id_number'      => $idNumber,
            'password_hash'  => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'first_name'     => trim((string) $this->request->getPost('firstname')),
            'last_name'      => trim((string) $this->request->getPost('lastname')),
            'email'          => strtolower(trim((string) $this->request->getPost('email'))),
            'contact_number' => null,
            'role_id'        => (int) $roleRecord['id'],
            'department_id'  => $departmentId,
            'course'         => $requestedRole === 'student' ? trim((string) $this->request->getPost('course')) : null,
            'year_level'     => $requestedRole === 'student' ? trim((string) $this->request->getPost('year_level')) : null,
            'is_active'      => 1,
        ];

        try {
            $this->userModel->insert($userData);
        } catch (\Throwable $e) {
            log_message('error', 'Registration DB Error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Unable to complete registration. Please ensure MySQL is running in XAMPP.');
        }

        return redirect()->to(base_url('login'))
            ->with('success', 'Registration complete! You can now log in using ID: ' . esc($idNumber));
    }

    /**
     * Direct alias for Student Registration
     */
    public function studentRegister()
    {
        return $this->register();
    }

    /**
     * Direct alias for Instructor Registration
     */
    public function instructorRegister()
    {
        return $this->register();
    }

    /**
     * Terminate user session (POST /logout or GET /logout)
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'You have been logged out successfully.');
    }

    /**
     * Helper to route users to their role landing page
     */
    protected function redirectBasedOnRole(string $role)
    {
        return match (strtolower($role)) {
            'admin'      => redirect()->to(base_url('admin/overview')),
            'instructor' => redirect()->to(base_url('instructor/bookings')),
            default      => redirect()->to(base_url('student/dashboard')),
        };
    }
}
