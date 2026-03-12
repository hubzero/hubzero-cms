{{--
  com_installer warnings composer — Missing composer.json notice

  Variables: $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

  Toolbar::title(Lang::txt('COM_INSTALLER_TITLE_PACKAGES'), 'install');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_installer');
      Toolbar::divider();
  }
  Toolbar::help('warnings');

  $example      = Component::path('com_installer') . '/config/composer.json.dist';
  $composerPath = PATH_APP . '/composer.json';
@endphp

<form action="{{ Route::url('index.php?option=com_installer&controller=warnings', false) }}"
      method="post" name="adminForm" id="item-form">

  <div role="alert" class="alert alert-error mb-4">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
    </svg>
    <span>{!! Lang::txt('COM_INSTALLER_MSG_WARNINGS_MISSING_COMPOSER', e($composerPath)) !!}</span>
  </div>

  @if (file_exists($example))
    @php
      $contents = file_get_contents($example);
    @endphp
    @if ($contents)
      @php
        $site     = preg_replace('/[^a-zA-Z0-9\-]/', '', strtolower(Config::get('sitename')));
        $json     = json_decode($contents);
        $json->name = $site . '/' . $site . '-app';
        $contents = json_encode($json, JSON_PRETTY_PRINT);
        $contents = str_replace('\/', '/', $contents);
      @endphp
      <div class="form-control">
        <label for="sample" class="label text-base-content">
          <span class="label-text text-base-content">{{ Lang::txt('COM_INSTALLER_MSG_WARNINGS_MISSING_COMPOSER_SAMPLE') }}</span>
        </label>
        <textarea name="sample" id="sample"
                  rows="28"
                  class="textarea textarea-bordered font-mono text-xs w-full">{{ $contents }}</textarea>
      </div>
    @endif
  @endif

</form>
