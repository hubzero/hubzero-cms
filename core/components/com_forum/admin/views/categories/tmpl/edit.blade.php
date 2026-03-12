{{--
  Forum Category — Admin edit view

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
    title="{{ Lang::txt('COM_FORUM') }}: {{ Lang::txt('COM_FORUM_CATEGORIES') }}: {{ $row->get('id') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE') }}"
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
        <label for="field-section_id" class="label">
          {{ Lang::txt('COM_FORUM_FIELD_SECTION') }} <span class="text-error">*</span>
        </label>
        <select name="fields[section_id]" id="field-section_id"
                class="select select-bordered w-full" required>
          <option value="-1">{{ Lang::txt('COM_FORUM_FIELD_SECTION_SELECT') }}</option>
          @foreach($sections as $group => $sects)
            <optgroup label="{{ $group }}">
              @foreach($sects as $sect)
                <option value="{{ $sect->id }}" @selected($row->get('section_id') == $sect->id)>
                  {{ $sect->title }}
                </option>
              @endforeach
            </optgroup>
          @endforeach
        </select>
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

      <div class="admin-field">
        <label for="field-description" class="label">{{ Lang::txt('COM_FORUM_FIELD_DESCRIPTION') }}</label>
        <textarea name="fields[description]"
                  id="field-description"
                  class="textarea textarea-bordered w-full"
                  rows="5">{{ $row->get('description') }}</textarea>
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
            @if($row->get('modified_by'))
              <tr>
                <td>{{ Lang::txt('COM_FORUM_FIELD_MODIFIER') }}</td>
                <td>{{ $row->modifier->get('name') }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_FORUM_FIELD_MODIFIED') }}</td>
                <td>{{ Date::of($row->get('modified'))->toLocal() }}</td>
              </tr>
            @endif
          </tbody>
        </table>
    </x-admin-fieldset>

    <x-admin-fieldset legend="{{ Lang::txt('JGLOBAL_FIELDSET_PUBLISHING') }}">
        <div class="admin-field">
          <label class="label cursor-pointer justify-start gap-2">
            <input type="checkbox"
                   name="fields[closed]"
                   id="field-closed"
                   class="checkbox checkbox-sm"
                   value="1"
                   @checked($row->get('closed')) />
            <span class="label-text">{{ Lang::txt('COM_FORUM_FIELD_CLOSED') }}</span>
          </label>
        </div>

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
  @if($row->get('modified_by'))
    <input type="hidden" name="fields[modified_by]" value="{{ $row->get('modified_by') }}" />
    <input type="hidden" name="fields[modified]" value="{{ $row->get('modified') }}" />
  @endif
</x-admin-edit>
