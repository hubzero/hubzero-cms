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
 * Migration script for adding entry for Publications - Citations plugin
 **/
class Migration20190620000000PlgPublicationsCitations extends Base
{
	/**
	 * Table name
	 *
	 * @var  string
	 **/
	public static $table = '#__citations_assoc';

	/**
	 * Up
	 **/
	public function up()
	{
		$table = self::$table;

		if ($this->db->tableExists($table) && $this->db->tableExists('#__publication_versions'))
		{
			$query = "SELECT * FROM `$table` WHERE `tbl`='publication'";
			$this->db->setQuery($query);
			$citations = $this->db->loadObjectList();

			foreach ($citations as $citation)
			{
				$query = "SELECT id, publication_id FROM `#__publication_versions` WHERE `publication_id`=" . $this->db->quote($citation->oid);
				$this->db->setQuery($query);
				$versions = $this->db->loadObjectList();

				foreach ($versions as $i => $version)
				{
					if ($i == 0)
					{
						$query = "UPDATE `$table` SET `oid`=" . $this->db->quote($version->id) . " WHERE `id`=" . $this->db->quote($citation->id);

						$msg = sprintf('Updated citation association #%s from publication ID %s to publication version ID %s', $citation->id, $citation->oid, $version->id);
					}
					else
					{
						$query = "INSERT INTO `$table` (`cid`, `oid`, `type`, `tbl`) VALUES (" . $this->db->quote($citation->cid) . ", " . $this->db->quote($version->id) . ", " . $this->db->quote($citation->type) . ", 'publication')";

						$msg = sprintf('Creating citation association for citation ID %s and publication version ID %s', $citation->cid, $version->id);
					}
					$this->db->setQuery($query);
					if (!$this->db->query())
					{
						$this->log('Query failed: ' . $query, 'error');
					}
					else
					{
						$this->log($msg);
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
			$query = "SELECT * FROM `$table` WHERE `tbl`='publication'";
			$this->db->setQuery($query);
			$citations = $this->db->loadObjectList();

			foreach ($citations as $citation)
			{
				$query = "SELECT id, publication_id FROM `#__publication_versions` WHERE `id`=" . $this->db->quote($citation->oid);
				$this->db->setQuery($query);
				$version = $this->db->loadObject();

				if ($version && $version->id)
				{
					$query = "UPDATE `$table` SET `oid`=" . $this->db->quote($version->publication_id) . " WHERE `id`=" . $this->db->quote($citation->id);
					$this->db->setQuery($query);

					if (!$this->db->query())
					{
						$this->log('Query failed: ' . $query, 'error');
					}
					else
					{
						$this->log(sprintf('Updated citation association #%s from publication version ID %s to publication ID %s', $citation->id, $citation->oid, $version->publication_id));
					}
				}
			}
		}
	}
}
