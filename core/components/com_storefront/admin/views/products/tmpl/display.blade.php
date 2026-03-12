{{--
  Storefront Products — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');
  $sort    = $filters['sort'] ?? '';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Products', 'storefront');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
  }
  Toolbar::spacer();
  Toolbar::help('products');

  $db = App::get('db');
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
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] }}"
               placeholder="{{ Lang::txt('JSEARCH_FILTER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_STOREFRONT_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search,filter-type">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter-type" class="text-sm">{{ Lang::txt('COM_STOREFRONT_TYPE') }}:</label>
      <select name="type" id="filter-type"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected($filters['type'] == -1)>
          {{ Lang::txt('COM_STOREFRONT_FILTER_TYPE') }}
        </option>
        @foreach ($types as $type)
          <option value="{{ $type->ptId }}" @selected($filters['type'] == $type->ptId)>
            {{ $type->ptName }}
          </option>
        @endforeach
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
          <th>{!! Html::grid('sort', 'COM_STOREFRONT_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'Alias', 'pAlias', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_STOREFRONT_PRODUCT_TYPE', 'ptName', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('SKUs (published)') }}</th>
          <th>{!! Html::grid('sort', 'COM_STOREFRONT_STATE', 'state', $sortDir, $sort) !!}</th>
          @if ($config->get('productAccess'))
            <th>{{ Lang::txt('COM_STOREFRONT_ACCESS') }}</th>
          @else
            <th>{!! Html::grid('sort', 'COM_STOREFRONT_ACCESS', 'access', $sortDir, $sort) !!}</th>
          @endif
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="7">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach ($rows as $i => $row)
          @php
            switch ($row->pActive) {
                case 1:
                    $stateCls  = 'badge-success';
                    $stateTask = 'unpublish';
                    $stateText = Lang::txt('COM_STOREFRONT_PUBLISHED');
                    break;
                case 2:
                    $stateCls  = 'badge-error';
                    $stateTask = 'publish';
                    $stateText = Lang::txt('COM_STOREFRONT_TRASHED');
                    break;
                case 0:
                default:
                    $stateCls  = 'badge-ghost';
                    $stateTask = 'publish';
                    $stateText = Lang::txt('COM_STOREFRONT_UNPUBLISHED');
                    break;
            }

            $key = $row->pId;
            $skuInfo    = $skus->$key;
            $skuTotal   = $skuInfo->active + $skuInfo->inactive;
            $skuDisplay = $skuTotal;
            if ($skuTotal > 0) {
                $skuDisplay .= ' (' . $skuInfo->active . ')';
            }
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->pId }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->pName }}"
                     data-check-item />
            </td>
            <td>
              @if ($canDo->get('core.edit'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->pId, false) }}"
                   class="link link-hover text-primary font-medium"
                   title="{{ Lang::txt('COM_STOREFRONT_EDIT_PRODUCT') }}">
                  {{ $row->pName }}
                </a>
              @else
                {{ $row->pName }}
              @endif
            </td>
            <td>{{ $row->pAlias }}</td>
            <td>{{ $row->ptName }}</td>
            <td>
              <a href="{{ Route::url('index.php?option=' . $option . '&controller=skus&task=display&id=' . $row->pId, false) }}"
                 class="link link-hover text-primary"
                 title="{{ Lang::txt('View SKUs') }}">
                {{ $skuDisplay }}
              </a>
              @if ($canDo->get('core.edit.create'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=skus&task=add&pId=' . $row->pId, false) }}"
                   class="link link-hover text-primary ml-1"
                   title="{{ Lang::txt('Add SKU') }}">
                  [+]
                </a>
              @endif
            </td>
            <td class="column-status">
              @if ($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $stateTask . '&id=' . $row->pId, false) }}"
                   title="{{ Lang::txt('COM_STOREFRONT_SET_TASK', $stateTask) }}">
                  <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
              @endif
            </td>
            <td>
              @if ($config->get('productAccess'))
                @php
                  $sql = "SELECT `agId` FROM `#__storefront_product_access_groups`"
                      . " WHERE `exclude`=0 AND `pId`=" . $db->quote($row->pId);
                  $db->setQuery($sql);
                  $accessInclude = $db->loadColumn();
                  $agInclude = [];
                  foreach ($accessInclude as $access) {
                      if (array_key_exists($access, $ag)) {
                          $agInclude[] = $ag[$access];
                      }
                  }
                  $includeList = !empty($agInclude)
                      ? implode(', ', $agInclude)
                      : Lang::txt('(none)');

                  $sql = "SELECT `agId` FROM `#__storefront_product_access_groups`"
                      . " WHERE `exclude`=1 AND `pId`=" . $db->quote($row->pId);
                  $db->setQuery($sql);
                  $accessExclude = $db->loadColumn();
                  $agExclude = [];
                  foreach ($accessExclude as $access) {
                      if (array_key_exists($access, $ag)) {
                          $agExclude[] = $ag[$access];
                      }
                  }
                  $excludeList = !empty($agExclude)
                      ? implode(', ', $agExclude)
                      : Lang::txt('(none)');
                @endphp
                {{ Lang::txt('User is:') }} {{ $includeList }}
                <br />
                {{ Lang::txt('User is not:') }} {{ $excludeList }}
              @else
                {{ array_key_exists($row->access, $ag) ? $ag[$row->access] : $ag[0] }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
