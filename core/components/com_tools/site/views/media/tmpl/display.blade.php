{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $formAction = Route::url('index.php?option=' . $option);
@endphp

<form action="{{ $formAction }}" name="adminForm" id="adminForm" method="post" enctype="multipart/form-data">
    <fieldset>
        <label for="upload">
            <input type="file" class="file-input file-input-bordered" name="upload" id="upload" />
            <input type="submit" class="btn btn-primary btn-sm" value="{{ strtolower(Lang::txt('COM_TOOLS_UPLOAD')) }}" />
        </label>

        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="resource" value="{{ $resource }}" />
        <input type="hidden" name="task" value="upload" />
    </fieldset>

    @if ($__view->getError())
        <div class="alert alert-error">
            {!! implode('<br>', $__view->getErrors()) !!}
        </div>
    @endif

    @if (count($folders) == 0 && count($docs) == 0)
        <p>{{ Lang::txt('COM_TOOLS_SCREENSHOTS_ABOUT') }}</p>
    @else
        <table class="table table-zebra">
            <tbody>
                @php
                    $docsCopy = $docs;
                @endphp
                @foreach ($docsCopy as $docName => $docFile)
                    @php
                        $subdird = ($subdir && $subdir != DS) ? $subdir . DS : DS;
                        $resId = $row->alias ? $row->alias : $resource;
                        $downloadUrl = Route::url(
                            'index.php?option=com_resources&id=' . $resId
                            . '&task=download&file=' . $docFile
                        );
                        $deleteHref = 'index.php?option=' . $option
                            . '&controller=' . $controller
                            . '&task=delete&file=' . $docFile
                            . '&resource=' . $resource
                            . '&tmpl=component&subdir=' . $subdir
                            . '&' . Session::getFormToken() . '=1';
                        $confirmMsg = Lang::txt(
                            'Are you sure you want to delete the file "%s"?',
                            $docFile
                        );
                    @endphp
                    <tr>
                        <td width="100%">
                            {{ $downloadUrl }}
                        </td>
                        <td>
                            <a
                                class="btn btn-error btn-xs icon-delete delete delete-file"
                                href="{{ $deleteHref }}"
                                target="filer"
                                data-confirm="{{ $confirmMsg }}"
                                title="{{ Lang::txt('JACTION_DELETE') }}"
                            >
                                <span>{{ Lang::txt('JACTION_DELETE') }}</span>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {!! Html::input('token') !!}
</form>
