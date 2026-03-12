{{--
  Resource Children — Admin child resource list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo  = \Components\Resources\Helpers\Permissions::getActions('resource');
  $parentId = $filters['parent_id'] ?? 0;
  $__view->css();

  $orderings = $rows->fieldsByKey('associative_ordering');
  $colspan   = ($parentId > 0) ? 9 : 7;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_CHILDREN') }}"
    icon="resources"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        @if($parentId > 0)
          <tr>
            <th colspan="{{ $colspan }}">
              @php
                $parentUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&task=edit&id=' . $parentId, false
                );
              @endphp
              <a href="{{ $parentUrl }}" class="link link-primary font-medium">
                {{ $parent->title }}
              </a>
            </th>
          </tr>
        @endif
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{{ Lang::txt('COM_RESOURCES_COL_ID') }}</th>
          <th>{{ Lang::txt('COM_RESOURCES_COL_TITLE') }}</th>
          <th>{{ Lang::txt('COM_RESOURCES_COL_STATUS') }}</th>
          <th>{{ Lang::txt('COM_RESOURCES_COL_ACCESS') }}</th>
          <th>{{ Lang::txt('COM_RESOURCES_COL_TYPE') }}</th>
          @if($parentId > 0)
            <th colspan="3">{{ Lang::txt('COM_RESOURCES_COL_ORDER') }}</th>
          @endif
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="{{ $colspan }}">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $now = Date::toSql();

            // Status
            switch ($row->published) {
                case 0:
                    $statusLabel = Lang::txt('JUNPUBLISHED');
                    $statusBadge = 'badge-ghost';
                    $statusTask  = 'publish';
                    break;
                case 1:
                    if ($now <= $row->publish_up) {
                        $statusLabel = Lang::txt('COM_RESOURCES_PENDING');
                        $statusBadge = 'badge-warning';
                    } elseif (!$row->publish_down || $row->publish_down == '0000-00-00 00:00:00' || $now <= $row->publish_down) {
                        $statusLabel = Lang::txt('JPUBLISHED');
                        $statusBadge = 'badge-success';
                    } else {
                        $statusLabel = Lang::txt('COM_RESOURCES_EXPIRED');
                        $statusBadge = 'badge-warning';
                    }
                    $statusTask = 'unpublish';
                    break;
                case 2:
                    $statusLabel = Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL');
                    $statusBadge = 'badge-info';
                    $statusTask  = 'publish';
                    break;
                case 3:
                    $statusLabel = Lang::txt('COM_RESOURCES_NEW');
                    $statusBadge = 'badge-warning';
                    $statusTask  = 'publish';
                    break;
                case 4:
                    $statusLabel = Lang::txt('JTRASHED');
                    $statusBadge = 'badge-error';
                    $statusTask  = 'publish';
                    break;
                case 5:
                    $statusLabel = Lang::txt('COM_RESOURCES_DRAFT_INTERNAL');
                    $statusBadge = 'badge-info';
                    $statusTask  = 'publish';
                    break;
                default:
                    $statusLabel = '-';
                    $statusBadge = '';
                    $statusTask  = '';
                    break;
            }

            // Access
            $accessMap = [
                0 => ['badge-success', 'accessregistered', 'COM_RESOURCES_ACCESS_PUBLIC'],
                1 => ['badge-info', 'accessspecial', 'COM_RESOURCES_ACCESS_REGISTERED'],
                2 => ['badge-warning', 'accessprotected', 'COM_RESOURCES_ACCESS_SPECIAL'],
                3 => ['badge-accent', 'accessprivate', 'COM_RESOURCES_ACCESS_PROTECTED'],
                4 => ['badge-error', 'accesspublic', 'COM_RESOURCES_ACCESS_PRIVATE'],
            ];
            $acc = $accessMap[$row->access] ?? $accessMap[0];

            // Type display
            if ($row->get('logical_type') && $row->logicaltype) {
                $typec = $row->logicaltype->get('type') . ' (' . $row->type->get('type') . ')';
            } else {
                $typec = $row->type->get('type') ?? '';
            }

            $isCheckedOut = $row->checked_out
                || ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00');

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->id
                . '&pid=' . $parentId, false
            );
            $stateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $statusTask . '&id=' . $row->id
                . '&pid=' . $parentId
                . '&' . Session::getFormToken() . '=1', false
            );
            $accessUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $acc[1] . '&id=' . $row->id
                . '&pid=' . $parentId
                . '&' . Session::getFormToken() . '=1', false
            );
          @endphp
          <tr>
            <td class="column-check">
              @if($isCheckedOut)
                <span class="badge badge-sm badge-ghost">
                  {{ Lang::txt('JLIB_HTML_CHECKED_OUT') }}
                </span>
              @else
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->id }}"
                       class="checkbox checkbox-sm"
                       data-check-item
                       aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $row->title) }}" />
              @endif
            </td>
            <td>{{ $row->id }}</td>
            <td>
              @if($isCheckedOut && $row->checked_out != User::get('id') || !$canDo->get('core.edit'))
                {{ $row->title }}
              @else
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $row->title }}
                </a>
              @endif
              @if($row->standalone != 1 && $row->path != '')
                <br /><span class="text-xs text-muted-foreground">{{ $row->path }}</span>
              @endif
            </td>
            <td>
              @if($isCheckedOut || !$canDo->get('core.edit.state'))
                <span class="badge badge-sm whitespace-nowrap {{ $statusBadge }}">{{ $statusLabel }}</span>
              @else
                <a href="{{ $stateUrl }}" class="badge badge-sm whitespace-nowrap {{ $statusBadge }}">
                  {{ $statusLabel }}
                </a>
              @endif
            </td>
            <td>
              @if($isCheckedOut || !$canDo->get('core.edit.state'))
                <span class="badge badge-sm whitespace-nowrap {{ $acc[0] }}">{{ Lang::txt($acc[2]) }}</span>
              @else
                <a href="{{ $accessUrl }}" class="badge badge-sm whitespace-nowrap {{ $acc[0] }}">
                  {{ Lang::txt($acc[2]) }}
                </a>
              @endif
            </td>
            <td>{{ $typec }}</td>
            @if($parentId > 0)
              <td>
                @php
                  $canOrderUp = isset($orderings[$i - 1])
                      ? ($row->associative_ordering != $orderings[$i - 1])
                      : true;
                @endphp
                {!! $rows->pagination->orderUpIcon($i, $canOrderUp) !!}
              </td>
              <td>
                @php
                  $canOrderDown = isset($orderings[$i + 1])
                      ? ($row->associative_ordering != $orderings[$i + 1])
                      : true;
                @endphp
                {!! $rows->pagination->orderDownIcon($i, $rows->pagination->total, $canOrderDown) !!}
              </td>
              <td>{{ $row->associative_ordering }}</td>
            @endif
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="task" value="{{ $task }}" autocomplete="off" />
  <input type="hidden" name="viewtask" value="{{ $task }}" />
  <input type="hidden" name="pid" value="{{ $parentId }}" />
</x-admin-form>
