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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers;

use Hubzero\Base\Object;
use Hubzero\Utility\String;
use App;

require_once PATH_CORE . DS . 'components' . DS . 'com_groups' . DS . 'helpers' . DS . 'document' . DS . 'renderer.php';

class Document extends Object
{
	public $document     = null;
	public $group        = null;
	public $content      = null;
	public $page         = null;
	public $tab          = null;
	public $allowed_tags = array('module','modules','stylesheet','script');
	private $_tags       = array();

	/**
	 * Parse Document Content
	 *
	 * @return  void
	 */
	public function parse()
	{
		// check to make sure we have content
		if (!$this->get('document'))
		{
			App::abort(406, '\Components\Groups\Helpers\Document: Requires document to parse');
		}

		// parse content
		// get all group includes
		if (preg_match_all('#<group:include([^>]*)/>#', $this->get('document'), $matches))
		{
			// get number of matches
			$count = count($matches[1]);

			//loop through each match
			for ($i = 0; $i < $count; $i++)
			{
				$attribs = String::parseAttributes($matches[1][$i]);

				$type   = (isset($attribs['type'])) ? strtolower(trim($attribs['type'])) : null;
				$name   = (isset($attribs['name'])) ? $attribs['name'] : $type;

				unset($attribs['type']);
				$params = $attribs;

				$this->_tags[$matches[0][$i]] = array(
					'type'   => $type,
					'name'   => $name,
					'params' => $params
				);
			}
		}

		// return this
		return $this;
	}

	/**
	 * Render Document
	 *
	 * @return  string
	 */
	public function render()
	{
		// vars to hold replace values
		$replace = array();
		$with    = array();

		// include renderer class
		require_once __DIR__ . DS . 'document' . DS . 'renderer.php';

		// loop through all includes and render
		foreach ($this->_tags as $tag => $props)
		{
			$replace[] = $tag;
			$with[]    = $this->_getBuffer($tag, $props['type'], $props['name'], $props['params']);
		}

		// replace group includes
		$this->set('document', str_replace($replace, $with, $this->get('document')));

		// reset tags
		$this->_tags = array();

		//return this for chainability
		return $this;
	}

	/**
	 * Return or echo Content
	 *
	 * @param   boolean  $echo  Echo document or return?
	 * @return  mixed
	 */
	public function output($echo = false)
	{
		if ($echo)
		{
			echo $this->get('document');
		}
		else
		{
			return $this->get('document');
		}
	}


	/**
	 * Get Template Buffer
	 *
	 * @return  string
	 */
	private function _getBuffer( $tag, $type = null, $name = null, $params = array() )
	{
		// make sure we have type
		// make sure this type is in allowed tags
		if ($type === null || !in_array($type, $this->get('allowed_tags')))
		{
			return "<!-- [[{$tag}]]: Include Invalid or Not Allowed Here -->";
		}

		// class name for renderer
		$renderClass = '\\Components\\Groups\\Helpers\\Document\\Renderer\\' . ucfirst($type);

		// build path to renderer
		$path = __DIR__ . DS . 'document' . DS . 'renderer' . DS . $type . '.php';

		// include renderer if exists
		if (file_exists($path))
		{
			require_once $path;
		}

		// if we still dont have a class return null
		if (!class_exists($renderClass))
		{
			return null;
		}

		// instantiate renderer and call render
		$renderer          = new $renderClass();
		$renderer->group   = $this->get('group');
		$renderer->page    = $this->get('page');
		$renderer->content = $this->get('content');
		$renderer->allMods = $this->get('allMods', 0);
		$renderer->name    = $name;
		$renderer->params  = (object) $params;
		return $renderer->render();
	}
}