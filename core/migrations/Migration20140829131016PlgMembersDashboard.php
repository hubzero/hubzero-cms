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
 * Migration script for changing member dashboard module position name
 **/
class Migration20140829131016PlgMembersDashboard extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// set new dashboard mod position
		$params = $this->getParams('plg_members_dashboard');
		if ($params->get('position'))
		{
			$params->set('position', 'memberDashboard');
			$this->savePluginParams('members', 'dashboard', $params->toArray());
		}

		// update all modules positions currently set to myhub
		$query = "UPDATE `#__modules` SET `position`='memberDashboard' WHERE `position`='myhub';";
		$this->db->setQuery($query);
		$this->db->query();
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// set new dashboard mod position
		$params = $this->getParams('plg_members_dashboard');
		if ($params->get('position'))
		{
			$params->set('position', 'myhub');
			$this->savePluginParams('members', 'dashboard', $params->toArray());
		}

		// update all modules positions currently set to myhub
		$query = "UPDATE `#__modules` SET `position`='myhub' WHERE `position`='memberDashboard';";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
