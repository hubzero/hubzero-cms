{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\App;
use Hubzero\Facades\Config;
use Hubzero\Facades\Date;
use Hubzero\Facades\Filesystem;
use Hubzero\Facades\User;

$__view->css('hubpresenter.css')
    ->js('hubpresenter.js')
    ->js('hubpresenter.plugins.js')
    ->css('jquery.colpick.css', 'system')
    ->js('jquery.colpick', 'system');

// get the manifest for the presentation
$contents = file_get_contents(PATH_APP . DS . $manifest);
// content folder
$content_folder = $content_folder;
$content_url = $content_url;

// decode the json formatted manifest so we can use the information
$presentation = json_decode($contents);
$presentation = $presentation->presentation;
if (!is_object($presentation)) {
    $presentation = new stdClass();
    $presentation->slides = array();
    $presentation->media = array();
    $presentation->placeholder = null;
    $presentation->duration = null;
}

// get this resource
$rr = \Components\Resources\Models\Entry::oneOrFail($resid);

$doc->setTitle(stripslashes($rr->title));

// get the parent resource
$parent = $rr->parents()
    ->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
    ->rows()
    ->first();

// check to see if parent type is series
if ($parent && $parent->id && ($parent->type->get('type') == 'Series' || $parent->type->get('type') == 'Courses')) {
    // if we have a series get children
    $children = $parent->children()
        ->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
        ->rows();

    // remove any children without a HUBpresenter
    foreach ($children as $k => $c) {
        $sub_child = $c->children()
            ->whereEquals('published', \Components\Resources\Models\Entry::STATE_PUBLISHED)
            ->rows();

        $hasHUBpresenter = false;

        foreach ($sub_child as $sc) {
            if (strtolower($sc->type->get('title')) == 'hubpresenter') {
                $hasHUBpresenter = true;
            }
        }

        if (!$hasHUBpresenter) {
            $children->drop($k);
        }
    }
} else {
    $children = null;
}

$lectureAuthors = array();
if ($parent && $parent->id) {
    // get the contributors for the resource
    $sql = "SELECT authorid, role, name FROM `#__author_assoc` "
         . "WHERE subtable='resources' "
         . "AND subid=" . $parent->id . " "
         . "ORDER BY ordering";

    $database = App::get('db');
    $database->setQuery($sql);
    $lectureAuthors = $database->loadObjectList();
}

// get the author names from ids
$a = array();

if (!empty($lectureAuthors)) {
    foreach ($lectureAuthors as $la) {
        // if this is a submitter lets continue
        if ($la->role == 'submitter') {
            continue;
        }
        // load author object
        $author = User::getInstance($la->authorid);
        if (is_object($author) && $author->id) {
            $a[] = '<a href="' . $author->link() . '">' . $author->name . '</a>';
        } else {
            $a[] = $la->name;
        }
    }
}

// check to see if already have subtitles
if (!isset($presentation->subtitles)) {
    $presentation->subtitles = array();
}

// make sure source is full path to assets folder
$subFiles = array();
foreach ($presentation->subtitles as $k => $subtitle) {
    if (!strpos($subtitle->source, DS)) {
        $subtitle->source_url = $content_url . DS . $subtitle->source;
        $subtitle->source = $content_folder . DS . $subtitle->source;
    }

    $subFiles[] = $subtitle->source;
}

// get all local subtitles
$localSubtitles = Filesystem::files(PATH_APP . DS . $content_folder, '.srt|.SRT');

// add local subtitles too
foreach ($localSubtitles as $k => $sub) {
    $info     = pathinfo($sub);
    $name     = str_replace('-auto', '', $info['filename']);
    $autoplay = (strstr($info['filename'], '-auto')) ? 1 : 0;
    $source   = $content_folder . DS . $sub;

    // add each subtitle
    $sub = new stdClass();
    $sub->type     = 'SRT';
    $sub->name     = ucfirst($name);
    $sub->source   = $source;
    $sub->source_url = $content_url . $sub;
    $sub->autoplay = $autoplay;

    // make sure we dont already have this file.
    if (!in_array($sub->source, $subFiles)) {
        $presentation->subtitles[] = $sub;
    }
}

// reset keys
$presentation->subtitles = array_values($presentation->subtitles);

$isHd = isset($presentation->format) && strtoupper($presentation->format) == 'HD';
$presentationFormat = $isHd ? 'presentation-hd' : '';

$isLeftVideo = isset($presentation->videoPosition)
    && $presentation->videoPosition == "left"
    && strtolower($presentation->type) == 'video';
$cls = $isLeftVideo ? "move-left" : "";
@endphp

<div id="presenter-nav-bar">
    <a href="/resources/{{ $rr->id }}" id="powered" title="Powered by {{ Config::get('sitename') }}">
        <span>powered by</span> {{ Config::get('sitename') }}
    </a>

    @if ($children)
        <form name="presentation-picker" id="presentation-picker" method="post">
            <label for="presentation">Select a different presentation:
                <select name="presentation" id="presentation" class="select select-bordered">
                    <optgroup label="{{ e($parent->title) }}">
                        @foreach ($children as $c)
                            @php
                                $isAdmin = $user->get("usertype") == 'Administrator'
                                    || $user->get("usertype") == 'Super Administrator';
                            @endphp
                            @if (Date::toSql() > $c->publish_up || $isAdmin)
                                <option @if ($c->title == $rr->title) selected @endif
                                    value="{{ $c->id }}">{{ e($c->title) }}</option>
                            @endif
                        @endforeach
                    </optgroup>
                </select>
            </label>
            <noscript><input type="submit" name="presentations-submit" value="Go" /></noscript>
            <input type="hidden" name="option" value="com_resources" />
            <input type="hidden" name="task" value="selectpresentation" />
        </form>
    @endif
</div>

<div id="presenter-container" class="{{ $presentationFormat }}" data-id="{{ $resid }}">
    <div id="presenter-header">
        <div id="title">{!! $rr->title !!}</div>
        <div id="author">
            @if ($a)
                {!! 'by: ' . implode(', ', $a) !!}
            @endif
        </div>
    </div>{{-- /#header --}}

    <div id="presenter-content">
        <div id="presenter-left">
            <div id="slides">
                <ul class="no-js">
                    @php $counter = 0; @endphp
                    @foreach ($presentation->slides as $slide)
                        <li id="slide_{{ $counter }}"
                            title="{{ $slide->title }}"
                            time="{{ $slide->time }}">
                            @if ($slide->type == 'Image')
                                <img src="{{ $content_url . DS . $slide->media }}"
                                     alt="{{ $slide->title }}" />
                            @else
                                <video class="slidevideo" preload="metadata" muted>
                                    @foreach ($slide->media as $source)
                                        <source src="{{ $content_url . DS . $source->source }}" />
                                    @endforeach
                                    <a href="{{ $content_url . DS . $slide->media[0]->source }}"
                                       class="flowplayer_slide"
                                       id="flowplayer_slide_{{ $counter }}"></a>
                                </video>
                                <img src="{{ $content_url . DS . $slide->media[3]->source }}"
                                     alt="{{ $slide->title }}"
                                     class="imagereplacement">
                            @endif
                        </li>
                        @php $counter++; @endphp
                    @endforeach
                </ul>
            </div>{{-- /#slides --}}
            <div id="control-box" class="no-controls" data-theme="dark">
                <div id="progress-bar"></div>
                <div id="control-buttons">
                    <div id="control-buttons-left" class="cf">
                        <button type="button" id="previous"
                            class="tooltips control"
                            aria-label="Previous Slide"
                            title="Previous Slide">Previous</button>
                        <button type="button" id="play-pause"
                            class="tooltips control"
                            aria-label="Play/Pause"
                            title="Play Presentation">Pause</button>
                        <button type="button" id="next"
                            class="tooltips control"
                            aria-label="Next Slide"
                            title="Next Slide">Next</button>
                        <div id="media-progress"></div>
                    </div>
                    <div id="control-buttons-right" class="cf">
                        <button type="button" id="subtitle"
                            class="tooltips control"
                            aria-label="Subtitles and Captions">
                            Subtitles/Captions
                        </button>
                        <div class="control-container subtitle-controls">
                            <h3>Captions/Transcript</h3>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="label">
                                    <label for="subtitle-selector">Captions:</label>
                                </div>
                                <div class="col-span-2 input">
                                    <select id="subtitle-selector"
                                        class="select select-bordered select-sm">
                                        <option value="">None/Off</option>
                                    </select>
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-2">
                                <div class="label">
                                    <label for="transcript-selector">Transcript:</label>
                                </div>
                                <div class="col-span-2 input">
                                    <select class="transcript-selector select select-bordered select-sm">
                                        <option value="">None/Off</option>
                                    </select>
                                </div>
                            </div>

                            <span class="options-toggle">Options</span>
                            <div class="subtitle-settings hide">
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="label">
                                        <label for="font-selector">Font:</label>
                                    </div>
                                    <div class="input">
                                        <select id="font-selector"
                                            class="select select-bordered select-sm">
                                            <option value="Arial" selected>Arial</option>
                                            <option value="Times New Roman">Times New Roman</option>
                                            <option value="Tahoma">Tahoma</option>
                                            <option value="Trebuchet MS">Trebuchet MS</option>
                                            <option value="Verdana">Verdana</option>
                                            <option value="Courier New">Courier New</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="label">
                                        <label for="font-size-selector">Font Size:</label>
                                    </div>
                                    <div class="input">
                                        <select id="font-size-selector"
                                            class="select select-bordered select-sm">
                                            <option value="12">Small</option>
                                            <option value="18" selected>Medium</option>
                                            <option value="24">Large</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="label">
                                        <label for="font-color">Font Color:</label>
                                    </div>
                                    <div class="input">
                                        <div id="font-color" data-color="FFF"></div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div class="label">
                                        <label for="background-color">Background:</label>
                                    </div>
                                    <div class="input">
                                        <div id="background-color" data-color="000"></div>
                                    </div>
                                </div>
                                <div class="subtitle-settings-preview-container">
                                    <div class="subtitle-settings-preview">
                                        <div class="test">This is an Example</div>
                                    </div>
                                </div>
                                <div class="actions">
                                    <button type="button" class="btn btn-primary"
                                        id="subtitle-settings-save">Save</button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="volume"
                            class="control"
                            aria-label="Volume">
                            Volume
                        </button>
                        <div class="control-container volume-controls">
                            <div id="volume-bar"></div>
                        </div>

                        <button type="button" id="settings"
                            class="control"
                            aria-label="Playback Settings"
                            title="Adjust Settings for Playback">
                            Settings
                        </button>
                        <div class="control-container settings-controls">
                            <h3>Settings</h3>
                            <div class="grid grid-cols-2 gap-2">
                                <div class="label">
                                    <label for="speed">Playback Rate:</label>
                                </div>
                                <div class="input">
                                    <select id="speed"
                                        class="select select-bordered select-sm">
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

                        <button type="button" id="link"
                            class="control"
                            aria-label="Share Link"
                            title="Link to this Spot in Presentation">
                            Link
                        </button>
                        <div class="control-container link-controls">
                            <h3>Link to Video <span>- at current position</span></h3>
                            <div>
                                <input type="text" value="ss"
                                    class="input input-bordered input-sm"
                                    aria-label="Shareable link" />
                                <span class="hint">(Command/Ctrl + C to Copy)</span>
                            </div>
                        </div>

                        <button type="button" id="switch"
                            class="tooltips control"
                            aria-label="Switch Video and Slide Placement"
                            title="Switch Placement of Video and Slides">Switch</button>
                    </div>
                </div>
            </div>{{-- /#control-box --}}
        </div>{{-- /#left --}}

        <div id="presenter-right">
            <div id="media" class="{{ $cls }}">
                @if (strtolower($presentation->type) == 'video')
                    <video id="player" preload="auto" controls="controls"
                        data-mediaid="{{ $rr->id }}">
                        @foreach ($presentation->media as $media)
                            @php
                                switch ($media->type) {
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

                                // get the source
                                $source = $media->source;

                                // is this the mp4 (need for flash)
                                if (in_array($media->type, array('mp4', 'm4v'))) {
                                    $mp4 = $media->source;
                                }

                                // if were playing local files
                                if (substr($media->source, 0, 4) != 'http') {
                                    $source = $content_url . DS . $source;
                                    if (in_array($media->type, array('mp4', 'm4v'))) {
                                        $mp4 = $content_url . DS . $mp4;
                                    }
                                }
                            @endphp
                            <source src="{{ $source }}" type="{{ $type }}">
                        @endforeach

                        <a href="{{ $mp4 }}"
                            id="flowplayer"
                            data-mediaid="{{ $rr->id }}"></a>
                        @if (count($presentation->subtitles) > 0)
                            @foreach ($presentation->subtitles as $subtitle)
                                @php
                                    // get file modified time
                                    $source = $subtitle->source;
                                    $source_url = $subtitle->source_url;
                                    $auto = $subtitle->autoplay;

                                    // if were playing local files
                                    if (substr($subtitle->source, 0, 4) != 'http') {
                                        $modified = filemtime(PATH_APP . DS . $source);
                                    } else {
                                        $modified = '123456789';
                                    }
                                @endphp
                                <div
                                    data-autoplay="{{ $auto }}"
                                    data-type="subtitle"
                                    data-lang="{{ $subtitle->name }}"
                                    data-src="{{ $source_url }}?v={{ $modified }}"></div>
                            @endforeach
                        @endif
                    </video>
                @else
                    <audio id="player" preload="auto" controls="controls"
                        data-mediaid="{{ $rr->id }}">
                        @foreach ($presentation->media as $source)
                            @php
                                switch ($source->type) {
                                    case 'mp3':
                                        $type = 'audio/mp3';
                                        break;
                                    case 'ogv':
                                    case 'ogg':
                                        $type = 'audio/ogg';
                                        break;
                                }
                            @endphp
                            <source src="{{ $content_url . DS . $source->source }}"
                                type="{{ $type }}" />
                        @endforeach
                        <a href="{{ $content_url . DS . $presentation->media[0]->source }}"
                           id="flowplayer"
                           @if (isset($presentation->duration) && $presentation->duration)
                               duration="{{ $presentation->duration }}"
                           @endif
                           data-mediaid="{{ $rr->id }}"></a>
                    </audio>

                    @if (isset($presentation->placeholder) && $presentation->placeholder)
                        <img src="{{ $content_url . DS . $presentation->placeholder }}"
                             title=""
                             id="placeholder" />
                    @endif
                @endif
                <div id="video-subtitles"></div>
            </div>
            <div id="list">
                <ul id="list_items">
                    @php
                        $num = 0;
                        $counter = 0;
                        $last_slide_id = 0;
                    @endphp
                    @foreach ($presentation->slides as $slide)
                        @if ((int)$slide->slide != $last_slide_id)
                            <li id="list_{{ $counter }}">
                                @php
                                    // use thumb if possible
                                    $thumb = $content_folder . DS
                                        . (is_array($slide->media) ? $slide->media[0] : $slide->media);
                                    if (
                                        isset($slide->thumb)
                                        && $slide->thumb
                                        && file_exists(PATH_APP . DS . $content_folder . DS . $slide->thumb)
                                    ) {
                                        $thumb = $content_url . DS . $slide->thumb;
                                    }
                                @endphp
                                <img src="{{ $thumb }}" alt="{{ $slide->title }}" />
                                <span>
                                    @php
                                        $num++;
                                        $max = 30;
                                        $elipsis = '&hellip;';
                                    @endphp
                                    {!! $num . '. ' . substr($slide->title, 0, $max) !!}@if (strlen($slide->title) > $max){!! $elipsis !!}@endif
                                </span>
                                <span class="time">{{ $slide->time }}</span>
                                <div id="list-slider-{{ $counter }}" class="list-slider"></div>
                                <div class="list-progress">00:00/00:00</div>
                            </li>
                        @endif
                        @php
                            $last_slide_id = $slide->slide;
                            $counter++;
                        @endphp
                    @endforeach
                </ul>
            </div>
        </div>{{-- /#right --}}
    </div>{{-- /#content --}}

    <div id="transcript-container">
        <div id="transcript-toolbar">
            <div id="transcript-select"></div>
            <input type="text" id="transcript-search"
                class="input input-bordered input-sm"
                aria-label="Search Transcript"
                placeholder="Search Transcript..." />
            <button type="button" id="font-bigger"
                aria-label="Increase font size"></button>
            <button type="button" id="font-smaller"
                aria-label="Decrease font size"></button>
        </div>
        <div id="transcripts"></div>
    </div>
    <div class="bottom-controls">
        <button type="button" class="btn btn-outline"
            aria-label="Pop out">Pop Out</button>
    </div>
</div>
