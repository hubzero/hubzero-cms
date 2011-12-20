<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'Hubzero_View_Helper_Html'
 * 
 * Long description (if any) ...
 */
class Hubzero_View_Helper_Html
{

	/**
	 * Short description for 'error'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function error($msg, $tag='p')
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}

	/**
	 * Short description for 'warning'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @param      string $tag Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function warning($msg, $tag='p')
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'."\n";
	}

	/**
	 * Short description for 'div'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $txt Parameter description (if any) ...
	 * @param      string $cls Parameter description (if any) ...
	 * @param      string $id Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>';
		$html .= ($txt != '') ? "\n".$txt."\n" : '';
		$html .= '</div><!-- / ';
		if ($id)
		{
			$html .= '#'.$id;
		}
		if ($cls)
		{
			$html .= '.'.$cls;
		}
		$html .= ' -->'."\n";
		return $html;
	}

	/**
	 * Short description for 'hed'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $level Parameter description (if any) ...
	 * @param      string $words Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function hed( $level, $words, $class='' )
	{
		$html  = '<h'.$level;
		$html .= ($class) ? ' class="'.$class.'"' : '';
		$html .= '>'.$words.'</h'.$level.'>'."\n";
		return $html;
	}

	/**
	 * Short description for 'xhtml'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function xhtml( $text )
	{
		$text = stripslashes($text);
		$text = strip_tags($text);
		$text = str_replace('&amp;', '&', $text);
		$text = str_replace('&', '&amp;', $text);
		$text = str_replace('&amp;quot;', '&quot;', $text);
		$text = str_replace('&amp;lt;', '&lt;', $text);
		$text = str_replace('&amp;gt;', '&gt;', $text);

		return $text;
	}

	/**
	 * Short description for 'shortenText'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $text Parameter description (if any) ...
	 * @param      integer $chars Parameter description (if any) ...
	 * @param      integer $p Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function shortenText($text, $chars=300, $p=1, $striptags=1)
	{
		if ($striptags)
		{
			$text = strip_tags($text);
		}
		$text = trim($text);

		if (strlen($text) > $chars)
		{
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' &#8230;';
		}
		if ($text == '')
		{
			$text = '&#8230;';
		}
		if ($p)
		{
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}

	/**
	 * Short description for 'purifyText'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$text Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function purifyText( &$text )
	{
		$text = stripslashes($text);
		$text = preg_replace( '/{kl_php}(.*?){\/kl_php}/s', '', $text );
		$text = preg_replace( '/{.+?}/', '', $text );
		$text = preg_replace( "'<script[^>]*>.*?</script>'si", '', $text );
		$text = preg_replace( '/<a\s+.*?href="([^"]+)"[^>]*>([^<]+)<\/a>/is', '\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text );
		$text = preg_replace( '/&nbsp;/', ' ', $text );
		$text = preg_replace( '/&amp;/', ' ', $text );
		$text = preg_replace( '/&quot;/', ' ', $text );
		$text = str_replace("\n",' ',$text);
		$text = str_replace("\r",' ',$text);
		$text = str_replace("\t",' ',$text);
		$text = strip_tags( $text );
		return $text;
	}

	/**
	 * Short description for 'str_highlight'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @param      array $needles Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function str_highlight($text, $needles)
	{
		$highlight = '<span class="highlight">\1</span>';

		$pattern = '#(?!<.*?)(%s)(?![^<>]*?>)#i';
		$sl_pattern = '#<a\s(?:.*?)>(%s)</a>#i';

		foreach ($needles as $needle)
		{
			$needle = preg_quote($needle);
			$regex = sprintf($pattern, $needle);
			$text = preg_replace($regex, $highlight, $text);
		}
		return $text;
	}

	/**
	 * Short description for 'ampReplace'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $text Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function ampReplace( $text )
	{
		$text = str_replace( '&&', '*--*', $text );
		$text = str_replace( '&#', '*-*', $text );
		$text = str_replace( '&amp;', '&', $text );
		$text = preg_replace( '|&(?![\w]+;)|', '&amp;', $text );
		$text = str_replace( '*-*', '&#', $text );
		$text = str_replace( '*--*', '&&', $text );

		return $text;
	}

	/**
	 * Short description for 'mkt'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $stime Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function mkt($stime)
	{
		if ($stime && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}

	/**
	 * Short description for 'timeAgoo'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      number $timestamp Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = time();

		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;

		// Set the periods of time
		$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');

		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);

		// Ensure the script has found a match
		if ($val < 0) $val = 0;

		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);

		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1) $periods[$val].= 's';

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);

		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)) {
			$text .= Hubzero_View_Helper_Html::timeAgoo($new_time);
		}

		return $text;
	}

	/**
	 * Short description for 'timeAgo'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $timestamp Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function timeAgo($timestamp)
	{
		$text = Hubzero_View_Helper_Html::timeAgoo($timestamp);

		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
	}

	/**
	 * Short description for 'niceidformat'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $someid Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public static function niceidformat($someid)
	{
		while (strlen($someid) < 5)
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}

	/**
	 * Short description for 'thumbit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $thumb Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public static function thumbit($thumb)
	{
		$image = explode('.',$thumb);
		$n = count($image);
		$image[$n-2] .= '_thumb';
		$end = array_pop($image);
		$image[] = $end;
		$thumb = implode('.',$image);

		return $thumb;
	}

	/**
	 * Short description for 'getFileAttribs'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $path Parameter description (if any) ...
	 * @param      string $base_path Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public static function getFileAttribs( $path, $base_path='' )
	{
		// Return nothing if no path provided
		if (!$path) {
			return '';
		}

		if ($base_path) {
			// Strip any trailing slash
			if (substr($base_path, -1) == DS) {
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure a starting slash
			if (substr($base_path, 0, 1) != DS) {
				$base_path = DS.$base_path;
			}
		}

		// Ensure a starting slash
		if (substr($path, 0, 1) != DS) {
			$path = DS.$path;
		}
		if (substr($path, 0, strlen($base_path)) == $base_path) {
			// Do nothing
		} else {
			$path = $base_path.$path;
		}
		$path = JPATH_ROOT.$path;

		$file_name_arr = explode(DS,$path);
	    $type = end($file_name_arr);

		$fs = '';

		// Get the file size if the file exist
		if (file_exists( $path )) {
			$fs = filesize( $path );
		}

		$html  = '<span class="caption">('.$type;
		if ($fs) {
			switch ($type)
			{
				case 'HTM':
				case 'HTML':
				case 'PHP':
				case 'ASF':
				case 'SWF': $fs = ''; break;
				default:
					$fs = Hubzero_View_Helper_Html::formatSize($fs);
					break;
			}

			$html .= ($fs) ? ', '.$fs : '';
		}
		$html .= ')</span>';

		return $html;
	}

	/**
	 * Short description for 'formatSize'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $file_size Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function formatSize($file_size)
	{
		if ($file_size >= 1073741824) {
			$file_size = round($file_size / 1073741824 * 100) / 100 . ' <abbr title="gigabytes">Gb</abbr>';
		} elseif ($file_size >= 1048576) {
			$file_size = round($file_size / 1048576 * 100) / 100 . ' <abbr title="megabytes">Mb</abbr>';
		} elseif ($file_size >= 1024) {
			$file_size = round($file_size / 1024 * 100) / 100 . ' <abbr title="kilobytes">Kb</abbr>';
		} else {
			$file_size = $file_size . ' <abbr title="bytes">b</abbr>';
		}
		return $file_size;
	}

	/**
	 * Short description for 'filesize_r'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $path Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public static function filesize_r($path)
	{
		if (!file_exists($path)) {
			return 0;
		}
		if (is_file($path)) {
			return filesize($path);
		}
		$ret = 0;
		foreach (glob($path."/*") as $fn)
		{
			$ret += Hubzero_View_Helper_Html::filesize_r($fn);
		}
		return $ret;
	}
}
