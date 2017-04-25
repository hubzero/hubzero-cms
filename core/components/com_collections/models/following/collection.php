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

use Components\Collections\Models;

require_once(__DIR__ . DS . 'base.php');
require_once(dirname(__DIR__) . DS . 'collection.php');

/**
 * Model class for following a collection
 */
class Collection extends Base
{
	/**
	 * Collection
	 *
	 * @var object
	 */
	private $_obj = null;

	/**
	 * File path
	 *
	 * @var string
	 */
	private $_image = null;

	/**
	 * URL
	 *
	 * @var string
	 */
	private $_baselink = null;

	/**
	 * Constructor
	 *
	 * @param   integer  $id  Collection ID
	 * @return  void
	 */
	public function __construct($oid=null)
	{
		$this->_obj = new Models\Collection($oid);

		switch ($this->_obj->get('object_type'))
		{
			case 'group':
				$group = \Hubzero\User\Group::getInstance($this->_obj->get('object_id'));
				if (!$group)
				{
					$group = new \Hubzero\User\Group();
					$group->set('gidNumber', $this->_obj->get('object_id'));
					$group->set('cn', $this->_obj->get('object_id'));
					$group->set('alias', $this->_obj->get('object_id'));
				}
				$this->_baselink = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=collections&scope=' . $this->_obj->get('alias');
			break;

			case 'member':
			default:
				$this->_baselink = 'index.php?option=com_members&id=' . $this->_obj->get('object_id') . '&active=collections&task=' . $this->_obj->get('alias');
			break;
		}
	}

	/**
	 * Get the creator of this entry
	 *
	 * Accepts an optional property name. If provided
	 * it will return that property value. Otherwise,
	 * it returns the entire User object
	 *
	 * @param   string  $property
	 * @return  mixed
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
					$this->_creator = \User::getInstance($this->_obj->get('created_by'));
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
	 * Get an image path
	 *
	 * @return  string
	 */
	public function image()
	{
		return $this->_image;
	}

	/**
	 * Get an alias
	 *
	 * @return  string
	 */
	public function alias()
	{
		return $this->_obj->get('alias');
	}

	/**
	 * Get a title
	 *
	 * @return  string
	 */
	public function title()
	{
		return $this->_obj->get('title');
	}

	/**
	 * Get a link
	 *
	 * @param   string  $what
	 * @return  object
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
