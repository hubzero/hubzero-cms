<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for updating custom footer module links to point to com_users, rather than com_user
 **/
class Migration20131209221353ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT `id`, `content` FROM `#__modules` WHERE `position` = 'footer' AND `module` = 'mod_custom'";
		$this->db->setQuery($query);
		$results = $this->db->loadObjectList();

		if ($results && count($results) > 0)
		{
			foreach ($results as $r)
			{
				$look_for     = array('/user/remind', '/user/reset');
				$replace_with = array('/users/remind', '/users/reset');
				$new_content  = str_replace($look_for, $replace_with, $r->content);

				if ($new_content != $r->content)
				{
					$query = "UPDATE `#__modules` SET `content` = " . $this->db->quote($new_content) . " WHERE `id` = " . $this->db->quote($r->id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}