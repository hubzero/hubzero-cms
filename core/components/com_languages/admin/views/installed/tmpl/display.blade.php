{{--
  com_languages — Installed language packs list

  Variables: $rows (paginated), $filters (client_id), $option, $controller

  Radio button selection — no bulk checkboxes. "Set Default" acts on
  the selected radio value.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $canDo    = \Components\Languages\Helpers\Utilities::getActions();
  $clientId = $filters['client_id'] ?? 0;
  $client   = $clientId ? Lang::txt('JADMINISTRATOR') : Lang::txt('JSITE');
  $canChange = User::authorise('core.edit.state', $option);
  $pagination = $rows->pagination;

  $formAction = Route::url(
      'index.php?option=' . $option . '&controller=installed&client=' . $clientId, false
  );
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_LANGUAGES_VIEW_INSTALLED_TITLE') }}"
    icon="langmanager"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<form action="{{ $formAction }}"
      method="post"
      id="adminForm"
      name="adminForm">

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="priority-6">{{ Lang::txt('COM_LANGUAGES_HEADING_NUM') }}</th>
          <th>{{-- radio --}}</th>
          <th class="title">{{ Lang::txt('COM_LANGUAGES_HEADING_LANGUAGE') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_LANGUAGES_FIELD_LANG_TAG_LABEL') }}</th>
          <th class="priority-3">{{ Lang::txt('JCLIENT') }}</th>
          <th>{{ Lang::txt('COM_LANGUAGES_HEADING_DEFAULT') }}</th>
          <th class="priority-5">{{ Lang::txt('HVERSION') }}</th>
          <th class="priority-6">{{ Lang::txt('JDATE') }}</th>
          <th class="priority-5">{{ Lang::txt('JAUTHOR') }}</th>
          <th class="priority-6">{{ Lang::txt('COM_LANGUAGES_HEADING_AUTHOR_EMAIL') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="10">
            {!! $pagination !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($rows as $i => $row)
          <tr class="{{ isset($row->missing) ? 'opacity-50' : '' }}">
            <td class="priority-6">{{ $pagination->getRowOffset($i) }}</td>
            <td>
              @if (empty($row->missing))
                <input type="radio"
                       id="cb{{ $i }}"
                       name="cid"
                       value="{{ $row->language }}"
                       class="radio radio-sm"
                       title="{{ $i + 1 }}" />
              @endif
            </td>
            <td>{{ $row->name }}</td>
            <td class="priority-4">{{ $row->language }}</td>
            <td class="priority-3">{{ $client }}</td>
            <td>
              {!! Html::grid('isdefault', $row->published, $i, '', !$row->published && $canChange) !!}
            </td>
            <td class="priority-5">{{ $row->version }}</td>
            <td class="priority-6">{{ $row->creationDate }}</td>
            <td class="priority-5">{{ $row->author }}</td>
            <td class="priority-6">{{ $row->authorEmail }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="10">
              <x-empty-state
                  icon="search"
                  :message="Lang::txt('COM_LANGUAGES_NO_LANGUAGES')" />
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option"     value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task"       value="" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}

</form>
