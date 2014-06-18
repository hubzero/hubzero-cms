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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'following' . DS . 'abstract.php');

/**
 * Model class for following a group
 */
class CollectionsModelFollowingGroup extends CollectionsModelFollowingAbstract
{
	/**
	 * \Hubzero\User\Group
	 *
	 * @var object
	 */
	private $_obj = NULL;

	/**
	 * File path
	 *
	 * @var string
	 */
	private $_image = NULL;

	/**
	 * URL
	 *
	 * @var string
	 */
	private $_baselink = NULL;

	/**
	 * JDatabase
	 *
	 * @var object
	 */
	//private $_db = NULL;

	/**
	 * Constructor
	 *
	 * @param      integer $id Group ID
	 * @return     void
	 */
	public function __construct($oid=null)
	{
		//$this->_db = JFactory::getDBO();

		$this->_obj = \Hubzero\User\Group::getInstance($oid);

		$this->_baselink = 'index.php?option=com_groups&cn=' . $this->_obj->get('cn') . '&active=collections';
	}

	/**
	 * Returns a reference to a model object
	 *
	 * This method must be invoked as:
	 *     $inst = CollectionsModelFollowingGroup::getInstance($oid);
	 *
	 * @param      mixed $oid Group ID or alias
	 * @return     object CollectionsModelFollowingGroup
	 */
	static function &getInstance($oid=null)
	{
		static $instances;

		if (!isset($instances))
		{
			$instances = array();
		}

		if (!isset($instances[$oid]))
		{
			$instances[$oid] = new CollectionsModelFollowingGroup($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get the group's image
	 *
	 * @return     string
	 */
	public function image()
	{
		if (!isset($this->_image))
		{
			$config = JComponentHelper::getParams('com_groups');
			if ($this->_obj->get('logo'))
			{
				$this->_image = DS . trim($config->get('uploadpath', '/site/groups'), DS) . DS . $this->_obj->get('gidNumber') . DS . $this->_obj->get('logo');
			}
			else
			{
				$this->_image = '/components/com_groups/assets/img/group_default_logo.png';
			}
		}
		return $this->_image;
	}

	/**
	 * Get the group's alias
	 *
	 * @return     string
	 */
	public function alias()
	{
		return $this->_obj->get('cn');
	}

	/**
	 * Get the group's title
	 *
	 * @return     string
	 */
	public function title()
	{
		return $this->_obj->get('description');
	}

	/**
	 * Get the URL for this group
	 *
	 * @return     string
	 */
	public function link($what='base')
	{
		switch (strtolower(trim($what)))
		{
			case 'follow':
				return $this->_baselink . '&scope=follow';
			break;

			case 'unfollow':
				return $this->_baselink . '&scope=unfollow';
			break;

			case 'base':
			default:
				return $this->_baselink;
			break;
		}
	}
}
