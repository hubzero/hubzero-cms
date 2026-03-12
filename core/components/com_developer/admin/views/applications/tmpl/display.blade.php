{{--
  Developer Applications — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Developer\Helpers\Permissions::getActions('application');

  $sort    = $filters['sort'] ?? 'name';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_DEVELOPER') . ': ' . Lang::txt('COM_DEVELOPER_APPLICATIONS'));
  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::custom('resetclientsecret', 'refresh', 'refresh', 'COM_DEVELOPER_RESET_CLIENT_SECRET');
      Toolbar::custom('removetokens', 'cancel', 'cancel', 'COM_DEVELOPER_REVOKE_TOKENS');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('', 'delete');
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
    data-confirmreset="{{ Lang::txt('COM_DEVELOPER_RESET_CLIENT_SECRET_CONFIRM') }}"
    data-confirmrevoke="{{ Lang::txt('COM_DEVELOPER_REVOKE_TOKENS_CONFIRM') }}"
>
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
          <th>{!! Html::grid('sort', 'COM_DEVELOPER_COL_ID', 'id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_DEVELOPER_COL_NAME', 'name', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('COM_DEVELOPER_COL_STATE') }}</th>
          <th>{!! Html::grid('sort', 'COM_DEVELOPER_COL_CREATED', 'created', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_DEVELOPER_COL_CREATED_BY', 'created_by', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('COM_DEVELOPER_COL_HUB_ACCOUNT') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="7">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'),
                false, false
            );

            // State
            if ($row->isPublished()) {
                $stateText = Lang::txt('JPUBLISHED');
                $stateCls  = 'badge-success';
                $stateTask = 'unpublish';
            } elseif ($row->isDeleted()) {
                $stateText = Lang::txt('JTRASHED');
                $stateCls  = 'badge-warning';
                $stateTask = 'publish';
            } else {
                $stateText = Lang::txt('JUNPUBLISHED');
                $stateCls  = 'badge-ghost';
                $stateTask = 'publish';
            }
          @endphp
          <tr>
            <td class="column-check">
              @if($canDo->get('core.edit'))
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->get('id') }}"
                       class="checkbox checkbox-sm"
                       aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                       data-check-item />
              @endif
            </td>
            <td>{{ $row->get('id') }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->get('name') }}
                </a>
              @else
                {{ $row->get('name') }}
              @endif
            </td>
            <td class="column-status">
              @if($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $stateTask . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1', false) }}">
                  <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
              @endif
            </td>
            <td>{{ Date::of($row->get('created'))->toLocal() }}</td>
            <td>{{ $row->creator->get('name', Lang::txt('(unknown)')) }}</td>
            <td>
              @if($row->isHubAccount())
                <span class="badge badge-sm badge-info">{{ Lang::txt('COM_DEVELOPER_COL_HUB_ACCOUNT') }}</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
