<?php

namespace App\Enums\JobApplications;

enum DocumentType: string
{
    case Resume = 'resume';
    case CoverLetter = 'cover_letter';
}
