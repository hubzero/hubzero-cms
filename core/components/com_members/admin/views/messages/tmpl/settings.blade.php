{{--
  Messaging Settings — Per-user notification preferences

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Lang::load('plg_members_messages', PATH_CORE . '/plugins/members/messages');

  $canDo = (User::authorise('core.admin', $option) || User::authorise('core.edit', $option));
@endphp

@if($__view->getError())
  <div class="alert alert-error">
    {{ Lang::txt('PLG_MEMBERS_MESSAGES_NO_COMPONENTS_FOUND') }}
  </div>
@else
  <form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=savesettings', false) !!}"
        method="post"
        name="adminForm"
        id="item-form">

    <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
      <table class="admin-table">
        @if($canDo)
          <caption class="p-3">
            <button type="submit" class="btn btn-sm btn-primary">
              {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS') }}
            </button>
          </caption>
        @endif
        <thead>
          <tr>
            <th>{{ Lang::txt('PLG_MEMBERS_MESSAGES_SENT_WHEN') }}</th>
            @foreach($notimethods as $notimethod)
              <th class="text-center">
                {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_' . strtoupper($notimethod)) }}
              </th>
            @endforeach
          </tr>
        </thead>
        @if($canDo)
          <tfoot>
            <tr>
              <td colspan="{{ count($notimethods) + 1 }}">
                <button type="submit" class="btn btn-sm btn-primary">
                  {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SAVE_SETTINGS') }}
                </button>
              </td>
            </tr>
          </tfoot>
        @endif
        <tbody>
          @php $sheader = ''; @endphp
          @foreach($components as $component)
            @if($component->name != $sheader)
              @php
                $sheader = $component->name;
                Lang::load($component->name, \Hubzero\Facades\Component::path($component->name) . '/site');

                $display_header = Lang::hasKey($component->name)
                    ? Lang::txt($component->name)
                    : ucfirst(str_replace('com_', '', $component->name));
              @endphp
              <tr class="bg-base-200">
                <th>{{ $display_header }}</th>
                @foreach($notimethods as $notimethod)
                  <th class="text-center text-xs">
                    {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_' . strtoupper($notimethod)) }}
                  </th>
                @endforeach
              </tr>
            @endif
            <tr>
              <th class="font-normal">{{ $component->title }}</th>
              {!! \Components\Members\Admin\Controllers\Messages::selectMethod(
                  $notimethods,
                  $component->action,
                  $settings[$component->action]['methods'],
                  $settings[$component->action]['ids']
              ) !!}
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="task" value="savesettings" />
    <input type="hidden" name="id" value="{{ $member->get('uidNumber') }}" />
    <input type="hidden" name="tmpl" value="{{ Request::getWord('tmpl') }}" />
    {!! Html::input('token') !!}
  </form>
@endif
