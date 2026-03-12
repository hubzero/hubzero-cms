{{--
  Members — Media upload (tmpl=component)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $formUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller, false
  );
  $removeUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=remove&id=' . $profile->get('id')
      . '&file=profile.png&'
      . Session::getFormToken() . '=1'
      . '&tmpl=' . Request::getCmd('tmpl'), false
  );
  $confirmMsg = Lang::txt('COM_MEMBERS_CONFIRM_DELETE_PICTURE', 'Are you sure you want to remove this photo?');
@endphp

<div class="p-4 space-y-4">

  {{-- Preview row --}}
  <div class="flex gap-6 items-end">
    <div class="flex flex-col items-center gap-1">
      <div class="w-32 h-32 rounded-full bg-base-200 border-2 border-base-300 overflow-hidden">
        <img src="{{ $profile->picture(0, false) }}"
             alt="{{ Lang::txt('COM_MEMBERS_MEDIA_PICTURE') }}"
             class="w-full h-full object-cover"
             id="conimage" />
      </div>
      <span class="text-xs text-muted-foreground">200 &times; 200</span>
    </div>

    <div class="flex flex-col items-center gap-1">
      <div class="w-12 h-12 rounded-full bg-base-200 border border-base-300 overflow-hidden">
        <img src="{{ $profile->picture(0, true) }}"
             alt=""
             class="w-full h-full object-cover"
             id="memberthumb" />
      </div>
      <span class="text-xs text-muted-foreground">50 &times; 50</span>
    </div>
  </div>

  {{-- Upload form --}}
  <form action="{!! $formUrl !!}"
        method="post"
        enctype="multipart/form-data">
    <input type="hidden" name="option"     value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="tmpl"       value="{{ Request::getCmd('tmpl') }}" />
    <input type="hidden" name="id"         value="{{ $profile->get('id') }}" />
    <input type="hidden" name="task"       value="upload" />
    {!! Html::input('token') !!}

    <input type="file"
           name="upload"
           id="upload"
           class="hidden"
           accept=".png,.jpg,.jpeg,.gif,.jp2,.jpx"
           data-file-display="upload-filename" />

    <div class="flex flex-wrap gap-2 items-center">
      <label for="upload"
             class="btn btn-sm btn-ghost border border-base-300 font-normal cursor-pointer">
        Choose photo&hellip;
      </label>
      <span id="upload-filename" class="text-sm text-muted-foreground italic"></span>
      <button type="submit" class="btn btn-sm btn-primary">
        {{ Lang::txt('COM_MEMBERS_MEDIA_UPLOAD') }}
      </button>
    </div>

    @if ($__view->getError())
      <p class="mt-2 text-sm text-error">{{ $__view->getError() }}</p>
    @endif
  </form>

  {{-- Delete photo --}}
  <div>
    <a href="{!! $removeUrl !!}"
       data-confirm="{{ $confirmMsg }}"
       class="btn btn-sm btn-ghost text-error font-normal">
      &minus; {{ Lang::txt('JACTION_DELETE') }}
    </a>
  </div>

</div>
