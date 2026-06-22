<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SidebarDocumentStat extends Model
{
    protected $table = 'sidebar_document_stats';

    protected $fillable = [
        'service_type_id',
        'document_type_id',
        'month',
        'year',
        'total_count',
    ];

    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class, 'document_type_id');
    }
}
