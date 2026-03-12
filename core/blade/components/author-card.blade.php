@props([
    'name' => '',
    'avatar' => '',
    'profileUrl' => '',
    'bio' => '',
    'affiliation' => '',
])

<div class="author-card">
    <div class="author-card-inner">
        <div class="author-card-avatar">
            <img src="{{ $avatar }}" alt="" />
        </div>
        <div class="author-card-info">
            <h3 class="author-card-name">
                @if($profileUrl)
                    <a href="{{ $profileUrl }}">{{ $name }}</a>
                @else
                    {{ $name }}
                @endif
            </h3>
            @if($affiliation)
                <p class="author-card-affiliation">{{ $affiliation }}</p>
            @endif
            @if($bio)
                <p class="author-card-bio">{{ $bio }}</p>
            @endif
        </div>
    </div>
    @if($slot->isNotEmpty())
        <div class="author-card-extra">
            {{ $slot }}
        </div>
    @endif
</div>
