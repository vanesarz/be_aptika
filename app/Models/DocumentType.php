<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class DocumentType extends Model
{
    protected $table = 'document_types';

    protected $fillable = [
        'name',
    ];

    public function sidebarDocumentStats(): HasMany
    {
        return $this->hasMany(SidebarDocumentStat::class, 'document_type_id');
    }
}
