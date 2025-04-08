<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotifyMe extends Model
{
    use HasFactory;

    protected $table = 'notify_me';

    // Fields that are mass assignable
    protected $fillable = [
        'name',
        'email',
        'company',
    ];
}
