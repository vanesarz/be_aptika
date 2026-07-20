<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'is_read',
        'read_at',
        'board_id',
        'task_id',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_read'  => 'boolean',
        'read_at'  => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function board()
    {
        return $this->belongsTo(Board::class)->select(['id', 'name']);
    }

    public function task()
    {
        return $this->belongsTo(Task::class)->select(['id', 'title', 'status']);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id')->select(['id', 'name']);
    }
}
