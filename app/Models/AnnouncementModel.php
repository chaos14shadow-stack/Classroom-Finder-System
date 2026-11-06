<?php

namespace App\Models;

use CodeIgniter\Model;

class AnnouncementModel extends Model
{
    protected $table            = 'announcements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'title',
        'message',
        'is_pinned',
        'author_id',
    ];

    protected bool $allowEmptyInserts = false;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Retrieve published announcements with author details, pinned first then newest
     */
    public function getPublishedAnnouncements(int $limit = 0): array
    {
        $builder = $this->select('announcements.*, users.first_name, users.last_name, users.id_number')
            ->join('users', 'users.id = announcements.author_id', 'left')
            ->orderBy('announcements.is_pinned', 'DESC')
            ->orderBy('announcements.created_at', 'DESC');

        if ($limit > 0) {
            $builder->limit($limit);
        }

        return $builder->findAll();
    }

    /**
     * Retrieve the currently pinned announcement
     */
    public function getPinnedAnnouncement(): ?array
    {
        return $this->select('announcements.*, users.first_name, users.last_name')
            ->join('users', 'users.id = announcements.author_id', 'left')
            ->where('announcements.is_pinned', 1)
            ->orderBy('announcements.updated_at', 'DESC')
            ->first();
    }

    /**
     * Save an announcement with transactional auto-unpinning of any previous pinned announcement
     */
    public function saveWithPinManagement(array $data, ?int $id = null): bool
    {
        $this->db->transStart();

        $isPinned = isset($data['is_pinned']) && ((int) $data['is_pinned'] === 1);

        if ($isPinned) {
            // Unpin all other announcements so only one stays pinned
            $this->builder()->update(['is_pinned' => 0]);
        }

        if ($id !== null && $id > 0) {
            $this->update($id, $data);
        } else {
            $this->insert($data);
        }

        $this->db->transComplete();

        return $this->db->transStatus() !== false;
    }
}

