<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers;

use Hubzero\Base\Obj;
use Hubzero\Utility\Str;
use App;

require_once __DIR__ . DS . 'document' . DS . 'renderer.php';

class Document extends Obj
{
	/**
	 * Document
	 *
	 * @var  object
	 */
	public $document     = null;

	/**
	 * Group
	 *
	 * @var  object
	 */
	public $group        = null;

	/**
	 * Content
	 *
	 * @var  object
	 */
	public $content      = null;

	/**
	 * Page
	 *
	 * @var  object
	 */
	public $page         = null;

	/**
	 * Tab
	 *
	 * @var  string
	 */
	public $tab          = null;

	/**
	 * Allowed tags
	 *
	 * @var  array
	 */
	public $allowed_tags = array('module','modules','stylesheet','script');

	/**
	 * Tags
	 *
	 * @var  array
	 */
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
				$attribs = Str::parseAttributes($matches[1][$i]);

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
	private function _getBuffer($tag, $type = null, $name = null, $params = array())
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
		$renderer = new $renderClass();
		$renderer->group   = $this->get('group');
		$renderer->page    = $this->get('page');
		$renderer->content = $this->get('content');
		$renderer->allMods = $this->get('allMods', 0);
		$renderer->name    = $name;
		$renderer->params  = (object) $params;
		return $renderer->render();
	}
}
