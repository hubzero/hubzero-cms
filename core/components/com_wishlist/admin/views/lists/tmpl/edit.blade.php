{{--
  Wishlist List — Admin edit

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\User;

  $canDo = \Components\Wishlist\Helpers\Permissions::getActions('list');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WISHLIST') }}: {{ Lang::txt('COM_WISHLIST_LIST') }}: {{ $row->get('id') ? Lang::txt('COM_WISHLIST_EDIT') : Lang::txt('COM_WISHLIST_NEW') }}"
    icon="wishlist"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Left column — Details --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_WISHLIST_DETAILS') }}">
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
      <div class="admin-field">
        <label class="label" for="field-category">
          {{ Lang::txt('COM_WISHLIST_CATEGORY') }}
        </label>
        <select name="fields[category]"
                id="field-category"
                class="select select-bordered w-full">
          <option value="">{{ Lang::txt('COM_WISHLIST_SELECT_CATEGORY') }}</option>
          <option value="general"
                  @selected($row->get('category') == 'general')>
            {{ Lang::txt('COM_WISHLIST_CATEGORY_GENERAL') }}
          </option>
          <option value="group"
                  @selected($row->get('category') == 'group')>
            {{ Lang::txt('COM_WISHLIST_CATEGORY_GROUP') }}
          </option>
          <option value="resource"
                  @selected($row->get('category') == 'resource')>
            {{ Lang::txt('COM_WISHLIST_CATEGORY_RESOURCE') }}
          </option>
        </select>
      </div>
      <div class="admin-field">
        <label class="label" for="field-referenceid">
          {{ Lang::txt('COM_WISHLIST_REFERENCEID') }}
        </label>
        <input type="text"
               name="fields[referenceid]"
               id="field-referenceid"
               class="input input-bordered w-full"
               maxlength="11"
               value="{{ $row->get('referenceid', '') }}" />
      </div>
    </div>

    <div class="admin-field">
      <label class="label" for="field-title">
        {{ Lang::txt('COM_WISHLIST_TITLE') }}
      </label>
      <input type="text"
             name="fields[title]"
             id="field-title"
             class="input input-bordered w-full"
             maxlength="150"
             value="{{ $row->get('title', '') }}" />
    </div>

    <div class="admin-field">
      <label class="label" for="field-description">
        {{ Lang::txt('COM_WISHLIST_DESCRIPTION') }}
      </label>
      <input type="text"
             name="fields[description]"
             id="field-description"
             class="input input-bordered w-full"
             maxlength="255"
             value="{{ $row->get('description', '') }}" />
    </div>
  </x-admin-fieldset>

  {{-- Right column — Sidebar --}}
  @slot('sidebar')
    @if ($row->get('id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_WISHLIST_FIELD_ID') }}</td>
              <td>{{ $row->get('id') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_WISHLIST_FIELD_CREATED') }}</td>
              <td>
                <time datetime="{{ $row->get('created') }}">
                  {{ Date::of($row->get('created'))->toLocal() }}
                </time>
              </td>
            </tr>
            <tr>
              @php $creator = User::getInstance($row->get('created_by')); @endphp
              <td>{{ Lang::txt('COM_WISHLIST_FIELD_CREATOR') }}</td>
              <td>{{ $creator->get('name', Lang::txt('unknown')) }}</td>
            </tr>
          </tbody>
        </table>
      </x-admin-fieldset>
    @endif

    <x-admin-fieldset legend="{{ Lang::txt('COM_WISHLIST_PARAMETERS') }}">
      <div class="admin-field">
        <label class="label" for="field-state">
          {{ Lang::txt('COM_WISHLIST_STATE') }}
        </label>
        <select name="fields[state]"
                id="field-state"
                class="select select-bordered w-full">
          <option value="0" @selected($row->get('state') == 0)>
            {{ Lang::txt('JUNPUBLISHED') }}
          </option>
          <option value="1" @selected($row->get('state') == 1)>
            {{ Lang::txt('JPUBLISHED') }}
          </option>
          <option value="2" @selected($row->get('state') == 2)>
            {{ Lang::txt('JTRASHED') }}
          </option>
        </select>
      </div>

      <div class="admin-field">
        <label class="label" for="field-public">
          {{ Lang::txt('COM_WISHLIST_PRIVACY') }}
        </label>
        <select name="fields[public]"
                id="field-public"
                class="select select-bordered w-full">
          <option value="0" @selected($row->get('public') == 0)>
            {{ Lang::txt('COM_WISHLIST_PRIVATE') }}
          </option>
          <option value="1" @selected($row->get('public') == 1)>
            {{ Lang::txt('COM_WISHLIST_PUBLIC') }}
          </option>
        </select>
      </div>
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="fields[created]" value="{{ $row->get('created') }}" />
  <input type="hidden" name="fields[created_by]" value="{{ $row->get('created_by') }}" />
</x-admin-edit>
