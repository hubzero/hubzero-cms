@props([
    'id' => '',
    'name' => '',
    'avatar' => '',
    'profileUrl' => '',
    'date' => '',
    'time' => '',
    'datetime' => '',
    'isAuthor' => false,
    'isReported' => false,
    'anonymous' => false,
    'depth' => 0,
])

<li class="comment {{ $isAuthor ? 'comment-author' : '' }} {{ $isReported ? 'comment-reported' : '' }}"
    id="c{{ $id }}">
    <div class="comment-meta">
        <div class="avatar">
            <div class="w-10 rounded-full">
                <img src="{{ $avatar }}" alt="" />
            </div>
        </div>
        <div class="comment-meta-text">
            <span class="comment-name">
                @if($profileUrl && !$anonymous)
                    <a class="link link-hover font-semibold" href="{{ $profileUrl }}">{{ $name }}</a>
                @else
                    <span class="font-semibold">{{ $anonymous ? 'Anonymous' : $name }}</span>
                @endif
            </span>
            <a class="comment-time link link-hover text-base-content/50 text-sm" href="#c{{ $id }}">
                <time datetime="{{ $datetime }}">{{ $date }} at {{ $time }}</time>
            </a>
        </div>
    </div>

    <div class="comment-body">
        @if($isReported)
            <p class="text-base-content/40 italic">This comment has been reported as abusive.</p>
        @else
            {{ $slot }}
        @endif
    </div>

    @if(isset($actions) && $actions->isNotEmpty())
        <div class="comment-actions">
            {{ $actions }}
        </div>
    @endif

    @if(isset($replies) && $replies->isNotEmpty())
        <ul class="comment-replies">
            {{ $replies }}
        </ul>
    @endif
</li>
