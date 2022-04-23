<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'display_name',
        'parent_id'
    ];

    public function permissions() {
        return $this->hasMany('App\Models\Crm\Permission','parent_id');
    }

    public function module() {
        return $this->belongsTo('App\Models\Crm\Permission','parent_id');
    }
}
