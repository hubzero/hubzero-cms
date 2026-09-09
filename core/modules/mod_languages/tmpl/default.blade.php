{{--
  Languages module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="mod-languages{{ $moduleclass_sfx }}">
  @if ($headerText)
    <div class="mb-2"><p>{{ $headerText }}</p></div>
  @endif

  @if ($params->get('dropdown', 1))
    <form name="lang" method="post" action="{{ htmlspecialchars(Request::current()) }}">
      <select class="select select-bordered select-sm w-full" onchange="document.location.replace(this.value);">
        @foreach ($list as $language)
          @php
            $dir = Lang::getInstance($language->lang_code)->isRTL() ? 'rtl' : 'ltr';
          @endphp
          <option dir="{{ $dir }}" value="{{ $language->link }}"
                  {{ $language->active ? 'selected' : '' }}>
            {{ $language->title_native }}
          </option>
        @endforeach
      </select>
    </form>
  @else
    <ul class="flex {{ $params->get('inline', 1) ? 'flex-wrap gap-2' : 'flex-col gap-1' }}">
      @foreach ($list as $language)
        @if ($params->get('show_active', 0) || !$language->active)
          @php
            $langDir = Lang::getInstance($language->lang_code)->isRTL() ? 'rtl' : 'ltr';
          @endphp
          <li class="{{ $language->active ? 'font-bold' : '' }}" dir="{{ $langDir }}">
            <a href="{{ $language->link }}" class="link link-hover">
              @if ($params->get('image', 1))
                <img src="{{ $__module->img($language->image . '.gif') }}"
                     alt="{{ $language->title_native }}" />
              @else
                {{ $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef) }}
              @endif
            </a>
          </li>
        @endif
      @endforeach
    </ul>
  @endif

  @if ($footerText)
    <div class="mt-2"><p>{{ $footerText }}</p></div>
  @endif
</div>
