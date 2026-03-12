{{--
  Admin edit/create form wrapper.

  Two-column grid with form validation and hidden fields.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@props([
    'option'     => '',
    'controller' => '',
    'enctype'    => '',
])

@php
  $formUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller,
      false, false
  );
  $invalidMsg = Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED');
@endphp

<form action="{{ $formUrl }}"
      method="post"
      name="adminForm"
      id="item-form"
      class="editform"
      data-invalid-msg="{{ $invalidMsg }}"
      @if($enctype) enctype="{{ $enctype }}" @endif>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_24rem] gap-6">
    <div class="min-w-0">
      {{ $slot }}
    </div>

    @if(isset($sidebar) && $sidebar->isNotEmpty())
      <div>
        {{ $sidebar }}
      </div>
    @endif
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="save" />

  {!! \Hubzero\Facades\Html::input('token') !!}
</form>
