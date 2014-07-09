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

// include tables
require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_groups' . DS . 'tables' . DS . 'page.php';
require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_groups' . DS . 'tables' . DS . 'page.hit.php';
require_once JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_groups' . DS . 'tables' . DS . 'page.version.php';

// include models 
require_once JPATH_ROOT . DS . 'components' . DS . 'com_groups' . DS . 'models' . DS . 'page' . DS . 'version' . DS . 'archive.php';

class GroupsModelPage extends \Hubzero\Base\Model
{
	/**
	 * JTable
	 * 
	 * @var string
	 */
	protected $_tbl = null;
	
	/**
	 * Table name
	 * 
	 * @var string
	 */
	protected $_tbl_name = 'GroupsTablePage';
	
	/**
	 * Versions List
	 * 
	 * @var \Hubzero\Base\ItemList
	 */
	protected $_versions = null;
	
	/**
	 * Versions Count
	 * 
	 * @var int
	 */
	protected $_versions_count = null;
	
	/**
	 * Constructor
	 * 
	 * @param      mixed     Object Id
	 * @return     void
	 */
	public function __construct( $oid = null )
	{
		// create needed objects
		$this->_db = JFactory::getDBO();
		
		// load page jtable
		$this->_tbl = new $this->_tbl_name($this->_db);
		
		// load object 
		if (is_numeric($oid))
		{
			$this->_tbl->load( $oid );
		}
		else if(is_object($oid) || is_array($oid))
		{
			$this->bind( $oid );
		}
		
		// load versions
		$pageVersionArchive = new GroupsModelPageVersionArchive();
		$this->_versions = $pageVersionArchive->versions('list', array(
			'pageid'  => $this->get('id'),
			'orderby' => 'version DESC'
		));
	}
	
	
	/**
	 * Get Page Versions
	 *
	 */
	public function versions()
	{
		return $this->_versions;
	}
	
	
	/**
	 * Load Page Version
	 *
	 * @param     mixed            Version Id
	 * @return    Hubzero\Base\Model    Page Version Object
	 */
	public function version( $vid = null )
	{
		// var to hold version
		$version = new GroupsModelPageVersion();
		
		// make sure we have versions to return
		if ($this->_versions->count() > 0)
		{
			// return version object
			if ($vid == null || $vid == 0 || $vid == 'current')
			{
				$version = $this->_versions->first();
			}
			else if (is_numeric($vid))
			{
				$version = $this->_versions->fetch('version', $vid);
			}
		}
		
		//return version
		return $version;
	}
	
	/**
	 * Load Page Category
	 *
	 * @return    Hubzero\Base\Model    Page Category Object
	 */
	public function category()
	{
		// var to hold version
		$category = new GroupsModelPageCategory($this->get('category'));
		
		//return version
		return $category;
	}
	
	
	/**
	 * Load Approved Page version
	 *
	 * @return    Hubzero\Base\Model    Page Version Object
	 */
	public function approvedVersion()
	{
		return $this->_versions->fetch('approved', 1);
	}
	
	
	/**
	 * Check to see if group owns page
	 *
	 * @param     $group     \Hubzero\User\Group Object
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
	 * Generate a unique page alias or slug
	 * 
	 * @return     string
	 */
	public function uniqueAlias()
	{
		// if we didnt set an alias lets build one from the title
		$alias = trim($this->get('alias'));
		if ($alias == null)
		{
			$alias = str_replace(' ', '_', trim($this->get('title')));
		}
		
		// force lowercase letters
		$alias = strtolower($alias);
		
		// allow only alpha numeric chars, dashes, and underscores
		$alias = preg_replace("/[^-_a-z0-9]+/", "", $alias);
		
		// make sure alias isnt a reserved term
		$group   = \Hubzero\User\Group::getInstance( $this->get('gidNumber') );
		$plugins = \Hubzero\User\Group\Helper::getPluginAccess( $group );
		$reserved = array_keys($plugins);
		if (in_array($alias, $reserved))
		{
			$alias .= '_page';
		}
		
		// get current page
		$page = new GroupsModelPage( $this->get('id') );
		$currentAlias = $page->get('alias');
		
		// only against our pages if alias has changed
		if ($currentAlias != $alias)
		{
			// make sure we dont already have a page with the same alias
			// get group pages
			$pageArchive = GroupsModelPageArchive::getInstance();
			$aliases = $pageArchive->pages('alias', array(
				'gidNumber' => $group->get('gidNumber'),
				'state' => array(0,1)
			));
			
			// Append random number if page already exists
			while (in_array($alias, $aliases))
			{
				$alias .= mt_rand(1, 9);
			}
		}
		
		// return sanitized alias
		return $alias;
	}
	
	/**
	 * Get the next page order
	 * 
	 * @return     string
	 */
	public function getNextOrder( $gidNumber )
	{
		$where = "gidNumber=" . $this->_db->quote($gidNumber);
		$order = $this->_tbl->getNextOrder($where);
		return $order;
	}
	
	/**
	 * Reorder page
	 *
	 * @param     $move         Direction and Magnitude
	 * @return    INT
	 */
	public function move($move, $gidNumber)
	{
		// build where statement
		$where = "gidNumber=" . $this->_db->quote($gidNumber);
		
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
			$this->_tbl->move($dir.'1', $where);
		}
	}

	/**
	 * Method to build url to page
	 * @return [type] [description]
	 */
	public function url()
	{
		// loag group
		$group = \Hubzero\User\Group::getInstance($this->get('gidNumber'));

		// base link
		$pageLink = 'index.php?option=com_groups&cn=' . $group->get('cn');
		
		// if we not linking to the home page
		if (!$this->get('home'))
		{
			$pageLink .= '&active=' . $this->get('alias');
		}
		
		// return routed link
		return JRoute::_($pageLink);
	}
}