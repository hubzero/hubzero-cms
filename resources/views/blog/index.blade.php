@extends('layouts.app')

@section('title', 'Blog — Hubzero')

@section('content')
    <x-page-header title="Blog" subtitle="News and updates from the community" />

    <div class="space-y-6">
        @forelse ($entries as $entry)
            <x-card>
                <h2 class="text-xl font-semibold text-gray-900">
                    <a href="{{ $entry->url }}" class="hover:text-blue-600">{{ $entry->title }}</a>
                </h2>
                <div class="mt-1 flex items-center gap-2 text-sm text-gray-500">
                    <span>{{ $entry->creator->name ?? 'Anonymous' }}</span>
                    <span>&middot;</span>
                    <time datetime="{{ ($entry->publish_up ?? $entry->created)->toIso8601String() }}">
                        {{ ($entry->publish_up ?? $entry->created)->format('M j, Y') }}
                    </time>
                    @if ($entry->comments_count)
                        <span>&middot;</span>
                        <span>{{ $entry->comments_count }} {{ Str::plural('comment', $entry->comments_count) }}</span>
                    @endif
                </div>
                <p class="mt-3 text-gray-700 text-sm leading-relaxed">{{ $entry->excerpt }}</p>
            </x-card>
        @empty
            <p class="text-gray-500">No blog entries yet.</p>
        @endforelse
    </div>
@endsection
