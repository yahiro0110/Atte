<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'role', 'password'];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class)
            ->where('date', now()->format('Y-m-d'));
    }
}
