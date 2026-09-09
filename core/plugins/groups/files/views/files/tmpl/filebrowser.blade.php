{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;

$__view->css();

$type          = Request::getWord('type', '', 'get');
$ckeditor      = Request::getString('CKEditor', '', 'get');
$ckeditorFunc  = Request::getInt('CKEditorFuncNum', 0, 'get');
$ckeditorQuery = '&type=' . $type . '&CKEditor=' . $ckeditor . '&CKEditorFuncNum=' . $ckeditorFunc;
@endphp

<div class="files-wrap">
    <div class="upload-browser flex gap-4">
        @foreach ($notifications as $notification)
            <div class="alert {{ $notification['type'] === 'error' ? 'alert-error' : ($notification['type'] === 'warning' ? 'alert-warning' : 'alert-info') }}">
                {{ $notification['message'] }}
            </div>
        @endforeach

        <div class="upload-browser-col flex-none w-64">
            <div class="toolbar flex items-center justify-between mb-2">
                <div class="font-semibold">{{ Lang::txt('COM_GROUPS_MEDIA_GROUP_FILES') }}</div>
                @if ($authorized && $group->published == 1)
                    @php
                        $addFolderUrl = Route::url(
                            'index.php?option=com_groups&cn='
                            . $group->get('cn')
                            . '&controller=media&task=addfolder'
                            . '&tmpl=component&protected=true'
                        );
                    @endphp
                    <div class="buttons">
                        <a href="{{ $addFolderUrl }}"
                            class="btn btn-sm btn-primary action-addfolder">
                            <svg xmlns="http://www.w3.org/2000/svg" class="inline-block w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            {{ Lang::txt('Add folder') }}
                        </a>
                    </div>
                @endif
            </div>

            <div class="foldertree border border-base-300 rounded-box p-2 overflow-auto max-h-64 mb-2"
                data-activefolder="{{ $activeFolder }}">
                {!! $folderTree !!}
            </div>

            <div class="foldertree-list hidden">
                {!! $folderList !!}
            </div>

            @if ($authorized && $group->published == 1)
                @php
                    $uploadAction = Route::url(
                        'index.php?option=com_groups&cn='
                        . $group->get('cn')
                        . '&controller=media&task=ajaxupload&no_html=1&'
                        . Session::getFormToken() . '=1'
                    );
                @endphp
                <form action="{{ Route::url('index.php?option=' . $option) }}"
                    method="post"
                    enctype="multipart/form-data"
                    class="upload-browser-uploader mt-2">
                    <fieldset>
                        <div id="ajax-uploader"
                            class="border-2 border-dashed border-base-300 rounded-box p-4 text-center"
                            data-instructions="{{ Lang::txt('Click or drop file') }}"
                            data-action="{{ $uploadAction }}">
                            <noscript>
                                <p><input type="file" name="upload" id="upload" class="file-input file-input-bordered w-full" /></p>
                                <p><input type="submit" value="{{ Lang::txt('UPLOAD') }}" class="btn btn-primary mt-2" /></p>
                            </noscript>
                        </div>
                        <input type="hidden" name="option" value="{{ $option }}" />
                        <input type="hidden" name="controller" value="media" />
                        <input type="hidden" name="task" value="upload" />
                        <input type="hidden" name="listdir" id="listdir" value="{{ $group->get('gidNumber') }}" />
                        <input type="hidden" name="tmpl" value="component" />
                        {!! Html::input('token') !!}
                    </fieldset>
                </form>
            @endif
        </div>

        @php
            $iframeSrc = Route::url(
                'index.php?option=com_groups&cn='
                . $group->get('cn')
                . '&controller=media&task=listfiles&tmpl=component&type='
                . $ckeditorQuery
            );
        @endphp
        <div class="upload-browser-col flex-1">
            <iframe class="upload-browser-filelist-iframe w-full h-full min-h-96 border border-base-300 rounded-box"
                src="{{ $iframeSrc }}"></iframe>
        </div>
    </div>
</div>
