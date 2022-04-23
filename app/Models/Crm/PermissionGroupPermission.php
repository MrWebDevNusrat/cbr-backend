<?php

namespace App\Models\Crm;

use App\Traits\HasCompositePrimaryKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermissionGroupPermission extends Model
{
    use HasFactory;

//    use HasCompositePrimaryKey;

    public $timestamps = false;
    public $incrementing = false;

    public $primaryKey = ['permission_group_id', 'permission_id'];

    protected $fillable = [
        'permission_group_id',
        'permission_id'
    ];

    public function permission_group()
    {
        return $this->belongsTo('App\Models\Crm\PermissionGroup');
    }

    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
