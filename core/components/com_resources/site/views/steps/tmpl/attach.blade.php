{{--
  Resources contribution attach step — file upload or child resource attachment.

  Variables from controller:
    $title      — page title
    $option     — component option string
    $controller — controller name
    $task       — current task
    $row        — resource model
    $step       — current step number
    $steps      — array of step names
    $next_step  — next step number
    $id         — resource id
    $progress   — progress array

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $base = rtrim(Request::base(true), '/');

  $__view->css('create.css');
  $__view->css('autocompleter.css');
  $__view->js('create.js');

  $group_cn = Request::getString('group', '');
  $draftUrl = Route::url('index.php?option=' . $option . '&task=draft');
  $actionUrl = Route::url(
      'index.php?option=' . $option
      . '&task=draft&step=' . $next_step
      . '&group=' . $group_cn
      . '&id=' . $id
  );
  $attachSrc = 'index.php?option=' . $option
      . '&controller=attachments&id=' . $id
      . '&tmpl=component';
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-primary" href="{{ $draftUrl }}">
      {{ Lang::txt('COM_CONTRIBUTE_NEW_SUBMISSION') }}
    </a>
  @endslot

  @include('steps::steps', [
      'option'   => $option,
      'step'     => $step,
      'steps'    => $steps,
      'id'       => $id,
      'group_cn' => $group_cn,
      'resource' => $row,
      'progress' => $progress,
  ])

  @if($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
      <span>{{ $__view->getError() }}</span>
    </div>
  @endif

  <form action="{{ $actionUrl }}" method="post" id="hubForm">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
      <div class="lg:col-span-2">
        <x-form-section :heading="Lang::txt('COM_CONTRIBUTE_ATTACH_ATTACHMENTS')">

          <div class="asset-uploader">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              @if(!$row->type->get('collection'))
                @php
                  $__view->js('jquery.fileuploader.js', 'system');
                  $__view->js('fileupload.js');
                @endphp
                @include('steps::attach_fileuploader', ['id' => $id])
              @else
                @php
                  $__view->js('addchild.js');
                @endphp
                @include('steps::attach_addchild', ['id' => $id])
              @endif
            </div>

            <iframe width="100%"
                    height="500"
                    name="attaches"
                    id="attaches"
                    title="{{ Lang::txt('COM_CONTRIBUTE_ATTACH_ATTACHMENTS') }}"
                    src="{{ $attachSrc }}"></iframe>
          </div>

          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="{{ $controller }}" />
          <input type="hidden" name="task" value="{{ $task }}" />
          <input type="hidden" name="step" value="{{ $next_step }}" />
          <input type="hidden" name="id" value="{{ $id }}" />

        </x-form-section>
      </div>

      <div>
        <div class="card bg-base-100 shadow-sm">
          <div class="card-body text-sm text-base-content/70">
            <h4 class="font-semibold text-base-content">
              {{ Lang::txt('COM_CONTRIBUTE_ATTACH_WHAT_ARE_ATTACHMENTS') }}
            </h4>
            <p>{{ Lang::txt('COM_CONTRIBUTE_ATTACH_EXPLANATION') }}</p>

            <h4 class="font-semibold text-base-content mt-4">
              {{ Lang::txt('COM_CONTRIBUTE_ACCESS') }}
            </h4>
            <p>
              <strong>{{ Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC') }}</strong>
              = {{ Lang::txt('COM_CONTRIBUTE_ACCESS_PUBLIC_EXPLANATION') }}
            </p>
            <p>
              <strong>{{ Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED') }}</strong>
              = {{ Lang::txt('COM_CONTRIBUTE_ACCESS_REGISTERED_EXPLANATION') }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex justify-end">
      <button type="submit" class="btn btn-primary">
        {{ Lang::txt('COM_CONTRIBUTE_NEXT') }}
      </button>
    </div>
  </form>

</x-page-container>
