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

class GroupsModelModule extends \Hubzero\Model
{
	/**
	 * Table name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'GroupsTableModule';
	
	/**
	 * Menu Items
	 * 
	 * @var array
	 */
	private $_menu_items = null;
	
	/**
	 * Constructor
	 * 
	 * @param      mixed     $
	 * @return     void
	 */
	public function __construct( $oid )
	{
		$this->_db = JFactory::getDBO();
		
		$this->_tbl = new GroupsTableModule($this->_db);
		
		if (is_numeric($oid))
		{
			$this->_tbl->load($oid);
		}
		else if(is_object($oid) || is_array($oid))
		{
			$this->bind( $oid );
		}
	}
	
	/**
	 * Get module menu
	 *
	 * @param     $rtrn       What do we want back
	 * @param     $filters    Array of filters to use when getting menu
	 * @param     $clear      Fetch an updated list
	 * @return    \Hubzero\ItemList
	 */
	public function menu( $rtrn = 'list', $filters = array(), $clear = false )
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
				if (!($this->_menu_items instanceof \Hubzero\ItemList) || $clear)
				{
					if ($results = $tbl->getMenu( $filters ))
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
					$this->_menu_items = new \Hubzero\ItemList($results);
				}
				return $this->_menu_items;
			break;
		}
	}
	
	/**
	 * BUild module menu
	 *
	 * @return    BOOL
	 */
	public function buildMenu( $modulesMenu = array() )
	{
		// create module menu object
		$tbl = new GroupsTableModuleMenu($this->_db);
		
		// delete any previous menu items
		if (!$tbl->deleteMenus( $this->get('id') ))
		{
			$this->setError($tbl->getError());
			return false;
		}
		
		// get module id and array of pages
		$moduleid = $this->get('id');
		$assigned = (isset($modulesMenu['assigned'])) ? $modulesMenu['assigned'] : array();
		$pages    = ($modulesMenu['assignment'] == '0') ? array(0) : $assigned;
		
		// create new menus
		if (!$tbl->createMenus( $moduleid, $pages ))
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
	 * @return    BOOL
	 */
	public function displayOnPage( $pageid = null )
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
	 * @param     $group     Hubzero_Group Object
	 * @return    BOOL
	 */
	public function belongsToGroup( $group )
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
	 * @param    bool    $check              Run the Table Check Method
	 * @param    bool    $trustedContent     Is content trusted
	 * @return   void
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
	 * @param     $position     Module Position
	 * @return    INT
	 */
	public function getNextOrder($position)
	{
		$order = $this->_tbl->getNextOrder("position='".$position."'");
		return $order;
	}
	
	/**
	 * Reorder Module for position
	 *
	 * @param     $move         Direction and Magnitude
	 * @param     $position     Module Position
	 * @return    INT
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
	 * Purify the HTML content via HTML Purifier
	 * 
	 * @param     string    $content           Unpurified HTML content
	 * @param     bool      $trustedContent    Is the content trusted?
	 * @return    string
	 */
	public static function purify( $content, $trustedContent = false )
	{
		// load html purifier
		require_once JPATH_ROOT . DS . 'vendor' . DS .'ezyang' . DS . 'htmlpurifier' . DS . 'library' . DS . 'HTMLPurifier.auto.php';
		
		// create config
		$config = HTMLPurifier_Config::createDefault();
		$config->set('AutoFormat.Linkify', true);
		$config->set('AutoFormat.RemoveEmpty', true);
		$config->set('AutoFormat.RemoveEmpty.RemoveNbsp', true);
		$config->set('Output.CommentScriptContents', false);
		$config->set('Output.TidyFormat', true);
		$config->set('Cache.SerializerPath', JPATH_ROOT . DS . 'cache' . DS . 'htmlpurifier');
		
		//create array of custom filters
		$filters = array();
		
		// is this trusted content
		if ($trustedContent)
		{
			$config->set('CSS.Trusted', true);
			$config->set('HTML.Trusted', true);
			
			$filters[] = new HTMLPurifier_Filter_ExternalScripts();
			$filters[] = new HTMLPurifier_Filter_Php();
		}
		
		// set filter configs
		$config->set('Filter.Custom', $filters);
		
		// purify and return
		$purifier = new HTMLPurifier( $config );
		return $purifier->purify( $content );
	}
}