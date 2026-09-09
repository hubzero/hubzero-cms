{{--
  Group member invitation form.

  Variables from controller:
    $title         — string: page title
    $option        — string: component option
    $group         — Group object
    $invites       — array: pre-filled invite logins
    $msg           — string: pre-filled message text
    $return        — string: return URL (base64)
    $notifications — array: queued notification messages

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css();

  $groupUrl  = Route::url('index.php?option=' . $option . '&cn=' . $group->get('cn'));
  $actionUrl = Route::url('index.php?option=' . $option);
  $imgSrc    = Request::base(true)
      . '/core/components/com_groups/site/assets/img/invite_example.jpg';
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-sm" href="{{ $groupUrl }}">
      {{ Lang::txt('COM_GROUPS_ACTION_BACK_TO_GROUP') }}
    </a>
  @endslot

  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_GROUPS_INVITE_SIDEBAR_HELP_TITLE')">
      <p class="text-sm text-base-content/70 mb-3">
        {{ Lang::txt('COM_GROUPS_INVITE_SIDEBAR_HELP_DESC') }}
      </p>
      <img class="invite-example rounded w-full"
           src="{{ $imgSrc }}"
           alt="Example Auto-Completer" />
    </x-sidebar-card>
  @endslot

  @foreach($notifications as $notification)
    <div class="alert alert-{{ $notification['type'] === 'passed' ? 'success' : e($notification['type']) }}"
         role="alert">
      {!! $notification['message'] !!}
    </div>
  @endforeach

  <form action="{{ $actionUrl }}" method="post" id="hubForm">
    <x-form-section :heading="Lang::txt('COM_GROUPS_INVITE_SECTION_TITLE')">
      <p class="mb-4">
        {{ Lang::txt('COM_GROUPS_INVITE_SECTION_DESC', $group->get('description')) }}
      </p>

      <x-form-field name="logins" inputId="acmembers"
                    :label="Lang::txt('COM_GROUPS_INVITE_LOGINS')"
                    :required="true">
        @php
          $inviteList = implode(', ', $invites);
          $mc = Event::trigger(
              'hubzero.onGetMultiEntry',
              [['members', 'logins', 'acmembers', '', $inviteList]]
          );
        @endphp
        @if(count($mc) > 0)
          {!! $mc[0] !!}
        @else
          <input type="text" name="logins" id="acmembers"
                 value="{{ e(implode(', ', $invites)) }}"
                 class="input input-bordered w-full" />
        @endif
        <span class="text-sm text-base-content/60">
          {{ Lang::txt('COM_GROUPS_INVITE_LOGINS_HINT') }}
        </span>
      </x-form-field>

      <x-form-field name="msg" inputId="msg"
                    :label="Lang::txt('COM_GROUPS_INVITE_MESSAGE')">
        <textarea name="msg" id="msg" rows="12"
                  class="textarea textarea-bordered w-full"
        >{{ e(stripslashes($msg)) }}</textarea>
      </x-form-field>
    </x-form-section>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="membership" />
    <input type="hidden" name="task" value="doinvite" />
    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="return" value="{{ $return }}" />
    {!! Html::input('token') !!}

    <div class="mt-6">
      <button type="submit" class="btn btn-success">
        {{ Lang::txt('COM_GROUPS_INVITE_BTN_TEXT') }}
      </button>
    </div>
  </form>
</x-page-container>
