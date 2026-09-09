{{--
  Fork course form.

  Variables from controller (forkTask):
    $course — Course model instance to fork
    $return — Return URL (base64-encoded)

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

  $pageTitle = Lang::txt('COM_COURSES_FORK_COURSE');

  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_COURSES'), 'index.php?option=' . $option);
  }
  Pathway::append($pageTitle, '');
  Document::setTitle($pageTitle);

  $__view->css();

  $formUrl   = Route::url('index.php?option=' . $option, false);
  $cancelUrl = Route::url('index.php?option=' . $option, false);
  $forkTitle = Lang::txt('COM_COURSES_FORK_TITLE', stripslashes($course->get('title')));
@endphp

<x-page-container :title="$pageTitle">
  @slot('actions')
    <a class="btn btn-ghost" href="{{ $cancelUrl }}">
      {{ Lang::txt('JCANCEL') }}
    </a>
  @endslot

  <form action="{{ $formUrl }}" method="post" class="max-w-2xl">
    <x-form-section :heading="Lang::txt('COM_COURSES_FORK_ENTRY')">
      <p class="text-sm text-base-content/70 mb-4">
        {{ Lang::txt('COM_COURSES_FORK_EXPLANATION') }}
      </p>

      <x-form-field
          name="fields[alias]"
          inputId="course_alias_field"
          :label="Lang::txt('COM_COURSES_FIELD_ALIAS')"
          :hint="Lang::txt('COM_COURSES_FIELD_ALIAS_HINT')">
        <input type="text"
               name="fields[alias]"
               id="course_alias_field"
               class="input input-bordered w-full"
               value="{{ e($course->get('alias') . '_fork') }}"
               autocomplete="off" />
      </x-form-field>

      <x-form-field
          name="fields[title]"
          inputId="field-title"
          :label="Lang::txt('COM_COURSES_FIELD_TITLE')">
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               value="{{ e($forkTitle) }}" />
      </x-form-field>
    </x-form-section>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">
        {{ Lang::txt('COM_COURSES_FORK') }}
      </button>
      <a class="btn btn-ghost" href="{{ $cancelUrl }}">
        {{ Lang::txt('JCANCEL') }}
      </a>
    </div>

    <input type="hidden" name="id" value="{{ $course->get('id') }}" />
    <input type="hidden" name="gid" value="{{ $course->get('alias') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="course" />
    <input type="hidden" name="task" value="dofork" />
    <input type="hidden" name="return" value="{{ e($return) }}" />
    {!! Html::input('token') !!}
  </form>
</x-page-container>
