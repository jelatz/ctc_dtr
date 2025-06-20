<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Notifications\Notifiable;

class Dtr extends Model
{
    use Notifiable;

    protected $table = 'dtr';

    protected $fillable = [
        'dtr_date',
        'time_in',
        'time_out',
        'status',
        'employee_id',
        'name',
        'department',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }
}
