{{--
  Course create/edit form.

  Variables from controller (editTask):
    $course — Course model instance
    $title  — Page title string

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;

  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_COURSES'), 'index.php?option=' . $option);
  }
  Pathway::append($title, '');
  Document::setTitle($title);

  $__view->css();
  $__view->js();

  $isNew    = !$course->get('id');
  $formUrl  = Route::url('index.php?option=' . $option, false);
  $cancelUrl = Route::url('index.php?option=' . $option, false);
  $checkUrl = Route::url(
      'index.php?option=' . $option . '&controller=course&task=courseavailability&no_html=1',
      false
  );
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-ghost" href="{{ $cancelUrl }}">
      {{ Lang::txt('JCANCEL') }}
    </a>
  @endslot

  <form action="{{ $formUrl }}" method="post" class="max-w-2xl">
    <x-form-section :heading="Lang::txt('COM_COURSES_NEW_CREATE_ENTRY')">
      @if($isNew)
        <x-form-field
            name="course[alias]"
            inputId="course_alias_field"
            :label="Lang::txt('COM_COURSES_FIELD_ALIAS')"
            :hint="Lang::txt('COM_COURSES_FIELD_ALIAS_HINT')"
            :required="true">
          <input type="text"
                 name="course[alias]"
                 id="course_alias_field"
                 class="input input-bordered w-full"
                 value="{{ e($course->get('alias')) }}"
                 autocomplete="off"
                 required
                 data-route="{{ $checkUrl }}" />
        </x-form-field>
      @else
        <input type="hidden" name="alias" value="{{ e($course->get('alias')) }}" />
      @endif

      <x-form-field
          name="course[title]"
          inputId="field-title"
          :label="Lang::txt('COM_COURSES_FIELD_TITLE')"
          :required="true">
        <input type="text"
               name="course[title]"
               id="field-title"
               class="input input-bordered w-full"
               value="{{ e(stripslashes($course->get('title', ''))) }}"
               required />
      </x-form-field>

      <x-form-field
          name="course[blurb]"
          inputId="field-blurb"
          :label="Lang::txt('COM_COURSES_FIELD_BLURB')"
          :hint="Lang::txt('COM_COURSES_FIELD_BLURB_HINT')">
        <textarea name="course[blurb]"
                  id="field-blurb"
                  class="textarea textarea-bordered w-full"
                  rows="3">{{ e(stripslashes($course->get('blurb', ''))) }}</textarea>
      </x-form-field>

      <x-form-field
          name="tags"
          inputId="actags"
          :label="Lang::txt('COM_COURSES_FIELD_TAGS')"
          :hint="Lang::txt('COM_COURSES_FIELD_TAGS_HINT')">
        @php
          $tf = Event::trigger('hubzero.onGetMultiEntry', [
              ['tags', 'tags', 'actags', '', $course->tags('string')]
          ]);
        @endphp
        @if(count($tf) > 0)
          {!! implode("\n", $tf) !!}
        @else
          <input type="text"
                 name="tags"
                 id="actags"
                 class="input input-bordered w-full"
                 value="{{ e($course->tags('string')) }}" />
        @endif
      </x-form-field>

      <x-form-field
          name="params[allow_forks]"
          inputId="params-allow_forks"
          :label="Lang::txt('COM_COURSES_ALLOW_FORKS')"
          type="checkbox">
        <input type="checkbox"
               class="checkbox"
               name="params[allow_forks]"
               id="params-allow_forks"
               value="1"
               @if($isNew || $course->config('allow_forks')) checked @endif />
      </x-form-field>
    </x-form-section>

    <p class="text-sm text-base-content/70 mb-6">
      {{ Lang::txt('COM_COURSES_NEW_EXPLANATION') }}
    </p>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">
        {{ Lang::txt('COM_COURSES_SAVE') }}
      </button>
      <a class="btn btn-ghost" href="{{ $cancelUrl }}">
        {{ Lang::txt('JCANCEL') }}
      </a>
    </div>

    <input type="hidden" name="course[state]" value="{{ $course->get('state') }}" />
    <input type="hidden" name="course[id]" value="{{ $course->get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="course" />
    <input type="hidden" name="task" value="save" />
    {!! Html::input('token') !!}
  </form>
</x-page-container>
