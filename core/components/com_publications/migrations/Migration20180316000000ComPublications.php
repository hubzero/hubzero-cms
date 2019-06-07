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
 * Migration script for making sure accepted timestamp is set
 **/
class Migration20180316000000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_versions'))
		{
			$query = "SELECT id, published_up FROM `#__publication_versions` WHERE state=1 AND (accepted IS NULL or accepted='0000-00-00 00:00:00');";

			$this->db->setQuery($query);
			$pubs = $this->db->loadObjectList();

			if ($pubs)
			{
				foreach ($pubs as $pub)
				{
					$query = "UPDATE `#__publication_versions` SET accepted=" . $this->db->quote($pub->published_up) . " WHERE id=" . $this->db->quote($pub->id);

					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_versions'))
		{
			$query = "SELECT id FROM `#__publication_versions` WHERE state=1 AND accepted = published_up;";

			$this->db->setQuery($query);
			$pubs = $this->db->loadObjectList();

			if ($pubs)
			{
				foreach ($pubs as $pub)
				{
					$query = "UPDATE `#__publication_versions` SET accepted=" . $this->db->quote('0000-00-00 00:00:00') . " WHERE id=" . $this->db->quote($pub->id);

					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}
