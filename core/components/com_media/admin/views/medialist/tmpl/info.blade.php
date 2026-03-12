{{--
  com_media — File/folder info modal

  Variables: $data (array: type, name, path, size, [width], [height], modified), $option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$ext  = '';
$icon = '';
if ($data['type'] !== 'folder') {
    $ext  = Filesystem::extension($data['name']);
    $icon = Html::asset('image', 'assets/filetypes/' . $ext . '.svg', '', null, true, true);
    if (!$icon) {
        $icon = Html::asset('image', 'assets/filetypes/file.svg', '', null, true, true);
    }
} else {
    $icon = Html::asset('image', 'assets/filetypes/folder.svg', '', null, true, true);
}

$fileSize = \Components\Media\Admin\Helpers\MediaHelper::parseSize($data['size'] ?? 0);
$imgAlt   = e(Lang::txt('COM_MEDIA_IMAGE_TITLE', $data['name'], $fileSize));

$formAction = Route::url(
    'index.php?option=' . $option
    . '&controller=medialist&file=' . urlencode($data['path']), false
);
@endphp
<form action="{{ $formAction }}"
    id="component-form" method="post"
    name="adminForm" autocomplete="off">
    <fieldset>
        <h2 class="modal-title">
            {{ Lang::txt('COM_MEDIA_FILE_INFO') }}
        </h2>
    </fieldset>
    <div class="grid grid-cols-12 gap-4">
        <div class="col-span-5">
            <div class="media-preview">
                <div class="media-preview-inner">
                    @if ($data['type'] === 'img')
                        @php $imgWidth = ($data['width'] < 260) ? $data['width'] : 260; @endphp
                        <div class="media-thumb img-preview {{ $ext }}"
                            title="{{ $data['name'] }}">
                            <span class="media-preview-shim"></span><!--
                            --><img
                                src="{{ COM_MEDIA_BASEURL . $data['path'] }}"
                                alt="{{ $imgAlt }}"
                                width="{{ $imgWidth }}" />
                        </div>
                    @else
                        <div class="media-thumb doc-item {{ $ext }}"
                            title="{{ $data['name'] }}">
                            <span class="media-preview-shim"></span><!--
                            --><img src="{{ $icon }}"
                                alt="{{ $imgAlt }}"
                                width="80" />
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-span-7">
            <div class="input-wrap">
                <span class="media-info-label">
                    {{ Lang::txt('COM_MEDIA_LIST_HEADER_NAME') }}:
                </span>
                <span class="media-info-value">{{ $data['name'] }}</span>
            </div>
            <div class="input-wrap">
                <span class="media-info-label">
                    {{ Lang::txt('COM_MEDIA_LIST_HEADER_PATH') }}:
                </span>
                <span class="media-info-value">{{ $data['path'] }}</span>
            </div>
            @if ($data['type'] !== 'folder')
                @if ($data['type'] === 'img')
                    <div class="grid grid-cols-3 gap-4">
                        <div>
                @endif
                <div class="input-wrap">
                    <span class="media-info-label">
                        {{ Lang::txt('COM_MEDIA_LIST_HEADER_SIZE') }}:
                    </span>
                    <span class="media-info-value">
                        {{ \Hubzero\Utility\Number::formatBytes($data['size']) }}
                    </span>
                </div>
                @if ($data['type'] === 'img')
                        </div>
                        <div>
                            <div class="input-wrap">
                                <span class="media-info-label">
                                    {{ Lang::txt('COM_MEDIA_LIST_HEADER_WIDTH') }}:
                                </span>
                                <span class="media-info-value">
                                    {{ $data['width'] }}px
                                </span>
                            </div>
                        </div>
                        <div>
                            <div class="input-wrap">
                                <span class="media-info-label">
                                    {{ Lang::txt('COM_MEDIA_LIST_HEADER_HEIGHT') }}:
                                </span>
                                <span class="media-info-value">
                                    {{ $data['height'] }}px
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
            <div class="input-wrap">
                <span class="media-info-label">
                    {{ Lang::txt('COM_MEDIA_LIST_HEADER_MODIFIED') }}:
                </span>
                <span class="media-info-value">
                    {{ Date::of($data['modified'])->toSql() }}
                </span>
            </div>
        </div>
    </div>
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="{{ $option }}" />
    {!! Html::input('token') !!}
</form>
