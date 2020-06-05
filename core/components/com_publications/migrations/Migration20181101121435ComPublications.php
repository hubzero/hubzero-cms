<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing series attachments to use version ID instead of publication ID
 **/
class Migration20181101121435ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "SELECT * FROM `#__publication_attachments` WHERE `object_name`='publication'";
		$this->db->setQuery($query);
		$attachments = $this->db->loadObjectList();

		foreach ($attachments as $attachment)
		{
			$query = "SELECT id FROM `#__publication_versions` WHERE `publication_id`=" . $attachment->object_id . " AND state=1 ORDER BY version_number DESC LIMIT 1";
			$this->db->setQuery($query);
			$version = $this->db->loadResult();

			if ($version)
			{
				$query = "UPDATE `#__publication_attachments` SET `object_id`=" . $version . " WHERE `object_name`='publication' AND `id`=" . $attachment->id;
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$query = "SELECT * FROM `#__publication_attachments` WHERE `object_name`='publication'";
		$this->db->setQuery($query);
		$attachments = $this->db->loadObjectList();

		foreach ($attachments as $attachment)
		{
			$query = "SELECT publication_id FROM `#__publication_versions` WHERE `id`=" . $attachment->object_id;
			$this->db->setQuery($query);
			$pub = $this->db->loadResult();

			if ($pub)
			{
				$query = "UPDATE `#__publication_attachments` SET `object_id`=" . $pub . " WHERE `object_name`='publication' AND `id`=" . $attachment->id;
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
