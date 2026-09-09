{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $base = rtrim(Request::base(true), '/');
@endphp

@if (!$allowupload)
    <div class="alert alert-warning">
        {{ Lang::txt('COM_TOOLS_SUPPORTING_DOCS_ONLY_CURRENT') }}
    </div>
@endif

@php
    $formAction = Route::url(
        'index.php?option=' . $option . '&controller=' . $controller
    );
    $allowupload = true;
@endphp

<form action="{{ $formAction }}" name="hubForm" id="attachments-form" method="post" enctype="multipart/form-data">
    <fieldset>
        <label for="upload">
            <input type="file" class="file-input file-input-bordered" name="upload" id="upload" />
            <input type="submit" class="btn btn-primary btn-sm" value="{{ strtolower(Lang::txt('COM_TOOLS_UPLOAD')) }}" />
        </label>

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="save" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="pid" id="pid" value="{{ $id }}" />
        <input type="hidden" name="path" id="path" value="{{ $path }}" />
    </fieldset>
</form>

@if ($__view->getError())
    <div class="alert alert-error">
        {!! implode('<br>', $__view->getErrors()) !!}
    </div>
@endif

@if ($children)
    @php
        $uploadBase = $cparams->get('uploadpath');
        $n = count($children);
    @endphp

    @if ($allowupload)
        <p>{{ Lang::txt('COM_TOOLS_ATTACH_EDIT_TITLE_EXPLANATION') }}</p>
    @endif

    <table class="table table-zebra">
        <tbody>
            @foreach ($children as $i => $child)
                @php
                    // Determine file URL
                    switch ($child->get('type')) {
                        case 12:
                            $url = $child->path
                                ? $child->path
                                : 'index.php?option=com_resources&id=' . $child->id;
                            break;
                        default:
                            $url = $child->path;
                            break;
                    }

                    // Determine file type CSS class
                    $type = Filesystem::extension($url);
                    $type = (strlen($type) > 3) ? substr($type, 0, 3) : $type;
                    if ($child->get('type') == 12) {
                        $liclass = 'ftitle html';
                    } else {
                        $type = $type ?: 'html';
                        $liclass = 'ftitle ' . $type;
                    }

                    $dlUrl = Route::url('index.php?option=com_resources&task=download&id=' . $child->id);
                    $fileAttribs = \Components\Tools\Helpers\Html::getFileAttribs($url, $uploadBase);
                @endphp
                <tr>
                    <td width="100%">
                        @if ($allowupload)
                            <span class="{{ $liclass }} item:name id:{{ $child->id }}" data-id="{{ $child->id }}">
                                {{ $__view->escape($child->title) }}
                            </span><br />
                            <span class="caption">(<a href="{{ $dlUrl }}" title="{{ $child->title }}">{{ $fileAttribs }}</a>)</span>
                        @else
                            <span><a href="{{ $dlUrl }}">{{ $__view->escape($child->title) }}</a></span>
                        @endif
                    </td>
                    @if ($allowupload)
                        <td class="d">
                            @if ($i > 0)
                                @php
                                    $upUrl = $base . '/index.php?option=' . $option
                                        . '&controller=' . $controller
                                        . '&tmpl=component&pid=' . $resource->id
                                        . '&id=' . $child->id . '&task=reorder&move=up';
                                @endphp
                                <a href="{{ $upUrl }}" class="btn btn-ghost btn-xs order up" title="{{ Lang::txt('COM_TOOLS_MOVE_UP') }}">
                                    <span>{{ Lang::txt('COM_TOOLS_MOVE_UP') }}</span>
                                </a>
                            @endif
                        </td>
                        <td class="u">
                            @if ($i < $n - 1)
                                @php
                                    $downUrl = $base . '/index.php?option=' . $option
                                        . '&controller=' . $controller
                                        . '&tmpl=component&pid=' . $resource->id
                                        . '&id=' . $child->id . '&task=reorder&move=down';
                                @endphp
                                <a href="{{ $downUrl }}" class="btn btn-ghost btn-xs order down" title="{{ Lang::txt('COM_TOOLS_MOVE_DOWN') }}">
                                    <span>{{ Lang::txt('COM_TOOLS_MOVE_DOWN') }}</span>
                                </a>
                            @endif
                        </td>
                        <td class="t">
                            @php
                                $delUrl = $base . '/index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=delete&tmpl=component&id=' . $child->id
                                    . '&pid=' . $resource->id;
                            @endphp
                            <a href="{{ $delUrl }}" class="btn btn-error btn-xs icon-delete delete">
                                <span>{{ Lang::txt('COM_TOOLS_DELETE') }}</span>
                            </a>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    <p>{{ Lang::txt('COM_TOOLS_ATTACH_NONE_FOUND') }}</p>
@endif
