<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @param   string  $string   String to sanitize
	 * @param   array   $allowed  An array of additional characters that are not to be removed.
	 * @return  string  Sanitized string
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
	 * @param   string  $str  String to sanitize
	 * @return  string  whitespace sanitized string
	 */
	public static function stripWhitespace($str)
	{
		return preg_replace('/\s{2,}/u', ' ', preg_replace('/[\n\r\t]+/', '', $str == null ? '' : $str));
	}

	/**
	 * Strips image tags from output
	 *
	 * @param   string  $str  String to sanitize
	 * @return  string  String with images stripped.
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
	 * @param   string  $text  Text
	 * @return  string  The text without links
	 */
	public static function stripLinks($text)
	{
		return preg_replace('|<a\s+[^>]+>|im', '', preg_replace('|<\/a>|im', '', $text));
	}

	/**
	 * Strips scripts and stylesheets from output
	 *
	 * @param   string  $str  String to sanitize
	 * @return  string  String with <link>, <img>, <script>, <style> elements and html comments removed.
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
	 * @param   string  $str  String to sanitize
	 * @return  string  sanitized string
	 */
	public static function stripAll($str)
	{
		return self::stripScripts(
			self::stripImages(
				self::stripTags(
					self::stripWhitespace($str)
				)
			)
		);
	}

	/**
	 * Strips the specified tags from output. First parameter is string from
	 * where to remove tags. All subsequent parameters are tags.
	 * If no tags defined, ALL tags will be stripped.
	 *
	 * Ex.`$clean = Sanitize::stripTags($dirty, 'b', 'p', 'div');`
	 *
	 * Will remove all `<b>`, `<p>`, and `<div>` tags from the $dirty string.
	 *
	 * @param   string  $str  String to sanitize
	 * @return  string  sanitized String
	 */
	public static function stripTags($str)
	{
		$params = func_get_args();

		if (count($params) <= 1)
		{
			return strip_tags($str);
		}

		for ($i = 1, $count = count($params); $i < $count; $i++)
		{
			$str = preg_replace('/<' . $params[$i] . '\b[^>]*>/i', '', $str);
			$str = preg_replace('/<[\\\]*\/' . $params[$i] . '[^>]*>/i', '', $str);
		}
		return $str;
	}

	/**
	 * Clean out any cross site scripting attempts (XSS)
	 *
	 * @param   string  $string  Data to sanitize
	 * @return  string  Sanitized data
	 */
	public static function clean($string)
	{
		if (empty($string))
		{
			return $string;
		}

		// strip out any KL_PHP, script, style, HTML comments
		$string = preg_replace('/{kl_php}(.*?){\/kl_php}/is', '', $string);
		$regex =
			'/(<link[^>]+rel="[^"]*stylesheet"[^>]*>|' .
			'<script[^>]*>.*?<\/script>|' .
			'<style[^>]*>.*?<\/style>|' .
			'<\?php.*?\?>|' .
			'<!--.*?-->)/is';
		$string = preg_replace($regex, '', $string);

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
		$string = preg_replace('#(<[^>]+[\x00-\x20\"\'])(on|xmlns)[^>]*>#iUu', "$1>", $string);

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
			'applet', 'meta', 'xml', 'blink', 'link', 'style',
			'script', 'embed', 'object', 'iframe', 'frame', 'frameset',
			'ilayer', 'layer', 'bgsound', 'title', 'base'
		);

		return $string;
	}

	/**
	 * Replace discouraged characters introduced by Microsoft Word
	 *
	 * @param   string   $text        Text to clean
	 * @param   boolean  $quotesOnly  Only clean quotes (single and double)
	 * @return  string
	 */
	public static function cleanMsChar($text, $quotesOnly=false)
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
	 * @param   string  $text     Text to clean
	 * @param   array   $options  Array of key => value pairs
	 * @return  string
	 */
	public static function html($text, $options = [])
	{
		$config = self::_buildHtmlPurifierConfig($options);
		$htmlPurifierWhitelist  = $config->getHTMLDefinition(true);

		self::_addElementsToHtmlPurifierWhitelist($htmlPurifierWhitelist);
		self::_addAttributesToHtmlPurifierWhitelist($htmlPurifierWhitelist);

		$purifier = new \HTMLPurifier($config);

		return $purifier->purify($text);
	}

	/**
	 * Builds HTML purification configuration
	 *
	 * @param    array    $options   Custom purifier configuration options
	 * @return   object   $config    HTML purifier configuration
	 */
	protected static function _buildHtmlPurifierConfig($options)
	{
		$config = \HTMLPurifier_Config::createDefault();
		$root = str_replace(['http://', 'https://', '.'], ['', '', '\.'], \App::get('request')->root());
		$defaultSettings = [
			'AutoFormat.Linkify' => false,
			'AutoFormat.RemoveEmpty' => true,
			'AutoFormat.RemoveEmpty.RemoveNbsp' => false,
			'Output.CommentScriptContents' => false,
			'Output.TidyFormat' => true,
			'Attr.AllowedFrameTargets' => ['_blank'],
			'Attr.EnableID' => true,
			'HTML.AllowedCommentsRegexp' => '/./',
			'HTML.SafeIframe' => true,
			'URI.SafeIframeRegexp' => "%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/|$root)%",
		];
		$combinedSettings = array_merge($defaultSettings, $options);

		self::_findOrCreateClientSerializerDirectory($combinedSettings);

		foreach ($combinedSettings as $setting => $value)
		{
			$config->set($setting, $value);
		}

		return $config;
	}

	/**
	 * Finds or creates client serializer directory
	 *
	 * @param    array   $purifierConfigSettings   HTML purifier configuration settings
	 * @return   void
	 */
	protected static function _findOrCreateClientSerializerDirectory($purifierConfigSettings)
	{
		$client = \App::get('client');
		$clientAlias = isset($client->alias) ? $client->alias : $client->name;
		$clientSerializerPath = PATH_APP . "/cache/$clientAlias/htmlpurifier";

		if (!is_dir($clientSerializerPath))
		{
			\App::get('filesystem')->makeDirectory($clientSerializerPath);
		}

		if (is_dir($clientSerializerPath))
		{
			$purifierConfigSettings['Cache.SerializerPath'] = $clientSerializerPath;
		}
	}

	/**
	 * Adds elements to HTML purifier whitelist
	 *
	 * @param    object   $htmlPurifierWhitelist HTML purifier whitelist
	 * @return   void
	 */
	protected static function _addElementsToHtmlPurifierWhitelist($htmlPurifierWhitelist)
	{
		$styleElement = [
			'name' => 'style',
			'contentSet' => 'Block',
			'allowedChildren' => 'Flow',
			'attributeCollection' => 'Common',
			'attributes' => [],
			'excludes' => []
		];

		$mapElement = [
			'name' => 'map',
			'contentSet' => 'Block',
			'allowedChildren' => 'Flow',
			'attributeCollection' => 'Common',
			'attributes' => [
				'name'  => 'CDATA',
				'id'    => 'ID',
				'title' => 'CDATA',
			],
			'excludes' => ['map' => true]
		];

		$areaElement = [
			'name' => 'area',
			'contentSet' => 'Block',
			'allowedChildren' => 'Empty',
			'attributeCollection' => 'Common',
			'attributes' => [
				'name'      => 'CDATA',
				'id'        => 'ID',
				'alt'       => 'Text',
				'coords'    => 'CDATA',
				'accesskey' => 'Character',
				'nohref'    => new \HTMLPurifier_AttrDef_Enum(['nohref']),
				'href'      => 'URI',
				'shape'     => new \HTMLPurifier_AttrDef_Enum(['rect','circle','poly','default']),
				'tabindex'  => 'Number',
				'target'    => new \HTMLPurifier_AttrDef_Enum(['_blank','_self','_target','_top'])
			],
			'excludes' => ['area' => true]
		];

		$elementSettings = [$styleElement, $mapElement, $areaElement];

		foreach ($elementSettings as $settings)
		{
			$element = $htmlPurifierWhitelist->addElement(
				$settings['name'],
				$settings['contentSet'],
				$settings['allowedChildren'],
				$settings['attributeCollection'],
				$settings['attributes']
			);

			$element->excludes = $settings['excludes'];
		}
	}

	/**
	 * Adds attributes to HTML purifier whitelist
	 *
	 * @param    object   $htmlPurifierWhitelist HTML purifier whitelist
	 * @return   void
	 */
	protected static function _addAttributesToHtmlPurifierWhitelist($htmlPurifierWhitelist)
	{
		$htmlPurifierWhitelist->addAttribute('img', 'usemap', 'CDATA');
	}

	/**
	 * Method to be called by another php script. Processes for XSS and
	 * specified bad code.
	 *
	 * @param   mixed   $source  Input string/array-of-string to be 'cleaned'
	 * @param   string  $type    Return type for the variable (INT, UINT, FLOAT, BOOLEAN, WORD, ALNUM, CMD, BASE64, STRING, ARRAY, PATH, NONE)
	 * @return  mixed   Cleaned version of input parameter
	 */
	public static function filter($source, $type = 'string')
	{
		// Handle the type constraint
		switch (strtoupper($type))
		{
			case 'INT':
			case 'INTEGER':
				// Only use the first integer value
				preg_match('/-?[0-9]+/', (string) $source, $matches);
				$result = @ (int) $matches[0];
				break;

			case 'UINT':
				// Only use the first integer value
				preg_match('/-?[0-9]+/', (string) $source, $matches);
				$result = @ abs((int) $matches[0]);
				break;

			case 'FLOAT':
			case 'DOUBLE':
				// Only use the first floating point value
				preg_match('/-?[0-9]+(\.[0-9]+)?/', (string) $source, $matches);
				$result = @ (float) $matches[0];
				break;

			case 'BOOL':
			case 'BOOLEAN':
				$result = (bool) $source;
				break;

			case 'WORD':
				$result = (string) preg_replace('/[^A-Z_]/i', '', $source);
				break;

			case 'ALNUM':
				$result = (string) preg_replace('/[^A-Z0-9]/i', '', $source);
				break;

			case 'CMD':
				$result = (string) preg_replace('/[^A-Z0-9_\.-]/i', '', $source);
				$result = ltrim($result, '.');
				break;

			case 'BASE64':
				$result = (string) preg_replace('/[^A-Z0-9\/+=]/i', '', $source);
				break;

			case 'STRING':
				$result = (string) self::clean(self::_decode((string) $source));
				break;

			case 'HTML':
				$result = (string) self::clean((string) $source);
				break;

			case 'ARRAY':
				$result = (array) $source;
				break;

			case 'PATH':
				$pattern = '/^[A-Za-z0-9_-]+[A-Za-z0-9_\.-]*([\\\\\/][A-Za-z0-9_-]+[A-Za-z0-9_\.-]*)*$/';
				preg_match($pattern, (string) $source, $matches);
				$result = @ (string) $matches[0];
				break;

			case 'USERNAME':
				$result = (string) preg_replace('/[\x00-\x1F\x7F<>"\'%&]/', '', $source);
				break;

			default:
				// Are we dealing with an array?
				if (is_array($source))
				{
					foreach ($source as $key => $value)
					{
						// filter element for XSS and other 'bad' code etc.
						if (is_string($value))
						{
							$source[$key] = self::clean(self::_decode($value));
						}
					}
					$result = $source;
				}
				else
				{
					// Or a string?
					if (is_string($source) && !empty($source))
					{
						// filter source for XSS and other 'bad' code etc.
						$result = self::clean(self::_decode($source));
					}
					else
					{
						// Not an array or string.. return the passed parameter
						$result = $source;
					}
				}
				break;
		}

		return $result;
	}

	/**
	 * Try to convert to plaintext
	 *
	 * @param   string  $source  The source string.
	 * @return  string  Plaintext string
	 */
	protected static function _decode($source)
	{
		static $ttr;

		if (!is_array($ttr))
		{
			// Entity decode
			$trans_tbl = get_html_translation_table(HTML_ENTITIES, ENT_COMPAT);

			foreach ($trans_tbl as $k => $v)
			{
				$ttr[$v] = function_exists('mbstring') ? mbstring($k) : $k;
			}
		}

		$source = strtr($source, $ttr);

		// Convert decimal
		$source = preg_replace_callback(
			'/&#(\d+);/m',
			function ($matches)
			{
				$str = chr($matches[1]);
				if (function_exists('mbstring'))
				{
					$str = mbstring($str);
				}
				return $str;
			},
			$source
		);

		// Convert hex
		$source = preg_replace_callback(
			'/&#x([a-f0-9]+);/mi',
			function ($matches)
			{
				$str = chr('0x' . $matches[1]);
				if (function_exists('mbstring'))
				{
					$str = mbstring($str);
				}
				return $str;
			},
			$source
		);

		return $source;
	}

	/**
	 * Replace or remove characters invalid for a proper name
	 * (name, givenName, middleName, surname)
	 *
	 * - (Unicode) invisible control characters and unused code points are removed
	 * - (Unicode) ASCII or Latin-1 control characters: 0x00–0x1F and 0x7F–0x9F are removed
	 * - (Unicode) invisible formatting indicators are removed
	 * - (Unicode) any code point reserved for private use are removed
	 * - (Unicode) one half of a surrogate pair in UTF-16 encoding are removed
	 * - (Unicode) any code point to which no character has been assigned are removed
	 * - Less than sign (<) is converted to left bracket ([)
	 * - Greater than sign (>) is converted to right bracket (])
	 * - Ampersand (&) is replaced with an ampersand with a space on each side ( & )
	 * - Multiple consecutive whitespace is collapsed to a single space
	 * - Leading and trailing spaces are removed
	 * - Colon (:) is converted to dash (-)
	 *
	 * @param   string  $source  The source string.
	 * @return  string  cleaned string
	 */
	public static function cleanProperName($source)
	{
		if ($source == null)
		{
			return '';
		}

		return preg_replace(
			array('/[^\P{C}\s]/u',
				'/:/u',
				'/</u',
				'/>/u',
				'/&/u',
				'/\s+/u',
				'/(^\s+|\s+$)/u',
			),
			array(
				'',
				'-',
				'[',
				']',
				' & ',
				' ',
				'',
			),
			$source);
	}
}
