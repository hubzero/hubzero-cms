{{--
  Solr Search — Edit searchable component

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('edit');
  $__view->js('editsearchable');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SEARCH_EDIT_COMPONENT') }}"
    icon="search"
    option="{{ $option }}"
>
  <x-slot:buttons>
    <button type="button" class="btn btn-sm btn-success" data-task="save">
      {{ Lang::txt('COM_SEARCH_SAVE_FACET') }}
    </button>
    <a href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=display', false) !!}"
       class="btn btn-sm">
      {{ Lang::txt('JCANCEL') }}
    </a>
  </x-slot:buttons>
</x-admin-toolbar>

@include('com_search::admin.views.shared.tmpl._submenu')

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Details --}}
    <div class="lg:col-span-2">
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

          <div class="admin-field">
            <label for="field-title" class="label">
              {{ Lang::txt('COM_SEARCH_FIELD_TITLE') }}
            </label>
            <input type="text"
                   name="fields[title]"
                   id="field-title"
                   class="input input-bordered w-full"
                   maxlength="250"
                   value="{{ $searchComponent->title }}" />
          </div>

          <div class="admin-field">
            <label for="field-custom" class="label">
              {{ Lang::txt('COM_SEARCH_FIELD_CUSTOM') }}
            </label>
            <input type="text"
                   name="fields[custom]"
                   id="field-custom"
                   class="input input-bordered w-full"
                   maxlength="250"
                   value="{{ $searchComponent->custom }}" />
          </div>

      </x-admin-fieldset>

      {{-- Filters --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_SEARCH_COMPONENT_FILTERS_LIST') }}">
          <input type="hidden"
                 name="filter-schema"
                 value="{{ json_encode($filters) }}" />
          <div class="articles-container"></div>
      </x-admin-fieldset>
    </div>

    {{-- Sidebar --}}
    <div>
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
          <table class="admin-meta">
            <tbody>
              <tr>
                <td>{{ Lang::txt('ID') }}</td>
                <td>{{ $searchComponent->get('id', 0) }}</td>
              </tr>
            </tbody>
          </table>
      </x-admin-fieldset>

      {{-- Add Filter --}}
      <x-admin-fieldset legend="{{ Lang::txt('COM_SEARCH_COMPONENT_FILTER_ADD') }}">

          <div class="admin-field">
            <label for="searchable-filter-field" class="label">
              {{ Lang::txt('COM_SEARCH_COMPONENT_FILTER_FIELD') }}
            </label>
            @if (!empty($availableFields))
              <select name="add-filter"
                      id="searchable-filter-field"
                      class="select select-bordered w-full">
                @foreach ($availableFields as $field)
                  <option value="{{ $field }}">{{ $field }}</option>
                @endforeach
              </select>
            @else
              <input type="text"
                     name="add-filter"
                     id="searchable-filter-field"
                     class="input input-bordered w-full" />
            @endif
          </div>

          <div class="admin-field">
            <label for="filter-type" class="label">
              {{ Lang::txt('COM_SEARCH_COMPONENT_FILTER_TYPE') }}
            </label>
            <select id="filter-type"
                    name="filter-type"
                    class="select select-bordered w-full">
              <option value="list">List</option>
              <option value="daterange">Date Range</option>
              <option value="textfield">Text</option>
            </select>
          </div>

          <button type="button" id="add-filter" class="btn btn-sm btn-success">
            Add Filter
          </button>

      </x-admin-fieldset>
    </div>
  </div>

  {!! Html::input('token') !!}
  <input type="hidden" name="id" value="{{ $searchComponent->id }}" />
  <input type="hidden" name="option" value="com_search" />
  <input type="hidden" name="controller" value="searchable" />
  <input type="hidden" name="task" value="save" />
  <input type="hidden" name="action" value="edit" />
</form>
