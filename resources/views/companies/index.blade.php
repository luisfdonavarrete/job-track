<x-layouts::app :title="__('Companies')">
    <div class="jt-page">
        <header class="flex flex-wrap items-center justify-between gap-4">
            <div><p class="jt-eyebrow">{{ __('Your network') }}</p><h1 class="jt-title">{{ __('Companies') }}</h1><p class="mt-2 text-sm text-zinc-500 dark:text-zinc-400">{{ __('Get to know the teams behind your next opportunity.') }}</p></div>
            <a href="{{ route('companies.create') }}" class="jt-primary">{{ __('Add company') }}</a>
        </header>
        @if (session('status'))<p role="status" class="jt-notice">{{ session('status') }}</p>@endif
        <div class="jt-table">
            <table class="w-full text-left text-sm">
                <thead class="bg-zinc-100 dark:bg-zinc-800"><tr><th scope="col" class="px-4 py-3">{{ __('Company') }}</th><th scope="col" class="px-4 py-3">{{ __('Location') }}</th><th scope="col" class="px-4 py-3">{{ __('Actions') }}</th></tr></thead>
                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                    @forelse ($companies as $company)
                        <tr>
                            <th scope="row" class="px-4 py-4 font-medium"><a href="{{ route('companies.show', $company) }}" class="underline">{{ $company->name }}</a></th>
                            <td class="px-4 py-4">{{ $company->location ?: __('Not specified') }}</td>
                            <td class="px-4 py-4"><a href="{{ route('companies.edit', $company) }}" class="underline" aria-label="{{ __('Edit :name', ['name' => $company->name]) }}">{{ __('Edit') }}</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-4 py-12 text-center">{{ __('No companies yet. Add your first company to get started.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $companies->links() }}
    </div>
</x-layouts::app>
