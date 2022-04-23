<?php

namespace App\Models\Crm;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Plank\Mediable\Mediable;

class Cbr extends Model
{
    use SoftDeletes;
    use Mediable;

    protected $fillable = [
        'valute_id',
        'num_code',
        'char_code',
        'nominal',
        'value',
        'date_id',
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public function getPublishAtAttribute($date)
    {
        return $date ? Carbon::createFromFormat('Y-m-d', $date)->format('d.m.Y') : '';
    }

    public function setPublishAtAttribute($date)
    {
        return $this->attributes['publish_at'] = Carbon::createFromFormat('d.m.Y', $date)->format('Y-m-d');
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
        return $query->where('journal_types.status', 1);
    }
}
