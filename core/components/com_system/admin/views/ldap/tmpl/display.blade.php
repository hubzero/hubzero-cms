{{--
  System — LDAP Management

  Variables from controller:
    $config — component params (batch_limit)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_SYSTEM_LDAP_CONFIGURATION'), 'config');
  Toolbar::preferences($option, '550');

  // Keep component CSS/JS — AJAX batch progress depends on ldap.js
  $__view->css('ldap')->js('ldap');

  $batchLimit  = $config->get('batch_limit', 1000);
  $progressUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=exportusersbatch&'
      . Session::getFormToken()
      . '=1&no_html=1&limit=' . $batchLimit . '&start=', false
  );
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="max-w-2xl space-y-6">

    <div role="alert" class="alert alert-warning">
      <span>{!! Lang::txt('COM_SYSTEM_LDAP_WARNING_IRREVERSIBLE') !!}</span>
    </div>

    {{-- Users --}}
    <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
      <legend class="fieldset-legend">{{ Lang::txt('COM_SYSTEM_LDAP_USERS') }}</legend>

      <div class="space-y-4">
        <div class="flex items-start gap-4">
          <div class="shrink-0">
            <input type="submit"
                   name="exportUsers"
                   id="exportUsers"
                   value="{{ Lang::txt('COM_SYSTEM_LDAP_EXPORT_TO_LDAP') }}"
                   class="btn btn-sm btn-primary"
                   data-delay="3"
                   data-start="0"
                   data-progress="{{ $progressUrl }}" />
          </div>
          <div>
            <p class="text-sm">{{ Lang::txt('COM_SYSTEM_LDAP_EXPORT_USERS_TO_LDAP') }}</p>
            <div class="progress-container mt-2 hidden">
              <strong class="text-sm">
                {{ Lang::txt('COM_SYSTEM_LDAP_RUN_PROGRESS') }}
                <span class="progress-percentage">0%</span>
              </strong>
              <div class="progress mt-1"></div>
            </div>
          </div>
        </div>

        <div class="flex items-start gap-4">
          <div class="shrink-0">
            <input type="submit"
                   name="deleteUsers"
                   id="deleteUsers"
                   value="{{ Lang::txt('COM_SYSTEM_LDAP_DELETE_FROM_LDAP') }}"
                   class="btn btn-sm btn-error btn-outline" />
          </div>
          <p class="text-sm">{{ Lang::txt('COM_SYSTEM_LDAP_DELETE_USERS_FROM_LDAP') }}</p>
        </div>
      </div>
    </fieldset>

    {{-- Groups --}}
    <fieldset class="fieldset bg-base-100 border border-base-300 rounded-box p-4">
      <legend class="fieldset-legend">{{ Lang::txt('COM_SYSTEM_LDAP_GROUPS') }}</legend>

      <div class="space-y-4">
        <div class="flex items-start gap-4">
          <div class="shrink-0">
            <input type="submit"
                   name="exportGroups"
                   id="exportGroups"
                   value="{{ Lang::txt('COM_SYSTEM_LDAP_EXPORT_TO_LDAP') }}"
                   class="btn btn-sm btn-primary" />
          </div>
          <p class="text-sm">{{ Lang::txt('COM_SYSTEM_LDAP_EXPORT_GROUPS_TO_LDAP') }}</p>
        </div>

        <div class="flex items-start gap-4">
          <div class="shrink-0">
            <input type="submit"
                   name="deleteGroups"
                   id="deleteGroups"
                   value="{{ Lang::txt('COM_SYSTEM_LDAP_DELETE_FROM_LDAP') }}"
                   class="btn btn-sm btn-error btn-outline" />
          </div>
          <p class="text-sm">{{ Lang::txt('COM_SYSTEM_LDAP_DELETE_GROUPS_FROM_LDAP') }}</p>
        </div>
      </div>
    </fieldset>

  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" />
</form>
