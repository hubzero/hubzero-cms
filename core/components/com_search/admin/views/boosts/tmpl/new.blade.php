{{--
  Solr Search — New boost

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SEARCH_HEADING_BOOST_NEW') }}"
    icon="search"
    option="{{ $option }}"
>
  <x-slot:buttons>
    <button type="button" class="btn btn-sm btn-success" data-task="create">
      {{ Lang::txt('JAPPLY') }}
    </button>
    <a href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=list', false) !!}"
       class="btn btn-sm">
      {{ Lang::txt('JCANCEL') }}
    </a>
  </x-slot:buttons>
</x-admin-toolbar>

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm"
      id="item-form">

  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        <label for="boost-type" class="label">
          {{ Lang::txt('COM_SEARCH_FIELDS_BOOST_TYPE') }}
        </label>
        <select name="boost[document_type]"
                id="boost-type"
                class="select select-bordered w-full">
          @foreach ($typeOptions as $typeOption)
            <option value="{{ $typeOption }}"
                    @selected($boost->getFormattedFieldValue() == $typeOption)>
              {{ $typeOption }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="admin-field">
        <label for="boost-strength" class="label">
          {{ Lang::txt('COM_SEARCH_FIELDS_BOOST_STRENGTH') }}
        </label>
        <input type="number"
               name="boost[strength]"
               id="boost-strength"
               class="input input-bordered w-full"
               step="1"
               value="{{ $boost->getStrength() }}" />
      </div>

  </x-admin-fieldset>

  <input type="hidden" name="option" value="com_search" />
  <input type="hidden" name="controller" value="boosts" />
  <input type="hidden" name="task" value="new" />
</form>
