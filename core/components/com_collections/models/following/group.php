<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Collections\Models\Following;

require_once(__DIR__ . DS . 'base.php');

/**
 * Model class for following a group
 */
class Group extends Base
{
	/**
	 * \Hubzero\User\Group
	 *
	 * @var  object
	 */
	private $_obj = NULL;

	/**
	 * File path
	 *
	 * @var  string
	 */
	private $_image = NULL;

	/**
	 * URL
	 *
	 * @var string
	 */
	private $_baselink = NULL;

	/**
	 * Constructor
	 *
	 * @param   integer  $id  Group ID
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		$this->_obj = \Hubzero\User\Group::getInstance($oid);

		$this->_baselink = 'index.php?option=com_groups&cn=' . $this->_obj->get('cn') . '&active=collections';
	}

	/**
	 * Get the group's image
	 *
	 * @return  string
	 */
	public function image()
	{
		if (!isset($this->_image))
		{
			$config = \Component::params('com_groups');
			if ($this->_obj->get('logo'))
			{
				$this->_image = DS . trim($config->get('uploadpath', '/site/groups'), DS) . DS . $this->_obj->get('gidNumber') . DS . $this->_obj->get('logo');
			}
			else
			{
				$this->_image = '/core/components/com_groups/site/assets/img/group_default_logo.png';
			}
		}
		return $this->_image;
	}

	/**
	 * Get the group's alias
	 *
	 * @return  string
	 */
	public function alias()
	{
		return $this->_obj->get('cn');
	}

	/**
	 * Get the group's title
	 *
	 * @return  string
	 */
	public function title()
	{
		return $this->_obj->get('description');
	}

	/**
	 * Get the URL for this group
	 *
	 * @return  string
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
