{{--
  Create new offering form.

  Variables from controller (newofferingTask):
    $course   — Course model instance
    $offering — Offering model instance (new/empty)
    $title    — Page title string
    $no_html  — bool, true if rendering in component mode

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;

  if (!$no_html) {
      if (Pathway::count() <= 0) {
          Pathway::append(Lang::txt('COM_COURSES'), 'index.php?option=' . $option);
      }
      Pathway::append($title, '');
      Document::setTitle($title);
  }

  $formUrl   = Route::url('index.php?option=' . $option, false);
  $backUrl   = Route::url($course->link(), false);
@endphp

@if(!$no_html)
<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-ghost" href="{{ $backUrl }}">
      {{ Lang::txt('COM_COURSES_BACK') }}
    </a>
  @endslot
@endif

  <form action="{{ $formUrl }}" method="post" class="max-w-2xl">
    <x-form-section :heading="Lang::txt('COM_COURSES_FIELDSET_NEW_OFFERING')">
      @if(!$no_html)
        <p class="text-sm text-base-content/70 mb-4">
          {{ Lang::txt('COM_COURSES_NEW_OFFERING_EXPLANATION') }}
        </p>
      @endif

      <x-form-field
          name="offering[alias]"
          inputId="field-alias"
          :label="Lang::txt('COM_COURSES_FIELD_OFFERING_ALIAS')"
          :hint="Lang::txt('COM_COURSES_FIELD_OFFERING_ALIAS_HINT')">
        <input type="text"
               name="offering[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               value="{{ e($offering->get('alias')) }}" />
      </x-form-field>

      <x-form-field
          name="offering[title]"
          inputId="field-title"
          :label="Lang::txt('COM_COURSES_FIELD_TITLE')"
          :required="true">
        <input type="text"
               name="offering[title]"
               id="field-title"
               class="input input-bordered w-full"
               value="{{ e(stripslashes($offering->get('title', ''))) }}"
               required />
      </x-form-field>
    </x-form-section>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">
        {{ Lang::txt('COM_COURSES_SAVE') }}
      </button>
      @if(!$no_html)
        <a class="btn btn-ghost" href="{{ $backUrl }}">
          {{ Lang::txt('JCANCEL') }}
        </a>
      @endif
    </div>

    <input type="hidden" name="offering[state]" value="{{ $offering->get('state', 1) }}" />
    <input type="hidden" name="offering[course_id]" value="{{ $course->get('id') }}" />
    <input type="hidden" name="offering[id]" value="{{ $offering->get('id') }}" />
    <input type="hidden" name="gid" value="{{ $course->get('alias') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="offering" />
    <input type="hidden" name="task" value="saveoffering" />
    {!! Html::input('token') !!}
  </form>

@if(!$no_html)
</x-page-container>
@endif
