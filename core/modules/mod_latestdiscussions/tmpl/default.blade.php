{{--
  Latest Discussions module — daisyUI layout.

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
        @php
          $c++;
          $post->set('section', $categories[$post->get('category_id')]->section);
          $post->set('category', $categories[$post->get('category_id')]->alias);

          if ($post->get('scope_id') == 0) {
              $locationUrl = Route::url('index.php?option=com_forum');
              $locationText = Lang::txt('MOD_LATESTDISCUSSIONS_SITE_FORUM');
          } else {
              $locationUrl = Route::url(
                  'index.php?option=com_groups&cn=' . $post->get('group_alias')
              );
              $locationText = stripslashes($post->get('group_title'));
          }

          $title = ($post->get('parent') && isset($threads[$post->get('parent')]))
              ? stripslashes($threads[$post->get('parent')])
              : stripslashes($post->get('title'));
        @endphp
        <li class="list-row p-3">
          <div class="min-w-0 grow">
            <h4 class="font-semibold">
              <a href="{{ Route::url($post->link()) }}" class="link link-hover">
                {{ $title }}
              </a>
            </h4>
            <div class="flex flex-wrap items-center gap-x-2 text-xs text-base-content/60 mt-0.5">
              <span>
                @if ($post->get('anonymous'))
                  <em>{{ Lang::txt('JANONYMOUS') }}</em>
                @else
                  @php
                    $creatorUrl = Route::url(
                        'index.php?option=com_members&id=' . $post->creator()->get('id')
                    );
                  @endphp
                  <a href="{{ $creatorUrl }}" class="link link-hover">
                    {{ stripslashes($post->creator()->get('name')) }}</a>,
                @endif
                {{ Lang::txt('MOD_LATESTDISCUSSIONS_IN') }}
              </span>
              <a href="{{ $locationUrl }}" class="link link-hover">{{ $locationText }}</a>
              <time datetime="{{ $post->get('created') }}">
                {{ Lang::txt('MOD_LATESTDISCUSSIONS_AT_TIME_ON_DATE', $post->created('time'), $post->created('date')) }}
              </time>
            </div>
            @if ($charlimit > 0)
              <p class="text-sm mt-1">
                {{ \Hubzero\Utility\Str::truncate(strip_tags($post->get('comment')), $charlimit) }}
              </p>
            @endif
          </div>
        </li>
      @endforeach
    </ul>
  @else
    <p class="text-base-content/60">{{ Lang::txt('MOD_LATESTDISCUSSIONS_NO_RESULTS') }}</p>
  @endif

  @if ($more = $params->get('morelink', ''))
    <p class="mt-2">
      <a href="{{ $more }}" class="btn btn-sm btn-outline">
        {{ Lang::txt('MOD_LATESTDISCUSSIONS_MORE_RESULTS') }}
      </a>
    </p>
  @endif

  @if ($params->get('feedlink', 'yes') == 'yes')
    @php
      $rssUrl = Route::url('index.php?option=com_forum&task=latest.rss', true, -1);
    @endphp
    <p class="mt-2">
      <a href="{{ $rssUrl }}" class="btn btn-sm btn-outline">
        {{ Lang::txt('MOD_LATESTDISCUSSIONS_FEED') }}
      </a>
    </p>
  @endif
</div>
