{{--
  com_installer repositories edit — Create/edit a Composer repository

  Variables: $config, $alias, $isNew, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;
  use Hubzero\Utility\Arr;

  $canDo   = \Components\Installer\Admin\Helpers\Installer::getActions();
  $repoName = Arr::getValue($config, 'name', '');

  Toolbar::title(Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY') . ': ' . $repoName, 'packages');
  if ($canDo->get('core.edit')) {
      Toolbar::save();
      Toolbar::spacer();
  }
  Toolbar::cancel();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
    action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=save', false) }}"
>
  @if ($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
      <span>{{ $__view->getError() }}</span>
    </div>
  @endif

  <x-admin-fieldset legend="{{ Lang::txt('COM_INSTALLER_PACKAGES_BASIC_INFO') }}">

      <div class="admin-field">
        <label for="field-name" class="label">
            {{ Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_NAME') }}
            <span class="text-error">*</span>
        </label>
        <input type="text"
               name="name"
               id="field-name"
               class="input input-bordered w-full"
               value="{{ Arr::getValue($config, 'name', '') }}"
               required />
      </div>

      <div class="admin-field">
        <label for="field-alias" class="label">
            {{ Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_ALIAS') }}
            <span class="text-error">*</span>
        </label>
        <input type="text"
               name="alias"
               id="field-alias"
               class="input input-bordered w-full"
               value="{{ isset($alias) ? e($alias) : '' }}"
               required />
      </div>

      <div class="admin-field">
        <label for="field-description" class="label">
            {{ Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_DESCRIPTION') }}
            <span class="text-error">*</span>
        </label>
        <input type="text"
               name="description"
               id="field-description"
               class="input input-bordered w-full"
               value="{{ Arr::getValue($config, 'description', '') }}"
               required />
      </div>

      <div class="admin-field">
        <label for="field-url" class="label">
            {{ Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_URL') }}
            <span class="text-error">*</span>
        </label>
        <input type="text"
               name="url"
               id="field-url"
               class="input input-bordered w-full"
               value="{{ Arr::getValue($config, 'url', '') }}"
               required />
      </div>

      <div class="admin-field">
        <label for="field-type" class="label">
          {{ Lang::txt('COM_INSTALLER_PACKAGES_REPOSITORY_TYPE') }}
        </label>
        @php $configType = Arr::getValue($config, 'type', ''); @endphp
        <select name="type" id="field-type" class="select select-bordered w-full max-w-xs">
          <option value="github" @selected($configType === 'github')>Github</option>
          <option value="gitlab" @selected($configType === 'gitlab')>Gitlab</option>
        </select>
      </div>
  </x-admin-fieldset>

  @slot('sidebar')
    @if (!$isNew)
      <div role="alert" class="alert alert-warning">
        <div>
          <p class="text-sm mb-2">Removing this repository will prevent Composer from fetching packages from it.</p>
          <a class="btn btn-sm btn-error"
             href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&alias=' . $alias . '&task=remove', false) }}">
            {{ Lang::txt('Remove Repository') }}
          </a>
        </div>
      </div>
    @endif
  @endslot

  <input type="hidden" name="oldAlias" value="{{ $alias }}" />
  <input type="hidden" name="isNew" value="{{ $isNew ? 'true' : 'false' }}" />
  <input type="hidden" name="task" value="save" autocomplete="off" />
</x-admin-edit>
