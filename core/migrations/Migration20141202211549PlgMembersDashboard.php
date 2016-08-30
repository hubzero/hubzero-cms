<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script fixing dashboard migration stuff.
 **/
class Migration20141202211549PlgMembersDashboard extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// get dashboard params
		$pluginParams = $this->getParams('plg_members_dashboard');

		if ($this->db->tableExists('#__xprofiles_dashboard_preferences'))
		{
			// delete all null user preferences
			$sql = "DELETE FROM `#__xprofiles_dashboard_preferences` WHERE `preferences`='[]';";
			$this->db->setQuery($sql);
			$this->db->query();
		}

		// only continue if plugin defaults are NOT set
		$defaults = trim($pluginParams->get('defaults', ''));
		if ($defaults != '[]')
		{
			return;
		}

		// array to hold new defaults
		$defaults = array();

		if ($this->db->tableExists('#__modules'))
		{
			// get top 6 modules
			$sql = "SELECT id FROM `#__modules` WHERE `position`='memberDashboard' AND `published`=1 AND `client_id`=0 ORDER BY `ordering` LIMIT 6;";
			$this->db->setQuery($sql);
			$modules = $this->db->loadColumn();

			// create default
			$col = 0;
			$row = 1;
			foreach ($modules as $k => $module)
			{
				$col = $col + 1;
				if ($col > 3)
				{
					$col = 1;
					$row = 3;
				}

				array_push($defaults, array(
					'module' => $module,
					'col'    => $col,
					'row'    => $row,
					'size_x' => 1,
					'size_y' => 2
				));
			}
		}
		// update params & save
		$pluginParams->set('defaults', $defaults);
		$this->savePluginParams('members', 'dashboard', $pluginParams->toArray());
	}
}