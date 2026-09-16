<?php

namespace App\Enums\JobApplications;

enum Status: string
{
    case Applied = 'applied';
    case Interviewing = 'interviewing';
    case Offered = 'offered';
    case Rejected = 'rejected';

    public function toString(): string
    {
        return ucfirst($this->value);
    }

    public function colorClass(): string
    {
        return match ($this) {
            self::Applied => 'bg-blue-100 text-blue-800 dark:bg-blue-950 dark:text-blue-300',
            self::Interviewing => 'bg-amber-100 text-amber-800 dark:bg-amber-950 dark:text-amber-300',
            self::Offered => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950 dark:text-emerald-300',
            self::Rejected => 'bg-zinc-100 text-zinc-700 dark:bg-zinc-800 dark:text-zinc-300',
        };
    }
}
