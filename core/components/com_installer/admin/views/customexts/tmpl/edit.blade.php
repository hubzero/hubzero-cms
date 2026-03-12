{{--
  com_installer customexts edit — Create/edit a custom extension

  Variables: $row, $form, $task, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo   = \Components\Installer\Admin\Helpers\Installer::getActions();
  $helpers = '\Components\Installer\Admin\Helpers\Installer';
  $isNew   = !$row->get('extension_id');
  $text    = $isNew ? Lang::txt('JACTION_CREATE') : Lang::txt('JACTION_EDIT');

  Toolbar::title(Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADER') . ': ' . $text);
  if ($canDo->get('core.edit')) {
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    task="save"
>
  {{-- Error display --}}
  @if ($__view->getErrors())
    <div role="alert" class="alert alert-error mb-4">
      {!! implode('<br>', $__view->getErrors()) !!}
    </div>
  @endif

  <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">

      <div class="admin-field">
        {!! $form->getLabel('url') !!}
        <input type="text"
               name="fields[url]"
               id="url"
               maxlength="250"
               class="input input-bordered w-full"
               value="{{ $row->get('url') }}"
               required />
      </div>

      <div class="admin-field">
        {!! $form->getLabel('name') !!}
        <input type="text"
               name="fields[name]"
               id="name"
               maxlength="250"
               class="input input-bordered w-full"
               value="{{ $row->get('name') }}"
               required />
      </div>

      <div class="admin-field">
        {!! $form->getLabel('alias') !!}
        <input type="text"
               name="fields[alias]"
               id="alias"
               maxlength="250"
               class="input input-bordered w-full"
               value="{{ $row->get('alias') }}"
               required />
      </div>

      <div class="admin-field">
        {!! $form->getLabel('type') !!}
        <select name="fields[type]" id="type" class="select select-bordered w-full" required>
          <option value="">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_TYPE_SELECT') }}</option>
          {!! Html::select('options', $helpers::TypeOptions(), 'value', 'text', $row['type'], true) !!}
        </select>
      </div>

      <div class="admin-field">
        {!! $form->getLabel('folder') !!}
        <select name="fields[folder]" id="folder" class="select select-bordered w-full">
          <option value="">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_FOLDER_SELECT') }}</option>
          {!! Html::select('options', $helpers::GroupOptions(), 'value', 'text', $row['folder']) !!}
        </select>
      </div>

      <div class="admin-field">
        {!! $form->getLabel('description') !!}
        <textarea name="fields[description]" id="description"
                  rows="4"
                  class="textarea textarea-bordered w-full">{{ $row->get('description') }}</textarea>
      </div>

      <div class="admin-field">
        {!! $form->getLabel('apikey') !!}
        <input type="text"
               name="fields[apikey]"
               id="apikey"
               maxlength="250"
               class="input input-bordered w-full"
               value="{{ $row->get('apikey') }}" />
      </div>

      <div class="admin-field">
        {!! $form->getLabel('git_branch') !!}
        <input type="text"
               name="fields[git_branch]"
               id="git_branch"
               maxlength="250"
               class="input input-bordered w-full"
               value="{{ $row->get('git_branch') }}" />
      </div>

      <div class="admin-field">
        {!! $form->getLabel('client_id') !!}
        <select name="fields[client_id]" id="client_id" class="select select-bordered w-full">
          <option value="">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_CLIENT_SELECT') }}</option>
          {!! Html::select('options', $helpers::LocationOptions(), 'value', 'text', $row['client_id'], true) !!}
        </select>
      </div>
  </x-admin-fieldset>

  @slot('sidebar')
    <table class="table table-sm border border-base-300 rounded-box">
      <tbody>
        @if ($row->created && $row->created != '0000-00-00 00:00:00')
          <tr>
            <th class="text-xs text-muted-foreground font-medium">{{ Lang::txt('JGLOBAL_FIELD_CREATED_LABEL') }}</th>
            <td class="text-sm">
              <time datetime="{{ $row->created }}">
                {{ Date::of($row->created)->toLocal() }}
              </time>
            </td>
          </tr>
        @endif
        @if ($row->created_by)
          @php
            $creator = User::getInstance($row->created_by);
            $creatorName = $creator->get('name', Lang::txt('COM_PLUGINS_UNKNOWN'));
          @endphp
          <tr>
            <th class="text-xs text-muted-foreground font-medium">{{ Lang::txt('JGLOBAL_FIELD_CREATED_BY_LABEL') }}</th>
            <td class="text-sm">{{ $creatorName . ' (' . $row->created_by . ')' }}</td>
          </tr>
        @endif
        @if ($row->modified && $row->modified != '0000-00-00 00:00:00')
          <tr>
            <th class="text-xs text-muted-foreground font-medium">{{ Lang::txt('JGLOBAL_FIELD_MODIFIED_LABEL') }}</th>
            <td class="text-sm">
              <time datetime="{{ $row->modified }}">
                {{ Date::of($row->modified)->toLocal() }}
              </time>
            </td>
          </tr>
        @endif
        @if ($row->modified_by)
          @php
            $modifier = User::getInstance($row->modified_by);
            $modName  = $modifier->get('name', Lang::txt('COM_PLUGINS_UNKNOWN'));
          @endphp
          <tr>
            <th class="text-xs text-muted-foreground font-medium">{{ Lang::txt('JGLOBAL_FIELD_MODIFIED_BY_LABEL') }}</th>
            <td class="text-sm">{{ $modName . ' (' . $row->modified_by . ')' }}</td>
          </tr>
        @endif
      </tbody>
    </table>
  @endslot

  <input type="hidden" name="fields[extension_id]" value="{{ $row->get('extension_id') }}" />
</x-admin-edit>
