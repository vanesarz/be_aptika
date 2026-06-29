<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpdProposal extends Model
{
    protected $table = 'spd_proposals';

    protected $fillable = [
        'user_id',
        'orderer_name',
        'orderer_nip',
        'orderer_position',
        'employee_name',
        'employee_nip',
        'employee_rank',
        'employee_position',
        'purpose',
        'transportation',
        'departure_place',
        'destination',
        'start_date',
        'end_date',
        'budget_estimate',
        'followers',
        'status',
    ];

    protected $casts = [
        'followers' => 'array',
        'budget_estimate' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
