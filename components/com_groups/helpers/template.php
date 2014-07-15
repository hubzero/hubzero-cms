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

class GroupsHelperTemplate extends GroupsHelperDocument
{
	public $error = null;

	/**
	 * Array of group include tags allowed
	 * (all tags)
	 */
	public $allowed_tags = array('module', 'modules', 'toolbar', 'menu', 'content', 'googleanalytics', 'stylesheet', 'script');

	/**
	 * Override parse template to get document content
	 *
	 * @return    void
	 */
	public function parse()
	{
		// check to make sure we have group
		if (!$this->get('group'))
		{
			JError::raiseError(406, 'Missing Needed Hubzero Group Object');
		}

		// define base path
		$params = JComponentHelper::getParams('com_groups');
		$base   = $params->get('uploadpath', '/site/groups');
		$base   = DS . trim($base, DS) . DS . $this->group->get('gidNumber') . DS . 'template' . DS;

		// fetch template file (sets document for parsing)
		$this->_fetch($base);

		// call parse
		return parent::parse();
	}

	/**
	 * Return Content
	 *
	 * @return string
	 */
	public function output($echo = false)
	{
		// parse php code
		ob_start();
		eval("?> ".$this->get('document')." <?php ");
		$this->set('document', ob_get_clean());

		// run output declared in parent
		parent::output($echo);
	}

	/**
	 * Fetches Template File
	 *
	 * @return    void
	 */
	private function _fetch( $base )
	{
		// only fetch template if we dont already have one
		// or if we are forcing it to fetch again
		if ($this->get('document') === null)
		{
			// var to hold our final template
			$template = null;

			// build array of possible page templates to load
			$possibleTemplates = array(
				$base . 'index.php',
				$base . 'default.php'
			);

			// if we have an active page, add other template possibilities
			if ($this->page !== null)
			{
				$possibleTemplates[] = $base . 'page.php';
				$possibleTemplates[] = $base . 'page-' . $this->page->get('id') . '.php';
				$possibleTemplates[] = $base . 'page-' . $this->page->get('alias') . '.php';
				$possibleTemplates[] = $base . $this->page->get('template') . '.php';
				$possibleTemplates = array_reverse($possibleTemplates);
			}

			// get the template we want to load
			foreach ($possibleTemplates as $possibleTemplate)
			{
				if (file_exists(JPATH_ROOT . $possibleTemplate))
				{
					$template = $possibleTemplate;
					break;
				}
			}

			// do we have a problem houston?
			if ($this->get('error') !== null)
			{
				$template = $base . 'error.php';
			}

			//we we dont have a super group template
			if ($template === null)
			{
				JError::raiseError(500, 'Missing "Super Group" template file.');
				return;
			}

			// load the template & set docuement
			$this->set('document', $this->_load( JPATH_ROOT . $template ));
		}

		// return this for chainability
		return $this;
	}


	/**
	 * Does the group have a specified template
	 *
	 * @return    void
	 */
	public static function hasTemplate($group, $template)
	{
		// define base path
		$params = JComponentHelper::getParams('com_groups');
		$base   = $params->get('uploadpath', '/site/groups');
		$base   = DS . trim($base, DS) . DS . $group->get('gidNumber') . DS . 'template' . DS;

		// add php extension
		if (substr($template, -4, 4) != '.php')
		{
			$template .= '.php';
		}

		// does the file exist?
		return file_exists(JPATH_ROOT . $base . $template);
	}

	/**
	 * Load Template File
	 *
	 * @return    void
	 */
	private function _load( $template )
	{
		ob_start();
		require_once $template;
		$contents = ob_get_clean();
		return $contents;
	}
}