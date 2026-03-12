@props([
    'likes' => 0,
    'dislikes' => 0,
    'vote' => '',
    'likeUrl' => '',
    'dislikeUrl' => '',
    'disabled' => false,
])

@php
    $canVote = !$disabled && $likeUrl;
    $votedLike = $vote === 'yes' || $vote === 'like';
    $votedDislike = $vote === 'no' || $vote === 'dislike';
@endphp

<span class="vote-widget">
    <span class="vote-like {{ $votedLike ? 'chosen' : '' }}">
        @if($canVote && !$votedLike)
            <a class="vote-btn" href="{{ $likeUrl }}" title="Vote up">
        @else
            <span class="vote-btn {{ $votedLike ? 'vote-active' : '' }} {{ $disabled ? 'vote-disabled' : '' }}">
        @endif
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="vote-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V3a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0H22.5a2.25 2.25 0 0 1 0 4.5h-1.875M16.5 9h.008v.008H16.5V9Zm-1.5 0H8.25a2.25 2.25 0 0 0-2.244 2.077l-.862 6.9A2.25 2.25 0 0 0 7.388 20.5h5.362a2.25 2.25 0 0 0 2.244-2.077L15.5 14" />
                </svg>
                {{ $likes }}
        @if($canVote && !$votedLike)
            </a>
        @else
            </span>
        @endif
    </span>

    <span class="vote-dislike {{ $votedDislike ? 'chosen' : '' }}">
        @if($canVote && !$votedDislike)
            <a class="vote-btn" href="{{ $dislikeUrl }}" title="Vote down">
        @else
            <span class="vote-btn {{ $votedDislike ? 'vote-active' : '' }} {{ $disabled ? 'vote-disabled' : '' }}">
        @endif
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="vote-icon">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.498 15.25H4.372c-1.026 0-1.945-.694-2.054-1.715a12.137 12.137 0 0 1-.068-1.285c0-2.848.992-5.464 2.649-7.521C5.287 4.247 5.886 4 6.504 4h4.016a4.5 4.5 0 0 1 1.423.23l3.114 1.04a4.5 4.5 0 0 0 1.423.23h1.294M7.498 15.25c.618 0 .991.724.725 1.282A7.471 7.471 0 0 0 7.5 19.75 2.25 2.25 0 0 0 9.75 22a.75.75 0 0 0 .75-.75v-.633c0-.573.11-1.14.322-1.672.304-.76.93-1.33 1.653-1.715a9.04 9.04 0 0 0 2.86-2.4c.498-.634 1.226-1.08 2.032-1.08h.384" />
                </svg>
                {{ $dislikes }}
        @if($canVote && !$votedDislike)
            </a>
        @else
            </span>
        @endif
    </span>
</span>
