{{--
  Quota Classes — Edit entry

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Admin::getActions('component');
  $text  = ($task == 'editClass' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

  if ($canDo->get('core.edit')) {
      Toolbar::apply('applyClass');
      Toolbar::save('saveClass');
      Toolbar::spacer();
  }
  Toolbar::cancel('cancelClass');
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS_QUOTA_CLASSES') }}: {{ $text }}"
    icon="user"
/>

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-7">
      <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_QUOTA_CLASS_LEGEND') }}">
        <div class="admin-field">
          <label for="field-alias" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_ALIAS') }}</label>
          <input type="text"
                 name="fields[alias]"
                 id="field-alias"
                 class="input input-bordered w-full"
                 value="{{ $row->get('alias') }}"
                 @if($row->get('alias') == 'default') readonly @endif />
        </div>

        <div class="admin-field">
          <label for="field-soft_blocks" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_SOFT_BLOCKS') }}</label>
          <input type="text"
                 name="fields[soft_blocks]"
                 id="field-soft_blocks"
                 class="input input-bordered w-full"
                 value="{{ $row->get('soft_blocks') }}" />
        </div>

        <div class="admin-field">
          <label for="field-hard_blocks" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_HARD_BLOCKS') }}</label>
          <input type="text"
                 name="fields[hard_blocks]"
                 id="field-hard_blocks"
                 class="input input-bordered w-full"
                 value="{{ $row->get('hard_blocks') }}" />
        </div>

        <div class="admin-field">
          <label for="field-soft_files" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_SOFT_FILES') }}</label>
          <input type="text"
                 name="fields[soft_files]"
                 id="field-soft_files"
                 class="input input-bordered w-full"
                 value="{{ $row->get('soft_files') }}" />
        </div>

        <div class="admin-field">
          <label for="field-hard_files" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_HARD_FILES') }}</label>
          <input type="text"
                 name="fields[hard_files]"
                 id="field-hard_files"
                 class="input input-bordered w-full"
                 value="{{ $row->get('hard_files') }}" />
        </div>
      </x-admin-fieldset>

      <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_QUOTA_CLASS_USERGROUPS_LEGEND') }}">
        <p class="text-sm text-muted-foreground mb-3">
          {{ Lang::txt('COM_MEMBERS_QUOTA_CLASS_USERGROUPS_DESC') }}
        </p>
        @php
          Html::addIncludePath(\Hubzero\Facades\Component::path('com_members') . '/admin/helpers/html');
          $groups = [];
          foreach ($row->groups as $g) {
              $groups[] = $g->get('group_id');
          }
        @endphp
        {!! Html::access('usergroups', 'fields[groups]', $groups, true) !!}
      </x-admin-fieldset>
    </div>

    <div class="lg:col-span-5">
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_QUOTA_ID') }}</td>
            <td>{{ $row->get('id') }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_QUOTA_CLASS_USER_COUNT') }}</td>
            <td>{{ $row->get('id') ? $user_count : 0 }}</td>
          </tr>
        </table>
      </x-admin-fieldset>
    </div>
  </div>

  <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="saveClass" />
  {!! Html::input('token') !!}
</form>
