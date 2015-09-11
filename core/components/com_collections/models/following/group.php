<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
