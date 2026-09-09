{{--
  Featured Blog module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($__module->getError())
  <div role="alert" class="alert alert-error">
    <span>{{ Lang::txt('MOD_FEATUREDBLOG_MISSING_CLASS') }}</span>
  </div>
@elseif ($row)
  @php
    $base = rtrim(Request::base(true), '/');
    $row = $row;
    $blogUrl = Route::url($row->link());
    $thumbSrc = $base . '/core/modules/mod_featuredblog/assets/img/blog_thumb.gif';
  @endphp
  <div class="flex items-start gap-3 {{ $cls }}">
    <a href="{{ $blogUrl }}" class="shrink-0">
      <img class="size-12 rounded object-cover"
           src="{{ $thumbSrc }}"
           alt="{{ stripslashes($row->get('title')) }}" />
    </a>
    <p>
      <a href="{{ $blogUrl }}" class="link link-hover font-semibold">
        {{ stripslashes($row->get('title')) }}</a>@if ($row->get('content')):
        {{ \Hubzero\Utility\Str::truncate(strip_tags($row->content()), $txt_length) }}
      @endif
    </p>
  </div>
@endif
