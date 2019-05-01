<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	 * @var string
	 */
	private $_baselink = null;

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
