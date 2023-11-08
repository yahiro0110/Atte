<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'date', 'start_time', 'end_time', 'work_status'];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function breaktimes()
    {
        return $this->hasMany(Breaktime::class);
    }
}
