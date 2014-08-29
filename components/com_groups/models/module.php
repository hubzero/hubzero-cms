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
defined('_JEXEC') or die('Restricted access');

// include needed tables
require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_groups' . DS . 'tables' . DS . 'module.php';
require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_groups' . DS . 'tables' . DS . 'module.menu.php';

/**
 * Group module model class
 */
class GroupsModelModule extends \Hubzero\Base\Model
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	protected $_tbl_name = 'GroupsTableModule';

	/**
	 * Model context
	 *
	 * @var string
	 */
	protected $_context = 'com_groups.module.content';

	/**
	 * Menu Items
	 *
	 * @var array
	 */
	private $_menu_items = null;

	/**
	 * Constructor
	 *
	 * @param      mixed $oid
	 * @return     void
	 */
	public function __construct($oid)
	{
		$this->_db = JFactory::getDBO();

		$this->_tbl = new GroupsTableModule($this->_db);

		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if (is_object($oid) || is_array($oid))
		{
			$this->bind($oid);
		}
	}

	/**
	 * Get module menu
	 *
	 * @param     string  $rtrn    What do we want back
	 * @param     array   $filters Array of filters to use when getting menu
	 * @param     boolean $clear   Fetch an updated list
	 * @return    object  \Hubzero\Base\ItemList
	 */
	public function menu($rtrn = 'list', $filters = array(), $clear = false)
	{
		$tbl = new GroupsTableModuleMenu($this->_db);

		// make sure we have a moduleId
		if (!isset($filters['moduleid']))
		{
			$filters['moduleid'] = $this->get('id');
		}

		// get module menu items
		switch (strtolower($rtrn))
		{
			case 'list':
			default:
				if (!($this->_menu_items instanceof \Hubzero\Base\Model\ItemList) || $clear)
				{
					if ($results = $tbl->getMenu($filters))
					{
						foreach ($results as $key => $result)
						{
							$results[$key] = new GroupsModelModuleMenu($result);
						}
					}
					else
					{
						$results = array();
					}
					$this->_menu_items = new \Hubzero\Base\Model\ItemList($results);
				}
				return $this->_menu_items;
			break;
		}
	}

	/**
	 * Build module menu
	 *
	 * @param   array   $modulesMenu
	 * @return  boolean
	 */
	public function buildMenu($modulesMenu = array())
	{
		// create module menu object
		$tbl = new GroupsTableModuleMenu($this->_db);

		// delete any previous menu items
		if (!$tbl->deleteMenus($this->get('id')))
		{
			$this->setError($tbl->getError());
			return false;
		}

		// get module id and array of pages
		$moduleid = $this->get('id');
		$assigned = (isset($modulesMenu['assigned'])) ? $modulesMenu['assigned'] : array();
		$pages    = ($modulesMenu['assignment'] == '0') ? array(0) : $assigned;

		// create new menus
		if (!$tbl->createMenus($moduleid, $pages))
		{
			$this->setError($tbl->getError());
			return false;
		}

		// everything went smoothly
		return true;
	}

	/**
	 * Should we display module on this page?
	 *
	 * @param   integer $pageid
	 * @return  boolean
	 */
	public function displayOnPage($pageid = null)
	{
		// get module menu
		$menus = $this->menu('list');

		// if we only have one menu && menu pageid 0 (display on all pages)
		if ($menus->count() == 1 && $menus->first()->get('pageid') == 0)
		{
			return true;
		}

		// attempt to load menu for this page
		if ($menus->fetch('pageid', $pageid) !== null)
		{
			return true;
		}

		return false;
	}

	/**
	 * Check to see if group owns module
	 *
	 * @param   object  $group \Hubzero\User\Group
	 * @return  boolean
	 */
	public function belongsToGroup($group)
	{
		if ($this->get('gidNumber') == $group->get('gidNumber'))
		{
			return true;
		}
		return false;
	}

	/**
	 * Overload Store method so we can run some purifying before save
	 *
	 * @param   boolean  $check           Run the Table Check Method
	 * @param   boolean  $trustedContent  Is content trusted
	 * @return  void
	 */
	public function store($check = true, $trustedContent = false)
	{
		//get content
		$content = $this->get('content');

		// if content is not trusted, strip php and scripts
		if (!$trustedContent)
		{
			$content = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $content);
			$content = preg_replace('/<\?[\s\S]*?\?>/', '', $content);
		}

		// purify content
		$content = $this->purify($content, $trustedContent);

		// set the purified content
		$this->set('content', $content);

		// call parent store
		if (!parent::store($check))
		{
			return false;
		}
		return true;
	}

	/**
	 * Get the next order value for position
	 *
	 * @param   string  $position Module Position
	 * @return  integer
	 */
	public function getNextOrder($position)
	{
		$order = $this->_tbl->getNextOrder("position='".$position."'");
		return $order;
	}

	/**
	 * Reorder Module for position
	 *
	 * @param   string  $move      Direction and Magnitude
	 * @param   string  $position  Module Position
	 * @return  integer
	 */
	public function move($move, $position)
	{
		// determine if we need to move up or down
		$dir = '';
		if ($move < 0)
		{
			$dir = '-';
			$move = substr($move, 1);
		}

		// move the number of times different
		for ($i=0; $i < $move; $i++)
		{
			$this->_tbl->move($dir.'1', "position='".$position."'");
		}
	}

	/**
	 * Get the content of the page version
	 *
	 * @param   string  $as      Format to return state in [text, number]
	 * @param   integer $shorten Number of characters to shorten text to
	 * @return  string
	 */
	public function content($as='parsed', $shorten=0)
	{
		$as = strtolower($as);
		$options = array();

		switch ($as)
		{
			case 'parsed':
				$content = $this->get('content_parsed', null);
				if ($content == null)
				{
					// get group
					$group = \Hubzero\User\Group::getInstance(JRequest::getVar('cn', JRequest::getVar('gid', '')));

					// get base path 
					$basePath = JComponentHelper::getparams('com_groups')->get('uploadpath');

					// build config
					$config = array(
						'option'         => JRequest::getCmd('option', 'com_groups'),
						'scope'          => '',
						'pagename'       => $group->get('cn'),
						'pageid'         => 0,
						'filepath'       => $basePath . DS . $group->get('gidNumber') . DS . 'uploads',
						'domain'         => $group->get('cn'),
						'alt_macro_path' => JPATH_ROOT . $basePath . DS . $group->get('gidNumber') . DS . 'macros'
					);

					$content = stripslashes($this->get('content'));
					$this->importPlugin('content')->trigger('onContentPrepare', array(
						$this->_context,
						&$this,
						&$config
					));

					$this->set('content_parsed', $this->get('content'));
					$this->set('content', $content);

					return $this->content($as, $shorten);
				}

				$options['html'] = true;
			break;

			case 'clean':
				$content = strip_tags($this->content('parsed'));
			break;

			case 'raw':
			default:
				$content = stripslashes($this->get('content'));
				$content = preg_replace('/^(<!-- \{FORMAT:.*\} -->)/i', '', $content);
			break;
		}

		if ($shorten)
		{
			$content = \Hubzero\Utility\String::truncate($content, $shorten, $options);
		}
		return $content;
	}

	/**
	 * Purify the HTML content via HTML Purifier
	 *
	 * @param   string   $content         Unpurified HTML content
	 * @param   boolean  $trustedContent  Is the content trusted?
	 * @return  string
	 */
	public static function purify($content, $trustedContent = false)
	{
		// array to hold options
		$options = array();

		//create array of custom filters
		$filters = array(
			new HTMLPurifier_Filter_GroupInclude()
		);

		// is this trusted content
		if ($trustedContent)
		{
			$options['CSS.Trusted'] = true;
			$options['HTML.Trusted'] = true;

			$filters[] = new HTMLPurifier_Filter_ExternalScripts();
			$filters[] = new HTMLPurifier_Filter_Php();
		}

		// add our custom filters
		$options['Filter.Custom'] = $filters;

		// turn OFF linkify
		$options['AutoFormat.Linkify'] = false;

		// run hubzero html sanitize
		return \Hubzero\Utility\Sanitize::html($content, $options);
	}
}