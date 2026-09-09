{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Document;

$__view->css('video.css')
     ->js('video.js')
     ->js('hubpresenter.plugins.js')
     ->css('jquery.colpick.css', 'system')
     ->js('jquery.colpick', 'system');

// base url for the resource
$base = PATH_APP . DS . trim($config->get('uploadpath'), DS);
$base = substr($base, strlen(PATH_ROOT));

// presentation manifest
$presentation = $manifest->presentation;

// determine height and width
$width  = (isset($presentation->width) && $presentation->width != 0) ? $presentation->width . 'px' : 'auto';
$height = (isset($presentation->height) && $presentation->height != 0) ? $presentation->height . 'px' : 'auto';
@endphp

<div id="video-container" class="paused">
    @if (count($presentation->media) > 0)
        <video webkit-playsinline
               playsinline
               controls="controls"
               id="video-player"
               data-mediaid="{{ $resource->id }}">
            @foreach ($presentation->media as $video)
                @php
                switch ($video->type) {
                    case 'ogg':
                    case 'ogv':
                        $type = "video/ogg;";
                        break;
                    case 'webm':
                        $type = "video/webm;";
                        break;
                    case 'mp4':
                    case 'm4v':
                    default:
                        $type = "video/mp4;";
                        break;
                }

                // video source
                $source = $video->source;

                $pattern = '/^(.*?)(\/+)(app\/+site\/+resources)'
                    . '(\/+)([12]\d\d\d)(\/+)(0\d|1[012])'
                    . '(\/+)(\d{5})\/*(.*)$/m';
                if (preg_match($pattern, $source, $matches)) {
                    $url = '/resources/' . $matches[9] . '/download/' . $matches[10];
                } else {
                    $url = $source;
                }
                @endphp
                <source src="{{ $url }}" type="{{ $type }}" />
            @endforeach

            <a href="{{ $url }}"
                id="video-flowplayer"
                style="width: {{ $width }}; height: {{ $height }};"
                data-mediaid="{{ $resource->id }}"></a>

            @if (count($presentation->subtitles) > 0)
                @foreach ($presentation->subtitles as $subtitle)
                    @php
                        // get file modified time
                        $source = $subtitle->source;
                        $auto   = $subtitle->autoplay;

                        // if were playing local files
                        $modified = '123456789';
                        if (substr($subtitle->source, 0, 4) != 'http') {
                            $source   = $base . $source;
                            if (file_exists(PATH_CORE . $source)) {
                                $modified = filemtime(PATH_CORE . $source);
                            }
                        }
                    @endphp
                    <div
                        data-autoplay="{{ $auto }}"
                        data-type="subtitle"
                        data-lang="{{ $subtitle->name }}"
                        data-src="{{ $source }}?v={{ $modified }}"></div>
                @endforeach
            @endif
        </video>
    @endif

    <div id="control-box" class="no-controls" data-theme="dark">
        <div id="progress-bar"></div>
        <div id="control-buttons">
            <div id="control-buttons-left" class="cf">
                <button type="button"
                   id="play-pause"
                   class="tooltips control paused"
                   aria-label="Play/Pause"
                   title="Play Presentation">Pause</button>
                <div id="media-progress"></div>
            </div>
            <div id="control-buttons-right" class="cf">
                <button type="button" id="subtitle" class="tooltips control" aria-label="Subtitles and Captions">
                    Subtitles/Captions
                    <div class="control-container subtitle-controls">
                        <h3>Captions/Transcript</h3>
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-4 label">
                                <label for="subtitle-selector">Captions:</label>
                            </div>
                            <div class="col-span-8 input">
                                <select id="subtitle-selector" class="select select-bordered select-sm">
                                    <option value="">None/Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-4 label">
                                <label for="transcript-selector">Transcript:</label>
                            </div>
                            <div class="col-span-8 input">
                                <select class="transcript-selector select select-bordered select-sm">
                                    <option value="">None/Off</option>
                                </select>
                            </div>
                        </div>

                        <span class="options-toggle">Options</span>
                        <div class="subtitle-settings hide">
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-6 label">
                                    <label for="font-selector">Font:</label>
                                </div>
                                <div class="col-span-6 input">
                                    <select id="font-selector" class="select select-bordered select-sm">
                                        <option value="Arial" selected>Arial</option>
                                        <option value="Times New Roman">Times New Roman</option>
                                        <option value="Tahoma">Tahoma</option>
                                        <option value="Trebuchet MS">Trebuchet MS</option>
                                        <option value="Verdana">Verdana</option>
                                        <option value="Courier New">Courier New</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-6 label">
                                    <label for="font-size-selector">Font Size:</label>
                                </div>
                                <div class="col-span-6 input">
                                    <select id="font-size-selector" class="select select-bordered select-sm">
                                        <option value="12">Small</option>
                                        <option value="18" selected>Medium</option>
                                        <option value="24">Large</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-6 label">
                                    <label for="font-color">Font Color:</label>
                                </div>
                                <div class="col-span-6 input">
                                    <div id="font-color" data-color="FFF"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-6 label">
                                    <label for="background-color">Background:</label>
                                </div>
                                <div class="col-span-6 input">
                                    <div id="background-color" data-color="000"></div>
                                </div>
                            </div>
                            <div class="grid grid-cols-12 gap-2">
                                <div class="col-span-12 subtitle-settings-preview-container">
                                    <div class="subtitle-settings-preview">
                                        <div class="test">This is an Example</div>
                                    </div>
                                </div>
                            </div>
                            <div class="actions">
                                <button class="btn btn-primary"
                                        id="subtitle-settings-save">Save</button>
                            </div>
                        </div>
                    </div>
                </button>
                <button type="button" id="volume" class="tooltips control" aria-label="Volume">
                    Volume
                    <div class="control-container volume-controls">
                        <div id="volume-bar"></div>
                    </div>
                </button>
                <button type="button"
                   id="settings"
                   class="tooltips control"
                   aria-label="Playback Settings"
                   title="Adjust Settings for Playback">
                    Settings
                    <div class="control-container settings-controls">
                        <h3>Settings</h3>
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-6 label">
                                <label for="speed">Playback Rate:</label>
                            </div>
                            <div class="col-span-6 input">
                                <select id="speed" class="select select-bordered select-sm">
                                    <option value=".25">.25</option>
                                    <option value=".5">.5</option>
                                    <option selected value="1">Normal</option>
                                    <option value="1.25">1.25</option>
                                    <option value="1.5">1.5</option>
                                    <option value="2">2</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </button>
                <button type="button"
                   id="link"
                   class="tooltips control"
                   aria-label="Share Link"
                   title="Link to this Spot in Presentation">
                    Link
                    <div class="control-container link-controls">
                        <h3>Link to Video <span>- at current position</span></h3>
                        <div class="grid grid-cols-12 gap-2">
                            <div class="col-span-12">
                                <input type="text" value="ss" />
                                <span class="hint">(Command/Ctrl + C to Copy)</span>
                            </div>
                        </div>
                    </div>
                </button>
            </div>
        </div>
    </div>{{-- /#control-box --}}

    <div id="video-subtitles"></div>
</div>{{-- /#video-container --}}

<div id="transcript-container">
    <div id="transcript-toolbar">
        <div id="transcript-select"></div>
        <input type="text" id="transcript-search" class="input input-bordered input-sm" placeholder="Search Transcript..." />
        <button type="button" id="font-bigger" aria-label="Increase font size"></button>
        <button type="button" id="font-smaller" aria-label="Decrease font size"></button>
    </div>
    <div id="transcripts"></div>
</div>
<div class="bottom-controls">
    <button type="button" class="btn btn-outline embed-popout">Pop Out</button>
</div>
@php
Document::setTitle($resource->title);
@endphp
