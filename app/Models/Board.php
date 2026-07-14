<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Board extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'created_by',
        'start_date',
        'end_date',
        'status',
        'visibility',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function pm()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members()
    {
        return $this->hasMany(BoardMember::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}