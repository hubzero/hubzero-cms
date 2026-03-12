{{--
  Mailing List — Manage emails

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo   = \Components\Newsletter\Helpers\Permissions::getActions('mailinglist');
  $sort    = $filters['sort'] ?? 'email';
  $sortDir = $filters['sort_Dir'] ?? 'ASC';
  $status  = $filters['status'] ?? 'all';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILINGLISTS') }}: {{ $list->name }}"
    icon="list"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      <label for="filter-status" class="text-sm">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS') }}:</label>
      <select name="status" id="filter-status"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="all" @selected($status == 'all')>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_ALL') }}</option>
        <option value="active" @selected($status == 'active')>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_ACTIVE') }}</option>
        <option value="removed" @selected($status == 'removed')>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_REMOVED') }}</option>
        <option value="unsubscribed" @selected($status == 'unsubscribed')>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_UNSUBSCRIBED') }}</option>
        <option value="inactive" @selected($status == 'inactive')>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS_INACTIVE') }}</option>
      </select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>
            {!! Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_EMAIL', 'email', $sortDir, $sort) !!}
          </th>
          <th>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_STATUS') }}</th>
          <th>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_CONFIRMED') }}</th>
          <th>
            {!! Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_DATE_ADDED', 'date_added', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_NEWSLETTER_MAILINGLIST_MANAGE_DATE_CONFIRMED', 'date_confirmed', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $list_emails->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($list_emails as $i => $le)
          @php
            $resendUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=sendconfirmation'
                . '&id=' . $le->id
                . '&mid=' . $list->id,
                false, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="email_id[]"
                     id="cb{{ $i }}"
                     value="{{ $le->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $le->email }}"
                     data-check-item />
            </td>
            <td>
              <a href="mailto:{{ $le->email }}" class="link link-hover text-primary">
                {{ $le->email }}
              </a>
              @if($le->unsubscribe && $le->unsubscribe->reason)
                <p class="text-xs text-muted-foreground mt-1">
                  <strong>{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_MANAGE_UNSUBSCRIBE_REASON') }}</strong>
                  {{ $le->unsubscribe->reason }}
                </p>
              @endif
            </td>
            <td>{{ ucfirst($le->status) }}</td>
            <td>
              @if($le->confirmed)
                {{ Lang::txt('JYES') }}
              @else
                {{ Lang::txt('JNO') }}
                (<a href="{{ $resendUrl }}" class="link link-primary text-sm">{{ Lang::txt('Send Confirmation') }}</a>)
              @endif
            </td>
            <td>
              <time datetime="{{ $le->date_added }}">
                {{ Date::of($le->date_added)->format('M d, Y @ g:ia') }}
              </time>
            </td>
            <td>
              @if($le->date_confirmed && $le->date_confirmed != '0000-00-00 00:00:00')
                <time datetime="{{ $le->date_confirmed }}">
                  {{ Date::of($le->date_confirmed)->format('M d, Y @ g:ia') }}
                </time>
              @else
                {{ Lang::txt('NA') }}
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted-foreground">
              @php
                $addEmailUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&task=addemail&id=' . $list->id,
                    false, false
                );
              @endphp
              {{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_NO_EMAILS_PLAIN') }}
              <a href="{{ $addEmailUrl }}" class="link link-primary">{{ Lang::txt('COM_NEWSLETTER_MAILINGLIST_ADD_EMAILS_LINK') }}</a>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="task" value="manage" />
  <input type="hidden" name="id[]" value="{{ $list->id }}" />
  <input type="hidden" name="mid" value="{{ $list->id }}" />
</x-admin-form>
