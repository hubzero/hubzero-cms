<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2017 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2017 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die;

/**
 * Code syntax highlighting plugin
 */
class plgContentGeshi extends \Hubzero\Plugin\Plugin
{
	/**
	 * Prepare the content for display
	 *
	 * @param   string   $context  The context of the content being passed to the plugin.
	 * @param   object   $row      The article object.  Note $article->text is also available
	 * @param   object   $params   The article params
	 * @param   integer  $page     The 'page' number
	 * @return  void
	 */
	public function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		// Simple performance check to determine whether bot should process further.
		if (Hubzero\Utility\String::contains($row->text, 'pre>') === false)
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
		// Initialise variables.
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
