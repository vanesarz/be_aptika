<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketHistory extends Model
{
    protected $table = 'ticket_histories';

    protected $fillable = [
        'form_perubahan_it_id',
        'status',
        'keterangan',
    ];
}