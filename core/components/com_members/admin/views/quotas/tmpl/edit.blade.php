{{--
  User Quotas — Edit entry

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Members\Helpers\Admin::getActions('component');
  $text  = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
  $__view->js();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS_QUOTAS') }}: {{ $text }}"
    icon="user"
    :canDo="$canDo"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_MEMBERS_QUOTA_LEGEND') }}">
    @if(!$row->get('user_id'))
      <div class="admin-field">
        <label for="field-user_id" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_USER') }}</label>
        @php
          $mc = Event::trigger('hubzero.onGetMultiEntry', [['members', 'fields[user_id]', 'field-user_id', '', '']]);
        @endphp
        @if(count($mc) > 0)
          {!! implode("\n", $mc) !!}
        @else
          <input type="text"
                 name="fields[user_id]"
                 id="field-user_id"
                 class="input input-bordered w-full"
                 value="" />
        @endif
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_MEMBERS_QUOTA_USER_HINT') }}</p>
      </div>
    @else
      <input type="hidden" name="fields[user_id]" id="field-user_id" value="{{ $row->get('user_id') }}" />
    @endif

    <div class="admin-field">
      <label for="class_id" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_CLASS') }}</label>
      {!! $classes !!}
    </div>

    <div class="admin-field">
      <label for="field-soft_blocks" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_SOFT_BLOCKS') }}</label>
      <input type="text"
             name="fields[soft_blocks]"
             id="field-soft_blocks"
             class="input input-bordered w-full"
             value="{{ $row->get('soft_blocks', '') }}"
             @if($row->get('class_id', '')) readonly @endif />
    </div>

    <div class="admin-field">
      <label for="field-hard_blocks" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_HARD_BLOCKS') }}</label>
      <input type="text"
             name="fields[hard_blocks]"
             id="field-hard_blocks"
             class="input input-bordered w-full"
             value="{{ $row->get('hard_blocks', '') }}"
             @if($row->get('class_id')) readonly @endif />
    </div>

    <div class="admin-field">
      <label for="field-soft_files" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_SOFT_FILES') }}</label>
      <input type="text"
             name="fields[soft_files]"
             id="field-soft_files"
             class="input input-bordered w-full"
             value="{{ $row->get('soft_files', '') }}"
             @if($row->get('class_id')) readonly @endif />
    </div>

    <div class="admin-field">
      <label for="field-hard_files" class="label">{{ Lang::txt('COM_MEMBERS_QUOTA_HARD_FILES') }}</label>
      <input type="text"
             name="fields[hard_files]"
             id="field-hard_files"
             class="input input-bordered w-full"
             value="{{ $row->get('hard_files', '') }}"
             @if($row->get('class_id')) readonly @endif />
    </div>

    <input type="hidden" name="fields[id]" value="{{ $row->get('id') }}" />
  </x-admin-fieldset>

  <x-slot name="sidebar">
    @if($row->get('user_id'))
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
        <table class="admin-meta">
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_QUOTA_ID') }}</td>
            <td>{{ $row->get('user_id') }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_QUOTA_USERNAME') }}</td>
            <td>{{ $row->get('username') }}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_QUOTA_NAME') }}</td>
            <td>{{ $row->get('name') }}</td>
          </tr>
          <tr>
            @php
              $space = isset($du['info']['space']) ? $du['info']['space'] / 1024 : 0;
            @endphp
            <td>{{ Lang::txt('COM_MEMBERS_QUOTA_SPACE') }}</td>
            <td>{!! Lang::txt('COM_MEMBERS_QUOTA_SPACE_DISPLAY', $space, $du['percent'] ?? 0) !!}</td>
          </tr>
          <tr>
            <td>{{ Lang::txt('COM_MEMBERS_QUOTA_FILES') }}</td>
            <td>{{ $du['info']['files'] ?? 0 }}</td>
          </tr>
        </table>
      </x-admin-fieldset>
    @endif
  </x-slot>
</x-admin-edit>
