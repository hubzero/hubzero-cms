{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;

$__view->css();
$__view->js();
@endphp

<form action="{{ Route::url($member->link() . '&active=messages&task=archive') }}" method="post">
  {{-- Filter row --}}
  <div class="flex flex-wrap items-center gap-2 mb-3">
    <label for="msg-filter" class="font-medium">
      {{ Lang::txt('PLG_MEMBERS_MESSAGES_FROM') }}
    </label>
    <input type="hidden" name="inaction" value="archive" />
    <select id="msg-filter" class="select select-bordered select-sm" name="filter">
      <option value="">{{ Lang::txt('PLG_MEMBERS_MESSAGES_ALL') }}</option>
      @if ($components)
        @foreach ($components as $comp)
          @php
            $compName = substr($comp->component, 4);
          @endphp
          <option value="{{ $compName }}"
            {{ $compName == $filters['filter'] ? 'selected' : '' }}>
            {{ $compName }}
          </option>
        @endforeach
      @endif
    </select>
    <button type="submit" class="btn btn-sm btn-neutral">
      {{ Lang::txt('PLG_MEMBERS_MESSAGES_FILTER') }}
    </button>
  </div>

  {{-- Actions row --}}
  <div class="flex flex-wrap items-center gap-2 mb-4">
    <label for="msg-action" class="sr-only">
      {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_WITH_SELECTED') }}
    </label>
    <select id="msg-action" class="select select-bordered select-sm" name="action">
      <option value="">{{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_WITH_SELECTED') }}</option>
      <option value="markasread">{{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_MARK_AS_READ') }}</option>
      <option value="markasunread">{{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_MARK_AS_UNREAD') }}</option>
      <option value="sendtoinbox">{{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_INBOX') }}</option>
      <option value="sendtotrash">{{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_SEND_TO_TRASH') }}</option>
    </select>
    <input type="hidden" name="activetab" value="archive" />
    <button type="submit" class="btn btn-sm btn-neutral">
      {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_APPLY') }}
    </button>
  </div>

  <div class="overflow-x-auto">
    <table class="table table-zebra w-full">
      <thead>
        <tr>
          <th scope="col" class="w-8">
            <label for="msgall" class="sr-only">
              {{ Lang::txt('PLG_MEMBERS_MESSAGES_MSG_WITH_SELECTED') }}
            </label>
            <input type="checkbox" class="checkbox checkbox-sm" name="msgall" id="msgall" value="all" />
          </th>
          <th scope="col" class="w-20"><span class="sr-only">Status</span></th>
          <th scope="col">{{ Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT') }}</th>
          <th scope="col">{{ Lang::txt('PLG_MEMBERS_MESSAGES_FROM') }}</th>
          <th scope="col">{{ Lang::txt('PLG_MEMBERS_MESSAGES_DATE_RECEIVED') }}</th>
          <th scope="col" class="w-12"><span class="sr-only">{{ Lang::txt('PLG_MEMBERS_MESSAGES_TRASH') }}</span></th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            @php
              $pageNav = new \Hubzero\Pagination\Paginator(
                  $total,
                  $filters['start'],
                  $filters['limit']
              );
              $pageNav->setAdditionalUrlParam('id', $member->get('id'));
              $pageNav->setAdditionalUrlParam('active', 'messages');
              $pageNav->setAdditionalUrlParam('task', 'archive');
              $pageNav->setAdditionalUrlParam('action', '');
            @endphp
            {!! $pageNav->render() !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @if ($rows)
          @foreach ($rows as $row)
            @php
              $isRead = ($row->whenseen && $row->whenseen != '0000-00-00 00:00:00');
              $component = (substr($row->component, 0, 4) == 'com_')
                  ? substr($row->component, 4)
                  : $row->component;

              $subject = $row->subject;
              if ($component == 'support') {
                  $parts = explode(' ', $row->subject);
                  array_pop($parts);
                  $subject = implode(' ', $parts);
              }

              $messageUrl = Route::url(
                  $member->link() . '&active=messages&msg=' . $row->id
              );

              $trashUrl = Route::url(
                  $member->link()
                  . '&active=messages&mid[]=' . $row->id
                  . '&action=sendtotrash&activetab=archive&'
                  . Session::getFormToken() . '=1'
              );
            @endphp
            <tr>
              <td>
                <label for="msg{{ $row->id }}" class="sr-only">
                  {{ $subject }}
                </label>
                <input class="checkbox checkbox-sm"
                       type="checkbox"
                       id="msg{{ $row->id }}"
                       value="{{ $row->id }}"
                       name="mid[]" />
              </td>
              <td>
                @if ($isRead)
                  <span class="badge badge-ghost badge-sm">{{ Lang::txt('PLG_MEMBERS_MESSAGES_READ') }}</span>
                @else
                  <span class="badge badge-info badge-sm">{{ Lang::txt('PLG_MEMBERS_MESSAGES_STATUS_UNREAD') }}</span>
                @endif
              </td>
              <td>
                <a class="{{ !$isRead ? 'font-bold' : '' }}"
                   href="{{ $messageUrl }}">
                  {{ $subject }}
                </a>
              </td>
              <td>
                @if (substr($row->type, -8) == '_message')
                  @if ($row->anonymous)
                    {{ Lang::txt('JANONYMOUS') }}
                  @else
                    @php
                      $u = User::getInstance($row->created_by);
                      $profileUrl = Route::url(
                          'index.php?option=' . $option . '&id=' . $u->get('id')
                      );
                    @endphp
                    <a href="{{ $profileUrl }}">{{ $u->get('name') }}</a>
                  @endif
                @else
                  {{ Lang::txt('PLG_MEMBERS_MESSAGES_SYSTEM', $component) }}
                @endif
              </td>
              <td>
                <time datetime="{{ $row->created }}">
                  {{ Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
                </time>
              </td>
              <td>
                <a class="btn btn-ghost btn-xs"
                   href="{{ $trashUrl }}"
                   aria-label="{{ Lang::txt('PLG_MEMBERS_MESSAGES_REMOVE_MESSAGE') ?? Lang::txt('PLG_MEMBERS_MESSAGES_TRASH') }}">
                  {{ Lang::txt('PLG_MEMBERS_MESSAGES_TRASH') }}
                </a>
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="6">{{ Lang::txt('PLG_MEMBERS_MESSAGES_NONE') }}</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>

  {!! Html::input('token') !!}
</form>
