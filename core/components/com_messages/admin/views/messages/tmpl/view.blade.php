{{--
  com_messages — Read single message

  Variables: $item (Message model with from_user_name, subject, message, date_time)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  Toolbar::title(Lang::txt('COM_MESSAGES_VIEW_PRIVATE_MESSAGE'), 'inbox');

  $sender    = User::getInstance($item->user_id_from);
  $canManage = $sender->authorise('core.manage', 'com_messages')
               && $sender->authorise('core.login.admin');

  if ($sender->authorise('core.admin') || $canManage) {
      Toolbar::custom('message.reply', 'restore', 'restore', 'COM_MESSAGES_TOOLBAR_REPLY', false);
  }
  Toolbar::cancel('message.cancel');
  Toolbar::help('read');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <div class="bg-base-100 rounded-box border border-base-300 p-6 max-w-3xl">

    {{-- Message metadata --}}
    <table class="admin-meta w-full mb-6">
      <tbody>
        <tr>
          <th class="text-xs font-medium text-muted-foreground pr-4 py-1.5 whitespace-nowrap w-32">
            {{ Lang::txt('COM_MESSAGES_FIELD_USER_ID_FROM_LABEL') }}
          </th>
          <td class="text-sm py-1.5">
            {{ $item->from ? $item->from->name : '' }}
          </td>
        </tr>
        <tr>
          <th class="text-xs font-medium text-muted-foreground pr-4 py-1.5 whitespace-nowrap">
            {{ Lang::txt('COM_MESSAGES_FIELD_DATE_TIME_LABEL') }}
          </th>
          <td class="text-sm py-1.5">
            @if ($item->date_time && $item->date_time !== '0000-00-00 00:00:00')
              <time datetime="{{ $item->date_time }}">
                {{ Date::of($item->date_time)->toLocal(Lang::txt('DATE_FORMAT_LC2')) }}
              </time>
            @endif
          </td>
        </tr>
        <tr>
          <th class="text-xs font-medium text-muted-foreground pr-4 py-1.5 whitespace-nowrap align-top">
            {{ Lang::txt('COM_MESSAGES_FIELD_SUBJECT_LABEL') }}
          </th>
          <td class="text-sm py-1.5 font-semibold">
            {{ $item->subject }}
          </td>
        </tr>
      </tbody>
    </table>

    {{-- Message body --}}
    <div class="border-t border-base-300 pt-4">
      <p class="text-xs font-medium text-muted-foreground mb-2">
        {{ Lang::txt('COM_MESSAGES_FIELD_MESSAGE_LABEL') }}
      </p>
      <pre class="whitespace-pre-wrap font-sans text-sm leading-relaxed text-base-content bg-base-200 rounded-box p-4 border border-base-300">{{ $item->message }}</pre>
    </div>

  </div>

  <input type="hidden" name="reply_id" value="{{ (int) $item->message_id }}" />
</x-admin-form>
