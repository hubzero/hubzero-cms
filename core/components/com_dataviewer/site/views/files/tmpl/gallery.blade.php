@php
/**
 * Gallery view template (Blade / daisyUI).
 *
 * Renders an image gallery with thumbnails, viewer, and toolbar.
 * This is a standalone page (not embedded in the site template).
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Variables are passed directly by Blade: $imageList, $imageViewer, $htmlPath
@endphp
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>{{ Lang::txt('COM_DATAVIEWER_GALLERY_TITLE') }}</title>
        <link rel="stylesheet" type="text/css"
            href="/core/assets/css/jquery.ui.min.css" />
        <link rel="stylesheet" type="text/css"
            href="{{ $htmlPath }}/css/gallery.css" />
        <script src="/core/assets/js/jquery.js"></script>
        <script src="/core/assets/js/jquery.ui.min.js"></script>
        <script src="{{ $htmlPath }}/js/gallery.js"></script>
    </head>
    <body>
    <div id="dv_wrapper" class="ui-widget ui-widget-content ui-corner-all">
        <div id="dv_gallery_list" class="ui-widget ui-widget-header ui-corner-top">
            <table class="p-0 m-0">
                <tr>
                    @foreach ($imageList as $img)
                    <td>{!! $img !!}</td>
                    @endforeach
                </tr>
            </table>
        </div>

        <div id="dv_gallery_viewer">
            <br />
            {!! implode("\n", $imageViewer) !!}
            <br />
            <div id="dv_gallery_desc" class="ui-widget ui-widget-content ui-corner-all"
                style="display:none; margin: 0 20px; border-style: inset;">
                {{ Lang::txt('COM_DATAVIEWER_GALLERY_DESC_PLACEHOLDER') }}
            </div>
            <br />
        </div>

        <div class="dv_gallery_toolbar ui-widget ui-widget-header ui-corner-bottom">
            <span id="dv_gallery_dl_image">
                <a href="" target="_blank">
                    <img src="{{ $htmlPath }}/img/download-l.png"
                        alt="{{ Lang::txt('COM_DATAVIEWER_GALLERY_DOWNLOAD_ALT') }}"
                        title="{{ Lang::txt('COM_DATAVIEWER_GALLERY_DOWNLOAD_TITLE') }}"
                        style="border: 1px #DDD solid;" />
                </a>
            </span>
            &nbsp;
            <input type="checkbox" id="description" /><label for="description">{{ Lang::txt('COM_DATAVIEWER_GALLERY_DESCRIPTION') }}</label>
            [ <span id="color">{{ Lang::txt('COM_DATAVIEWER_GALLERY_BACKGROUND') }}
                <input type="radio" id="color1" name="color" value="#3C3C3C" checked="checked" />
                <label for="color1">{{ Lang::txt('COM_DATAVIEWER_GALLERY_DARK') }}</label>
                <input type="radio" id="color2" name="color" value="#ECECEC" />
                <label for="color2">{{ Lang::txt('COM_DATAVIEWER_GALLERY_LIGHT') }}</label>
            </span> ]
            &nbsp;&nbsp;&nbsp;
            <button type="button" id="dv-gallery-close" class="btn btn-xs btn-error">{{ Lang::txt('COM_DATAVIEWER_GALLERY_CLOSE') }}</button>
        </div>
    </div>
    <script>
        document.getElementById('dv-gallery-close').addEventListener('click', function() {
            window.close();
        });
    </script>
    </body>
</html>
