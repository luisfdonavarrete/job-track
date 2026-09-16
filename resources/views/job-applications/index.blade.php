<x-layouts::app :title="__('Job applications')">
    <div class="jt-page">
        <header class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="jt-eyebrow">{{ __('Your next chapter') }}</p>
                <h1 class="jt-title text-zinc-900 dark:text-white">{{ __('Job applications') }}</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Keep track of your applications and upcoming follow-ups.') }}</p>
            </div>
            <span class="rounded-full bg-zinc-100 px-3 py-1 text-sm text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">
                {{ trans_choice(':count application|:count applications', $jobApplications->total()) }}
            </span>
            <a href="{{ route('job-applications.create') }}" class="jt-primary">{{ __('Add application') }}</a>
        </header>
        @if (session('status'))<p role="status" class="jt-notice">{{ session('status') }}</p>@endif

        <div class="jt-table">
            <table class="w-full text-left text-sm">
                <caption class="sr-only">{{ __('Job applications, statuses, salaries, and follow-up dates') }}</caption>
                <thead class="border-b border-zinc-200 bg-zinc-50 text-zinc-600 dark:border-zinc-700 dark:bg-zinc-800 dark:text-zinc-300">
                    <tr>
                        <th scope="col" class="px-5 py-3 font-medium">{{ __('Position') }}</th>
                        <th scope="col" class="px-5 py-3 font-medium">{{ __('Status') }}</th>
                        <th scope="col" class="px-5 py-3 font-medium">{{ __('Salary range') }}</th>
                        <th scope="col" class="px-5 py-3 font-medium">{{ __('Applied') }}</th>
                        <th scope="col" class="px-5 py-3 font-medium">{{ __('Follow-up') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($jobApplications as $application)
                        <tr class="text-zinc-600 dark:text-zinc-300">
                            <th scope="row" class="min-w-56 px-5 py-4 font-normal">
                                <div class="font-medium text-zinc-900 dark:text-white"><a href="{{ route('job-applications.show', $application) }}" class="underline">{{ $application->title }}</a></div>
                                <div class="mt-1 text-sm">{{ $application->company->name }}</div>
                                <div class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">{{ $application->location ?: __('Location not specified') }}</div>
                            </th>
                            <td class="px-5 py-4">
                                <span @class([
                                    'inline-flex rounded-full px-2.5 py-1 text-xs font-medium whitespace-nowrap',
                                    $application->status->colorClass() => true,
                                ])>{{ $application->status->toString() }}</span>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 tabular-nums">
                                @if ($application->salary_min !== null && $application->salary_max !== null)
                                    {{ number_format($application->salary_min) }} – {{ number_format($application->salary_max) }}
                                @elseif ($application->salary_min !== null)
                                    {{ __('From :amount', ['amount' => number_format($application->salary_min)]) }}
                                @elseif ($application->salary_max !== null)
                                    {{ __('Up to :amount', ['amount' => number_format($application->salary_max)]) }}
                                @else
                                    {{ __('Not specified') }}
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $application->applied_at?->format('M j, Y') ?? __('Not recorded') }}</td>
                            <td class="whitespace-nowrap px-5 py-4">{{ $application->follow_up_at?->format('M j, Y') ?? __('Not scheduled') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-16 text-center">
                                <p class="font-medium text-zinc-900 dark:text-white">{{ __('No applications yet') }}</p>
                                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Your job applications will appear here.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $jobApplications->links() }}
    </div>
</x-layouts::app>
