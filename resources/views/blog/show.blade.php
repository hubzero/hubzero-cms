@extends('layouts.app')

@section('title', $entry->title . ' — Blog — Hubzero')

@section('content')
    <div class="mb-6">
        <a href="/blog" class="text-sm text-blue-600 hover:text-blue-800">&larr; Back to Blog</a>
    </div>

    <article>
        <x-page-header :title="$entry->title">
            <div class="flex items-center gap-2 text-sm text-gray-500 mt-1">
                <span>{{ $entry->creator->name ?? 'Anonymous' }}</span>
                <span>&middot;</span>
                <time datetime="{{ ($entry->publish_up ?? $entry->created)->toIso8601String() }}">
                    {{ ($entry->publish_up ?? $entry->created)->format('M j, Y \a\t g:ia') }}
                </time>
            </div>
        </x-page-header>

        <x-card>
            <div class="prose prose-sm max-w-none text-gray-700">
                {!! $entry->content !!}
            </div>
        </x-card>
    </article>

    @if ($entry->comments->count())
        <section class="mt-10">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">
                {{ $entry->comments->count() }} {{ Str::plural('Comment', $entry->comments->count()) }}
            </h2>

            <div class="space-y-6">
                @foreach ($entry->comments as $comment)
                    <x-comment :comment="$comment" />
                @endforeach
            </div>
        </section>
    @endif
@endsection
