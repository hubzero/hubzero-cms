{{--
  Posts — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\User;

  $canDo = \Components\Collections\Helpers\Permissions::getActions('post');
  $text  = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  if (!$row->get('id')) {
      $row->set('created_by', User::get('id'));
      $row->set('created', Date::toSql());
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_COLLECTIONS') }}: {{ Lang::txt('COM_COLLECTIONS_POSTS') }}: {{ $text }}"
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
          <label for="field-item_id" class="label">
            {{ Lang::txt('COM_COLLECTIONS_FIELD_ITEM_ID') }}
            <span class="text-error">*</span>
          </label>
          <input type="text"
                 name="fields[item_id]"
                 id="field-item_id"
                 class="input input-bordered w-full"
                 maxlength="11"
                 required
                 value="{{ $row->get('item_id', '') }}" />
        </div>

        <div class="admin-field">
          <label for="field-collection_id" class="label">
            {{ Lang::txt('COM_COLLECTIONS_FIELD_COLLECTION_ID') }}
            <span class="text-error">*</span>
          </label>
          <input type="text"
                 name="fields[collection_id]"
                 id="field-collection_id"
                 class="input input-bordered w-full"
                 maxlength="11"
                 required
                 value="{{ $row->get('collection_id', '') }}" />
        </div>
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
              <td>{{ Lang::txt('COM_COLLECTIONS_FIELD_ORIGINAL') }}</td>
              <td>{{ $row->get('original') ? Lang::txt('JYES') : Lang::txt('JNO') }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    {{-- Publishing --}}
    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">

        <div class="admin-field">
          <label class="checkbox-label cursor-pointer flex items-center gap-2">
            <input type="checkbox"
                   name="fields[original]"
                   id="field-original"
                   class="checkbox checkbox-sm"
                   value="1"
                   @checked($row->get('original') == 1) />
            <span>{{ Lang::txt('COM_COLLECTIONS_FIELD_ORIGINAL') }}</span>
          </label>
        </div>

    </x-admin-fieldset>
  @endslot

  {{-- Hidden fields --}}
  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
</x-admin-edit>
