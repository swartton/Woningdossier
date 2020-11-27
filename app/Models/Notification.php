<?php

namespace App\Models;

use App\Traits\GetMyValuesTrait;
use App\Traits\GetValueTrait;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use GetMyValuesTrait;
    use GetValueTrait;

    protected $fillable = [
        'type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
