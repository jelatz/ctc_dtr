<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Dtr extends Model
{
    use Notifiable;

    protected $table = 'dtr';

    protected $fillable = [
        'user_id',
        'date',
        'time_in',
        'time_out',
        'status',
        'employee_id',
        'name',
        'department',
    ];
}
