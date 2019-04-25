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
 * Migration script for updates to member dashboard features
 **/
class Migration20140508120000PlgMembersDashboard extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__xprofiles_dashboard_preferences') && $this->db->tableExists('#__myhub'))
		{
			$this->db->setQuery("SELECT extension_id, params FROM `#__extensions` WHERE `folder` = 'members' AND `element` = 'dashboard' LIMIT 1");
			$dashboardPlugin = $this->db->loadObject();
			$params = json_decode($dashboardPlugin->params);

			$newDefaults = array();
			if (isset($params->defaults))
			{
				$oldDefaultCols = array_map("trim", explode(';', $params->defaults));

				foreach ($oldDefaultCols as $col => $oldCol)
				{
					$newDefault  = array();
					$oldDefaults = array_map('trim', explode(',', $oldCol));

					foreach ($oldDefaults as $row => $pref)
					{
						$newDefault['module'] = $pref;
						$newDefault['col']    = $col + 1;
						$newDefault['row']    = ($row*2) + 1;
						$newDefault['size_x'] = 1;
						$newDefault['size_y'] = 2;
						$newDefaults[]        = $newDefault;
					}
				}
			}

			// make sure we have object
			if (!isset($params) || !is_object($params))
			{
				$params = new stdClass;
			}

			$params->defaults = $newDefaults;

			// switch allow customization param to make sense.
			if (isset($params->allow_customization))
			{
				if ($params->allow_customization == 1)
				{
					$params->allow_customization = 0;
				}
				else
				{
					$params->allow_customization = 1;
				}
			}

			$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote('"'.json_encode($params).'"') . " WHERE `extension_id`=" . $this->db->quote($dashboardPlugin->extension_id);
			$this->db->setQuery($query);
			$this->db->query();

			// create dashboard prefs table
			$query = "CREATE TABLE IF NOT EXISTS `#__xprofiles_dashboard_preferences` (
						  `uidNumber` int(11) unsigned NOT NULL,
						  `preferences` text,
						  `modified` datetime DEFAULT NULL,
						  UNIQUE KEY `uidNumber` (`uidNumber`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();


			// move over exxisting preferences
			$this->db->setQuery("SELECT * FROM `#__myhub` GROUP BY uid");
			$preferences = $this->db->loadObjectList();

			$newpreferences = array();
			foreach ($preferences as $preference)
			{
				$newPrefCols = array();
				$oldPrefCols = array_map("trim", explode(';', $preference->prefs));

				foreach ($oldPrefCols as $col => $oldCol)
				{
					$newPref = array();
					$oldPrefs = array_map('trim', explode(',', $oldCol));

					foreach ($oldPrefs as $row => $pref)
					{
						$newPref['module'] = $pref;
						$newPref['col']    = $col + 1;
						$newPref['row']    = ($row*2) + 1;
						$newPref['size_x'] = 1;
						$newPref['size_y'] = 2;

						$newPrefCols[] = $newPref;
					}

				}

				$newpreferences[] = "(".$preference->uid.",'".json_encode($newPrefCols)."','".$preference->modified."')";
			}

			// if we have some prefs to move over
			if (count($newpreferences) > 0)
			{
				foreach ($newpreferences as $pref)
				{
					$query = "INSERT INTO `#__xprofiles_dashboard_preferences`(uidNumber,preferences,modified) VALUES " . $pref;
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// drop old myhub tables
			$query = "DROP TABLE IF EXISTS `#__myhub`;";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "DROP TABLE IF EXISTS `#__myhub_params`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// create myhub table
		$query = "CREATE TABLE IF NOT EXISTS `#__myhub` (
					  `uid` int(11) NOT NULL,
                      `prefs` varchar(200) DEFAULT NULL,
                      `modified` datetime DEFAULT '0000-00-00 00:00:00'
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->setQuery($query);
		$this->db->query();

		// create myhub params table
		$query = "CREATE TABLE IF NOT EXISTS `#__myhub_params` (
					    `uid` int(11) NOT NULL,
                        `mid` int(11) NOT NULL,
                        `params` text
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$this->db->setQuery($query);
		$this->db->query();


		// remove new table
		$query = "DROP TABLE IF EXISTS `#__xprofiles_dashboard_preferences`;";
		$this->db->setQuery($query);
		$this->db->query();
	}
}
