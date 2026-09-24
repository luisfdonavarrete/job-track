<?php

namespace App\Enums\JobApplications;

enum DocumentType: string
{
    case Resume = 'resume';
    case CoverLetter = 'cover_letter';

    public function toString(): string
    {
        return match ($this) {
            self::Resume => 'Resume',
            self::CoverLetter => 'Cover letter'
        };
    }
}
