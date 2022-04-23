<?php

namespace App\Models\Crm;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ResourceTypeTranslation extends Model
{
    public $incrementing = false;
    public $primaryKey = ['resource_type_id', 'language'];

    use Notifiable;

    protected $fillable = [
        'resource_type_id',
        'language',
        'name',
    ];
}
