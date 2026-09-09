@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$homeUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=filelist&tmpl=component');
@endphp

<div id="small-page">
    <div class="databrowser">
        <div id="filelist">
            <table class="table table-compact w-full">
                <caption class="text-left p-2">
                    <span class="icon-home">
                        @if(count($dirtree) > 0)
                            <a href="{{ $homeUrl }}">{{ Lang::txt('COM_TOOLS_HOME') }}</a>
                        @else
                            <span>{{ Lang::txt('COM_TOOLS_HOME') }}</span>
                        @endif
                    </span>
                    @if(count($dirtree) > 0)
                        @php $path = ''; $i = 0; @endphp
                        @foreach($dirtree as $branch)
                            @if($branch != '')
                                @php
                                    $path .= $branch . DS;
                                    $i++;
                                    $branchUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=filelist&tmpl=component&listdir=' . $path);
                                @endphp
                                <span>&raquo;</span>
                                <span class="icon-folder">
                                    @if($i != count($dirtree))
                                        <a href="{{ $branchUrl }}">{{ ucfirst($branch) }}</a>
                                    @else
                                        <span>{{ ucfirst($branch) }}</span>
                                    @endif
                                </span>
                            @endif
                        @endforeach
                    @endif
                </caption>
                <tbody>
                    @foreach($folders as $fullpath => $name)
                        @php
                            $dir = DS . $name;
                            $numFiles = count(\Hubzero\Facades\Filesystem::files($fullpath, '.', false, true, []));
                            $ld = ($listdir == DS) ? '' : $listdir;
                            $d = $ld ? $ld . DS . $name : DS . $name;
                            $folderUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=filelist&tmpl=component&listdir=' . urlencode($d));
                        @endphp
                        <tr>
                            <td width="100%">
                                <a class="icon-folder" href="{{ $folderUrl }}">{{ $dir }}</a>
                            </td>
                            <td></td>
                            <td>
                                @if($dir != '/data' && $dir != '/sessions')
                                    <a class="delete icon-delete delete-folder"
                                       href="index.php?option={{ $option }}&amp;controller={{ $controller }}&amp;task=deletefolder&amp;delFolder={{ urlencode($dir) }}&amp;listdir={{ urlencode($listdir) }}&amp;tmpl=component"
                                       data-confirm="{{ Lang::txt('Are you sure you want to delete the folder "%s"?', $dir) }}"
                                       data-files="{{ $numFiles }}"
                                       data-notempty="{{ Lang::txt('Sorry unable to delete folder because it is not empty') }}"
                                       target="filer"
                                       title="{{ Lang::txt('JACTION_DELETE') }}">
                                        {{ Lang::txt('JACTION_DELETE') }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach

                    @foreach($docs as $fullpath => $name)
                        <tr>
                            <td width="100%">
                                <span class="icon-file">{{ $name }}</span>
                            </td>
                            <td class="file-size">
                                {{ \Hubzero\Utility\Number::formatBytes(filesize($fullpath)) }}
                            </td>
                            <td>
                                <a class="delete icon-delete delete-file"
                                   href="index.php?option={{ $option }}&amp;controller={{ $controller }}&amp;task=deletefile&amp;file={{ $name }}&amp;listdir={{ $listdir }}&amp;tmpl=component"
                                   target="filer"
                                   data-confirm="{{ Lang::txt('Are you sure you want to delete the file "%s"?', $name) }}"
                                   title="{{ Lang::txt('JACTION_DELETE') }}">
                                    {{ Lang::txt('JACTION_DELETE') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($__view->getError())
            <div role="alert" class="alert alert-error mt-2">
                <span>{!! $__view->getError() !!}</span>
            </div>
        @endif
    </div>
</div>
