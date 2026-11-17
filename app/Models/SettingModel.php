<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'system_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'setting_key',
        'setting_value',
        'description',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get setting value by key with fallback default
     */
    public static function getSetting(string $key, $default = null)
    {
        $db = \Config\Database::connect();
        $row = $db->table('system_settings')->where('setting_key', $key)->get()->getRowArray();
        return $row ? $row['setting_value'] : $default;
    }

    /**
     * Set or update setting value by key
     */
    public static function setSetting(string $key, $value, ?string $description = null): bool
    {
        $db = \Config\Database::connect();
        $builder = $db->table('system_settings');
        $existing = $builder->where('setting_key', $key)->countAllResults();

        $data = [
            'setting_value' => (string) $value,
            'updated_at'    => date('Y-m-d H:i:s'),
        ];
        if ($description !== null) {
            $data['description'] = $description;
        }

        if ($existing > 0) {
            return $builder->where('setting_key', $key)->update($data);
        }

        $data['setting_key'] = $key;
        $data['created_at'] = date('Y-m-d H:i:s');
        return $builder->insert($data);
    }
}
