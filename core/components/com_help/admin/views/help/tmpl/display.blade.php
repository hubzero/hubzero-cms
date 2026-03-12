{{--
  Help Page — Admin display (iframe/popup content)

  Renders inside component.blade.php (bare shell with admin.css).
  Appears inside the toolbar popup modal or overview iframe.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<div class="p-6" id="help-top">

  @if($page !== 'index')
    <div class="mb-4">
      <button type="button"
              class="btn btn-ghost btn-sm"
              data-history-back>
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
             stroke-width="2" stroke="currentColor" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
        </svg>
        {{ Lang::txt('COM_HELP_GO_BACK') }}
      </button>
    </div>
  @endif

  <div class="help-content">
    {!! $content !!}
  </div>

  <div class="mt-8 pt-4 border-t border-base-300 flex items-center gap-4 text-sm text-muted-foreground">
    <a href="#help-top" class="link link-hover">{{ Lang::txt('COM_HELP_BACK_TO_TOP') }}</a>
    @if($page !== 'index')
      @php
        $indexUrl = Route::url(
            'index.php?option=com_help&component=' . $component . '&page=index',
            false, false
        );
      @endphp
      <a href="{{ $indexUrl }}" class="link link-hover">{{ Lang::txt('COM_HELP_INDEX') }}</a>
    @endif
    @if(!empty($modified))
      <span class="ml-auto">
        {{ Lang::txt('COM_HELP_LAST_MODIFIED', date('l, F d, Y @ g:ia', $modified)) }}
      </span>
    @endif
  </div>

</div>
