<?php

namespace App\Models;

use App\Enums\JobApplications\Status;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable('from_status', 'to_status')]
class ActivityHistory extends Model
{
    public function jobApplication(): BelongsTo
    {
        return $this->belongsTo(JobApplication::class);
    }

    protected function casts(): array
    {
        return [
            'from_status' => Status::class,
            'to_status' => Status::class,
        ];
    }
}
