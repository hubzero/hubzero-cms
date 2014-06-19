<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Utility;

/**
 * Data Sanitization.
 *
 * Removal of alphanumeric characters, SQL-safe slash-added strings, HTML-friendly strings.
 *
 * Largely inspired by CakePHP (http://cakephp.org) and Zend (http://framework.zend.com)
 */
class Sanitize
{
	/**
	 * Removes any non-alphanumeric characters.
	 *
	 * @param  string $string  String to sanitize
	 * @param  array  $allowed An array of additional characters that are not to be removed.
	 * @return string Sanitized string
	 */
	public static function paranoid($string, $allowed = array())
	{
		$allow = null;
		if (!empty($allowed))
		{
			foreach ($allowed as $value)
			{
				$allow .= "\\$value";
			}
		}

		if (!is_array($string))
		{
			return preg_replace("/[^{$allow}a-zA-Z0-9]/", '', $string);
		}

		$cleaned = array();
		foreach ($string as $key => $clean)
		{
			$cleaned[$key] = preg_replace("/[^{$allow}a-zA-Z0-9]/", '', $clean);
		}

		return $cleaned;
	}

	/**
	 * Strips extra whitespace from output
	 *
	 * @param  string $str String to sanitize
	 * @return string whitespace sanitized string
	 */
	public static function stripWhitespace($str)
	{
		return preg_replace('/\s{2,}/u', ' ', preg_replace('/[\n\r\t]+/', '', $str));
	}

	/**
	 * Strips image tags from output
	 *
	 * @param  string $str String to sanitize
	 * @return string String with images stripped.
	 */
	public static function stripImages($str)
	{
		$preg = array(
			'/(<a[^>]*>)(<img[^>]+alt=")([^"]*)("[^>]*>)(<\/a>)/i' => '$1$3$5<br />',
			'/(<img[^>]+alt=")([^"]*)("[^>]*>)/i' => '$2<br />',
			'/<img[^>]*>/i' => ''
		);

		return preg_replace(array_keys($preg), array_values($preg), $str);
	}

	/**
	 * Strips given text of all links (<a href=....)
	 *
	 * @param  string $text Text
	 * @return string The text without links
	 */
	public static function stripLinks($text)
	{
		return preg_replace('|<a\s+[^>]+>|im', '', preg_replace('|<\/a>|im', '', $text));
	}

	/**
	 * Strips scripts and stylesheets from output
	 *
	 * @param  string $str String to sanitize
	 * @return string String with <link>, <img>, <script>, <style> elements and html comments removed.
	 */
	public static function stripScripts($str)
	{
		$regex =
			'/(<link[^>]+rel="[^"]*stylesheet"[^>]*>|' .
			'style="[^"]*")|' . //<img[^>]*>|
			'<script[^>]*>.*?<\/script>|' .
			'<style[^>]*>.*?<\/style>|' .
			'<\?php.*?\?>|' .
			'<!--.*?-->/is';
		return preg_replace($regex, '', $str);
	}

	/**
	 * Strips extra whitespace, images, scripts and stylesheets from output
	 *
	 * @param string $str String to sanitize
	 * @return string sanitized string
	 */
	public static function stripAll($str)
	{
		return self::stripScripts(
			self::stripImages(
				self::stripWhitespace($str)
			)
		);
	}

	/**
	 * Strips the specified tags from output. First parameter is string from
	 * where to remove tags. All subsequent parameters are tags.
	 *
	 * Ex.`$clean = Sanitize::stripTags($dirty, 'b', 'p', 'div');`
	 *
	 * Will remove all `<b>`, `<p>`, and `<div>` tags from the $dirty string.
	 *
	 * @param  string $str,... String to sanitize
	 * @return string sanitized String
	 */
	public static function stripTags($str)
	{
		$params = func_get_args();

		for ($i = 1, $count = count($params); $i < $count; $i++)
		{
			$str = preg_replace('/<' . $params[$i] . '\b[^>]*>/i', '', $str);
			$str = preg_replace('/<\/' . $params[$i] . '[^>]*>/i', '', $str);
		}
		return $str;
	}

