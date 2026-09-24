<?php

namespace App\Models;

use App\Enums\JobApplications\DocumentType;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['original_filename', 'path', 'mime_type', 'type'])]
class ApplicationDocument extends Model
{
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
        ];
    }
}
