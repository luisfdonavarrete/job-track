<x-layouts::app :title="__('Dashboard')">
    <div class="jt-page">
        <header class="py-4">
            <p class="jt-eyebrow">{{ __('Your career, in motion') }}</p>
            <h1 class="jt-title">{{ __('Make room for your next opportunity.') }}</h1>
            <p class="mt-4 max-w-xl text-base leading-7 text-zinc-500 dark:text-zinc-400">{{ __('A clear view of where you have applied, who you are talking to, and what comes next.') }}</p>
        </header>
        <div class="grid gap-6 md:grid-cols-2">
            <a href="{{ route('job-applications.index') }}" class="group rounded-2xl border border-teal-200 bg-teal-50 p-8 transition hover:border-teal-500 dark:border-teal-900 dark:bg-teal-950/30">
                <p class="jt-eyebrow">{{ __('01 / Your progress') }}</p>
                <h2 class="text-2xl font-semibold">{{ __('Job applications') }} <span class="inline-block transition-transform group-hover:translate-x-1">→</span></h2>
                <p class="mt-3 text-sm leading-7 text-zinc-500 dark:text-zinc-400">{{ __('Review applications, update their status, and stay on top of follow-ups.') }}</p>
            </a>
            <a href="{{ route('companies.index') }}" class="group rounded-2xl border border-zinc-200 bg-white p-8 transition hover:border-teal-500 dark:border-zinc-700 dark:bg-zinc-900">
                <p class="jt-eyebrow">{{ __('02 / Your network') }}</p>
                <h2 class="text-2xl font-semibold">{{ __('Companies') }} <span class="inline-block transition-transform group-hover:translate-x-1">→</span></h2>
                <p class="mt-3 text-sm leading-7 text-zinc-500 dark:text-zinc-400">{{ __('Keep company details together and explore the teams you want to join.') }}</p>
            </a>
        </div>
        <div class="flex flex-wrap items-center justify-between gap-5 rounded-2xl border border-dashed border-zinc-300 p-6 dark:border-zinc-700">
            <div><h2 class="font-semibold">{{ __('Found something interesting?') }}</h2><p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Save an application while the details are fresh.') }}</p></div>
            <a href="{{ route('job-applications.create') }}" class="jt-primary">{{ __('Add application') }}</a>
        </div>
    </div>
</x-layouts::app>
