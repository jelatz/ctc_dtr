<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $table = 'schedules';

    protected $fillable = [
        'employee_id',
        'start_time',
        'end_time',
        'break_time',
        'shift_type',
    ];
}
