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
 * Migration script for fixing some display issues with old support tickets
 **/
class Migration20141022110100ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_tickets'))
		{
			$this->db->setQuery("SELECT id, report FROM `#__support_tickets` WHERE `created` < '2013-01-01 00:00:00' AND `report` LIKE '%\\\\\'%' AND `type`=0 AND `open`=1");

			if ($records = $this->db->loadObjectList())
			{
				foreach ($records as $row)
				{
					$row->report = str_replace('&quot;', '"', $row->report);
					$row->report = stripslashes($row->report);
					$row->report = html_entity_decode($row->report);
					$row->summary = substr($row->report, 0, 70);
					if (strlen($row->summary) >=70)
					{
						$row->summary .= '...';
					}

					$this->db->setQuery(
						"UPDATE `#__support_tickets`
						SET `report`=" . $this->db->quote($row->report) . ", `summary`=" . $this->db->quote($row->summary) . "
						WHERE `id`=" . $this->db->quote($row->id)
					);
					$this->db->query();
				}
			}
		}
	}
}
