{{--
  Group join request form.

  Variables from controller:
    $title         — string: page title
    $option        — string: component option
    $group         — Group object
    $notifications — array: queued notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $actionUrl = Route::url('index.php?option=' . $option);
  $allUrl    = Route::url('index.php?option=' . $option);
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-sm" href="{{ $allUrl }}">
      {{ Lang::txt('COM_GROUPS_ACTION_BACK_TO_ALL_GROUPS') }}
    </a>
  @endslot

  @slot('sidebar')
    <x-sidebar-card>
      <p class="text-sm text-base-content/70">
        {{ Lang::txt('COM_GROUPS_JOIN_HELP') }}
      </p>
    </x-sidebar-card>
  @endslot

  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
         role="alert">
      {!! $notification['message'] !!}
    </div>
  @endforeach

  <form action="{{ $actionUrl }}" method="post" id="hubForm">
    <x-form-section :heading="Lang::txt('COM_GROUPS_JOIN_SECTION_TITLE')">
      @if($group->get('restrict_msg'))
        <div class="alert alert-warning mb-4" role="alert">
          {{ Lang::txt('NOTE') }}: {{ e(stripslashes($group->get('restrict_msg'))) }}
        </div>
      @endif

      <x-form-field name="reason" inputId="reason"
                    :label="Lang::txt('COM_GROUPS_JOIN_REASON')">
        {!! $__view->editor(
            'reason',
            '',
            10,
            10,
            'reason',
            ['class' => 'form-control minimal no-footer']
        ) !!}
      </x-form-field>
    </x-form-section>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="membership" />
    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="task" value="dorequest" />
    {!! Html::input('token') !!}

    <div class="mt-6">
      <button type="submit" class="btn btn-success">
        {{ Lang::txt('COM_GROUPS_JOIN_BTN_TEXT') }}
      </button>
    </div>
  </form>
</x-page-container>
