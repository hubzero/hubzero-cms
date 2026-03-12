{{--
  Jobs — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo   = \Components\Jobs\Helpers\Permissions::getActions('job');
  $sort    = $filters['sortby'] ?? 'added';
  $sortDir = $filters['sortdir'] ?? 'DESC';

  $database = App::get('db');
  $jt = new \Components\Jobs\Tables\Type($database);
  $jc = new \Components\Jobs\Tables\Category($database);

  $now = Date::toSql();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_JOBS') }}"
    icon="job"
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
      @slot('search')
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_JOBS_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_JOBS_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot
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
          <th class="priority-4">
            {{ Lang::txt('COM_JOBS_COL_CODE') }}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_JOBS_COL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_JOBS_COL_COMPANY', 'location', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_JOBS_COL_STATUS', 'status', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_JOBS_COL_OWNER', 'adminposting', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_JOBS_COL_ADDED', 'added', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {{ Lang::txt('COM_JOBS_EXPIRATION') }}
          </th>
          <th class="priority-2">
            {{ Lang::txt('COM_JOBS_COL_APPLICATIONS') }}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="9">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $admin = ($row->employerid == 1);

            $curtype = $row->type > 0 ? $jt->getType($row->type) : '';
            $curcat  = $row->cid > 0  ? $jc->getCat($row->cid)   : '';

            $addedDate = Date::of($row->added)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

            $tipInfo  = Lang::txt('COM_JOBS_FIELD_CREATED') . ': ' . $addedDate . "\n";
            $tipInfo .= Lang::txt('COM_JOBS_FIELD_CREATOR') . ': ' . $row->addedBy;
            $tipInfo .= $admin ? ' ' . Lang::txt('COM_JOBS_ADMIN') : '';
            $tipInfo .= "\n";
            $tipInfo .= Lang::txt('COM_JOBS_FIELD_CATEGORY') . ': ' . $curcat . "\n";
            $tipInfo .= Lang::txt('COM_JOBS_FIELD_TYPE') . ': ' . $curtype;

            switch ($row->status) {
                case 0:
                    $stateText  = Lang::txt('COM_JOBS_STATUS_PENDING');
                    $stateClass = 'badge-warning';
                    break;
                case 1:
                    $expired = $row->inactive && $row->inactive < $now;
                    $stateText  = $expired
                        ? Lang::txt('COM_JOBS_STATUS_EXPIRED')
                        : Lang::txt('COM_JOBS_STATUS_ACTIVE');
                    $stateClass = $expired ? 'badge-error' : 'badge-success';
                    break;
                case 2:
                    $stateText  = Lang::txt('COM_JOBS_STATUS_DELETED');
                    $stateClass = 'badge-ghost';
                    break;
                case 3:
                    $stateText  = Lang::txt('COM_JOBS_STATUS_INACTIVE');
                    $stateClass = 'badge-ghost';
                    break;
                case 4:
                    $stateText  = Lang::txt('COM_JOBS_STATUS_DRAFT');
                    $stateClass = 'badge-info';
                    break;
                default:
                    $stateText  = '-';
                    $stateClass = '';
                    break;
            }

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->id,
                false, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                     data-check-item />
            </td>
            <td class="priority-4">
              {{ $row->code }}
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium"
                   title="{{ $tipInfo }}">
                  {{ $row->title }}
                </a>
              @else
                <span title="{{ $tipInfo }}">
                  {{ $row->title }}
                </span>
              @endif
            </td>
            <td class="priority-3">
              <span>{{ $row->companyName }}</span><br />
              <span class="text-xs text-muted-foreground">{{ $row->companyLocation }}</span>
            </td>
            <td>
              <span class="badge badge-sm whitespace-nowrap {{ $stateClass }}">{{ $stateText }}</span>
            </td>
            <td class="priority-3">
              @if($admin)
                <span class="badge badge-sm badge-primary">{{ Lang::txt('COM_JOBS_ADMIN') }}</span>
              @endif
            </td>
            <td class="priority-4">
              <time datetime="{{ $row->added }}">{{ $addedDate }}</time>
            </td>
            <td class="priority-4">
              @if($row->expiredate && $row->expiredate != '0000-00-00 00:00:00')
                <time datetime="{{ $row->expiredate }}">
                  {{ Date::of($row->expiredate)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
                </time>
              @else
                {{ Lang::txt('COM_JOBS_NEVER_EXPIRES') }}
              @endif
            </td>
            <td class="priority-2">
              {{ $row->applications }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
