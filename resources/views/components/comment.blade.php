@props(['comment', 'depth' => 0])

<div class="flex gap-3 {{ $depth > 0 ? 'ml-8 mt-4' : '' }}">
    <div class="shrink-0 w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-sm font-medium text-gray-600">
        {{ substr($comment->creator->name ?? '?', 0, 1) }}
    </div>
    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 text-sm">
            <span class="font-medium text-gray-900">{{ $comment->creator->name ?? 'Anonymous' }}</span>
            <span class="text-gray-400">&middot;</span>
            <time class="text-gray-500" datetime="{{ $comment->created->toIso8601String() }}">
                {{ $comment->created->format('M j, Y \a\t g:ia') }}
            </time>
        </div>
        <div class="mt-1 text-gray-700 text-sm prose prose-sm max-w-none">
            {!! $comment->content !!}
        </div>

        @foreach ($comment->replies as $reply)
            <x-comment :comment="$reply" :depth="$depth + 1" />
        @endforeach
    </div>
</div>
