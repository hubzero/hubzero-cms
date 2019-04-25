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
 * Migration script for removing excessiving escaping of content originating for early hub upgrades (before 1.2.0)
 **/
class Migration20150826208413ComContent extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__content'))
		{
			$this->db->setQuery("SELECT `id`, `attribs` from `#__content`;");

			$results = $this->db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					if (empty($r->attribs))
					{
						$attribs = "{}";
					}
					else
					{
						$attribs = $r->attribs;
						$attribs = preg_replace("/^{\"{\\\\\"{/", "{", $attribs);
						$attribs = preg_replace("/^{\"{/", "{", $attribs);
						$attribs = preg_replace("/}\\\\\":\\\\\\\"\\\\\"}\":\"\"}$/", "}", $attribs);
						$attribs = preg_replace("/}\":\"\"}$/", "}", $attribs);
						$attribs = preg_replace("/\\\\\\\\\\\\\"/", "\"", $attribs);
						$attribs = preg_replace("/\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"/", "\"", $attribs);
						$attribs = preg_replace("/\\\\\"/", "\"", $attribs);
					}

					$attribs = json_decode($attribs);

					if (json_last_error() === JSON_ERROR_NONE)
					{
						$attribs = json_encode($attribs);
						$this->db->setQuery("UPDATE `#__content` SET `attribs` = " . $this->db->quote($attribs) . " WHERE `id` = " . $this->db->quote($r->id));
						$this->db->query();
					}
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
	}
}
