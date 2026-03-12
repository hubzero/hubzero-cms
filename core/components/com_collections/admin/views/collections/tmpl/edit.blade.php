{{--
  Collection — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\User;

  $canDo = \Components\Collections\Helpers\Permissions::getActions('collection');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_COLLECTIONS') }}: {{ $text }}"
    icon="collections"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Main content column --}}
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-object_type" class="label">
            {{ Lang::txt('COM_COLLECTIONS_FIELD_OWNER_TYPE') }}
            <span class="text-error">*</span>
          </label>
          <select name="fields[object_type]" id="field-object_type"
                  class="select select-bordered w-full" required>
            <option value="member" @selected($row->get('object_type') == 'member')>
              {{ Lang::txt('COM_COLLECTIONS_FIELD_OWNER_TYPE_MEMBER') }}
            </option>
            <option value="group" @selected($row->get('object_type') == 'group')>
              {{ Lang::txt('COM_COLLECTIONS_FIELD_OWNER_TYPE_GROUP') }}
            </option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-object_id" class="label">
            {{ Lang::txt('COM_COLLECTIONS_FIELD_OWNER_ID') }}
            <span class="text-error">*</span>
          </label>
          <input type="text"
                 name="fields[object_id]"
                 id="field-object_id"
                 class="input input-bordered w-full"
                 maxlength="250"
                 required
                 value="{{ $row->get('object_id', '') }}" />
          <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_COLLECTIONS_FIELD_OWNER_ID_HINT') }}</p>
        </div>
      </div>

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_COLLECTIONS_FIELD_TITLE') }}
          <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               maxlength="250"
               required
               value="{{ $row->get('title', '') }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_ALIAS') }}</label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               maxlength="250"
               value="{{ $row->get('alias', '') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_COLLECTIONS_FIELD_ALIAS_HINT') }}</p>
      </div>

      <div class="admin-field">
        <label for="field-description" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_DESCRIPTION') }}</label>
        {!! $__view->editor(
            'fields[description]',
            e($row->get('description')),
            35,
            10,
            'field-description',
            ['class' => 'minimal no-footer', 'buttons' => false]
        ) !!}
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-layout" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_LAYOUT') }}</label>
          <select name="fields[layout]" id="field-layout"
                  class="select select-bordered w-full">
            <option value="grid" @selected($row->get('layout') == 'grid')>
              {{ Lang::txt('COM_COLLECTIONS_FIELD_LAYOUT_GRID') }}
            </option>
            <option value="list" @selected($row->get('layout') == 'list')>
              {{ Lang::txt('COM_COLLECTIONS_FIELD_LAYOUT_LIST') }}
            </option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-sort" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_SORT') }}</label>
          <select name="fields[sort]" id="field-sort"
                  class="select select-bordered w-full">
            <option value="created" @selected($row->get('sort') == 'created')>
              {{ Lang::txt('COM_COLLECTIONS_FIELD_SORT_CREATED') }}
            </option>
            <option value="ordering" @selected($row->get('sort') == 'ordering')>
              {{ Lang::txt('COM_COLLECTIONS_FIELD_SORT_ORDERING') }}
            </option>
          </select>
        </div>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Meta --}}
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_CREATOR') }}</td>
              <td>
                @php $editor = User::getInstance($row->get('created_by')); @endphp
                {{ $editor->get('name') }}
                <input type="hidden" name="fields[created_by]"
                       value="{{ $row->get('created_by') }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_CREATED') }}</td>
              <td>
                {{ Date::of($row->get('created'))->toLocal() }}
                <input type="hidden" name="fields[created]"
                       value="{{ $row->get('created') }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_LIKES') }}</td>
              <td>
                {{ $row->get('positive', 0) }}
                <input type="hidden" name="fields[positive]"
                       value="{{ $row->get('positive', 0) }}" />
              </td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_POSTS') }}</td>
              <td>{{ $row->posts()->total() }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">

        <div class="admin-field">
          <label for="field-state" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_STATE') }}</label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
            <option value="2" @selected($row->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-access" class="label">{{ Lang::txt('COM_COLLECTIONS_FIELD_ACCESS') }}</label>
          <select name="fields[access]" id="field-access"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('access') == 0)>{{ Lang::txt('COM_COLLECTIONS_ACCESS_PUBLIC') }}</option>
            <option value="1" @selected($row->get('access') == 1)>{{ Lang::txt('COM_COLLECTIONS_ACCESS_REGISTERED') }}</option>
            <option value="4" @selected($row->get('access') == 4)>{{ Lang::txt('COM_COLLECTIONS_ACCESS_PRIVATE') }}</option>
          </select>
        </div>

    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
</x-admin-edit>
