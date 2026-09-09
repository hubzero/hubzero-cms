{{--
  What's New module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="{{ $module->module }}"{!! $cssId ? ' id="' . $cssId . '"' : '' !!}>
  @if ($feed)
    @php
      $feedTitle = Lang::txt('MOD_WHATSNEW_SUBSCRIBE');
    @endphp
    <div class="flex justify-end mb-2">
      <a href="{{ $feedlink }}" $title="{{ $feedTitle }}" class="btn btn-sm btn-outline">
        {{ Lang::txt('MOD_WHATSNEW_NEWS_FEED') }}
      </a>
    </div>
  @endif

  @if (!$tagged)
    @if (count($rows) > 0)
      <ul class="list bg-base-100 rounded-box">
        @php $count = 0; @endphp
        @foreach ($rows as $row)
          @if (empty($row))
            @continue
          @endif
          @if ($count >= 6)
            @break
          @endif
          <li class="list-row p-3">
            <div class="min-w-0 grow">
              <a href="{{ Route::url($row->href) }}" class="link link-hover font-semibold">
                {{ stripslashes($row->title) }}
              </a>
              <div class="text-xs text-base-content/60 mt-0.5">
                <span>{{ Lang::txt('in') }}
                  {{ $row->area ? Lang::txt(stripslashes($row->area ?? '')) : Lang::txt(strtoupper(stripslashes($row->section ?? ''))) }}
                </span>
                @if ($row->publish_up)
                  <span>, {{ Date::of($row->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</span>
                @endif
              </div>
            </div>
          </li>
          @php $count++; @endphp
        @endforeach
      </ul>
    @else
      <p class="text-base-content/60">{{ Lang::txt('MOD_WHATSNEW_NO_RESULTS') }}</p>
    @endif
  @else
    @php
      $profileUrl = Route::url(
          'index.php?option=com_members&id=' . User::get('id')
          . '&active=profile#profile-interests'
      );
    @endphp
    <div class="flex items-center gap-2 text-sm mb-2">
      <span class="grow">
        {{ Lang::txt('MOD_WHATSNEW_MY_INTERESTS') }}:
        {!! $__module->formatTags($tags) !!}
      </span>
      <a href="{{ $profileUrl }}" class="btn btn-xs btn-outline">
        @if (count($tags) > 0)
          {{ Lang::txt('JACTION_EDIT') }}
        @else
          {{ Lang::txt('MOD_WHATSNEW_ADD_INTERESTS') }}
        @endif
      </a>
    </div>
    @if ($rows2 !== null && count($rows2) > 0)
      <ul class="list bg-base-100 rounded-box">
        @php $count = 0; @endphp
        @foreach ($rows2 as $row2)
          @if (empty($row2))
            @continue
          @endif
          @if ($count >= 6)
            @break
          @endif
          <li class="list-row p-3">
            <div class="min-w-0 grow">
              <a href="{{ Route::url($row2->href) }}" class="link link-hover font-semibold">
                {{ stripslashes($row2->title) }}
              </a>
              <div class="text-xs text-base-content/60 mt-0.5">
                <span>{{ Lang::txt('MOD_WHATSNEW_IN') }}
                  {{ $row2->section ? Lang::txt($row2->area) : Lang::txt(strtoupper($row2->section)) }}
                </span>
                @if ($row2->publish_up)
                  <span>, {{ Date::of($row2->publish_up)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</span>
                @endif
              </div>
            </div>
          </li>
          @php $count++; @endphp
        @endforeach
      </ul>
    @else
      <p class="text-base-content/60">{{ Lang::txt('MOD_WHATSNEW_NO_RESULTS') }}</p>
    @endif
  @endif

  @php
    $moreUrl = Route::url(
        'index.php?option=com_whatsnew&$period=' . $area . ':' . $period
    );
    $moreText = $area
        ? Lang::txt('MOD_WHATSNEW_VIEW_MORE_OF', e($area))
        : Lang::txt('MOD_WHATSNEW_VIEW_MORE');
  @endphp
  <p class="mt-2">
    <a href="{{ $moreUrl }}" class="btn btn-sm btn-outline">{{ $moreText }}</a>
  </p>
</div>
