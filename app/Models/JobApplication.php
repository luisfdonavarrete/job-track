<?php

namespace App\Models;

use App\Enums\JobApplications\Status;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table(key: 'id', keyType: 'uuid', incrementing: false)]
#[Fillable(['title', 'job_url', 'location', 'description', 'status', 'salary_min', 'salary_max', 'user_id', 'company_id', 'applied_at', 'follow_up_at'])]
class JobApplication extends Model
{
    use HasFactory;
    use HasUuids;

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'applied_at' => 'datetime',
            'follow_up_at' => 'datetime',
            'salary_min' => 'decimal:2',
            'salary_max' => 'decimal:2',
            'status' => Status::class,
        ];
    }
}
