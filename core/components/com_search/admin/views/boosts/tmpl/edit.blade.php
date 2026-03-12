{{--
  Solr Search — Edit boost

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css('boostsEdit');

  $created      = $boost->getCreated();
  $createdLocal = Date::of($created)->toLocal();
  $author       = $boost->getAuthor();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SEARCH_HEADING_BOOST_EDIT') }}"
    icon="search"
    option="{{ $option }}"
>
  <x-slot:buttons>
    <button type="button" class="btn btn-sm btn-success" data-task="update">
      {{ Lang::txt('JAPPLY') }}
    </button>
    <a href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=list', false) !!}"
       class="btn btn-sm">
      {{ Lang::txt('JCANCEL') }}
    </a>
    <button type="button"
            class="btn btn-sm btn-error btn-outline"
            data-task="destroy">
      {{ Lang::txt('COM_SEARCH_DELETE') }}
    </button>
  </x-slot:buttons>
</x-admin-toolbar>

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Details --}}
    <div class="lg:col-span-2">
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

          <div class="admin-field">
            <label for="boost-type" class="label">
              {{ Lang::txt('COM_SEARCH_FIELDS_BOOST_TYPE') }}
            </label>
            <input type="text"
                   id="boost-type"
                   class="input input-bordered w-full"
                   value="{{ $boost->getFormattedFieldValue() }}"
                   disabled />
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
    </div>

    {{-- Sidebar --}}
    <div>
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
          <table class="admin-meta">
            <tbody>
              <tr>
                <td>{{ Lang::txt('COM_SEARCH_COL_ID') }}</td>
                <td>{{ $boost->getId() }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_SEARCH_LABEL_CREATED_BY') }}</td>
                <td>
                  <a href="{{ $author->link() }}">
                    {{ $author->get('name') }}
                  </a>
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_SEARCH_LABEL_CREATED') }}</td>
                <td>{{ $createdLocal }}</td>
              </tr>
            </tbody>
          </table>
      </x-admin-fieldset>
    </div>
  </div>

  <input type="hidden" name="id" value="{{ $boost->getId() }}" />
  <input type="hidden" name="option" value="com_search" />
  <input type="hidden" name="controller" value="boosts" />
  <input type="hidden" name="task" value="edit" />
</form>
