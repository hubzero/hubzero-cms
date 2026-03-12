{{--
  com_installer packages edit — Install / change version of a Composer package

  Variables: $packageName, $installedPackage, $versions, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Installer\Admin\Helpers\Installer::getActions();
  $authors = [];
  if ($installedPackage) {
      foreach ($installedPackage->getAuthors() as $author) {
          $authors[] = $author['name'] . ' &lt;' . $author['email'] . '&gt;';
      }
  }

  Toolbar::title(Lang::txt('COM_INSTALLER_PACKAGES_PACKAGE') . ': ' . $packageName, 'packages');
  if ($canDo->get('core.edit')) {
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
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto] gap-6">

      <fieldset class="fieldset bg-base-200 rounded-box p-4">
        <legend class="fieldset-legend">{{ Lang::txt('COM_INSTALLER_PACKAGES_BASIC_INFO') }}</legend>
        <div class="form-control">
          <label for="field-version" class="label text-base-content">
            <span class="label-text text-base-content">{{ Lang::txt('COM_INSTALLER_PACKAGES_AVAILABLE_VERSIONS') }}</span>
          </label>
          <select name="packageVersion" id="field-version" class="select select-bordered w-full max-w-xs">
            @foreach ($versions as $version)
              <option value="{{ $version->getVersion() }}"
                @selected($installedPackage->getVersion() == $version->getVersion())>
                {{ $version->getFullPrettyVersion() }}
              </option>
            @endforeach
          </select>
        </div>
      </fieldset>

      <table class="table table-sm border border-base-300 rounded-box h-fit">
        <tbody>
          <tr>
            <th class="text-xs text-muted-foreground font-medium">{{ Lang::txt('COM_INSTALLER_PACKAGES_INSTALLED_VERSION') }}</th>
            <td class="text-sm tabular-nums">{{ $installedPackage->getFullPrettyVersion() }}</td>
          </tr>
          <tr>
            <th class="text-xs text-muted-foreground font-medium">{{ Lang::txt('COM_INSTALLER_PACKAGES_RELEASE_DATE') }}</th>
            <td class="text-sm">{{ $installedPackage->getReleaseDate()->format('Y-m-d H:i:s') }}</td>
          </tr>
          <tr>
            <th class="text-xs text-muted-foreground font-medium">{{ Lang::txt('COM_INSTALLER_PACKAGES_TYPE') }}</th>
            <td class="text-sm">{{ $installedPackage->getType() }}</td>
          </tr>
          <tr>
            <th class="text-xs text-muted-foreground font-medium">{{ Lang::txt('COM_INSTALLER_PACKAGES_AUTHORS') }}</th>
            <td class="text-sm">{!! implode(', ', $authors) !!}</td>
          </tr>
        </tbody>
      </table>

    </div>
  @endif

  <input type="hidden" name="packageName" value="{{ $packageName }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="task" value="install" />
  {!! Html::input('token') !!}
</form>
