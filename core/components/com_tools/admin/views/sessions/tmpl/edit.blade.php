{{--
  Tool Session Class — Admin edit/create form

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Toolbar;

  $text = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE');

  Toolbar::title(Lang::txt('COM_TOOLS_SESSION_CLASSES') . ': ' . $text, 'tools');
  Toolbar::apply();
  Toolbar::save();
  Toolbar::spacer();
  Toolbar::cancel('cancelclass');
@endphp

@if($__view->getError())
  <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
@endif

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    task="saveClass"
>
  <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_SESSION_CLASS_LEGEND') }}">

    <div class="admin-field">
      <label for="field-alias" class="label text-base-content">{{ Lang::txt('COM_TOOLS_SESSION_CLASS_ALIAS') }}</label>
      <input type="text"
             name="fields[alias]"
             id="field-alias"
             class="input input-bordered w-full"
             @readonly($row->alias == 'default')
             value="{{ $row->alias ?? '' }}" />
    </div>

    <div class="admin-field">
      <label for="field-jobs" class="label text-base-content">{{ Lang::txt('COM_TOOLS_SESSION_CLASS_JOBS') }}</label>
      <input type="text"
             name="fields[jobs]"
             id="field-jobs"
             class="input input-bordered w-full"
             value="{{ $row->jobs ?? '' }}" />
    </div>

  </x-admin-fieldset>

  <x-admin-fieldset legend="{{ Lang::txt('COM_TOOLS_SESSION_CLASS_USERGROUPS_LEGEND') }}">
    <p class="text-sm text-muted-foreground mb-3">{{ Lang::txt('COM_TOOLS_SESSION_CLASS_USERGROUPS_DESC') }}</p>
    @php
      Html::addIncludePath(Component::path('com_members') . '/admin/helpers/html');
    @endphp
    <div class="admin-field">
      {!! Html::access('usergroups', 'fields[groups]', $row->getGroupIds(), true) !!}
    </div>
  </x-admin-fieldset>

  @slot('sidebar')
    <table class="admin-meta">
      <tbody>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_SESSION_CLASS_ID') }}</td>
          <td>{{ $row->id ?: '—' }}</td>
        </tr>
        <tr>
          <td>{{ Lang::txt('COM_TOOLS_SESSION_CLASS_USER_COUNT') }}</td>
          <td>{{ $row->id ? $row->userCount() : 0 }}</td>
        </tr>
      </tbody>
    </table>
  @endslot

  <input type="hidden" name="fields[id]" value="{{ $row->id }}" />
</x-admin-edit>
