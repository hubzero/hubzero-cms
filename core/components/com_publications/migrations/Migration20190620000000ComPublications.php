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
 * Migration script for having tags associated with publication versions
 **/
class Migration20190620000000ComPublications extends Base
{
	/**
	 * Table name
	 *
	 * @var  string
	 **/
	public static $table = '#__tags_object';

	/**
	 * Up
	 **/
	public function up()
	{
		$table = self::$table;

		if ($this->db->tableExists($table) && $this->db->tableExists('#__publication_versions'))
		{
			$query = "SELECT DISTINCT objectid FROM `$table` WHERE `tbl`='publications'";
			$this->db->setQuery($query);
			$publications = $this->db->loadColumn();

			foreach ($publications as $publication)
			{
				$query = "SELECT id, publication_id FROM `#__publication_versions` WHERE `publication_id`=" . $this->db->quote($publication);
				$this->db->setQuery($query);
				$versions = $this->db->loadObjectList();

				foreach ($versions as $i => $version)
				{
					if ($i == 0)
					{
						$query = "UPDATE `$table` SET `objectid`=" . $this->db->quote($version->id) . " WHERE `tbl`='publications' AND `objectid`=" . $this->db->quote($publication);

						$this->db->setQuery($query);
						if (!$this->db->query())
						{
							$this->log('Query failed: ' . $query, 'error');
						}
						else
						{
							$this->log(sprintf('Updated tag association from publication ID %s to publication version ID %s', $publication, $version->id));
						}
					}
					else
					{
						$query = "SELECT * FROM `$table` WHERE `tbl`='publications' AND `objectid`=" . $publication;
						$this->db->setQuery($query);
						$tags = $this->db->loadObjectList();

						foreach ($tags as $tag)
						{
							$query = "INSERT INTO `$table` (`tagid`, `objectid`, `strength`, `taggerid`, `taggedon`, `tbl`, `label`) VALUES (" . $this->db->quote($tag->tagid) . ", " . $this->db->quote($version->id) . ", 1, " . $this->db->quote($tag->taggerid) . ", " . $this->db->quote($tag->taggedon) . ", 'publications', '')";

							$this->db->setQuery($query);
							if (!$this->db->query())
							{
								$this->log('Query failed: ' . $query, 'error');
							}
							else
							{
								$this->log(sprintf('Creating tag association for tag ID %s and publication version ID %s', $tag->tagid, $version->id));
							}
						}
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
		$table = self::$table;

		if ($this->db->tableExists($table) && $this->db->tableExists('#__publication_versions'))
		{
			$query = "SELECT * FROM `$table` WHERE `tbl`='publications'";
			$this->db->setQuery($query);
			$tags = $this->db->loadObjectList();

			foreach ($tags as $tag)
			{
				$query = "SELECT id, publication_id FROM `#__publication_versions` WHERE `id`=" . $this->db->quote($tag->objectid);
				$this->db->setQuery($query);
				$version = $this->db->loadObject();

				if ($version && $version->id)
				{
					$query = "UPDATE `$table` SET `objectid`=" . $this->db->quote($version->publication_id) . " WHERE `id`=" . $this->db->quote($tag->id);
					$this->db->setQuery($query);

					if (!$this->db->query())
					{
						$this->log('Query failed: ' . $query, 'error');
					}
					else
					{
						$this->log(sprintf('Updated tag association #%s from publication version ID %s to publication ID %s', $tag->id, $tag->objectid, $version->publication_id));
					}
				}
			}
		}
	}
}
