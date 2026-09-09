{{--
  Latest Blog entries module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="latest_discussions_module {{ $params->get('moduleclass_sfx') }}">
  @if (count($posts) > 0)
    <ul class="list bg-base-100 rounded-box">
      @php $c = 0; @endphp
      @foreach ($posts as $post)
        @if ($c >= $limit)
          @break
        @endif
        <li class="list-row items-start gap-3 p-3">
          @if ($params->get('details', 1))
            <div class="shrink-0">
              <img class="size-10 rounded-full object-cover"
                   src="{{ $post->creator->picture() }}" alt="" />
            </div>
          @endif
          <div class="min-w-0 grow">
            <h4 class="font-semibold">
              <a href="{{ Route::url($post->link()) }}" class="link link-hover">
                {{ stripslashes($post->get('title')) }}
              </a>
            </h4>
            @if ($params->get('details', 1))
              @php
                $memberUrl = Route::url(
                    'index.php?option=com_members&id=' . $post->get('created_by')
                );
              @endphp
              <div class="flex flex-wrap items-center gap-x-2 text-xs text-base-content/60 mt-0.5">
                <span>{{ Lang::txt('MOD_LATESTBLOG_ENTRY_NUMBER', $post->get('id')) }}</span>
                <time datetime="{{ $post->published() }}">{{ $post->published('date') }}</time>
                <time datetime="{{ $post->published() }}">{{ $post->published('time') }}</time>
                <a href="{{ $memberUrl }}" class="link link-hover">
                  {{ stripslashes($post->creator->get('name')) }}
                </a>
                <a href="{{ $post->link('base') }}" class="link link-hover">
                  {{ stripslashes($post->item('title')) }}
                </a>
              </div>
            @endif
            @if ($params->get('preview', 1))
              <p class="text-sm mt-1">
                @if ($pullout && $c == 0)
                  {{ \Hubzero\Utility\Str::truncate(strip_tags($post->content), $params->get('pulloutlimit', 500)) }}
                @else
                  {{ \Hubzero\Utility\Str::truncate(strip_tags($post->content), $params->get('charlimit', 100)) }}
                @endif
              </p>
            @endif
          </div>
        </li>
        @php $c++; @endphp
      @endforeach
    </ul>
  @else
    <p class="text-base-content/60">{{ Lang::txt('MOD_LATESTBLOG_NO_RESULTS') }}</p>
  @endif

  @if ($more = $params->get('morelink', ''))
    <p class="mt-2">
      <a href="{{ $more }}" class="btn btn-sm btn-outline">
        {{ Lang::txt('MOD_LATESTBLOG_MORE_RESULTS') }}
      </a>
    </p>
  @endif
</div>
