{{--
  Resources delete confirmation page — confirm deletion of a draft contribution.

  Variables from controller:
    $title      — page title
    $option     — component option string
    $controller — controller name
    $step       — current step number
    $steps      — array of step definitions
    $id         — resource id
    $row        — resource object
    $progress   — progress data for steps display

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('create.css');

  $draftUrl  = Route::url('index.php?option=' . $option . '&task=draft');
  $actionUrl = Route::url(
      'index.php?option=' . $option . '&task=discard&step=2&id=' . $row->id
  );
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-primary" href="{{ $draftUrl }}">
      {{ Lang::txt('New submission') }}
    </a>
  @endslot

  @include('steps::steps', [
      'option'   => $option,
      'step'     => $step,
      'steps'    => $steps,
      'id'       => $id,
      'resource' => $row,
      'progress' => $progress,
  ])

  @if ($__view->getError())
    <div role="alert" class="alert alert-warning mb-4">
      <span>{!! implode('<br />', $__view->getErrors()) !!}</span>
    </div>
  @endif

  <form id="hubForm"
        method="post"
        action="{{ $actionUrl }}"
        class="max-w-2xl">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      {{-- Sidebar warning --}}
      <div class="md:col-span-1">
        <div role="alert" class="alert alert-warning">
          <span>{{ Lang::txt('COM_CONTRIBUTE_DELETE_WARNING') }}</span>
        </div>
      </div>

      {{-- Main fieldset --}}
      <fieldset class="md:col-span-2">
        <legend class="text-lg font-semibold mb-4">
          {{ Lang::txt('COM_CONTRIBUTE_DELETE_LEGEND') }}
        </legend>

        <input type="hidden" name="id" value="{{ $row->id }}" />
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="discard" />
        <input type="hidden" name="step" value="2" />

        <p class="mb-4">
          <strong>{{ e(stripslashes($row->title)) }}</strong><br />
          {{ e(stripslashes($row->typetitle)) }}
        </p>

        <label class="flex items-center gap-2 cursor-pointer mb-6">
          <input type="checkbox"
                 name="confirm"
                 value="confirmed"
                 class="checkbox checkbox-error" />
          <span>{{ Lang::txt('COM_CONTRIBUTE_DELETE_CONFIRM') }}</span>
        </label>

        <div>
          <button type="submit" class="btn btn-error">
            {{ Lang::txt('COM_CONTRIBUTE_DELETE') }}
          </button>
        </div>
      </fieldset>
    </div>
  </form>

</x-page-container>
