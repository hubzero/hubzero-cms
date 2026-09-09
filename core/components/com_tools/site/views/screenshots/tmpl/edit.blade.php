@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$size = getimagesize($upath . DS . $file);
$w = ($size[0] > 600) ? $size[0] / 1.4444444 : $size[0];
$h = ($w != $size[0]) ? $size[1] / 1.4444444 : $size[1];
$title = (count($shot) > 0 && isset($shot[0]->title)) ? $shot[0]->title : '';
@endphp

@if($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
        <span>{!! implode('<br />', $__view->getErrors()) !!}</span>
    </div>
@endif

<div class="ss_pop">
    <div>
        <img src="{{ $wpath }}/{{ $file }}"
             width="{{ $w }}" height="{{ $h }}" alt="" />
    </div>
    <form action="{{ Route::url('index.php?option=' . $option) }}" name="hubForm"
          id="ss-pop-form" method="post" enctype="multipart/form-data">
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="version" value="{{ $version }}" />
        <input type="hidden" name="pid" id="pid" value="{{ $pid }}" />
        <input type="hidden" name="path" id="path" value="{{ $upath }}" />
        <input type="hidden" name="filename" id="filename" value="{{ $file }}" />
        <input type="hidden" name="vid" id="vid" value="{{ $vid }}" />
        <input type="hidden" name="task" value="save" />
        <fieldset>
            <label for="ss_title">
                {{ Lang::txt('COM_TOOLS_SS_TITLE') }}:
                <input type="text" name="title" id="ss_title" size="127" maxlength="127"
                       value="{{ e($title) }}" class="input input-bordered w-full" />
            </label>
            <input type="submit" id="ss_pop_save" class="btn btn-primary btn-sm mt-2"
                   value="{{ strtolower(Lang::txt('COM_TOOLS_SAVE')) }}" />
        </fieldset>
    </form>
</div>
