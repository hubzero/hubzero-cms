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
class Migration20190929000000ComPublications extends Base
{
	/**
	 * Table name
	 *
	 * @var  string
	 **/
	public static $afterTagTable = '#__tags_object';
	public static $beforeTagTable = '#__tags_object_old';

	public static $pubTable = '#__publication_versions';
		
	/**
	 * Up
	 **/
	public function up()
	{
		$afterTagTable = self::$afterTagTable;
		$beforeTagTable = self::$beforeTagTable;
		$pubTable = self::$pubTable;
		
		// First, delete all publication tags with taggedon date less than 07/09/2019 
		if ($this->db->tableExists($afterTagTable)) {
			$this->log('Deleting publication tags prior to 07/08/2019...');
			
			$query = "DELETE FROM `$afterTagTable` WHERE `tbl`='publications' AND `taggedon` < '2019-07-09'";
			$this->db->setQuery($query);
			if (!$this->db->query())
			{
				$this->log('- FAILED!', 'error');
			} else {
				$this->log('- SUCCESS!', 'success');
			}
		} else {
			$this->log(sprintf("- Table %s doesn't exist.  Aborting...", $afterTagTable), 'error');
		}
		
		// Only do this if the previous database backups exist
		if ($this->db->tableExists($beforeTagTable) && $this->db->tableExists($afterTagTable) && $this->db->tableExists($pubTable))
		{
			// Get all publications (up to pub id 1342) that has tags
			$query = "SELECT DISTINCT objectid FROM `$beforeTagTable` WHERE `tbl`='publications'";
			$this->db->setQuery($query);
			$publications = $this->db->loadColumn();

			// Get all tags for each publication in this list
			$pubtags = array();
			foreach ($publications as $publication)
			{
				$query = "SELECT * FROM `$beforeTagTable` WHERE `tbl`='publications' AND `objectid`=" . $publication;
				$this->db->setQuery($query);

				$pubtags[$publication] = $this->db->loadObjectList();
			}
			
			// Copy tags to ALL versions of publication prior to tagopalypse
			foreach ($publications as $publication)
			{
				$query = "SELECT id, publication_id FROM `$pubTable` WHERE `publication_id`=" . $this->db->quote($publication) . " AND `created` < '2019-07-09'";
				$this->db->setQuery($query);
				$versions = $this->db->loadObjectList();

				// Set a log for weirdness where no versions exist BUT DON'T DELETE
				if (count($versions) <= 0)
				{
					$this->log(sprintf('Edge case: Check tags for publication ID %s', $publication));

					continue;
				}

				// No need to continue if this is empty
				if (!isset($pubtags[$publication]) || count($pubtags[$publication]) <= 0)
				{
					continue;
				}

				// Need some filtering/debugging info first to handle possible duplicate records...
				$query = "SELECT MAX(`id`) FROM `$pubTable` WHERE `created` < '2019-07-09'";
				$this->db->setQuery($query);
				$max_ver_id = $this->db->loadResult();
				
				// Now go through all versions of publication. 				
				foreach ($versions as $i => $version)
				{
					foreach ($pubtags[$publication] as $tag)
					{
						// Check to make sure tagid-objectid-tbl doesn't exist in table already (might happen if version tags were updated post-apocalypse)
						$legit = True;
						
						$query = "SELECT `id` FROM `$afterTagTable` WHERE `tbl` = 'publications' AND `objectid` = " . $this->db->quote($version->id) . " AND `tagid` = " . $this->db->quote($tag->tagid);
						$this->db->setQuery($query);
						$duplicates = $this->db->loadColumn();
						if (count($duplicates) > 0)
						{
							$legit = False; // Don't add this since duplicate exists
							
							// Should only be 1 duplicate
							$this->log(sprintf('Warning: %s duplicate record(s) for (tag id, object id) = (%s, %s)', count($duplicates), $tag->tagid, $version->id), 'warning');
							
							// Manually edited?  Check to see if it is legit by checking: (1) is version id less than $max_ver_id?, and (2) is the taggedon date larger than 2019-07-08?
							$query = $query . " AND `objectid` <= $max_ver_id AND `taggedon` > '2019-07-08'";
							$this->db->setQuery($query);
							$duplicates = $this->db->loadColumn();
							if (count($duplicates) > 0)
							{
								// Someone manually edited the tag for this version post-apocalypse.  Leave in database.
								$this->log(sprintf('- Tag edited on %s.  Not transferring this tag.', $tag->taggedon), 'warning');
							} else {
								// Looks like there was a duplicate tag in the previous database, so just log and move on.
								$this->log('- Duplicate tag in database.', 'warning');
							}
						}
						
						if ($legit)
						{
							if ($i == 0)
							{
								$this->log(sprintf('Inserting tag %s for tag ID %s and publication version ID %s (pub ID %s)...', $tag->id, $tag->tagid, $version->id, $version->publication_id));
							
								$query = "INSERT INTO `$afterTagTable` (`id`, `tagid`, `objectid`, `strength`, `taggerid`, `taggedon`, `tbl`, `label`) VALUES (" . $this->db->quote($tag->id) . ", " . $this->db->quote($tag->tagid) . ", " . $this->db->quote($version->id) . ", 1, " . $this->db->quote($tag->taggerid) . ", " . $this->db->quote($tag->taggedon) . ", 'publications', " . $this->db->quote($tag->label) . ")";
							}
							else 
							{
								$this->log(sprintf('Creating tag association for tag ID %s and publication version ID %s (pub ID %s)...', $tag->tagid, $version->id, $version->publication_id));
							
								$query = "INSERT INTO `$afterTagTable` (`tagid`, `objectid`, `strength`, `taggerid`, `taggedon`, `tbl`, `label`) VALUES (" . $this->db->quote($tag->tagid) . ", " . $this->db->quote($version->id) . ", 1, " . $this->db->quote($tag->taggerid) . ", " . $this->db->quote($tag->taggedon) . ", 'publications', " . $this->db->quote($tag->label) . ")";
							}
								
							$this->db->setQuery($query);
							if (!$this->db->query())
							{
								$this->log('- FAILED!', 'error');
							}
						}
					}
				}
			}
		} else {
			$this->log("Tables do not exist.  Aborting...", 'error');
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$afterTagTable = self::$afterTagTable;
		$pubTable = self::$pubTable;

		if ($this->db->tableExists($afterTagTable) && $this->db->tableExists($pubTable))
		{
			$query = "SELECT * FROM `$afterTagTable` WHERE `tbl`='publications'";
			$this->db->setQuery($query);
			$tags = $this->db->loadObjectList();

			foreach ($tags as $tag)
			{
				$query = "SELECT id, publication_id FROM `$pubTable` WHERE `id`=" . $this->db->quote($tag->objectid);
				$this->db->setQuery($query);
				$version = $this->db->loadObject();

				if ($version && $version->id)
				{
					$this->log(sprintf('Updated tag association #%s from publication version ID %s to publication ID %s...', $tag->id, $tag->objectid, $version->publication_id));
					
					$query = "UPDATE `$afterTagTable` SET `objectid`=" . $this->db->quote($version->publication_id) . " WHERE `id`=" . $this->db->quote($tag->id);
					$this->db->setQuery($query);

					if (!$this->db->query())
					{
						$this->log('FAILED!', 'error');
					}
					else
					{
						$this->log('SUCCESS!', 'success');						
					}
				}
			}
		}
	}
}
