<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class GroupsHelperDocument extends \Hubzero\Base\Object
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
	 * @return void
	 */
	public function parse()
	{
		// check to make sure we have content
		if (!$this->get('document'))
		{
			JError::raiseError(406, 'GroupsHelperDocument: Requires document to parse');
		}

		// parse content
		// get all group includes
		if (preg_match_all('#<group:include([^>]*)/>#', $this->get('document'), $matches))
		{
			// import utility class
			jimport('joomla.utilities.utility');

			// get number of matches
			$count = count($matches[1]);

			//loop through each match
			for ($i = 0; $i < $count; $i++)
			{
				$attribs = JUtility::parseAttributes($matches[1][$i]);

				$type   = (isset($attribs['type'])) ? strtolower(trim($attribs['type'])) : null;
				$name   = (isset($attribs['name'])) ? $attribs['name'] : $type;

				unset($attribs['type']);
				$params = $attribs;

				$this->_tags[$matches[0][$i]] = array( 'type' => $type, 'name' => $name, 'params' => $params );
			}
		}

		// return this
		return $this;
	}

	/**
	 * Render Document
	 *
	 * @return    string
	 */
	public function render()
	{
		// vars to hold replace values
		$replace = array();
		$with    = array();

		// include renderer class
		require_once dirname(__FILE__) . DS . 'document' . DS . 'renderer.php';

		// loop through all includes and render
		foreach ($this->_tags as $tag => $props)
		{
			$replace[] = $tag;
			$with[]    = $this->_getBuffer( $tag, $props['type'], $props['name'], $props['params'] );
		}

		// replace group includes
		$this->set('document', str_replace($replace, $with, $this->get('document')) );

		// reset tags
		$this->_tags = array();

		//return this for chainability
		return $this;
	}

	/**
	 * Return Content
	 *
	 * @return string
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
	 * @return
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
		$renderClass = 'GroupsHelperDocumentRenderer' . ucfirst($type);

		// if we dont already have the class instantiated
		if (!class_exists($renderClass))
		{
			// build path to renderer
			$path = dirname(__FILE__) . DS . 'document' . DS . 'renderer' . DS . $type . '.php';

			// include renderer if exists
			if (file_exists($path))
			{
				require_once $path;
			}
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