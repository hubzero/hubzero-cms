{{--
  Rename file modal form.

  Variables:
    $group — Group object
    $file  — string: file path

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $fileInfo  = pathinfo($file);
  $actionUrl = Route::url('index.php?option=com_groups&cn=' . $group->get('cn')
      . '&controller=media&task=dorenamefile&no_html=1');
@endphp

<form action="{{ $actionUrl }}" method="post" class="hubForm">
  <fieldset>
    <legend>{{ Lang::txt('COM_GROUPS_MEDIA_RENAME_FILE') }}</legend>
    <label>
      {{ Lang::txt('COM_GROUPS_MEDIA_RENAME_CURRENT_NAME') }}:<br />
      <input type="hidden" name="file" value="{{ e($file) }}" />
      <input type="text" name="name" value="{{ e($fileInfo['basename']) }}" />
    </label>
    <p class="controls">
      {!! Html::input('token') !!}
      <button type="submit" class="btn icon-edit">{{ Lang::txt('COM_GROUPS_MEDIA_RENAME') }}</button>
    </p>
  </fieldset>
</form>
