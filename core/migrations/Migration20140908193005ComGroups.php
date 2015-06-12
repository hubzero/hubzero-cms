<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding nested pages & comments
 **/
class Migration20140908193005ComGroups extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// add parent
		if (!$this->db->tableHasField('#__xgroups_pages', 'parent'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` ADD COLUMN `parent` INT(11) DEFAULT 0 AFTER `gidNumber`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add depth
		if (!$this->db->tableHasField('#__xgroups_pages', 'depth'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` ADD COLUMN `depth` INT(11) DEFAULT 1 AFTER `parent`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add left
		if (!$this->db->tableHasField('#__xgroups_pages', 'lft'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` ADD COLUMN `lft` INT(11) AFTER `parent`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add right
		if (!$this->db->tableHasField('#__xgroups_pages', 'rgt'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` ADD COLUMN `rgt` INT(11) AFTER `lft`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// drop ordering
		if ($this->db->tableHasField('#__xgroups_pages', 'ordering'))
		{
			// get a list of all hub & super groups 
			$query = "SELECT gidNumber, cn, description FROM `#__xgroups` WHERE `type` IN(1,3);";
			$this->db->setQuery($query);
			$groups = $this->db->loadObjectList();

			// default home page content
			$defaultHomePageContent = "<!-- {FORMAT:HTML} -->\n<p>[[Group.DefaultHomePage()]]</p>";

			// run through each group
			// loading all of their pages
			// create a default home page if one does not exist
			foreach ($groups as $group)
			{
				$query = "SELECT * FROM `#__xgroups_pages` WHERE `gidNumber`=" . $group->gidNumber . " ORDER BY ordering ASC;";
				$this->db->setQuery($query);
				$pages = $this->db->loadObjectList();

				// locate the home page
				$homePage = null;
				foreach ($pages as $k => $page)
				{
					if ($page->home == 1)
					{
						$homePage = $page;
						unset($pages[$k]);
						break;
					}
				}

				// if we dont ahve a home page we need one
				if ($homePage == null)
				{
					// create page
					$query = "INSERT INTO `#__xgroups_pages` (`gidNumber`, `parent`, `depth`, `lft`, `alias`, `title`, `state`, `privacy`, `home`) 
								VALUES ({$group->gidNumber}, 0, 0, 1, 'overview', 'Overview', 1, 'default', 1);";
					$this->db->setQuery($query);
					$this->db->query();

					$homePageId = $this->db->insertid();

					// create page version
					$query = "INSERT INTO `#__xgroups_pages_versions` (`pageid`, `version`, `content`, `created`, `created_by`)
								VALUES ({$homePageId}, 1, " . $this->db->quote($defaultHomePageContent) . ", NOW(), 1000);";
					$this->db->setQuery($query);
					$this->db->query();
				}
				else
				{
					// update the home page
					$query = "UPDATE `#__xgroups_pages` SET `parent`=0, `depth`=0, `lft`=1, `alias`='overview', `title`='Overview' WHERE `id`={$homePage->id};";
					$this->db->setQuery($query);
					$this->db->query();

					$homePageId = $homePage->id;
				}

				// loop through other pages
				$left = 2;
				foreach ($pages as $page)
				{
					// left is home's left plus 1
					$right = $left + 1;

					// update the left, right, parent, & depth
					$query = "UPDATE `#__xgroups_pages` SET `parent`={$homePageId}, `lft`= {$left}, `rgt`= {$right}, `depth`= 1 WHERE `id`= {$page->id};";
					$this->db->setQuery($query);
					$this->db->query();

					// add 2 to left for next iteration
					$left += 2;
				}

				// update the home page after weve computed all the left & rights
				$query = "UPDATE `#__xgroups_pages` SET `rgt`= {$left} WHERE `id`={$homePageId};";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// drop ordering column
			$query = "ALTER TABLE `#__xgroups_pages` DROP COLUMN `ordering`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add comments
		if (!$this->db->tableHasField('#__xgroups_pages', 'comments'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` ADD COLUMN `comments` TINYINT;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// drop parent
		if ($this->db->tableHasField('#__xgroups_pages', 'parent'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` DROP COLUMN `parent`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// drop depth
		if ($this->db->tableHasField('#__xgroups_pages', 'depth'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` DROP COLUMN `depth`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// drop left
		if ($this->db->tableHasField('#__xgroups_pages', 'lft'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` DROP COLUMN `lft`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// drop right
		if ($this->db->tableHasField('#__xgroups_pages', 'rgt'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` DROP COLUMN `rgt`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add ordering
		if (!$this->db->tableHasField('#__xgroups_pages', 'ordering'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` ADD COLUMN `ordering` INT(11) AFTER `title`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		//remove comments
		if ($this->db->tableHasField('#__xgroups_pages', 'comments'))
		{
			$query = "ALTER TABLE `#__xgroups_pages` DROP COLUMN `comments`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}