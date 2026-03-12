{{--
  Projects Team — Add member popup

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $tmpl = Request::getString('tmpl', '');
  $text = ($task == 'edit') ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_NEW');

  if ($tmpl != 'component') {
      Toolbar::title(Lang::txt('COM_PROJECTS') . ': ' . $text);
      if (User::authorise('core.edit', $option)) {
          Toolbar::save();
      }
      Toolbar::cancel();
  }

  $routeUrl = Route::url('index.php?option=' . $option, false);

  $__view->js('team-new.blade.js');
@endphp

@foreach($__view->getErrors() as $error)
  <p class="alert alert-error">{{ $error }}</p>
@endforeach

<form action="{!! $routeUrl !!}" method="post" name="adminForm" id="component-form">

  @if($tmpl == 'component')
    <div class="flex gap-2 p-3 border-b border-base-300 mb-4">
      <button type="button"
              id="btn-save-addusers"
              class="btn btn-sm btn-primary"
              data-error-msg="{{ Lang::txt('COM_PROJECTS_ERROR_MISSING_INFORMATION') }}"
              data-redirect-url="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&project=' . $model->get('id'), false) }}">
        {{ Lang::txt('COM_PROJECTS_SAVE') }}
      </button>
      <button type="button"
              data-parent-callback="postMessage"
              data-callback-args='["admin-popup-close","*"]'
              class="btn btn-sm btn-ghost">
        {{ Lang::txt('JCANCEL') }}
      </button>
    </div>
  @endif

  <x-admin-fieldset legend="{{ Lang::txt('COM_PROJECTS_TEAM_ADD_NEW_MEMBERS') }}">

    <input type="hidden" name="project" value="{{ $model->get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="no_html" value="{{ $tmpl == 'component' ? '1' : '0' }}" />
    <input type="hidden" name="task" value="addusers" />

    <div class="admin-field">
      <label for="field-newmember" class="label">
        {{ Lang::txt('COM_PROJECTS_TEAM_ADD_IND_USER') }}
      </label>
      <input type="text"
             name="newmember"
             id="field-newmember"
             class="input input-bordered w-full"
             value="" />
    </div>

    <div class="divider text-sm">{{ Lang::txt('COM_PROJECTS_TEAM_OR') }}</div>

    <div class="admin-field">
      <label for="field-newgroup" class="label">
        {{ Lang::txt('COM_PROJECTS_TEAM_ADD_GROUP_OF_USERS') }}
      </label>
      <input type="text"
             name="newgroup"
             id="field-newgroup"
             class="input input-bordered w-full"
             value="" />
    </div>

    <div class="admin-field">
      <p class="label font-medium mb-1 text-base-content">{{ Lang::txt('COM_PROJECTS_TEAM_ROLE') }}</p>
      <div class="flex flex-col gap-2">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="radio"
                 name="role"
                 id="role_owner"
                 value="1"
                 class="radio radio-sm" />
          <span>{{ Lang::txt('COM_PROJECTS_TEAM_LABEL_OWNER') }}</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="radio"
                 name="role"
                 id="role_collaborator"
                 value="0"
                 class="radio radio-sm"
                 checked />
          <span>{{ Lang::txt('COM_PROJECTS_TEAM_LABEL_COLLABORATOR') }}</span>
        </label>
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="radio"
                 name="role"
                 id="role_reviewer"
                 value="5"
                 class="radio radio-sm" />
          <span>{{ Lang::txt('COM_PROJECTS_TEAM_LABEL_REVIEWER') }}</span>
        </label>
      </div>
    </div>

  </x-admin-fieldset>

  {!! Html::input('token') !!}
</form>
