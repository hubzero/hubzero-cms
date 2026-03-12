{{--
  com_installer warnings — System compatibility warnings

  Variables: $messages, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_INSTALLER_HEADER_WARNINGS'), 'install');
  Toolbar::help('warnings');
@endphp

<form action="{{ Route::url('index.php?option=com_installer&controller=warnings', false) }}"
      method="post" name="adminForm" id="item-form">

  @if (!count($messages))
    <div role="alert" class="alert alert-success">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
      </svg>
      <span>{{ Lang::txt('COM_INSTALLER_MSG_WARNINGS_NONE') }}</span>
    </div>
  @else
    <div class="space-y-2">
      @foreach ($messages as $message)
        <details class="collapse collapse-arrow bg-base-100 border border-warning/40 rounded-box">
          <summary class="collapse-title text-sm font-medium text-accent-dark">
            {{ $message['message'] }}
          </summary>
          <div class="collapse-content">
            <div class="pt-2 text-sm prose max-w-none">
              {!! $message['description'] !!}
            </div>
          </div>
        </details>
      @endforeach

      <details class="collapse collapse-arrow bg-base-100 border border-base-300 rounded-box">
        <summary class="collapse-title text-sm font-medium">
          {{ Lang::txt('COM_INSTALLER_MSG_WARNINGFURTHERINFO') }}
        </summary>
        <div class="collapse-content">
          <div class="pt-2 text-sm prose max-w-none">
            {!! Lang::txt('COM_INSTALLER_MSG_WARNINGFURTHERINFODESC') !!}
          </div>
        </div>
      </details>
    </div>
  @endif

  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}
</form>
