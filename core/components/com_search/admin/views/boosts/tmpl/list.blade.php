{{--
  Solr Search — Boosts list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('boostsList');

  $tagSearchEnabled = !!Component::params('com_search')->get('solr_tagsearch', 0);
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SEARCH_HEADING_BOOSTS') }}"
    icon="search"
    option="{{ $option }}"
    :preferences="true"
>
  <x-slot:buttons>
    <a href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=new', false) !!}"
       class="btn btn-sm btn-success">
      {{ Lang::txt('JTOOLBAR_NEW') }}
    </a>
  </x-slot:buttons>
</x-admin-toolbar>

@include('com_search::admin.views.shared.tmpl._submenu')

@if ($tagSearchEnabled)
  <div role="alert" class="alert alert-info mb-4">
    <span>{{ Lang::txt('COM_SEARCH_NOTICE_TAG_SEARCH') }}</span>
  </div>
@endif

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col" class="priority-5 w-[5%]">
          {!! Html::grid('sort', Lang::txt('COM_SEARCH_COL_ID'), 'id', $sortDirection, $sortField) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', Lang::txt('COM_SEARCH_COL_TYPE'), 'field_value', $sortDirection, $sortField) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', Lang::txt('COM_SEARCH_COL_STRENGTH'), 'strength', $sortDirection, $sortField) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($boosts as $boost)
        @php
          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=boosts&task=edit&id=' . $boost->getId(), false
          );
        @endphp
        <tr>
          <td class="priority-5">
            <a href="{{ $editUrl }}">{{ $boost->getId() }}</a>
          </td>
          <td>
            <a href="{{ $editUrl }}">{{ $boost->getFormattedFieldValue() }}</a>
          </td>
          <td>
            <a href="{{ $editUrl }}">{{ $boost->getStrength() }}</a>
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3">
          {!! $boosts->pagination !!}
        </td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="task" value="list" />
  <input type="hidden" name="filter_order" value="{{ $sortField }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDirection }}" />
</x-admin-form>
