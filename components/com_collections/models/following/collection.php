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
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'collection.php');

/**
 * Model class for following a collection
 */
class CollectionsModelFollowingCollection extends CollectionsModelFollowingAbstract
{
	/**
	 * CollectionsModelCollection
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
	 * @param      integer $id Collection ID
	 * @return     void
	 */
	public function __construct($oid=null)
	{
		//$this->_db = JFactory::getDBO();

		$this->_obj = new CollectionsModelCollection($oid);

		switch ($this->_obj->get('object_type'))
		{
			case 'group':
				$group = \Hubzero\User\Group::getInstance($this->_obj->get('object_id'));
				$this->_baselink = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=collections&scope=' . $this->_obj->get('alias');
			break;

			case 'member':
			default:
				$this->_baselink = 'index.php?option=com_members&id=' . $this->_obj->get('object_id') . '&active=collections&task=' . $this->_obj->get('alias');
			break;
		}
	}

	/**
	 * Returns a reference to a model object
	 *
	 * This method must be invoked as:
	 *     $inst = CollectionsModelFollowingCollection::getInstance($oid);
	 *
	 * @param      string $pagename The page to load
	 * @return     object CollectionsModelFollowingCollection
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
			$instances[$oid] = new CollectionsModelFollowingCollection($oid);
		}

		return $instances[$oid];
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire JUser object
	 *
	 * @return     mixed
	 */
	public function creator($property=null)
	{
		switch ($this->_obj->get('object_type'))
		{
			case 'group':
				if (!isset($this->_creator) || !is_object($this->_creator))
				{
					$this->_creator = \Hubzero\User\Group::getInstance($this->_obj->get('object_id'));
				}
				if ($property)
				{
					switch ($property)
					{
						case 'name':
							return $this->_creator->get('description');
						break;
						case 'alias':
							return $this->_creator->get('cn');
						break;
						case 'id':
							return $this->_creator->get('gidNumber');
						break;
					}
				}
			break;

			case 'member':
			default:
				if (!isset($this->_creator) || !is_object($this->_creator))
				{
					$this->_creator = JUser::getInstance($this->_obj->get('created_by'));
				}
				if ($property)
				{
					switch ($property)
					{
						case 'name':
							return $this->_creator->get('name');
						break;
						case 'alias':
							return $this->_creator->get('username');
						break;
						case 'id':
							return $this->_creator->get('id');
						break;
					}
				}
			break;
		}
		return $this->_creator;
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return     object
	 */
	public function image()
	{
		return $this->_image;
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return     object
	 */
	public function alias()
	{
		return $this->_obj->get('alias');
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return     object
	 */
	public function title()
	{
		return $this->_obj->get('title');
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return     object
	 */
	public function link($what='base')
	{
		switch (strtolower(trim($what)))
		{
			case 'follow':
				return $this->_baselink . '/follow';
			break;

			case 'unfollow':
				return $this->_baselink . '/unfollow';
			break;

			case 'base':
			default:
				return $this->_baselink;
			break;
		}
	}
}
