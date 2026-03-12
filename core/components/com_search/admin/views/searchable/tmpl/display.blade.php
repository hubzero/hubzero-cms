{{--
  Solr Search — Searchable components list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $__view->css();
  $__view->js('searchable');

  $sort_dir = $filters['sort_Dir'] ?? 'asc';
  $sort     = $filters['sort'] ?? 'id';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SEARCH_SOLR_COMPONENTS') }}"
    icon="search"
    option="{{ $option }}"
    :preferences="true"
>
  <x-slot:buttons>
    <a href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=add', false) !!}"
       class="btn btn-sm btn-success">
      {{ Lang::txt('JTOOLBAR_NEW') }}
    </a>
    <button type="button"
            class="btn btn-sm btn-error btn-outline"
            data-task="deleteIndex"
            data-confirm="{{ Lang::txt('COM_SEARCH_DELETE_COMPONENT_RESULTS') }}">
      {{ Lang::txt('COM_SEARCH_DELETE_COMPONENT_RESULTS') }}
    </button>
    <button type="button"
            class="btn btn-sm btn-error"
            data-task="trashIndex"
            data-confirm="{{ Lang::txt('COM_SEARCH_DELETE_COMPONENT_ENTRY') }}">
      {{ Lang::txt('COM_SEARCH_DELETE_COMPONENT_ENTRY') }}
    </button>
    <button type="button"
            class="btn btn-sm"
            data-task="discover">
      {{ Lang::txt('COM_SEARCH_SOLR_DISCOVER') }}
    </button>
  </x-slot:buttons>
</x-admin-toolbar>

@include('com_search::admin.views.shared.tmpl._submenu')

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col">
          <input type="checkbox" data-check-all
                 aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
        </th>
        <th scope="col" class="priority-5">
          {!! Html::grid('sort', 'ID', 'id', $sort_dir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'Title', 'title', $sort_dir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'Active?', 'state', $sort_dir, $sort) !!}
        </th>
        <th scope="col">Records</th>
        <th scope="col"></th>
      </tr>
    </thead>
    <tbody>
      @foreach ($components as $i => $component)
        @php
          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $component->get('id'),
              false, false
          );
          $isIndexed = $component->get('state') == $component::STATE_INDEXED;
          $componentName  = $component->getQueryName();
          $componentQuery = $component->getSearchQuery('hubtype');
          $componentCount = !empty($componentCounts[$componentName])
              ? $componentCounts[$componentName]
              : 0;
          $componentLink = Route::url(
              'index.php?option=com_search&controller=' . $controller
              . '&task=documentListing&facet=' . $componentQuery,
              false, false
          );
        @endphp
        <tr>
          <td>
            <input type="checkbox"
                   name="id[]"
                   id="cb{{ $i }}"
                   value="{{ $component->get('id') }}"
                   aria-label="{{ $component->title }}"
                   data-check-item />
          </td>
          <td class="priority-5">{{ $component->id }}</td>
          <td>
            <a href="{{ $editUrl }}">{{ $component->title }}</a>
          </td>
          <td>
            @if ($isIndexed)
              @php
                $deactivateUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&task=deleteIndex&id=' . $component->get('id')
                    . '&' . Session::getFormToken() . '=1', false
                );
              @endphp
              <a href="{{ $deactivateUrl }}" class="badge badge-success">Indexed</a>
            @else
              @php
                $activateUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&task=activateIndex&id=' . $component->get('id')
                    . '&' . Session::getFormToken() . '=1', false
                );
              @endphp
              <a href="{{ $activateUrl }}" class="badge badge-ghost">Not Indexed</a>
            @endif
          </td>
          <td>
            @if ($componentCount > 0)
              <a href="{{ $componentLink }}">{{ $componentCount }}</a>
            @else
              {{ $componentCount }}
            @endif
          </td>
          <td>
            @if ($isIndexed)
              @php
                $rebuildUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&task=activateIndex&id=' . $component->get('id')
                    . '&' . Session::getFormToken() . '=1', false
                );
              @endphp
              <a href="{{ $rebuildUrl }}" class="btn btn-xs btn-outline">
                Rebuild Index
              </a>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sort_dir }}" />
</x-admin-form>
