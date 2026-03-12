{{--
  Members — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo   = \Components\Members\Helpers\Admin::getActions('component');
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'id';

  $__view->css('members.blade.css')->js();

  // -- Toolbar setup --
  Toolbar::title(Lang::txt('COM_MEMBERS'));

  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option);
      Toolbar::getRoot()->appendButton(
          'Link',
          'buildprofile',
          'COM_MEMBERS_PROFILE',
          Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=profile', false
          )
      );
      Toolbar::spacer();

      $exportUrl = 'index.php?option=' . $option
          . '&controller=exports&task=run';
      foreach ($filters as $fKey => $fVal) {
          $exportUrl .= '&' . $fKey . '=' . $fVal;
      }
      Toolbar::getRoot()->appendButton(
          'Link',
          'download',
          'COM_MEMBERS_MENU_EXPORT',
          Route::url($exportUrl, false)
      );
      Toolbar::spacer();
  }

  if ($canDo->get('core.edit.state')) {
      Toolbar::custom('clearTerms', 'remove', '', 'COM_MEMBERS_CLEAR_TERMS', false);
      Toolbar::spacer();
      Toolbar::publishList('confirm', 'COM_MEMBERS_CONFIRM');
      Toolbar::unpublishList('unconfirm', 'COM_MEMBERS_UNCONFIRM');
      Toolbar::divider();
      Toolbar::custom('block', 'cancel', '', 'COM_MEMBERS_BLOCK', true);
      Toolbar::custom('unblock', 'restore', '', 'COM_MEMBERS_UNBLOCK', true);
      Toolbar::spacer();
  }

  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_MEMBERS_CONFIRMATION_WARNING');
      if ($canDo->get('core.deidentify')) {
          Toolbar::custom(
              'deidentify',
              'eye-close',
              '',
              'COM_MEMBERS_DEIDENTIFY',
              true
          );
      }
  }

  Toolbar::spacer();
  Toolbar::help('users');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <input type="text"
               name="search"
               class="input input-bordered input-sm"
               placeholder="{{ Lang::txt('COM_MEMBERS_SEARCH_PLACEHOLDER') }}"
               value="{{ $filters['search'] ?? '' }}"
               data-submit-on-change />
      @endslot

      <select name="activation"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('COM_MEMBERS_FILTER_EMAIL_CONFIRMED') }}">
        <option value="0">
          {{ Lang::txt('COM_MEMBERS_FILTER_EMAIL_CONFIRMED') }}
        </option>
        <option value="1"
                @selected(($filters['activation'] ?? 0) == 1)>
          {{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRMED_CONFIRMED') }}
        </option>
        <option value="-1"
                @selected(($filters['activation'] ?? 0) == -1)>
          {{ Lang::txt('COM_MEMBERS_FIELD_EMAIL_CONFIRMED_UNCONFIRMED') }}
        </option>
      </select>

      <select name="access"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('JOPTION_SELECT_ACCESS') }}">
        <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
        {!! Html::select(
            'options',
            Html::access('assetgroups'),
            'value',
            'text',
            $filters['access'] ?? ''
        ) !!}
      </select>

      <select name="state"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('COM_MEMBERS_FILTER_STATE') }}">
        <option value="*">{{ Lang::txt('COM_MEMBERS_FILTER_STATE') }}</option>
        {!! Html::select(
            'options',
            \Components\Members\Helpers\Admin::getStateOptions(),
            'value',
            'text',
            $filters['state'] ?? '*'
        ) !!}
      </select>

      <select name="approved"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('COM_MEMBERS_FILTER_APPROVED') }}">
        <option value="*">{{ Lang::txt('COM_MEMBERS_FILTER_APPROVED') }}</option>
        {!! Html::select(
            'options',
            \Components\Members\Helpers\Admin::getApprovedOptions(),
            'value',
            'text',
            $filters['approved'] ?? '*'
        ) !!}
      </select>

      <select name="group_id"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('COM_MEMBERS_FILTER_USERGROUP') }}">
        <option value="">{{ Lang::txt('COM_MEMBERS_FILTER_USERGROUP') }}</option>
        {!! Html::select(
            'options',
            \Components\Members\Helpers\Admin::getAccessGroups(),
            'value',
            'text',
            $filters['group_id'] ?? ''
        ) !!}
      </select>

      <select name="range"
              class="select select-bordered select-sm"
              data-submit-on-change
              aria-label="{{ Lang::txt('COM_MEMBERS_OPTION_FILTER_DATE') }}">
        <option value="">{{ Lang::txt('COM_MEMBERS_OPTION_FILTER_DATE') }}</option>
        {!! Html::select(
            'options',
            \Components\Members\Helpers\Admin::getRangeOptions(),
            'value',
            'text',
            $filters['range'] ?? ''
        ) !!}
      </select>
    </x-admin-filters>
  @endslot

  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col">
          <input type="checkbox" data-check-all aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
        </th>
        <th scope="col" class="priority-2">
          {!! Html::grid('sort', 'COM_MEMBERS_COL_ID', 'id', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_MEMBERS_COL_NAME', 'name', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-5">
          {!! Html::grid('sort', 'COM_MEMBERS_COL_USERNAME', 'username', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-6">
          {!! Html::grid('sort', 'COM_MEMBERS_COL_EMAIL', 'email', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-3 nowrap">
          {{ Lang::txt('COM_MEMBERS_COL_GROUPS') }}
        </th>
        <th scope="col" class="priority-4">
          {{ Lang::txt('COM_MEMBERS_STATUS') }}
        </th>
        <th scope="col" class="priority-3">
          {!! Html::grid('sort', 'COM_MEMBERS_COL_REGISTERED', 'registerDate', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-6">
          {!! Html::grid('sort', 'COM_MEMBERS_COL_LAST_VISIT', 'lastvisitDate', $sortDir, $sort) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $i => $row)
        @php
          $canEdit   = $canDo->get('core.edit');
          $canChange = User::authorise('core.edit.state', $option);

          // Super admin protection
          if (!User::authorise('core.admin')
              && Hubzero\Access\Access::check($row->get('id'), 'core.admin')
          ) {
              $canEdit   = false;
              $canChange = false;
          }

          // Build display name
          if (!$row->get('surname') && !$row->get('givenName')) {
              $bits = explode(' ', $row->get('name'));
              $row->set('surname', array_pop($bits));
              if (count($bits) >= 1) {
                  $row->set('givenName', array_shift($bits));
              }
              if (count($bits) >= 1) {
                  $row->set('middleName', implode(' ', $bits));
              }
          }

          $displayName = $row->get('surname', Lang::txt('COM_MEMBERS_UNDEFINED'))
              . ', '
              . $row->get('givenName', Lang::txt('COM_MEMBERS_UNDEFINED'))
              . ' ' . $row->get('middleName');

          // Activation state
          $activation = $row->get('activation');

          // Build groups list
          $groups = [];
          foreach ($row->accessgroups as $agroup) {
              $groups[] = $accessgroups->seek($agroup->get('group_id'))
                  ->get('title');
          }
          $groupNames = implode('<br />', $groups);

          // Incomplete check
          $incomplete    = false;
          $authenticator = 'hub';
          if (substr($row->get('email'), -8) == '@invalid') {
              $authenticator = Lang::txt('COM_MEMBERS_UNKNOWN');
              if ($lnk = Hubzero\Auth\Link::find_by_id(
                  abs(intval($row->get('username')))
              )) {
                  $domain = Hubzero\Auth\Domain::find_by_id(
                      $lnk->auth_domain_id
                  );
                  $authenticator = $domain->authenticator;
              }
              $incomplete = true;
          }

          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->get('id'), false
          );
          $debugUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=debug&id=' . $row->get('id'), false
          );
          $notesFilterUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=notes&search=uid%3A'
              . (int) $row->get('id'), false
          );
          $notesModalUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=notes&tmpl=component&task=modal&id='
              . (int) $row->get('id'), false
          );
          $addNoteUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=notes&task=add&user_id='
              . (int) $row->get('id'), false
          );
          $noteCount = $row->notes->count();
        @endphp
        <tr>
          <td>
            @if ($canEdit)
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     data-check-item
                     aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $row->get('name', $row->get('username'))) }}" />
            @endif
          </td>
          <td class="priority-2">
            {{ $row->get('id') }}
          </td>
          <td>
            <div class="float-right">
              @if ($noteCount)
                <a class="state filter"
                   href="{!! $notesFilterUrl !!}"
                   title="{{ Lang::txt('COM_MEMBERS_FILTER_NOTES') }}">
                  <span>{{ Lang::txt('COM_MEMBERS_NOTES') }}</span>
                </a>
                <a class="modal state notes"
                   href="{!! $notesModalUrl !!}"
                   title="{{ Lang::txts('COM_MEMBERS_N_USER_NOTES', $noteCount) }}">
                  <span>{{ Lang::txt('COM_MEMBERS_NOTES') }}</span>
                </a>
              @endif
              <a class="state notes"
                 href="{!! $addNoteUrl !!}"
                 title="{{ Lang::txt('COM_MEMBERS_ADD_NOTE') }}">
                <span>{{ Lang::txt('COM_MEMBERS_NOTES') }}</span>
              </a>
            </div>
            @if ($canEdit)
              <a href="{!! $editUrl !!}"
                 title="{{ $displayName }}">
                {{ $displayName }}
              </a>
            @else
              {{ $displayName }}
            @endif
            @if (Config::get('debug'))
              <a class="permissions button"
                 href="{!! $debugUrl !!}">
                {{ Lang::txt('COM_MEMBERS_DEBUG_USER') }}
              </a>
            @endif
          </td>
          <td class="priority-5">
            {{ $incomplete ? '--' : e($row->get('username')) }}
          </td>
          <td class="priority-6">
            {{ $incomplete ? '--' : e($row->get('email')) }}
          </td>
          <td class="center priority-3">
            @if (substr_count($groupNames, '<br />') > 1)
              <span title="{{ Lang::txt('COM_MEMBERS_HEADING_GROUPS') }}">
                {{ Lang::txt('COM_MEMBERS_MULTIPLE_GROUPS') }}
              </span>
            @else
              {!! $groupNames !!}
            @endif
          </td>
          <td class="center priority-4">
            @if ($row->get('block'))
              {{-- Blocked --}}
              <div class="btn-group dropdown user-state blocked">
                <span class="btn"
                      title="{{ Lang::txt('COM_MEMBERS_STATUS_BLOCKED_DESC') }}">
                  {{ Lang::txt('COM_MEMBERS_STATUS_BLOCKED') }}
                </span>
                @if ($canChange)
                  <span class="btn dropdown-toggle"></span>
                  <ul class="dropdown-menu">
                    <li>
                      <a class="grid-action grid-boolean icon-unban"
                         data-id="cb{{ $i }}"
                         data-task="unblock"
                         href="#toggle">
                        {{ Lang::txt('COM_MEMBERS_ACTION_UNBLOCK') }}
                      </a>
                    </li>
                  </ul>
                @endif
              </div>
            @elseif ($incomplete)
              {{-- Incomplete --}}
              <div class="btn-group dropdown user-state incomplete">
                <span class="btn"
                      title="{{ Lang::txt('COM_MEMBERS_STATUS_INCOMPLETE_DESC') }}">
                  {{ Lang::txt('COM_MEMBERS_STATUS_INCOMPLETE', $authenticator) }}
                </span>
              </div>
            @elseif ($activation <= 0)
              {{-- Unconfirmed --}}
              <div class="btn-group dropdown user-state unconfrmed">
                <span class="btn"
                      title="{{ Lang::txt('COM_MEMBERS_STATUS_UNCONFIRMED_DESC') }}">
                  {{ Lang::txt('COM_MEMBERS_STATUS_UNCONFIRMED') }}
                </span>
                @if ($canChange)
                  <span class="btn dropdown-toggle"></span>
                  <ul class="dropdown-menu">
                    <li>
                      <a class="grid-action grid-boolean icon-success"
                         data-id="cb{{ $i }}"
                         data-task="confirm"
                         href="#toggle">
                        {{ Lang::txt('COM_MEMBERS_ACTION_CONFIRM') }}
                      </a>
                    </li>
                    <li>
                      <a class="grid-action grid-boolean icon-resend"
                         data-id="cb{{ $i }}"
                         data-task="resendConfirm"
                         href="#toggle">
                        {{ Lang::txt('COM_MEMBERS_ACTION_RESEND') }}
                      </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a class="grid-action grid-boolean icon-ban"
                         data-id="cb{{ $i }}"
                         data-task="block"
                         href="#toggle">
                        {{ Lang::txt('COM_MEMBERS_ACTION_BLOCK') }}
                      </a>
                    </li>
                  </ul>
                @endif
              </div>
            @elseif (!$row->get('approved'))
              {{-- Unapproved --}}
              <div class="btn-group dropdown user-state confirmed unapproved">
                <span class="btn"
                      title="{{ Lang::txt('COM_MEMBERS_STATUS_UNAPPROVED_DESC') }}">
                  {{ Lang::txt('COM_MEMBERS_STATUS_UNAPPROVED') }}
                </span>
                @if ($canChange)
                  <span class="btn dropdown-toggle"></span>
                  <ul class="dropdown-menu">
                    <li>
                      <a class="grid-action grid-boolean icon-approve"
                         data-id="cb{{ $i }}"
                         data-task="approve"
                         href="#toggle">
                        {{ Lang::txt('COM_MEMBERS_ACTION_APPROVE') }}
                      </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a class="grid-action grid-boolean icon-ban"
                         data-id="cb{{ $i }}"
                         data-task="block"
                         href="#toggle">
                        {{ Lang::txt('COM_MEMBERS_ACTION_BLOCK') }}
                      </a>
                    </li>
                  </ul>
                @endif
              </div>
            @else
              {{-- Approved / Enabled --}}
              <div class="btn-group dropdown user-state confirmed approved enabled">
                <span class="btn"
                      title="{{ Lang::txt('COM_MEMBERS_STATUS_APPROVED_DESC') }}">
                  {{ Lang::txt('COM_MEMBERS_STATUS_APPROVED') }}
                </span>
                @if ($canChange)
                  <span class="btn dropdown-toggle"></span>
                  <ul class="dropdown-menu">
                    <li>
                      <a class="grid-action grid-boolean icon-unapprove"
                         data-id="cb{{ $i }}"
                         data-task="disapprove"
                         href="#toggle">
                        {{ Lang::txt('COM_MEMBERS_ACTION_UNAPPROVE') }}
                      </a>
                    </li>
                    <li class="divider"></li>
                    <li>
                      <a class="grid-action grid-boolean icon-ban"
                         data-id="cb{{ $i }}"
                         data-task="block"
                         href="#toggle">
                        {{ Lang::txt('COM_MEMBERS_ACTION_BLOCK') }}
                      </a>
                    </li>
                  </ul>
                @endif
              </div>
            @endif
          </td>
          <td class="priority-3">
            @php
              $regDate = $row->get('registerDate');
            @endphp
            <time datetime="{{ Date::of($regDate)->format('Y-m-d\TH:i:s') }}">
              {{ Date::of($regDate)->toLocal('Y-m-d') }}
            </time>
          </td>
          <td class="priority-6">
            @if (!$row->get('lastvisitDate')
                || $row->get('lastvisitDate') == '0000-00-00 00:00:00')
              <span class="text-muted-foreground">{{ Lang::txt('COM_MEMBERS_NEVER') }}</span>
            @else
              @php
                $lastVisit = $row->get('lastvisitDate');
              @endphp
              <time datetime="{{ Date::of($lastVisit)->format('Y-m-d\TH:i:s') }}">
                {{ Date::of($lastVisit)->toLocal('Y-m-d') }}
              </time>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="9">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

  @if (User::authorise('core.create', $option)
      && User::authorise('core.edit', $option)
      && User::authorise('core.edit.state', $option))
    @include('com_members::admin.views.members.tmpl.display_batch')
  @endif

</x-admin-form>
