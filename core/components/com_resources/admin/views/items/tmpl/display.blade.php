{{--
  Resources — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Resources\Helpers\Permissions::getActions('resource');
  $sort    = $filters['sort'] ?? 'title';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $__view->css();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}"
    icon="resources"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <x-admin-filters>
      @slot('search')
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-60"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_RESOURCES_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('COM_RESOURCES_GO') }}
        </button>
        <button type="button" class="btn btn-sm btn-ghost" data-clear-search>
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

        <select name="status" id="filter-status" class="select select-bordered select-sm" data-submit-on-change
                aria-label="{{ Lang::txt('COM_RESOURCES_FILTER_STATUS_ALL') }}">
          <option value="all" @selected(($filters['status'] ?? '') == 'all')>
            {{ Lang::txt('COM_RESOURCES_FILTER_STATUS_ALL') }}
          </option>
          <option value="2" @selected(($filters['status'] ?? '') == 2)>{{ Lang::txt('COM_RESOURCES_DRAFT_EXTERNAL') }}</option>
          <option value="5" @selected(($filters['status'] ?? '') == 5)>{{ Lang::txt('COM_RESOURCES_DRAFT_INTERNAL') }}</option>
          <option value="3" @selected(($filters['status'] ?? '') == 3)>{{ Lang::txt('COM_RESOURCES_PENDING') }}</option>
          <option value="0" @selected(($filters['status'] ?? '') === '0' || (($filters['status'] ?? '') == 0 && ($filters['status'] ?? '') !== 'all'))>{{ Lang::txt('JUNPUBLISHED') }}</option>
          <option value="1" @selected(($filters['status'] ?? '') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
          <option value="4" @selected(($filters['status'] ?? '') == 4)>{{ Lang::txt('JTRASHED') }}</option>
        </select>

        <select name="license" id="filter-license" class="select select-bordered select-sm" data-submit-on-change
                aria-label="{{ Lang::txt('COM_RESOURCES_FILTER_LICENSE_ALL') }}">
          <option value="all" @selected(($filters['license'] ?? '') == 'all')>
            {{ Lang::txt('COM_RESOURCES_FILTER_LICENSE_ALL') }}
          </option>
          @foreach($licenses as $license)
            <option value="{{ $license->get('name') }}" @selected(($filters['license'] ?? '') == $license->get('name'))>
              {{ $license->get('title') }}
            </option>
          @endforeach
        </select>

        <select name="type" id="filter-type" class="select select-bordered select-sm" data-submit-on-change
                aria-label="{{ Lang::txt('COM_RESOURCES_FILTER_TYPE_ALL') }}">
          <option value="">{{ Lang::txt('COM_RESOURCES_FILTER_TYPE_ALL') }}</option>
          @foreach($types as $type)
            <option value="{{ $type->id }}" @selected(($filters['type'] ?? '') == $type->id)>
              {{ $type->type }}
            </option>
          @endforeach
        </select>
  </x-admin-filters>

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
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_RESOURCES_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_RESOURCES_COL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_RESOURCES_COL_STATUS', 'published', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_RESOURCES_COL_ACCESS', 'access', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_RESOURCES_COL_MODIFIED', 'modified', $sortDir, $sort) !!}
          </th>
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_RESOURCES_COL_LICENSE', 'license', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_RESOURCES_COL_TYPE', 'type', $sortDir, $sort) !!}
          </th>
          <th>
            {{ Lang::txt('COM_RESOURCES_COL_CHILDREN') }}
          </th>
          <th>
            {{ Lang::txt('COM_RESOURCES_COL_TAGS') }}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="10">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $rowLicense = $row->get('license', $row->params->get('license'));
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

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->id, false
            );
            $stateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $statusTask . '&id=' . $row->id
                . '&' . Session::getFormToken() . '=1', false
            );
            $accessUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $acc[1] . '&id=' . $row->id
                . '&' . Session::getFormToken() . '=1', false
            );

            $tags      = count($row->tags());
            $children  = $row->children()->total();

            $isCheckedOut = $row->checked_out
                || ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00');
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
            <td class="priority-5">
              {{ $row->id }}
            </td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                {{ $row->title }}
              </a>
            </td>
            <td class="priority-3">
              @if($statusTask)
                <a href="{{ $stateUrl }}" class="badge badge-sm whitespace-nowrap {{ $statusBadge }}">
                  {{ $statusLabel }}
                </a>
              @else
                <span class="badge badge-sm whitespace-nowrap {{ $statusBadge }}">{{ $statusLabel }}</span>
              @endif
            </td>
            <td class="priority-3">
              <a href="{{ $accessUrl }}" class="badge badge-sm whitespace-nowrap {{ $acc[0] }}">
                {{ Lang::txt($acc[2]) }}
              </a>
            </td>
            <td class="priority-4">
              @if(!$row->modified || $row->modified == '0000-00-00 00:00:00')
                <span class="text-muted-foreground">{{ Lang::txt('COM_RESOURCES_NOT_MODIFIED') }}</span>
              @else
                <time datetime="{{ $row->modified }}" class="text-sm">
                  {{ Date::of($row->modified)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
                </time>
              @endif
            </td>
            <td class="priority-5">
              {{ $rowLicense ?? '' }}
            </td>
            <td class="priority-2">
              {{ $row->type->type ?? '' }}
            </td>
            <td>
              @if($children > 0)
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=children&pid=' . $row->id, false) }}"
                   class="link link-primary text-sm">
                  {{ $children }}
                </a>
              @else
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=addchild&pid=' . $row->id, false) }}"
                   class="btn btn-xs btn-ghost">
                  {{ Lang::txt('COM_RESOURCES_ADD') }}
                </a>
              @endif
            </td>
            <td>
              @php
                $tagsUrl = Route::url('index.php?option=' . $option . '&controller=tags&id=' . $row->id, false);
              @endphp
              @if($tags > 0)
                <a href="{{ $tagsUrl }}" class="link link-primary text-sm">
                  {{ $tags }}
                </a>
              @else
                <a href="{{ $tagsUrl }}" class="btn btn-xs btn-ghost">
                  {{ Lang::txt('COM_RESOURCES_ADD') }}
                </a>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
