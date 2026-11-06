<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'id_number',
        'password_hash',
        'first_name',
        'last_name',
        'email',
        'contact_number',
        'role_id',
        'department_id',
        'course',
        'year_level',
        'is_active',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Look up user by ID Number (unique username) with role and department
     */
    public function findByIdNumber(string $idNumber): ?array
    {
        return $this->select('users.*, roles.name AS role_name, departments.name AS department_name, departments.code AS department_code')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->where('users.id_number', trim($idNumber))
            ->first();
    }

    /**
     * Get user by primary key with role and department details
     */
    public function getUserWithDetails(int $id): ?array
    {
        return $this->select('users.*, roles.name AS role_name, departments.name AS department_name, departments.code AS department_code')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left')
            ->where('users.id', $id)
            ->first();
    }

    /**
     * Retrieve all users with role and department info (for Admin User Management)
     */
    public function getAllWithDetails(?string $roleFilter = null, ?string $search = null): array
    {
        $builder = $this->select('users.*, roles.name AS role_name, departments.name AS department_name, departments.code AS department_code')
            ->join('roles', 'roles.id = users.role_id', 'left')
            ->join('departments', 'departments.id = users.department_id', 'left');

        if (!empty($roleFilter) && $roleFilter !== 'all') {
            $builder->where('roles.name', strtolower($roleFilter));
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('users.first_name', $search)
                ->orLike('users.last_name', $search)
                ->orLike('users.id_number', $search)
                ->orLike('users.email', $search)
                ->groupEnd();
        }

        return $builder->orderBy('users.created_at', 'DESC')->findAll();
    }

    /**
     * Authenticate user credentials safely
     * Returns ['success' => bool, 'user' => ?array, 'error' => ?string]
     */
    public function verifyCredentials(string $idNumber, string $password): array
    {
        $user = $this->findByIdNumber($idNumber);

        if (!$user) {
            return ['success' => false, 'user' => null, 'error' => 'Invalid ID Number or Password.'];
        }

        if ((int) $user['is_active'] !== 1) {
            return ['success' => false, 'user' => null, 'error' => 'Your account has been deactivated. Please contact campus administration.'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'user' => null, 'error' => 'Invalid ID Number or Password.'];
        }

        return ['success' => true, 'user' => $user, 'error' => null];
    }

    /**
     * Strictly update editable profile attributes (cannot modify id_number or role_id)
     */
    public function updateProfileWhitelisted(int $userId, array $data): bool
    {
        $allowed = [
            'first_name',
            'last_name',
            'email',
            'contact_number',
            'course',
            'year_level',
            'department_id',
        ];

        $updateData = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            return false;
        }

        return $this->update($userId, $updateData);
    }

    /**
     * Update user password with secure hash
     */
    public function updatePasswordHash(int $userId, string $plainPassword): bool
    {
        return $this->update($userId, [
            'password_hash' => password_hash($plainPassword, PASSWORD_DEFAULT),
        ]);
    }

    /**
     * Toggle active/inactive status
     */
    public function toggleActiveStatus(int $userId): ?int
    {
        $user = $this->find($userId);
        if (!$user) {
            return null;
        }

        $newStatus = (int) $user['is_active'] === 1 ? 0 : 1;
        $this->update($userId, ['is_active' => $newStatus]);
        return $newStatus;
    }
}

