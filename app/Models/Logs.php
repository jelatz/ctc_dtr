<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Logs extends Model
{
    protected $table = 'dtr_logs';

    protected $fillable = [
        'employee_id',
        'dtr_date',
        'type',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'employee_id', 'employee_id');
    }
}
