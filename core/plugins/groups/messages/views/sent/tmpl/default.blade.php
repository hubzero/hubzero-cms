{{--
  Group Messages — sent messages list.

  Variables from plugin:
    $option     — component option string
    $group      — group object
    $rows       — message rows collection
    $total      — total message count
    $filters    — filters array (start, limit)
    $authorized — user authorization level

  @package    hubzero-cms
  @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $cn = $group->get('cn');
  $messagesUrl = Route::url(
      'index.php?option=' . $option . '&cn=' . $cn . '&active=messages'
  );
  $newMessageUrl = Route::url(
      'index.php?option=' . $option . '&cn=' . $cn . '&active=messages&action=new'
  );
@endphp

<h3 class="text-lg font-semibold mb-4" id="messages">
  {{ Lang::txt('MESSAGES') }}
</h3>

@if ($authorized == 'manager')
  <div class="mb-4">
    <a class="btn btn-primary btn-sm" href="{{ $newMessageUrl }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
      </svg>
      {{ Lang::txt('PLG_GROUPS_MESSAGES_SEND') }}
    </a>
  </div>
@endif

<div class="overflow-x-auto">
  <form action="{{ $messagesUrl }}" method="post">
    <table class="table table-zebra w-full">
      <caption class="text-left font-medium mb-2">
        {{ Lang::txt('PLG_GROUPS_MESSAGES_SENT') }}
        <span class="badge badge-ghost badge-sm ml-1">{{ count($rows) }}</span>
      </caption>
      <thead>
        <tr>
          <th scope="col">{{ Lang::txt('Subject') }}</th>
          <th scope="col">{{ Lang::txt('Message From') }}</th>
          <th scope="col">{{ Lang::txt('Date Sent') }}</th>
        </tr>
      </thead>
      <tbody>
        @if ($rows->count() > 0)
          @foreach ($rows as $row)
            @php
              $viewUrl = Route::url(
                  'index.php?option=' . $option
                  . '&cn=' . $cn
                  . '&active=messages&action=viewmessage&msg=' . $row->id
              );
              $memberUrl = Route::url(
                  'index.php?option=com_members&id=' . $row->created_by
              );
              $subject = e(stripslashes($row->subject));
              $name = e(stripslashes($row->name));
              $dateFormatted = Date::of($row->created)
                  ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
            @endphp
            <tr>
              <td>
                <a class="link link-hover" href="{{ $viewUrl }}">{{ $subject }}</a>
              </td>
              <td>
                <a class="link link-hover" href="{{ $memberUrl }}">{{ $name }}</a>
              </td>
              <td>
                <time datetime="{{ $row->created }}">{{ $dateFormatted }}</time>
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="3" class="text-center text-base-content/60">
              {{ Lang::txt('No messages found') }}
            </td>
          </tr>
        @endif
      </tbody>
    </table>
  </form>

  @php
    $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
    $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
    $pageNav->setAdditionalUrlParam('active', 'messages');
  @endphp
  {!! $pageNav->render() !!}
</div>
