<?php

namespace App\Models\Crm;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\Mediable;

class Date extends Model
{
    use Mediable;

    protected $fillable = [
        'date',
        'description',
        'created_at',
        'updated_at'
    ];

    public function getPublishAtAttribute($date)
    {
        return $date ? Carbon::createFromFormat('Y-m-d', $date)->format('d.m.Y') : '';
    }

    public function getCreatedAtAttribute($date)
    {
        return $date ? Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y H:i:s') : '';
    }

    public function getUpdatedAtAttribute($date)
    {
        return $date ? Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y H:i:s') : '';
    }

    public function getDeletedAtAttribute($date)
    {
        return $date ? Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y H:i:s') : '';
    }

    public function scopeActive($query)
    {
        return $query->where('dates.status', 1);
    }
}
