<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die;

/**
 * Code syntax highlighting plugin
 */
class plgContentGeshi extends \Hubzero\Plugin\Plugin
{
	/**
	 * Prepare the content for display
	 *
	 * @param   string   $context  The context of the content being passed to the plugin
	 * @param   object   $article  The row object
	 * @param   object   $params   The article params
	 * @param   integer  $page     The 'page' number
	 * @return  void
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		if ($context != 'com_content.article')
		{
			return true;
		}

		// Simple performance check to determine whether bot should process further.
		if (Hubzero\Utility\Str::contains($article->text, 'pre>') === false)
		{
			return true;
		}

		// Define the regular expression for the bot.
		$regex = "#<pre xml:\s*(.*?)>(.*?)</pre>#s";

		// Perform the replacement.
		$article->text = preg_replace_callback($regex, array(&$this, '_replace'), $article->text);

		return true;
	}

	/**
	 * Replaces the matched tags.
	 *
	 * @param   array   $matches  An array of matches (see preg_match_all)
	 * @return  string
	 */
	protected function _replace(&$matches)
	{
		require_once __DIR__ . '/geshi/geshi.php';

		$args = self::parseAttributes($matches[1]);
		$text = $matches[2];

		$lang  = Hubzero\Utility\Arr::getValue($args, 'lang', 'php');
		$lines = Hubzero\Utility\Arr::getValue($args, 'lines', 'false');

		$html_entities_match   = array("|\<br \/\>|", "#<#", "#>#", "|&#39;|", '#&quot;#', '#&nbsp;#');
		$html_entities_replace = array("\n", '&lt;', '&gt;', "'", '"', ' ');

		$text = preg_replace($html_entities_match, $html_entities_replace, $text);
		$text = str_replace('&lt;', '<', $text);
		$text = str_replace('&gt;', '>', $text);
		$text = str_replace("\t", '  ', $text);

		$geshi = new GeSHi($text, $lang);
		if ($lines == 'true')
		{
			$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
		}
		$text = $geshi->parse_code();

		return $text;
	}

	/**
	 * Method to extract key/value pairs out of a string with XML style attributes
	 *
	 * @param   string  $string  String containing XML style attributes
	 * @return  array   Key/Value pairs for the attributes
	 */
	protected static function parseAttributes($string)
	{
		// Initialise variables
		$attr = array();
		$retarray = array();

		// Let's grab all the key/value pairs using a regular expression
		preg_match_all('/([\w:-]+)[\s]?=[\s]?"([^"]*)"/i', $string, $attr);

		if (is_array($attr))
		{
			$numPairs = count($attr[1]);
			for ($i = 0; $i < $numPairs; $i++)
			{
				$retarray[$attr[1][$i]] = $attr[2][$i];
			}
		}

		return $retarray;
	}
}
