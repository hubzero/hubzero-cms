<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Config\Registry;

/**
 * Give tool pages their About tab back
 *
 * In 2014 the abouttool plugin was folded into about, and every resource
 * type that used it was switched over (Migration20141022103600). A migration
 * the next year (Migration20150826205631) then gave the tools type
 * plg_abouttool=1 wherever the setting was missing - which, after the first
 * migration, was everywhere - and plg_about=0. Since then a tool's page on
 * such a hub has had no About tab: no description, no credits, no citation,
 * nothing at all under its tabs.
 *
 * Any type still asking for the plugin that no longer exists is switched to
 * the one that does, as the 2014 migration meant. A type with both turned
 * off is left as it is, since that may be what its administrator chose.
 **/
class Migration20261029120000ComResources extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__resource_types'))
		{
			return;
		}

		$this->db->setQuery(
			"SELECT `id`, `params` FROM `#__resource_types`
			 WHERE `params` LIKE '%plg_abouttool=1%' OR `params` LIKE '%\"plg_abouttool\":\"1\"%'"
		);

		foreach ((array) $this->db->loadObjectList() as $row)
		{
			$params = new Registry($row->params);
			$params->set('plg_about', 1);
			$params->set('plg_abouttool', 0);

			$this->db->setQuery(
				"UPDATE `#__resource_types` SET `params` = " . $this->db->quote($params->toString())
				. " WHERE `id` = " . (int) $row->id
			);
			$this->db->query();
		}
	}

	/**
	 * Down
	 *
	 * Nothing to undo: the plugin these types asked for does not exist, so
	 * asking for it again would only take the tab away.
	 **/
	public function down()
	{
	}
}
