{{--
  Solr Search — Indexed document listing

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('solr');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SEARCH_SOLR_DOCUMENTS') }}"
    icon="search"
    option="{{ $option }}"
    :preferences="true"
>
  <x-slot:buttons>
    <a href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=display', false) !!}"
       class="btn btn-sm">
      {{ Lang::txt('JTOOLBAR_BACK') }}
    </a>
  </x-slot:buttons>
</x-admin-toolbar>

@include('com_search::admin.views.shared.tmpl._submenu')

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="filter"
               id="filter_search"
               class="input input-bordered input-sm w-full"
               value="{{ $filter }}"
               placeholder="{{ Lang::txt('COM_SEARCH_FILTER_SEARCH_PLACEHOLDER') }}" />
      @endslot
    </x-admin-filters>
  @endslot

  @include('com_search::admin.views.searchable.tmpl._recordtable')

  <input type="hidden" name="task" value="{{ $task }}" />
  <input type="hidden" name="facet" value="{{ $facet }}" />
</x-admin-form>
