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
 * Migration script for converting joomla upload max units
 **/
class Migration20131021211223Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT `extension_id`, `params` FROM `#__extensions` WHERE `element` = 'com_media'";
			$this->db->setQuery($query);
			$result = $this->db->loadObject();

			if ($result)
			{
				$params = json_decode($result->params);

				if ($params->upload_maxsize > 1000000)
				{
					$params->upload_maxsize = $params->upload_maxsize / 1000000;

					$query = "UPDATE `#__extensions` SET `params` = " . $this->db->quote(json_encode($params)) . " WHERE `extension_id` = " . $this->db->quote($result->extension_id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}
