{{--
  com_media — File path/URL modal

  Variables: $file (string, public URL), $option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$formAction = Route::url(
    'index.php?option=' . $option
    . '&controller=medialist&file=' . urlencode($file), false
);
@endphp
<form action="{{ $formAction }}"
    id="component-form" method="post"
    name="adminForm" autocomplete="off">
    <fieldset>
        <h2 class="modal-title">
            {{ Lang::txt('COM_MEDIA_FILE_LINK') }}
        </h2>
    </fieldset>
    <div class="manager">
        <input type="text" value="{{ $file }}" name="path"
            aria-label="{{ Lang::txt('COM_MEDIA_FILE_LINK') }}" />
        <input type="hidden" name="task" value="" />
        {!! Html::input('token') !!}
        <input type="hidden" name="option" value="{{ $option }}" />
    </div>
</form>
