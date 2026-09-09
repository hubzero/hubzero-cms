@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$base = rtrim(Request::base(true), '/');
$versionlabel = ($version == 'current')
    ? Lang::txt('COM_TOOLS_CURRENTLY_PUBLISHED')
    : Lang::txt('COM_TOOLS_DEVELOPMENT');
@endphp

@if($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
        <span>{!! implode('<br />', $__view->getErrors()) !!}</span>
    </div>
@endif

<form action="{{ Route::url('index.php?option=' . $option) }}"
      name="hubForm" id="screenshots-form" method="post" enctype="multipart/form-data">
    <h3>
        {{ Lang::txt('COM_TOOLS_EXISTING_SS') }}
        @if($published)
            @php
                $versionLbl = ($version == 'dev')
                    ? Lang::txt('COM_TOOLS_DEVELOPMENT') . ' ' . strtolower(Lang::txt('COM_TOOLS_VERSION'))
                    : Lang::txt('COM_TOOLS_CURRENTLY_PUBLISHED') . ' ' . strtolower(Lang::txt('COM_TOOLS_VERSION'));
            @endphp
            ({{ $versionLbl }})
        @endif
    </h3>

    @php
        $d = @dir($upath);
        $images = [];
        $tns = [];
        $all = [];
        $ordering = [];

        if ($d) {
            while (false !== ($entry = $d->read())) {
                if (is_file($upath . DS . $entry) && substr($entry, 0, 1) != '.' && strtolower($entry) !== 'index.html') {
                    if (preg_match("#bmp|gif|jpg|png|swf#i", $entry)) {
                        $images[] = $entry;
                    }
                    if (preg_match("#-tn#i", $entry)) {
                        $tns[] = $entry;
                    }
                    $images = array_diff($images, $tns);
                }
            }
            $d->close();
        }

        if ($images) {
            foreach ($images as $key => $value) {
                $tn = preg_replace('#\.[^.]*$#', '', $value) . '-tn.gif';
                if (!is_file($upath . DS . $tn)) {
                    unset($images[$key]);
                }
            }
            $images = array_values($images);
        }

        $b = 0;
        if ($images) {
            foreach ($images as $ima) {
                $new = ['img' => $ima, 'type' => explode('.', $ima)];
                if (count($shots) > 0) {
                    foreach ($shots as $si) {
                        if ($si->filename == $ima) {
                            $new['title'] = stripslashes($si->title);
                            $new['title'] = preg_replace('/"((.)*?)"/i', "&#147;\\1&#148;", $new['title']);
                            $new['ordering'] = $si->ordering;
                        }
                    }
                }
                $ordering[] = isset($new['ordering']) ? $new['ordering'] : $b;
                $b++;
                $all[] = $new;
            }
        }

        if (count($shots) > 0) {
            array_multisort($ordering, $all);
        } else {
            sort($all);
        }
        $images = $all;
    @endphp

    @if(count($images) > 0)
        <ul class="screenshots flex flex-wrap gap-4 mb-4">
            @for($i = 0; $i < count($images); $i++)
                @php
                    $tn = preg_replace('#\.[^.]*$#', '', $images[$i]['img']) . '-tn.gif';
                @endphp
                @if(is_file($upath . DS . $tn))
                    @if(strtolower(end($images[$i]['type'])) == 'swf')
                        {{-- SWF demos --}}
                    @else
                        @php
                            $k = $i + 1;
                            $title = (isset($images[$i]['title']) && $images[$i]['title'] != '') ? $images[$i]['title'] : Lang::txt('COM_TOOLS_SCREENSHOT') . ' #' . $k;
                            $editUrl = $base . '/index.php?option=' . $option . '&controller=' . $controller . '&task=edit&pid=' . $rid . '&filename=' . $images[$i]['img'] . '&version=' . $version . '&tmpl=component';
                            $delUrl = $base . '/index.php?option=' . $option . '&controller=' . $controller . '&task=delete&pid=' . $rid . '&filename=' . $images[$i]['img'] . '&version=' . $version . '&tmpl=component';
                        @endphp
                        <li class="relative">
                            <span class="dev_ss">
                                <a href="{{ $editUrl }}" class="icon-edit edit popup">{{ Lang::txt('COM_TOOLS_EDIT') }}</a>
                                <a href="{{ $delUrl }}" class="icon-delete delete">{{ Lang::txt('COM_TOOLS_DELETE') }}</a>
                            </span>
                            <a href="{{ $editUrl }}" class="popup" title="{{ $title }}">
                                <img src="{{ $wpath }}/{{ $tn }}" alt="{{ $title }}" id="ss_{{ $i }}" />
                            </a>
                        </li>
                    @endif

                    @if($i != (count($images) - 1))
                        @php
                            $reorderHref = $base . '/index.php?option=' . $option . '&controller=' . $controller . '&task=order&pid=' . $rid . '&fl=' . $images[$i + 1]['img'] . '&fr=' . $images[$i]['img'] . '&ol=' . ($i + 1) . '&or=' . $i . '&version=' . $version . '&tmpl=component';
                        @endphp
                        <li>
                            <a class="icon-reorder reorder" href="{{ $reorderHref }}" title="{{ Lang::txt('COM_TOOLS_REORDER') }}">
                                {{ Lang::txt('COM_TOOLS_REORDER') }}
                            </a>
                        </li>
                    @endif
                @endif
            @endfor
        </ul>
    @else
        <p>{{ Lang::txt('COM_TOOLS_UPLOAD_NO_SS') }}</p>
    @endif

    <h3>{{ Lang::txt('COM_TOOLS_UPLOAD_NEW_SS') }}</h3>
    <fieldset class="uploading">
        <label>
            <input type="file" name="upload" />
        </label>
        <label for="title">
            {{ Lang::txt('COM_TOOLS_SS_TITLE') }}:
            <input type="text" name="title" size="127" maxlength="127" value="" class="input input-bordered input-sm w-full max-w-xs" />
            <input type="submit" class="btn btn-primary btn-sm" value="{{ strtolower(Lang::txt('COM_TOOLS_UPLOAD')) }}" />
        </label>

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="tmpl" value="component" />
        <input type="hidden" name="changing_version" value="0" />
        <input type="hidden" name="version" id="version" value="{{ $version }}" />
        <input type="hidden" name="pid" id="pid" value="{{ $rid }}" />
        <input type="hidden" name="path" id="path" value="{{ $upath }}" />
        <input type="hidden" name="task" value="upload" />
    </fieldset>
</form>

@if($published && $version == 'dev')
    <form action="{{ Route::url('index.php?option=' . $option) }}"
          name="copySSForm" method="post" enctype="multipart/form-data">
        <fieldset>
            <legend>{{ Lang::txt('COM_TOOLS_COPY_SCREENSHOTS') }}</legend>
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="version" value="{{ $version }}" />
            <input type="hidden" name="controller" value="{{ $controller }}" />
            <input type="hidden" name="task" value="copy" />
            <input type="hidden" name="rid" value="{{ $rid }}" />
            <input type="hidden" name="tmpl" value="component" />
            <label>
                {{ Lang::txt('COM_TOOLS_FROM') }} {{ $version == 'dev' ? 'current' : 'development' }} {{ strtolower(Lang::txt('COM_TOOLS_VERSION')) }}
                <input type="submit" class="btn btn-sm" value="{{ strtolower(Lang::txt('COM_TOOLS_COPY')) }}" />
            </label>
        </fieldset>
    </form>
@endif
