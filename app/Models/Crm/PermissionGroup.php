<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function permissions()
    {
        return $this->hasMany('App\Models\Crm\PermissionGroupPermission', 'permission_group_id');
    }
}
