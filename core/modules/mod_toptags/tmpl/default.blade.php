{{--
  Top Tags module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $exclude = explode(',', $params->get('exclude', ''));
  $exclude = array_map('trim', $exclude);
@endphp

@if ($tags->count() > 0)
  @php
    $tl = [];
    foreach ($tags as $tag) {
        if (!in_array($tag->get('raw_tag'), $exclude)) {
            $tl[$tag->get('tag')] = $tag;
        }
    }
    if ($params->get('sortby') == 'alphabeta') {
        ksort($tl);
    }
  @endphp
  <div class="flex flex-wrap gap-2">
    @foreach ($tl as $tag)
      <a href="{{ Route::url('index.php?option=com_tags&tag=' . e($tag->get('tag'))) }}"
         class="badge badge-soft badge-primary">
        {{ $tag->get('raw_tag') }}
      </a>
    @endforeach
  </div>
  @if ($params->get('morelnk'))
    <p class="mt-2">
      <a href="{{ Route::url('index.php?option=com_tags') }}" class="btn btn-sm btn-outline">
        {{ Lang::txt('MOD_TOPTAGS_MORE') }}
      </a>
    </p>
  @endif
@else
  <p class="text-base-content/60">{{ $params->get('message', 'No $tags found.') }}</p>
@endif
