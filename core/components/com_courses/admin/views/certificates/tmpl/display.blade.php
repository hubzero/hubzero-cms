{{--
  Courses — Certificates admin designer view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $__view->css('certificates.css');
  $__view->js('certificates.blade.js');

  $routeUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );
  $certWidth  = $certificate->properties()->width;
  $certHeight = $certificate->properties()->height;
@endphp

@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_CERTIFICATE'),
      'courses'
  );
  Toolbar::custom('preview', 'preview', '', 'COM_COURSES_PREVIEW', false);
  if ($canDo->get('core.edit')) {
      Toolbar::spacer();
      Toolbar::apply();
      Toolbar::save();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::spacer();
      Toolbar::deleteList();
      Toolbar::spacer();
  }
  Toolbar::cancel();
  Toolbar::spacer();
  Toolbar::help('certificates');
@endphp

<form action="{{ $routeUrl }}" method="post" name="adminForm" id="item-form">

  <fieldset class="bg-base-100 rounded-box border border-base-300 p-4 mb-4">
    <div class="grid grid-cols-12 gap-4">
      <div class="col-span-7">
        <button type="button" class="btn btn-sm btn-outline placeholder" data-id="username">
          {{ Lang::txt('COM_COURSES_BTN_USERNAME') }}
        </button>
        <button type="button" class="btn btn-sm btn-outline placeholder" data-id="name">
          {{ Lang::txt('COM_COURSES_BTN_NAME') }}
        </button>
        <button type="button" class="btn btn-sm btn-outline placeholder" data-id="date">
          {{ Lang::txt('COM_COURSES_BTN_DATE') }}
        </button>
        <button type="button" class="btn btn-sm btn-outline placeholder" data-id="email">
          {{ Lang::txt('COM_COURSES_BTN_EMAIL') }}
        </button>
        <button type="button" class="btn btn-sm btn-outline placeholder" data-id="course">
          {{ Lang::txt('COM_COURSES_BTN_COURSE') }}
        </button>
        <button type="button" class="btn btn-sm btn-outline placeholder" data-id="offering">
          {{ Lang::txt('COM_COURSES_BTN_OFFERING') }}
        </button>
        <button type="button" class="btn btn-sm btn-outline placeholder" data-id="section">
          {{ Lang::txt('COM_COURSES_BTN_SECTION') }}
        </button>
      </div>
      <div class="col-span-5 text-right">
        <button type="button" class="btn btn-sm btn-error btn-outline" id="clear-canvas" data-id="clear">
          {{ Lang::txt('COM_COURSES_BTN_CLEAR') }}
        </button>
      </div>
    </div>
  </fieldset>

  <div id="certificate"
       data-width="{{ $certWidth }}"
       data-height="{{ $certHeight }}">
    @php
      $certificate->eachPage(function ($src, $idx) use ($certWidth, $certHeight) {
          echo '<img src="' . $src . '" id="page-' . $idx . '" width="' . $certWidth
              . '" height="' . $certHeight . '" alt="" />';
      });
    @endphp
    <canvas id="secondLayer"
            data-dimensions=""
            width="{{ $certWidth }}"
            height="{{ $certHeight }}"></canvas>
  </div>

  <input type="hidden"
         name="fields[properties]"
         id="field-properties"
         value="{{ $certificate->get('properties') }}" />
  <input type="hidden" name="fields[id]" value="{{ $certificate->get('id') }}" />
  <input type="hidden" name="fields[course_id]" value="{{ $certificate->get('course_id') }}" />
  <input type="hidden" name="certificate" value="{{ $certificate->get('id') }}" />
  <input type="hidden" name="course" value="{{ $certificate->get('course_id') }}" />
  <input type="hidden" name="boxchecked" value="1" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="save" />

  {!! Html::input('token') !!}
</form>
