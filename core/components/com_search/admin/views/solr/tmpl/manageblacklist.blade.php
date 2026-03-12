{{--
  Solr Search — Blacklist management

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css('solr');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SEARCH_SOLR_BLACKLIST') }}"
    icon="search"
    option="{{ $option }}"
    :preferences="true"
/>

@include('com_search::admin.views.shared.tmpl._submenu')

@if ($blacklist->count() > 0)
  <x-admin-form
      option="{{ $option }}"
      controller="{{ $controller }}"
  >
    <table class="admin-table">
      <thead>
        <tr>
          <th scope="col">{{ Lang::txt('COM_SEARCH_COL_ID') }}</th>
          <th scope="col">{{ Lang::txt('COM_SEARCH_COL_DOC_ID') }}</th>
          <th scope="col">{{ Lang::txt('COM_SEARCH_COL_CREATED') }}</th>
          <th scope="col">{{ Lang::txt('COM_SEARCH_COL_CREATED_BY') }}</th>
          <th scope="col"></th>
        </tr>
      </thead>
      <tbody>
        @foreach ($blacklist as $entry)
          @php
            $memberName = User::getInstance($entry->created_by)->name ?? Lang::txt('UNKNOWN');
            $removeUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=removeBlacklistEntry'
                . '&entryID=' . $entry->get('id'), false
            );
          @endphp
          <tr>
            <td>{{ $entry->id }}</td>
            <td>{{ $entry->doc_id }}</td>
            <td>{{ $entry->created }}</td>
            <td>{{ $memberName }}</td>
            <td>
              <a href="{{ $removeUrl }}" class="btn btn-sm btn-error btn-outline">
                Remove
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </x-admin-form>
@else
  <div role="alert" class="alert alert-warning">
    <span>{{ Lang::txt('COM_SEARCH_NO_BLACKLIST_ENTRIES') }}</span>
  </div>
@endif
