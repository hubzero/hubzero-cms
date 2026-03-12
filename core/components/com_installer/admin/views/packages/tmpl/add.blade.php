{{--
  com_installer packages add — Select and install a new Composer package

  Variables: $availablePackages, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

  Toolbar::title(Lang::txt('COM_INSTALLER_PACKAGES_PACKAGE') . ': ADD NEW PACKAGE', 'packages');
  if ($canDo->get('core.create')) {
      Toolbar::custom('install', 'download', 'download', 'COM_INSTALLER_INSTALL_BUTTON', false);
      Toolbar::spacer();
  }
  Toolbar::cancel();
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=install', false) }}"
      method="post" name="adminForm" id="item-form">

  @if ($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
      <span>{{ $__view->getError() }}</span>
    </div>
  @else
    <fieldset class="fieldset bg-base-200 rounded-box p-4 max-w-md">
      <legend class="fieldset-legend">{{ Lang::txt('COM_INSTALLER_PACKAGES_BASIC_INFO') }}</legend>
      <div class="form-control">
        <label for="field-packageName" class="label text-base-content">
          <span class="label-text text-base-content">{{ Lang::txt('COM_INSTALLER_PACKAGES_AVAILABLE_PACKAGES') }}</span>
        </label>
        <select name="packageName" id="field-packageName" class="select select-bordered w-full">
          @foreach ($availablePackages as $package)
            <option value="{{ $package->getName() }}">
              {{ $package->getPrettyName() }}
            </option>
          @endforeach
        </select>
      </div>
    </fieldset>
  @endif

  <input type="hidden" name="packageVersion" value="dev-master" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="task" value="install" />
  {!! Html::input('token') !!}
</form>