	/**
	 * Clean out any cross site scripting attempts (XSS)
	 *
	 * @param  string $string Data to sanitize
	 * @return string Sanitized data
	 */
	public static function clean($string)
	{
		if (empty($string))
		{
			return $string;
		}

		if (get_magic_quotes_gpc())
		{
			$string = stripslashes($string);
		}

		// strip out any KL_PHP, script, style, HTML comments
		$string = preg_replace('/{kl_php}(.*?){\/kl_php}/is', '', $string);
		$string = self::stripScripts($string);

		$string = str_replace(
			array('&amp;',     '&lt;',     '&gt;'),
			array('&amp;amp;', '&amp;lt;', '&amp;gt;'),
			$string
		);
		// Fix &entitiy\n;

		$string = preg_replace('#(&\#*\w+)[\x00-\x20]+;#u', "$1;", $string);
		$string = preg_replace('#(&\#x*)([0-9A-F]+);*#iu', "$1$2;", $string);
		//$string = html_entity_decode($string, ENT_COMPAT, "UTF-8");

		// Remove any attribute starting with "on" or xmlns
		$string = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu',"$1>", $string);

		// Remove javascript: and vbscript: protocol
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*)[\\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2nojavascript...', $string);
		$string = preg_replace('#([a-z]*)[\x00-\x20]*=([\'\"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iUu', '$1=$2novbscript...', $string);

		// <span style="width: expression(alert('Ping!'));"></span>
		// only works in ie...
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*expression[\x00-\x20]*\([^>]*>#iU', "$1>", $string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*behaviour[\x00-\x20]*\([^>]*>#iU', "$1>", $string);
		$string = preg_replace('#(<[^>]+)style[\x00-\x20]*=[\x00-\x20]*([\`\'\"]*).*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*>#iUu', "$1>", $string);

		// Remove namespaced elements (we do not need them...)
		$string = preg_replace('#</*\w+:\w[^>]*>#i', '', $string);

		// Remove really unwanted tags
		$string = self::stripTags($string,
			'applet', 'meta',  'xml',     'blink',  'link',  'style',
			'script', 'embed', 'object',  'iframe', 'frame', 'frameset',
			'ilayer', 'layer', 'bgsound', 'title',  'base'
		);

		return $string;
	}

	/**
	 * Replace discouraged characters introduced by Microsoft Word
	 *
	 * @param      string  $text       Text to clean
	 * @param      boolean $quotesOnly Only clean quotes (single and double)
	 * @return     string
	 */
	public function cleanMsChar($text, $quotesOnly=false)
	{
		$y = array(
			"\x7f"=>'',
			"\x80"=>'&#8364;',
			"\x81"=>'',
			"\x83"=>'&#402;',
			"\x85"=>'&#8230;',
			"\x86"=>'&#8224;',
			"\x87"=>'&#8225;',
			"\x88"=>'&#710;',
			"\x89"=>'&#8240;',
			"\x8a"=>'&#352;',
			"\x8b"=>'&#8249;',
			"\x8c"=>'&#338;',
			"\x8d"=>'',
			"\x8e"=>'&#381;',
			"\x8f"=>'',
			"\x90"=>'',
			"\x95"=>'&#8226;',
			"\x96"=>'&#8211;',
			"\x97"=>'&#8212;',
			"\x98"=>'&#732;',
			"\x99"=>'&#8482;',
			"\x9a"=>'&#353;',
			"\x9b"=>'&#8250;',
			"\x9c"=>'&#339;',
			"\x9d"=>'',
			"\x9e"=>'&#382;',
			"\x9f"=>'&#376;',
		);
		$x = array(
			"\x82"=>'\'',
			"\x84"=>'"',
			"\x91"=>'\'',
			"\x92"=>'\'',
			"\x93"=>'"',
			"\x94"=>'"'
		);
		if (!$quotesOnly)
		{
			$x = $y + $x;
		}

		$text = strtr($text, $x);

		return $text;
	}

	/**
	 * Run HTML through a purifier
	 *
	 * @param      string  $text    Text to clean
	 * @param      array   $options Array of key => value pairs
	 * @return     string
	 */
	public static function html($text, $options=array())
	{
		$config = \HTMLPurifier_Config::createDefault();
		$config->set('AutoFormat.Linkify', false);
		$config->set('AutoFormat.RemoveEmpty', true);
		$config->set('AutoFormat.RemoveEmpty.RemoveNbsp', false);
		$config->set('Output.CommentScriptContents', false);
		$config->set('Output.TidyFormat', true);
		$config->set('Attr.AllowedFrameTargets', array('_blank'));
		$config->set('Attr.EnableID', true);
		$config->set('HTML.AllowedCommentsRegexp', '/./');

		$path = JPATH_ROOT . DS . 'cache' . DS . 'htmlpurifier';
		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!\JFolder::create($path))
			{
				$path = '';
			}
		}

		if ($path)
		{
			$config->set('Cache.SerializerPath', $path);
		}

		if (!empty($options))
		{
			foreach ($options as $key => $val)
			{
				$config->set($key, $val);
			}
		}

		// allow style tags
		$def = $config->getHTMLDefinition(true);
		$form = $def->addElement('style', 'Block', 'Flow', 'Common', array());

		// purify text & return
		$purifier = new \HTMLPurifier($config);
		return $purifier->purify($text);
	}
}
