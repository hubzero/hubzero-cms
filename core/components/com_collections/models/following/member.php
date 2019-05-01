<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	private $_obj = null;

	/**
	 * File path
	 *
	 * @var  string
	 */
	private $_image = null;

	/**
	 * URL
	 *
	 * @var  string
	 */
	private $_baselink = null;

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
