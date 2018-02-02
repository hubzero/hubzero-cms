<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for migrating joomla content
 **/
class Migration20130924000002Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// Create assets table (all of this will only run the first time the table is created)
		if (!$this->db->tableExists('#__assets'))
		{
			$query = "CREATE  TABLE IF NOT EXISTS `#__assets` (
				`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
				`parent_id` INT(11) NOT NULL DEFAULT '0' ,
				`lft` INT(11) NOT NULL DEFAULT '0' ,
				`rgt` INT(11) NOT NULL DEFAULT '0' ,
				`level` INT(10) UNSIGNED NOT NULL ,
				`name` VARCHAR(50) NOT NULL ,
				`title` VARCHAR(100) NOT NULL ,
				`rules` VARCHAR(5120) NOT NULL ,
				PRIMARY KEY (`id`) ,
				UNIQUE INDEX `idx_asset_name` (`name` ASC) ,
				INDEX `idx_lft_rgt` (`lft` ASC, `rgt` ASC) ,
				INDEX `idx_parent_id` (`parent_id` ASC) )
			ENGINE = MYISAM
			DEFAULT CHARACTER SET = utf8
			COLLATE = utf8_general_ci;";

			$this->db->setQuery($query);
			$this->db->query();

			// Insert some default values
			$query = "INSERT INTO `#__assets` (`id`, `parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`)
			VALUES
				(1,0,0,0,0, 'root.1', 'Root Asset', '{\"core.login.site\":{\"1\":1,\"6\":1,\"2\":1},\"core.login.admin\":{\"6\":1},\"core.admin\":{\"8\":1},\"core.manage\":{\"7\":1},\"core.create\":{\"6\":1,\"3\":1},\"core.delete\":{\"6\":1},\"core.edit\":{\"6\":1,\"4\":1},\"core.edit.state\":{\"6\":1,\"5\":1},\"core.edit.own\":{\"6\":1,\"3\":1}}'),
				(2,1,0,0,1,'com_admin','com_admin','{}'),
				(3,1,0,0,1,'com_banners','com_banners','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
				(4,1,0,0,1,'com_cache','com_cache','{\"core.admin\":{\"7\":1},\"core.manage\":{\"7\":1}}'),
				(5,1,0,0,1,'com_checkin','com_checkin','{\"core.admin\":{\"7\":1},\"core.manage\":{\"7\":1}}'),
				(6,1,0,0,1,'com_config','com_config','{}'),
				(7,1,0,0,1,'com_contact','com_contact','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"core.edit.own\":[]}'),
				(8,1,0,0,1,'com_content','com_content','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":[],\"core.edit\":{\"4\":1},\"core.edit.state\":{\"5\":1},\"core.edit.own\":[]}'),
				(9,1,0,0,1,'com_cpanel','com_cpanel','{}'),
				(10,1,0,0,1,'com_installer','com_installer','{\"core.admin\":{\"7\":0},\"core.manage\":{\"7\":0},\"core.delete\":{\"7\":0},\"core.edit.state\":{\"7\":0}}'),
				(11,1,0,0,1,'com_languages','com_languages','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
				(12,1,0,0,1,'com_login','com_login','{}'),
				(13,1,0,0,1,'com_mailto','com_mailto','{}'),
				(14,1,0,0,1,'com_massmail','com_massmail','{}'),
				(15,1,0,0,1,'com_media','com_media','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":{\"5\":1}}'),
				(16,1,0,0,1,'com_menus','com_menus','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
				(17,1,0,0,1,'com_messages','com_messages','{\"core.admin\":{\"7\":1},\"core.manage\":{\"7\":1}}'),
				(18,1,0,0,1,'com_modules','com_modules','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
				(19,1,0,0,1,'com_newsfeeds','com_newsfeeds','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[],\"core.edit.own\":[]}'),
				(20,1,0,0,1,'com_plugins','com_plugins','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
				(21,1,0,0,1,'com_redirect','com_redirect','{\"core.admin\":{\"7\":1},\"core.manage\":[]}'),
				(22,1,0,0,1,'com_search','com_search','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1}}'),
				(23,1,0,0,1,'com_templates','com_templates','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.state\":[]}'),
				(24,1,0,0,1,'com_users','com_users','{\"core.admin\":{\"7\":1},\"core.manage\":[],\"core.create\":[],\"core.delete\":[],\"core.edit\":[],\"core.edit.own\":{\"6\":1},\"core.edit.state\":[]}'),
				(25,1,0,0,1,'com_weblinks','com_weblinks','{\"core.admin\":{\"7\":1},\"core.manage\":{\"6\":1},\"core.create\":{\"3\":1},\"core.delete\":[],\"core.edit\":{\"4\":1},\"core.edit.state\":{\"5\":1},\"core.edit.own\":[]}'),
				(26,1,0,0,1,'com_wrapper','com_wrapper','{}');";

			$this->db->setQuery($query);
			$this->db->query();

			// Insert all components as assets (parent is 0 because we don't need more than 1 entry per component - i.e. no sub items used for menus in 1.5)
			$this->db->setQuery('SELECT * FROM `#__components` WHERE parent = 0');
			$components = $this->db->loadObjectList();

			if (count($components) > 0)
			{
				// Build default ruleset
				$defaulRules = array(
					"core.admin"      => array(
						"7" => 1
						),
					"core.manage"     => array(
						"6" => 1
						),
					"core.create"     => array(),
					"core.delete"     => array(),
					"core.edit"       => array(),
					"core.edit.state" => array()
					);

				foreach ($components as $com)
				{
					// Make sure it isn't already in there
					$query = "SELECT id FROM `#__assets` WHERE `name` = " . $this->db->Quote($com->option);
					$this->db->setQuery($query);
					if ($this->db->loadResult())
					{
						continue;
					}

					// Craft query
					$query  = "INSERT INTO `#__assets` (`parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES ";
					$query .= "(";
					$query .= '1,';                                  // parent_id 1 is the root asset
					$query .= $this->db->Quote('') . ',';                  // lft
					$query .= $this->db->Quote('') . ',';                  // rgt
					$query .= '1,';                                  // level
					$query .= $this->db->Quote($com->option) . ',';        // name
					$query .= $this->db->Quote($com->option) . ',';        // title
					$query .= $this->db->Quote(json_encode($defaulRules)); // rules
					$query .= ");";

					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// Insert existing categories as assets (ignore root item)
			$this->db->setQuery('SELECT * FROM `#__categories` WHERE extension != "system"');
			$categories = $this->db->loadObjectList();

			if (count($categories) > 0)
			{
				foreach ($categories as $cat)
				{
					// Make sure it isn't already in there
					$query = "SELECT id FROM `#__assets` WHERE `name` = " . $this->db->Quote($cat->extension.'.category.'.$cat->id);
					$this->db->setQuery($query);
					if ($this->db->loadResult())
					{
						continue;
					}

					// Query for parent id
					$query = "SELECT `id` FROM `#__assets` WHERE `name` = " . $this->db->Quote($cat->extension);
					$this->db->setQuery($query);
					$result = $this->db->loadResult();
					if (!is_numeric($result))
					{
						// If we don't find the component entry, continue
						continue;
					}

					$query  = "INSERT INTO `#__assets` (`parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (";
					$query .= $this->db->Quote($result) . ',';                                                             // parent_id (from list entered above)
					$query .= $this->db->Quote('') . ',';                                                                  // lft
					$query .= $this->db->Quote('') . ',';                                                                  // rgt
					$query .= $cat->level+1 . ',';                                                                   // level
					$query .= $this->db->Quote($cat->extension.'.category.'.$cat->id) . ',';                               // name
					$query .= $this->db->Quote($cat->extension) . ',';                                                     // title
					$query .= $this->db->Quote('{"core.create":[],"core.delete":[],"core.edit":[],"core.edit.state":[]}'); // rules
					$query .= ");";
					$this->db->setQuery($query);
					$this->db->query();

					// Now, update the categories table with the asset id
					$id = $this->db->insertid();
					$query = "UPDATE `#__categories` SET `asset_id` = {$id} WHERE `id` = {$cat->id};";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// Now, go back and set parent_id for categories that are level 2 (those were original 1.5 categories, i.e. below sections)
			$query = "SELECT * FROM `#__categories` WHERE level = 2";
			$this->db->setQuery($query);
			$results = $this->db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					// Get the category id from the assets table
					$query = "SELECT `id` FROM `#__assets` WHERE name = " . $this->db->Quote('com_content.category.'.$r->id);
					$this->db->setQuery($query);
					$id = $this->db->loadResult();

					// Get the category parent id from the assets table
					$query = "SELECT `id` FROM `#__assets` WHERE name = " . $this->db->Quote('com_content.category.'.$r->parent_id);
					$this->db->setQuery($query);
					$parent_id = $this->db->loadResult();

					// Update the assets table
					$query = "UPDATE `#__assets` SET parent_id = {$parent_id} WHERE `id` = {$id}";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// We're going to go ahead and add asset_id here, as we need to insert into below
			if (!$this->db->tableHasField('#__content', 'asset_id') && $this->db->tableHasField('#__content', 'id'))
			{
				$query = "ALTER TABLE `#__content` ADD COLUMN `asset_id` INTEGER UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK to the #_assets table.' AFTER `id`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			// Insert articles
			$this->db->setQuery('SELECT * FROM `#__content`');
			$articles = $this->db->loadObjectList();

			if (count($articles) > 0)
			{
				foreach ($articles as $art)
				{
					// Query for parent ID
					$query = "SELECT `id`, `level` FROM `#__assets` WHERE `name` = " . $this->db->Quote('com_content.category.'.$art->catid);
					$this->db->setQuery($query);
					$obj    = $this->db->loadObject();
					$level  = (is_object($obj) && is_numeric($obj->level)) ? $obj->level+1 : 4;
					if (is_object($obj) && is_numeric($obj->id))
					{
						$result = $obj->id;
					}
					else
					{
						// We didn't find a parent id, so just use the 'uncategorised' category
						$query = "SELECT `asset_id` FROM `#__categories` WHERE `extension` = 'com_content' AND `alias` = 'uncategorised';";
						$this->db->setQuery($query);
						if (!$result = $this->db->loadResult())
						{
							continue;
						}
					}

					$query  = "INSERT INTO `#__assets` (`parent_id`, `lft`, `rgt`, `level`, `name`, `title`, `rules`) VALUES (";
					$query .= $this->db->Quote($result) . ',';                                            // parent_id
					$query .= $this->db->Quote('') . ',';                                                 // lft
					$query .= $this->db->Quote('') . ',';                                                 // rgt
					$query .= $level . ',';                                                         // level
					$query .= $this->db->Quote('com_content.article.'.$art->id) . ',';                    // name
					$query .= $this->db->Quote($art->title) . ',';                                        // title
					$query .= $this->db->Quote('{"core.delete":[],"core.edit":[],"core.edit.state":[]}'); // rules
					$query .= ")";
					$this->db->setQuery($query);
					$this->db->query();

					// Now, update the content table with the asset id
					$id = $this->db->insertid();
					$query = "UPDATE `#__content` SET `asset_id` = {$id} WHERE `id` = {$art->id};";
					$this->db->setQuery($query);
					$this->db->query();
				}
			}

			// Rule set for super admins only
			$rules = array(
				"core.admin"      => array(
					"7" => 1
					),
				"core.manage"     => array(
					"7" => 1
					),
				"core.create"     => array(
					"7" => 1
					),
				"core.delete"     => array(
					"7" => 1
					),
				"core.edit"       => array(
					"7" => 1
					),
				"core.edit.state" => array(
					"7" => 1
					)
				);
			$this->db->setQuery("UPDATE `#__assets` SET rules='".json_encode($rules)."' WHERE NAME= 'com_mailto' OR NAME='com_massmail' OR NAME='com_config';");
			$this->db->query();

			// If we have the nested set class available, use it to rebuild lft/rgt
			if (class_exists('JTableNested') && method_exists('JTableNested', 'rebuild'))
			{
				// Rebuild categories
				// Use the MySQL driver for this
				$config = \JFactory::getConfig();
				$database = \JDatabase::getInstance(
					array(
						'driver'   => 'mysql',
						'host'     => $config->get('host'),
						'user'     => $config->get('user'),
						'password' => $config->get('password'),
						'database' => $config->get('db')
					)
				);

				$table = new \JTableCategory($database);
				$table->rebuild();

				// Rebuild assets
				$this->rebuildAssets();
			}
		}
	}

	private function rebuildAssets($parentId=1, $leftId=0, $level=0)
	{
		$database = \JFactory::getDbo();
		$query = $database->getQuery(true);
		$query->select('id');
		$query->from('#__assets');
		$query->where('parent_id = %d');
		$query->order('parent_id, lft');
		$database->setQuery(sprintf($query, (int) $parentId));
		$children = $database->loadObjectList();

		$rightId = $leftId + 1;

		foreach ($children as $node)
		{
			$rightId = $this->rebuildAssets($node->id, $rightId, $level + 1);

			if ($rightId === false)
			{
				return false;
			}
		}

		$query = $database->getQuery(true);
		$query->update('#__assets');
		$query->set('lft = ' . (int) $leftId);
		$query->set('rgt = ' . (int) $rightId);
		$query->set('level = ' . (int) $level);
		$query->where('id = ' . (int) $parentId);
		$database->setQuery($query);
		$database->execute();

		return $rightId + 1;
	}
}
