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

require_once __DIR__ . DS . 'base.php';
require_once \Component::path('com_members') . DS . 'models' . DS . 'member.php';

/**
 * Model class for following a member
 */
class Member extends Base
{
	/**
	 * \Hubzero\User\User
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
		$this->_obj = \Components\Members\Models\Member::oneOrNew($oid);

		$this->_baselink = $this->_obj->link() . '&active=collections';
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
			$this->_image = $this->_obj->picture(0);
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
