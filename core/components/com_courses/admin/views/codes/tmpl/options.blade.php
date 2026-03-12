{{--
  Courses — Coupon Codes generate options (popup/component view)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $tmpl = Request::getString('tmpl', '');
  $isComponent = ($tmpl === 'component');

  $nextDate = new \DateTime('+1 month');
  $nextYear  = $nextDate->format('Y');
  $nextMonth = $nextDate->format('m');
  $nextDay   = $nextDate->format('d');

  $redirectUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&section=' . $section->get('id'),
      false, false
  );
  $formUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller, false
  );
@endphp

@if(!$isComponent)
  @php
    $canDo = \Components\Courses\Helpers\Permissions::getActions();

    Toolbar::title(
        Lang::txt('COM_COURSES') . ': '
        . Lang::txt('COM_COURSES_COUPON_CODE') . ': '
        . Lang::txt('COM_COURSES_GENERATE'),
        'courses'
    );
    if ($canDo->get('core.edit')) {
        Toolbar::save();
    }
    Toolbar::cancel();
  @endphp
@endif

<form action="{{ $formUrl }}"
      method="post"
      name="adminForm"
      id="{{ $isComponent ? 'component-form' : 'item-form' }}">

  @if($isComponent)
    <fieldset>
      <div class="flex items-center gap-2 p-2 border-b border-base-300 mb-4">
        <button type="button"
                id="btn-generate"
                class="btn btn-sm btn-primary"
                data-redirect="{{ $redirectUrl }}">
          {{ Lang::txt('COM_COURSES_GENERATE') }}
        </button>
        <button type="button" id="btn-cancel" class="btn btn-sm btn-ghost">
          {{ Lang::txt('JCANCEL') }}
        </button>
        <span class="text-sm ml-2">{{ Lang::txt('COM_COURSES_GENERATE_CODES') }}</span>
      </div>
    </fieldset>
  @endif

  <x-form-section title="{{ Lang::txt('JDETAILS') }}">
    <x-form-field
        label="{{ Lang::txt('COM_COURSES_FIELD_NUMBER_OF_CODES') }}"
        inputId="field-num"
    >
      <input type="text"
             name="num"
             id="field-num"
             class="input input-bordered input-sm w-20"
             value="5" />
    </x-form-field>

    <x-form-field
        label="{{ Lang::txt('COM_COURSES_FIELD_EXPIRES') }}"
        inputId="field-expires-year"
    >
      <div class="flex items-center gap-2">
        <div class="flex flex-col items-center">
          <span class="text-xs text-muted-foreground">YYYY</span>
          <input type="text"
                 name="expires[year]"
                 id="field-expires-year"
                 class="input input-bordered input-sm w-20"
                 value="{{ $nextYear }}" />
        </div>
        <div class="flex flex-col items-center">
          <span class="text-xs text-muted-foreground">MM</span>
          <input type="text"
                 name="expires[month]"
                 id="field-expires-month"
                 class="input input-bordered input-sm w-14"
                 value="{{ $nextMonth }}" />
        </div>
        <div class="flex flex-col items-center">
          <span class="text-xs text-muted-foreground">DD</span>
          <input type="text"
                 name="expires[day]"
                 id="field-expires-day"
                 class="input input-bordered input-sm w-14"
                 value="{{ $nextDay }}" />
        </div>
      </div>
    </x-form-field>
  </x-form-section>

  <input type="hidden" name="section" value="{{ $section->get('id') }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="no_html" value="{{ $isComponent ? '1' : '0' }}" />
  <input type="hidden" name="task" value="generate" />

  {!! Html::input('token') !!}
</form>
