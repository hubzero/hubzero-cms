{{--
  Course delete confirmation.

  Variables from controller (deleteTask):
    $course — Course model instance
    $title  — Page title string
    $msg    — Pre-filled message text
    $log    — Deletion log/impact info

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

  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_COURSES'), 'index.php?option=' . $option);
  }
  Pathway::append($title, '');
  Document::setTitle($title);

  $__view->css();
  $__view->js();

  $courseTitle = e($course->get('title'));
  $courseUrl   = Route::url('index.php?option=' . $option . '&gid=' . e($course->get('alias')), false);
  $editUrl    = Route::url(
      'index.php?option=' . $option . '&gid=' . e($course->get('alias')) . '&task=edit',
      false
  );
  $deleteUrl  = Route::url('index.php?option=' . $option, false);
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-ghost" href="{{ $courseUrl }}">
      {{ Lang::txt('COM_COURSES_BACK_TO_COURSE') }}
    </a>
  @endslot

  <x-confirm-dialog
      :title="Lang::txt('COM_COURSES_DELETE_CONFIRM_TITLE', $courseTitle)"
      :description="Lang::txt('COM_COURSES_DELETE_CONFIRM_DESCRIPTION')"
      :action="$deleteUrl"
      :confirmLabel="Lang::txt('COM_COURSES_DELETE')"
      :cancelUrl="$courseUrl"
      :error="''">

    <h3>{{ Lang::txt('COM_COURSES_DELETE_IMPACT_HEADING') }}</h3>
    <ul>
      <li>{{ Lang::txt('COM_COURSES_DELETE_IMPACT_COURSE', $courseTitle) }}</li>
      <li>{{ Lang::txt('COM_COURSES_DELETE_IMPACT_OFFERINGS') }}</li>
      <li>{{ Lang::txt('COM_COURSES_DELETE_IMPACT_ENROLLMENT') }}</li>
    </ul>

    <h3>{{ Lang::txt('COM_COURSES_DELETE_ALTERNATIVE_HEADING') }}</h3>
    <p>{{ Lang::txt('COM_COURSES_DELETE_ALTERNATIVE_EXPLANATION') }}</p>
    <p>
      <a class="link link-hover" href="{{ $editUrl }}">
        {{ Lang::txt('COM_COURSES_DELETE_ALTERNATIVE_LINK') }}
      </a>
    </p>

    <x-slot name="hiddenFields">
      <input type="hidden" name="gid" value="{{ e($course->get('cn')) }}" />
      <input type="hidden" name="task" value="delete" />
      <input type="hidden" name="process" value="1" />
      <input type="hidden" name="confirmdel" value="1" />
      <input type="hidden" name="option" value="{{ $option }}" />
      {!! Html::input('token') !!}
    </x-slot>
  </x-confirm-dialog>
</x-page-container>
