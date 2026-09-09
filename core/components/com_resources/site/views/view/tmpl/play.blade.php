{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\User;

$source = $activechild->basepath() . '/' . $activechild->path;
$url = '/resources/' . $activechild->id . '/download/' . $activechild->relativeurl();

// Get some attributes
$attribs = new \Hubzero\Config\Registry($activechild->get('attribs'));
$width  = $attribs->get('width', '');
$height = $attribs->get('height', '');

$attributes = $attribs->get('attributes', '');
if ($attributes) {
    $a = explode(',', $attributes);
    $bits = array();
    if ($a && is_array($a)) {
        foreach ($a as $b) {
            if (strstr($b, ':')) {
                $b = preg_split('#:#', $b);
                $bits[] = trim($b[0]) . '="' . trim($b[1]) . '"';
            }
        }
    }
    $attributes = implode(' ', $bits);
}

$type = '';
$arr  = explode('.', $url);
$type = end($arr);
$type = (strlen($type) > 4) ? 'html' : $type;
$type = (strlen($type) > 3) ? substr($type, 0, 3) : $type;

$width  = (intval($width) > 0) ? $width : 0;
$height = (intval($height) > 0) ? $height : 0;

$videos = array('mp4');
$audios = array('mp3');
$images = array('png', 'jpeg', 'jpe', 'jpg', 'gif', 'bmp');
$files  = array(
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pages',
    'ai', 'psd', 'tiff', 'dxf', 'eps', 'ps', 'ttf', 'xps',
    'zip', 'rar', 'svg'
);

$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)"
    .  "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";

$html = '';
$isExternalUrl = preg_match("/$UrlPtn/", $activechild->path);
@endphp

@if ($isExternalUrl)
    @php
        $url = $activechild->path;
        if (!empty($_SERVER['HTTPS'])) {
            $url = str_replace('http:', 'https:', $url);
        }
        $parsed = parse_url($url);
        $iframeWidth = $width ? $width : 640;
        $iframeHeight = $height ? $height : 360;

        if (stristr($parsed['host'], 'youtube')) {
            if (strstr($url, '?')) {
                $full_url_parts = explode('?', $url);
                $query_string_parts = explode("%26%2338%3B", urlencode($full_url_parts[1]));
                foreach ($query_string_parts as $qsp) {
                    $pairs_parts = explode("%3D", $qsp);
                    if ($pairs_parts[0] == 'v') {
                        $video_id = $pairs_parts[1];
                        break;
                    }
                }
                $url = 'https://www.youtube.com/embed/' . $video_id . '?wmode=transparent';
            }
        }
    @endphp
    <iframe
        width="{{ $iframeWidth }}"
        height="{{ $iframeHeight }}"
        src="{{ $url }}"
        frameborder="0"
        webkitAllowFullScreen
        mozallowfullscreen
        allowFullScreen
    ></iframe>
@elseif (is_file($source))
    @if (strtolower($type) == 'swf')
        @php
            $swfHeight = '400px';
            if ($no_html) {
                $swfHeight = '100%';
            }
            $rufle_path = Component::path('com_resources')
                . DS . 'site' . DS . 'assets' . DS . 'js' . DS . 'ruffle';
            $rufle_path = substr($rufle_path, strlen(PATH_ROOT));

            $ruffleConfig = ['publicPath' => $rufle_path];

            $__view->js($rufle_path . '/ruffle.js');
        @endphp
        <object
            classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
            codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0"
            width="100%"
            height="{{ $swfHeight }}"
            id="SlideContent"
            VIEWASTEXT
            data-ruffle-config='{{ json_encode($ruffleConfig) }}'
            data-ruffle-src="{{ $rufle_path }}/ruffle.js"
        >
            <param name="movie" value="{{ $url }}" />
            <param name="quality" value="high" />
            <param name="menu" value="false" />
            <param name="loop" value="false" />
            <param name="scale" value="showall" />
            <embed
                src="{{ $url }}"
                menu="false"
                quality="best"
                loop="false"
                width="100%"
                height="{{ $swfHeight }}"
                scale="showall"
                name="SlideContent"
                align=""
                type="application/x-shockwave-flash"
                pluginspage="https://www.macromedia.com/go/getflashplayer"
                swLiveConnect="true"
            ></embed>
        </object>
    @elseif (in_array(strtolower($type), $images))
        <img
            {!! $attributes !!}
            src="{{ $url }}"
            alt="{{ e(stripslashes($activechild->title ?? $resource->title ?? 'Resource')) }}"
        />
    @elseif (in_array(strtolower($type), $files))
        @php
            $token = '';
            if (!User::isGuest()) {
                $session = App::get('session');
                $session_id = $session->getId();
                $key = App::hash(@$_SERVER['HTTP_USER_AGENT']);
                $crypter = new \Hubzero\Encryption\Encrypter(
                    new \Hubzero\Encryption\Cipher\Simple(),
                    new \Hubzero\Encryption\Key('simple', $key, $key)
                );
                $token = base64_encode($crypter->encrypt($session_id));
            }

            $sef = Route::url(
                'index.php?option=com_resources&id=' . $activechild->id
                . '&task=download&file=' . basename($activechild->path)
                . '&token=' . $token
                . '&' . Session::getFormToken() . '=1'
            );
            $viewerUrl = 'https://docs.google.com/viewer?url='
                . urlencode(Request::base() . ltrim($sef, '/'))
                . '&embedded=true#:0.page.0';
        @endphp
        <iframe
            sandbox="allow-scripts allow-same-origin allow-popups"
            src="{{ $viewerUrl }}"
            width="100%"
            height="500"
            name="file_resource"
            frameborder="0"
            bgcolor="white"
        ></iframe>
    @elseif (in_array(strtolower($type), $videos))
        <video controls autoplay {!! $attributes !!}>
            <source src="{{ $url }}" type="video/mp4" />
        </video>
    @elseif (in_array(strtolower($type), $audios))
        <audio controls autoplay {!! $attributes !!}>
            <source src="{{ $url }}" type="audio/mpeg" />
        </audio>
    @elseif (strtolower($type) == 'jar')
        <applet
            {!! $attributes !!}
            archive="{{ $url }}"
            width="{{ ($width > 0) ? $width : '' }}"
            height="{{ ($height > 0) ? $height : '' }}"
        >
            @if ($width > 0)
                <param name="width" value="{{ $width }}" />
            @endif
            @if ($height > 0)
                <param name="height" value="{{ $height }}" />
            @endif
        </applet>
    @else
        <div role="alert" class="alert alert-error">{{ Lang::txt('COM_RESOURCES_FILE_BAD_TYPE') }}</div>
    @endif
@else
    <div role="alert" class="alert alert-error">{{ Lang::txt('COM_RESOURCES_FILE_NOT_FOUND') }}</div>
@endif
