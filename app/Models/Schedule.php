<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;


class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $fillable = [
        'employee_id',
        'start_time',
        'end_time',
        'break_time',
        'shift_type',
        'sched_date',
    ];


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }

    public function dtr(): HasOne
    {
        return $this->hasOne(Dtr::class, 'employee_id', 'employee_id');
    }
}
