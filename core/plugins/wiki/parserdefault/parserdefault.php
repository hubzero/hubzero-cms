<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Wiki plugin class for loading the default parser
 */
class plgWikiParserdefault extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Holds the parser for re-use
	 *
	 * @var  object
	 */
	public $parser;

	/**
	 * Get the wiki parser, creating a new one if not already existing or $getnew is set
	 *
	 * @param   array    $config  Options for initializing a parser
	 * @param   boolean  $getnew  Init a new parser?
	 * @return  object
	 */
	public function onGetWikiParser($config, $getnew=false)
	{
		if (!is_object($this->parser) || $getnew)
		{
			$path = dirname(__FILE__);
			if (is_file($path . DS . 'parser.php'))
			{
				include_once($path . DS . 'parser.php');
			}
			else
			{
				return null;
			}

			$config['option']   = (isset($config['option']))   ? $config['option']   : 'com_wiki';
			$config['scope']    = (isset($config['scope']))    ? $config['scope']    : '';
			$config['pagename'] = (isset($config['pagename'])) ? $config['pagename'] : '';
			$config['pageid']   = (isset($config['pageid']))   ? $config['pageid']   : 0;
			$config['filepath'] = (isset($config['filepath'])) ? $config['filepath'] : '';
			$config['domain']   = (isset($config['domain']))   ? $config['domain']   : null;
			$config['domain_id'] = (isset($config['domain_id']))   ? $config['domain_id']   : null;
			$config['url']      = (isset($config['url']))      ? $config['url'] : null;
			$config['loglinks'] = (isset($config['loglinks'])) ? $config['loglinks'] : null;

			$this->parser = new WikiParser($config);
		}
		return $this->parser;
	}

	/**
	 * Turns wiki markup to HTML
	 *
	 * @param   string   $text       Text to convert
	 * @param   array    $config     Options for initializing a parser
	 * @param   boolean  $fullparse  Do a full parse or ignore some things like macros?
	 * @param   boolean  $getnew     Init a new parser?
	 * @return  string
	 */
	public function onWikiParseText($text, $config, $fullparse=true, $getnew=false)
	{
		$parser = $this->onGetWikiParser($config, $getnew);
		$config['camelcase'] = (isset($config['camelcase']) ? $config['camelcase'] : 1);

		return is_object($parser) ? $parser->parse("\n" . $text, $fullparse, 0, $config['camelcase']) : $text;
	}
}
