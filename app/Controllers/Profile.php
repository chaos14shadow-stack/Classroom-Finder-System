<?php

namespace App\Controllers;

use App\Models\UserModel;

class Profile extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * View profile landing
     */
    public function index()
    {
        $role = strtolower((string) session()->get('role'));
        return match ($role) {
            'admin'      => redirect()->to(base_url('admin/profile')),
            'instructor' => redirect()->to(base_url('instructor/profile')),
            default      => redirect()->to(base_url('student/profile')),
        };
    }

    /**
     * Update profile details with strict whitelisting (POST /profile/update)
     */
    public function update()
    {
        $userId = (int) session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'User session expired.');
        }

        $rules = [
            'first_name'     => 'required|min_length[2]|max_length[50]',
            'last_name'      => 'required|min_length[2]|max_length[50]',
            'email'          => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'contact_number' => 'permit_empty|max_length[30]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Whitelisted fields only - id_number and role_id are strictly forbidden
        $updateData = [
            'first_name'     => trim((string) $this->request->getPost('first_name')),
            'last_name'      => trim((string) $this->request->getPost('last_name')),
            'email'          => strtolower(trim((string) $this->request->getPost('email'))),
            'contact_number' => $this->request->getPost('contact_number') ? trim((string) $this->request->getPost('contact_number')) : null,
        ];

        if ($this->request->getPost('course')) {
            $updateData['course'] = trim((string) $this->request->getPost('course'));
        }
        if ($this->request->getPost('year_level')) {
            $updateData['year_level'] = trim((string) $this->request->getPost('year_level'));
        }
        if ($this->request->getPost('department_id')) {
            $updateData['department_id'] = (int) $this->request->getPost('department_id');
        }

        $this->userModel->updateProfileWhitelisted($userId, $updateData);

        // Refresh session data
        session()->set([
            'name'           => $updateData['first_name'] . ' ' . $updateData['last_name'],
            'first_name'     => $updateData['first_name'],
            'last_name'      => $updateData['last_name'],
            'email'          => $updateData['email'],
            'contact_number' => $updateData['contact_number'],
        ]);

        return redirect()->back()->with('success', 'Profile information updated successfully.');
    }

    /**
     * Change password with verification of current password (POST /profile/change-password)
     */
    public function changePassword()
    {
        $userId = (int) session()->get('user_id');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to(base_url('login'))->with('error', 'User session expired.');
        }

        $rules = [
            'current_password'      => 'required',
            'new_password'          => 'required|min_length[8]',
            'password_confirmation' => 'required|matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $currentPassword = (string) $this->request->getPost('current_password');
        if (!password_verify($currentPassword, $user['password_hash'])) {
            return redirect()->back()->with('error', 'Incorrect current password. Verification failed.');
        }

        $newPassword = (string) $this->request->getPost('new_password');
        $this->userModel->updatePasswordHash($userId, $newPassword);

        return redirect()->back()->with('success', 'Password updated successfully!');
    }
}

