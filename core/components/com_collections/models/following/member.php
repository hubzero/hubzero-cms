<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Collections\Models\Following;

require_once(__DIR__ . DS . 'base.php');

/**
 * Model class for following a member
 */
class Member extends Base
{
	/**
	 * \Hubzero\User\Profile
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
	 * @var  string
	 */
	private $_baselink = NULL;

	/**
	 * Constructor
	 *
	 * @param   integer  $id  Member ID
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		$this->_obj = \Hubzero\User\Profile::getInstance($oid);

		$this->_baselink = 'index.php?option=com_members&id=' . $this->_obj->get('uidNumber') . '&active=collections';
	}

	/**
	 * Get the member's image
	 *
	 * @return  string
	 */
	public function image()
	{
		if (!isset($this->_image))
		{
			$this->_image = \Hubzero\User\Profile\Helper::getMemberPhoto($this->_obj, 0);
		}
		return $this->_image;
	}

	/**
	 * Get the member's username
	 *
	 * @return  string
	 */
	public function alias()
	{
		return $this->_obj->get('username');
	}

	/**
	 * Get the member's name
	 *
	 * @return  string
	 */
	public function title()
	{
		return $this->_obj->get('name');
	}

	/**
	 * Get the URL for this member
	 *
	 * @return  string
	 */
	public function link($what='base')
	{
		switch (strtolower(trim($what)))
		{
			case 'follow':
				return $this->_baselink . '&task=follow';
			break;

			case 'unfollow':
				return $this->_baselink . '&task=unfollow';
			break;

			case 'base':
			default:
				return $this->_baselink;
			break;
		}
	}
}
