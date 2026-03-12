{{--
  Forum Section — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;

  $canDo  = \Components\Forum\Helpers\Permissions::getActions('section');
  $access = Html::access('assetgroups');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_FORUM') }}: {{ Lang::txt('COM_FORUM_SECTIONS') }}: {{ $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE') }}"
    icon="forum"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="field-scope" class="label">{{ Lang::txt('COM_FORUM_FIELD_SCOPE') }}</label>
          <input type="text"
                 name="fields[scope]"
                 id="field-scope"
                 class="input input-bordered w-full"
                 maxlength="250"
                 value="{{ $row->get('scope') }}" />
        </div>
        <div class="admin-field">
          <label for="field-scope_id" class="label">{{ Lang::txt('COM_FORUM_FIELD_SCOPE_ID') }}</label>
          <input type="text"
                 name="fields[scope_id]"
                 id="field-scope_id"
                 class="input input-bordered w-full"
                 maxlength="250"
                 value="{{ $row->get('scope_id') }}" />
        </div>
      </div>

      <div class="admin-field">
        <label for="field-title" class="label">
          {{ Lang::txt('COM_FORUM_FIELD_TITLE') }} <span class="text-error">*</span>
        </label>
        <input type="text"
               name="fields[title]"
               id="field-title"
               class="input input-bordered w-full"
               maxlength="250"
               required
               value="{{ $row->get('title') }}" />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">{{ Lang::txt('COM_FORUM_FIELD_ALIAS') }}</label>
        <input type="text"
               name="fields[alias]"
               id="field-alias"
               class="input input-bordered w-full"
               maxlength="250"
               value="{{ $row->get('alias') }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_FORUM_FIELD_ALIAS_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tbody>
            <tr>
              <td>{{ Lang::txt('COM_FORUM_FIELD_CREATOR') }}</td>
              <td>{{ $row->creator->get('name') }}</td>
            </tr>
            <tr>
              <td>{{ Lang::txt('COM_FORUM_FIELD_CREATED') }}</td>
              <td>{{ $row->get('created') ? Date::of($row->get('created'))->toLocal() : '' }}</td>
            </tr>
          </tbody>
        </table>
    </x-admin-fieldset>

    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">
        <div class="admin-field">
          <label for="field-state" class="label">
            <span class="label-text">{{ Lang::txt('COM_FORUM_FIELD_STATE') }}</span>
          </label>
          <select name="fields[state]" id="field-state"
                  class="select select-bordered w-full">
            <option value="0" @selected($row->get('state') == 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
            <option value="1" @selected($row->get('state') == 1)>{{ Lang::txt('JPUBLISHED') }}</option>
            <option value="2" @selected($row->get('state') == 2)>{{ Lang::txt('JTRASHED') }}</option>
          </select>
        </div>

        <div class="admin-field">
          <label for="field-access" class="label">
            <span class="label-text">{{ Lang::txt('COM_FORUM_FIELD_ACCESS') }}</span>
          </label>
          <select name="fields[access]" id="field-access"
                  class="select select-bordered w-full">
            {!! Html::select('options', $access, 'value', 'text', $row->get('access')) !!}
          </select>
        </div>
    </x-admin-fieldset>

    @if($canDo->get('core.admin') && isset($form))
      <x-admin-fieldset legend="{{ Lang::txt('COM_FORUM_FIELDSET_RULES') }}">
          {!! $form->getInput('rules') !!}
      </x-admin-fieldset>
    @endif
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="fields[created_by]" value="{{ $row->get('created_by') }}" />
  <input type="hidden" name="fields[created]" value="{{ $row->get('created') }}" />
</x-admin-edit>
