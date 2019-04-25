<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing bad users parameter from older versions of joomla
 **/
class Migration20140730153624ComUsers extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params    = $this->getParams('com_users');
		$user_type = $params->get('new_usertype');

		if (is_string($user_type) && strlen($user_type) > 2)
		{
			$query = "SELECT `id` FROM `#__usergroups` WHERE `title` = " . $this->db->quote($user_type);
			$this->db->setQuery($query);
			if ($id = $this->db->loadResult())
			{
				$params->set('new_usertype', $id);
				$component = new \JTableExtension($this->db);
				$component->load(array('element'=>'com_users'));
				$component->set('params', (string) $params);
				$component->store();
			}
			else
			{
				$this->setError('Failed to convert new user type paramter of "' . $user_type . '" to an ID.', 'warning');
				return false;
			}
		}
	}
}
