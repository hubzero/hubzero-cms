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

include_once __DIR__ . '/markdown/block/CodeTrait.php';
include_once __DIR__ . '/markdown/block/FencedCodeTrait.php';
include_once __DIR__ . '/markdown/block/HeadlineTrait.php';
include_once __DIR__ . '/markdown/block/HtmlTrait.php';
include_once __DIR__ . '/markdown/block/ListTrait.php';
include_once __DIR__ . '/markdown/block/QuoteTrait.php';
include_once __DIR__ . '/markdown/block/RuleTrait.php';
include_once __DIR__ . '/markdown/block/TableTrait.php';
include_once __DIR__ . '/markdown/inline/CodeTrait.php';
include_once __DIR__ . '/markdown/inline/EmphStrongTrait.php';
include_once __DIR__ . '/markdown/inline/LinkTrait.php';
include_once __DIR__ . '/markdown/inline/StrikeoutTrait.php';
include_once __DIR__ . '/markdown/inline/UrlLinkTrait.php';
include_once __DIR__ . '/markdown/Parser.php';
include_once __DIR__ . '/markdown/Markdown.php';
include_once __DIR__ . '/markdown/MarkdownExtra.php';
include_once __DIR__ . '/markdown/GithubMarkdown.php';

/**
 * Markdown parser class
 */
class MarkdownParser
{
	/**
	 * A unique token
	 *
	 * @var string
	 */
	private $_token = null;

	/**
	 * Configuration options
	 *
	 * @var array
	 */
	private $_config = array(
		'option'    => null,
		'scope'     => null,
		'pagename'  => null,
		'pageid'    => null,
		'filepath'  => null,
		'path'      => null,
		'macros'    => true,
		'domain'    => '',
		'domain_id' => 0,
		'url'       => '',

		'fullparse' => true,
		'camelcase' => true,
		'loglinks'  => false,
		'style'     => 'Markdown'
	);

	/**
	 * Data store
	 *
	 * @var array
	 */
	private $_data = array(
		'input'  => null,
		'output' => null,
		'links'  => array()
	);

	/**
	 * Constructor
	 *
	 * @param      array $config Configuration options
	 * @return     void
	 */
	public function __construct($config=array())
	{
		if (is_array($config))
		{
			// We need this info for links that may get generated
			foreach ($config as $k => $s)
			{
				$this->set($k, $s);
			}
		}

		// Set the unique token
		$this->_token = "\x07UNIQ" . $this->_randomString();
	}

	/**
	 * Modifies a property of the object, creating it if it does not already exist.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set.
	 * @return  object  Method chaining
	 */
	public function set($property, $value = null)
	{
		$this->_config[$property] = $value;
		return $this;
	}

	/**
	 * Returns a property of the object or the default value if the property is not set.
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $default   The default value.
	 * @return  mixed   The value of the property.
	 */
	public function get($property, $default = null)
	{
		if (isset($this->_config[$property]))
		{
			return $this->_config[$property];
		}
		return $default;
	}

	/**
	 * Get the unique string
	 *
	 * @return     string
	 */
	public function token()
	{
		return $this->_token;
	}

	/**
	 * Get the raw input
	 *
	 * @return     string
	 */
	public function input()
	{
		return $this->_data['input'];
	}

	/**
	 * Get the parsed output
	 *
	 * @return     string
	 */
	public function output()
	{
		return $this->_data['output'];
	}

	/**
	 * Generate a unique prefix
	 *
	 * @return     integer
	 */
	private function _randomString()
	{
		return dechex(mt_rand(0, 0x7fffffff)) . dechex(mt_rand(0, 0x7fffffff));
	}

	/**
	 * Where all the magic takes place
	 * Turns raw wiki text to HTML
	 *
	 * @param      string  $text      Raw wiki markup
	 * @param      boolean $fullparse Full or limited parse? Limited does not parse macros
	 * @param      integer $linestart Parameter description (if any) ...
	 * @param      integer $camelcase Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	public function parse($text, $fullparse=true, $linestart=0, $camelcase=1)
	{
		// Store the raw input
		$this->_data['input'] = $text;

		// Set some configs
		$this->set('fullparse', $fullparse);
		$this->set('camelcase', $camelcase);
		if (!$this->get('fullparse'))
		{
			$this->set('camelcase', 0);
		}

		// Remove any trailing whitespace
		$text = rtrim($text);

		$cls = '\\cebe\\markdown\\' . $this->get('style', 'Markdown');

		$parser = new $cls();
		$text = $parser->parse($text);

		// If full parse and we have a page ID (i.e., this is a *wiki* page) and link logging is turned on...
		/*if ($this->get('fullparse') && $this->get('pageid') && $this->get('loglinks'))
		{
			$links = \Components\Wiki\Models\Link::blank();
			$links->updateLinks($this->get('pageid'), $this->_data['links']);
		}*/

		$this->_data['output'] = $text;

		if (trim($this->_data['input']) && !trim($this->_data['output']))
		{
			$this->_data['output']  = '<p class="warning">Parsing error resulted in empty content. Displaying raw markup below.</p>';
			$this->_data['output'] .= '<pre>' . htmlentities($this->_data['input'], ENT_COMPAT, 'UTF-8') . '</pre>';
		}

		return $this->_data['output'];
	}
}
