{{--
  Group deletion confirmation page.

  Variables from controller:
    $title         — string: page title
    $group         — Group object
    $option        — string: component option
    $notifications — array: queued notification messages
    $log           — string: group activity log summary
    $msg           — string: pre-filled message text

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css()->js();

  $groupUrl   = Route::url('index.php?option=' . $option . '&cn=' . $group->get('cn'));
  $deleteUrl  = Route::url('index.php?option=' . $option . '&task=delete');
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-sm" href="{{ $groupUrl }}">
      {{ Lang::txt('COM_GROUPS_ACTION_BACK_TO_GROUP') }}
    </a>
  @endslot

  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_GROUPS_DELETE_ARE_YOU_SURE_TITLE')">
      <p class="text-sm text-base-content/70 mb-4">
        {{ Lang::txt('COM_GROUPS_DELETE_ARE_YOU_SURE_DESC') }}
      </p>
      <p class="text-sm font-semibold mb-2">
        {{ Lang::txt('COM_GROUPS_DELETE_ALTERNATIVE_TITLE') }}
      </p>
      <p class="text-sm text-base-content/70 mb-4">
        {{ Lang::txt('COM_GROUPS_DELETE_ALTERNATIVE_DESC') }}
      </p>
      <a class="btn btn-sm btn-outline w-full" href="{{ $groupUrl }}&task=edit">
        {{ Lang::txt('COM_GROUPS_DELETE_ALTERNATIVE_BTN_TEXT') }}
      </a>
    </x-sidebar-card>
  @endslot

  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
         role="alert">
      {!! $notification['message'] !!}
    </div>
  @endforeach

  <form action="{{ $deleteUrl }}" method="post">
    <x-form-section :heading="Lang::txt('COM_GROUPS_DELETE_CONFIRM_BOX_HEADING')">
      <div class="alert alert-warning mb-4" role="alert">
        {!! Lang::txt('COM_GROUPS_DELETE_CONFIRM_BOX_WARNING', e($group->get('description')))
            . '<br /><br />' . $log !!}
      </div>

      <x-form-field name="confirmdel" inputId="confirmdel"
                    :label="Lang::txt('COM_GROUPS_DELETE_CONFIRM_CONFIRM', $group->get('cn'))"
                    :required="true">
        <input type="text" name="confirmdel" id="confirmdel" value=""
               class="input input-bordered w-full" required />
      </x-form-field>

      <x-form-field name="msg" inputId="msg"
                    :label="Lang::txt('COM_GROUPS_DELETE_CONFIRM_BOX_MESSAGE_LABEL')">
        <textarea name="msg" id="msg" rows="8"
                  class="textarea textarea-bordered w-full"
        >{{ $msg }}</textarea>
      </x-form-field>
    </x-form-section>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="task" value="dodelete" />
    {!! Html::input('token') !!}

    <div class="mt-6 flex gap-2">
      <button type="submit" class="btn btn-error">
        {{ Lang::txt('DELETE') }}
      </button>
      <a class="btn" href="{{ $groupUrl }}">
        {{ Lang::txt('JCANCEL') }}
      </a>
    </div>
  </form>
</x-page-container>
