<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$html = '';

	$url = $this->activechild->path;
	$url = DS . ltrim($url, DS);

	if (substr($url, 0, strlen($this->config->get('uploadpath'))) != $this->config->get('uploadpath'))
	{
		$url = DS . trim($this->config->get('uploadpath'), DS) . $url;
	}

	// Get some attributes
	$attribs = new \Hubzero\Config\Registry($this->activechild->get('attribs'));
	$width  = $attribs->get('width', '');
	$height = $attribs->get('height', '');

	$attributes = $attribs->get('attributes', '');
	if ($attributes)
	{
		$a = explode(',', $attributes);
		$bits = array();
		if ($a && is_array($a))
		{
			foreach ($a as $b)
			{
				if (strstr($b, ':'))
				{
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
	$files  = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pages', 'ai', 'psd', 'tiff', 'dxf', 'eps', 'ps', 'ttf', 'xps', 'zip', 'rar', 'svg');

	$UrlPtn  = "(?:https?:|mailto:|ftp:|gopher:|news:|file:)" . "(?:[^ |\\/\"\']*\\/)*[^ |\\t\\n\\/\"\']*[A-Za-z0-9\\/?=&~_]";

	if (preg_match("/$UrlPtn/", $this->activechild->path))
	{
		$url = $this->activechild->path;
		if (!empty($_SERVER['HTTPS']))
		{
			$url = str_replace('http:', 'https:', $url);
		}
		$parsed = parse_url($url);
		if (stristr($parsed['host'], 'youtube'))
		{
			// YouTube
			if (strstr($url, '?'))
			{
				//split the string into two parts
				//uri and query string
				$full_url_parts = explode('?', $url);

				//split apart any key=>value pairs in query string
				$query_string_parts = explode("%26%2338%3B", urlencode($full_url_parts[1]));

				//foreach query string parts
				//explode at equals sign
				//check to see if v is the first part and if it is set the second part to the video id
				foreach ($query_string_parts as $qsp)
				{
					$pairs_parts = explode("%3D", $qsp);
					if ($pairs_parts[0] == 'v')
					{
						$video_id = $pairs_parts[1];
						break;
					}
				}
				$url = 'https://www.youtube.com/embed/' . $video_id . '?wmode=transparent';
			}
			$html .= '<iframe width="' . ($width ? $width : 640) . '" height="' . ($height ? $height : 360) . '" src="' . $url . '" frameborder="0" allowfullscreen></iframe>';
		}
		else if (stristr($parsed['host'], 'vimeo'))
		{
			$html .= '<iframe width="' . ($width ? $width : 640) . '" height="' . ($height ? $height : 360) . '" src="' . $url . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		}
		else if (stristr($parsed['host'], 'blip'))
		{
			$html .= '<iframe width="' . ($width ? $width : 640) . '" height="' . ($height ? $height : 360) . '" src="' . $url . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		}
		else
		{
			$html .= '<iframe width="' . ($width ? $width : 640) . '" height="' . ($height ? $height : 360) . '" src="' . $url . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
		}
	}
	else if (is_file(PATH_APP . $url))
	{
		$base = substr(PATH_APP, strlen(PATH_ROOT));
		if (substr($url, 0, strlen($base)) != $base)
		{
			$url = $base . $url;
		}

		if (strtolower($type) == 'swf')
		{
			$height = '400px';
			if ($this->no_html)
			{
				$height = '100%';
			}
			$rufle_path =  Component::path('com_resources') . DS . 'site' . DS . 'assets' . DS . 'js' . DS . 'ruffle';
			$rufle_path = (substr($rufle_path, strlen(PATH_ROOT) + 0));
			$html .= '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="https://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,65,0" width="100%" height="'.$height.'" id="SlideContent" VIEWASTEXT>'."\n";
			$html .= ' <param name="movie" value="'. $url .'" />'."\n";
			$html .= ' <param name="quality" value="high" />'."\n";
			$html .= ' <param name="menu" value="false" />'."\n";
			$html .= ' <param name="loop" value="false" />'."\n";
			$html .= ' <param name="scale" value="showall" />'."\n";
			$html .= ' <embed src="'. $url .'" menu="false" quality="best" loop="false" width="100%" height="'.$height.'" scale="showall" name="SlideContent" align="" type="application/x-shockwave-flash" pluginspage="https://www.macromedia.com/go/getflashplayer" swLiveConnect="true"></embed>'."\n";
			$html .= '</object>'."\n";
			$html .= '<script>'."\n";
			$html .= 'window.RufflePlayer = window.RufflePlayer || {};'."\n";
			$html .= 'window.RufflePlayer.config = {'."\n";
			$html .= '  "publicPath": "' . $rufle_path. '",'."\n";
			$html .= '}'."\n";
			$html .= '</script>'."\n";
			$html .= '<script src="' . $rufle_path. '/ruffle.js"></script>'."\n";
        }
		else if (in_array(strtolower($type), $images))
		{
			$html .= '<img ' . $attributes . ' src="' . $url . '" alt="Image" />'."\n";
		}
		else if (in_array(strtolower($type), $files))
		{
			$token = '';

			if (!User::isGuest())
			{
				$session = App::get('session');

				$session_id = $session->getId();

				$key = App::hash(@$_SERVER['HTTP_USER_AGENT']);
				$crypter = new \Hubzero\Encryption\Encrypter(
					new \Hubzero\Encryption\Cipher\Simple,
					new \Hubzero\Encryption\Key('simple', $key, $key)
				);
				$token = base64_encode($crypter->encrypt($session_id));
			}

			$sef = Route::url('index.php?option=com_resources&id='.$this->activechild->id.'&task=download&file='.basename($this->activechild->path).'&token='.$token . '&' . Session::getFormToken() . '=1');

			$html .= '<iframe sandbox="allow-scripts allow-same-origin allow-popups" src="https://docs.google.com/viewer?url=' . urlencode(Request::base() . ltrim($sef, '/')).'&amp;embedded=true#:0.page.0" width="100%" height="500" name="file_resource" frameborder="0" bgcolor="white"></iframe>'."\n";
		}
		else if (in_array(strtolower($type), $videos))
		{
			$html .= '<video controls autoplay ' . $attributes . '>' . "\n";
			$html .= '    <source src="' . $url .  '" type="video/mp4"/>' . "\n";
			$html .= '</video>' . "\n";
		}
		else if (in_array(strtolower($type), $audios))
		{
			$html .= '<audio controls autoplay ' . $attributes . '>' . "\n";
			$html .= '    <source src="' . $url .  '" type="audio/mpeg"/>' . "\n";
			$html .= '</audio>' . "\n";
		}
		else if (strtolower($type) == 'jar')
		{
			$html .= '<applet ' . $attributes . ' archive="'. $url .'" width="';
			$html .= ($width > 0) ? $width : '';
			$html .= '" height="';
			$html .= ($height > 0) ? $height : '';
			$html .= '">'."\n";
			if ($width > 0)
			{
				$html .= ' <param name="width" value="'. $width .'" />'."\n";
			}
			if ($height > 0)
			{
				$html .= ' <param name="height" value="'. $height .'" />'."\n";
			}
			$html .= '</applet>'."\n";
		}
		else
		{
			$html .= '<p class="error">'.Lang::txt('COM_RESOURCES_FILE_BAD_TYPE').'</p>'."\n";
		}
	}
	else
	{
		$html .= '<p class="error">'.Lang::txt('COM_RESOURCES_FILE_NOT_FOUND').'</p>'."\n";
	}

echo $html;
