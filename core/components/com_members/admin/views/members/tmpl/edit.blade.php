{{--
  Members — Admin edit shell with tabs

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Members\Helpers\Admin::getActions('component');
  $text  = $profile->isNew()
      ? Lang::txt('JACTION_CREATE')
      : Lang::txt('JACTION_EDIT');

  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . $text, 'user');

  if ($canDo->get('core.edit') || $canDo->get('core.create')) {
      Toolbar::apply();
      Toolbar::save();
      Toolbar::spacer();
  }
  if ($canDo->get('core.create') && $canDo->get('core.manage')) {
      Toolbar::save2new();
  }
  Toolbar::cancel();
  Toolbar::divider();
  Toolbar::help('user');

  $__view->css('members.blade.css')->js();


  $formUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller, false
  );
@endphp

<form action="{!! $formUrl !!}"
      method="post"
      name="adminForm"
      id="item-form"
      class="editform form-validate"
      data-invalid-msg="{{ Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED') }}">

  {{-- Tab navigation --}}
  <div role="tablist" class="tabs tabs-bordered mb-6">
    <a role="tab" class="tab" data-tab-target="#page-account">
      {{ Lang::txt('COM_MEMBERS_SECTION_ACCOUNT') }}
    </a>
    <a role="tab" class="tab" data-tab-target="#page-profile">
      {{ Lang::txt('COM_MEMBERS_SECTION_PROFILE') }}
    </a>
    @if (User::authorise('core.admin', $option)
        || User::authorise('core.edit', $option))
      <a role="tab" class="tab" data-tab-target="#page-password">
        {{ Lang::txt('COM_MEMBERS_SECTION_PASSWORD') }}
      </a>
    @endif
    @if (!$profile->isNew())
      <a role="tab" class="tab" data-tab-target="#page-groups">
        {{ Lang::txt('COM_MEMBERS_SECTION_GROUPS') }}
      </a>
      <a role="tab" class="tab" data-tab-target="#page-hosts">
        {{ Lang::txt('COM_MEMBERS_SECTION_HOSTS') }}
      </a>
      <a role="tab" class="tab" data-tab-target="#page-messaging">
        {{ Lang::txt('COM_MEMBERS_SECTION_MESSAGING') }}
      </a>
      @foreach ($tabs as $tab)
        @if ($tab)
          <a role="tab" class="tab" data-tab-target="#page-{{ $tab['name'] }}">
            {{ $tab['label'] }}
          </a>
        @endif
      @endforeach
    @endif
  </div>

  {{-- Tab panels --}}
  <div id="page-account" class="tab-panel">
    @include('com_members::admin.views.members.tmpl.edit_user')
  </div>

  <div id="page-profile" class="tab-panel hidden">
    @include('com_members::admin.views.members.tmpl.edit_profile')
  </div>

  @if (User::authorise('core.admin', $option)
      || User::authorise('core.edit', $option))
    <div id="page-password" class="tab-panel hidden">
      @include('com_members::admin.views.members.tmpl.edit_password')
    </div>
  @endif

  @if (!$profile->isNew())
    <div id="page-groups" class="tab-panel hidden">
      @include('com_members::admin.views.members.tmpl.edit_groups')
    </div>

    <div id="page-hosts" class="tab-panel hidden">
      @include('com_members::admin.views.members.tmpl.edit_hosts')
    </div>

    <div id="page-messaging" class="tab-panel hidden">
      @include('com_members::admin.views.members.tmpl.edit_messaging')
    </div>

    @foreach ($tabs as $tab)
      @if ($tab)
        <div id="page-{{ $tab['name'] }}" class="tab-panel hidden">
          {!! $tab['content'] !!}
        </div>
      @endif
    @endforeach
  @endif

  <input type="hidden" name="id" value="{{ $profile->get('id') }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="save" />
  {!! Html::input('token') !!}
</form>
