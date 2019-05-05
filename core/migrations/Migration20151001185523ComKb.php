<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// Restricted access
defined('_HZEXEC_') or die();

/**
 * Migration script for moving KB categories to #__categories
 **/
class Migration20151001185523ComKb extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__faq_categories') && $this->db->tableExists('#__categories'))
		{
			if ($this->db->tableHasField('#__faq', 'section'))
			{
				$query = "SELECT * FROM `#__faq_categories`";
				$this->db->setQuery($query);
				$categories = $this->db->loadObjectList();

				$sub = array();
				$par = array();

				foreach ($categories as $category)
				{
					if ($category->section)
					{
						$sub[] = $category;
						continue;
					}

					$category->section = 1;
					$category->level   = 1;
					$category->path    = $category->alias;

					$par[$category->id] = $this->category($category);
				}

				foreach ($sub as $category)
				{
					$parent = (isset($par[$category->section]) ? $par[$category->section] : 1);

					$category->path    = $category->alias;
					foreach ($categories as $c)
					{
						if ($c->id == $category->section)
						{
							$category->path = $c->alias . '/' . $category->alias;
							break;
						}
					}
					$category->section = $parent;
					$category->level   = 2;

					$par[$category->id] = $this->category($category);
				}

				$query = "SELECT MAX(rgt) FROM `#__categories` LIMIT 1";
				$this->db->setQuery($query);
				if ($max = $this->db->loadResult())
				{
					$max = intval($max);
					$query = "UPDATE `#__categories` SET `rgt`=" . $this->db->quote($max + 1) . " WHERE `extension`='system' AND `title`='ROOT'";
					$this->db->setQuery($query);
					$this->db->query();
				}

				$query = "UPDATE `#__categories` SET `parent_id`=1 WHERE `extension`='com_kb' AND `parent_id`=0";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "SELECT id, section, category FROM `#__faq`";
				$this->db->setQuery($query);
				$articles = $this->db->loadObjectList();

				foreach ($articles as $article)
				{
					$key = ($article->category ? $article->category : $article->section);

					$article->category = (isset($par[$key]) ? $par[$key] : 0);

					$query = "UPDATE `#__faq` SET `category`=" . $this->db->quote($article->category) . " WHERE `id`=" . $this->db->quote($article->id);
					$this->db->setQuery($query);
					$this->db->query();
				}

				if ($this->db->tableHasKey('#__faq', 'idx_section'))
				{
					$query = "ALTER TABLE `#__faq` DROP INDEX `idx_section`";
					$this->db->setQuery($query);
					$this->db->query();
				}

				$query = "ALTER TABLE `#__faq` DROP `section`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableExists('#__faq_categories'))
			{
				$query = "DROP TABLE `#__faq_categories`;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableExists('#__faq') && !$this->db->tableExists('#__kb_articles'))
			{
				$query = "RENAME TABLE `#__faq` TO `#__kb_articles`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableExists('#__faq_comments') && !$this->db->tableExists('#__kb_comments'))
			{
				$query = "RENAME TABLE `#__faq_comments` TO `#__kb_comments`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableExists('#__faq_helpful_log') && !$this->db->tableExists('#__kb_votes'))
			{
				$query = "RENAME TABLE `#__faq_helpful_log` TO `#__kb_votes`";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__kb_votes` SET `type`=" . $this->db->quote('article') . " WHERE `type`=" . $this->db->quote('entry');
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
		if (!$this->db->tableExists('#__faq_categories') && $this->db->tableExists('#__categories'))
		{
			if (!$this->db->tableExists('#__faq_categories'))
			{
				$query = "CREATE TABLE `#__faq_categories` (
					  `id` int(11) NOT NULL AUTO_INCREMENT,
					  `title` varchar(200) DEFAULT NULL,
					  `alias` varchar(200) DEFAULT NULL,
					  `description` varchar(255) DEFAULT '',
					  `section` int(11) NOT NULL DEFAULT '0',
					  `state` tinyint(3) NOT NULL DEFAULT '0',
					  `access` tinyint(3) NOT NULL DEFAULT '0',
					  `asset_id` int(11) NOT NULL DEFAULT '0',
					  PRIMARY KEY (`id`),
					  KEY `idx_alias` (`alias`),
					  KEY `idx_section` (`section`),
					  KEY `idx_state` (`state`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__faq') && $this->db->tableExists('#__kb_articles'))
			{
				$query = "RENAME TABLE `#__kb_articles` TO `#__faq`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__faq_comments') && $this->db->tableExists('#__kb_comments'))
			{
				$query = "RENAME TABLE `#__kb_comments` TO `#__faq_comments`";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableExists('#__faq_votes') && $this->db->tableExists('#__kb_votes'))
			{
				$query = "RENAME TABLE `#__kb_votes` TO `#__faq_helpful_log`";
				$this->db->setQuery($query);
				$this->db->query();

				$query = "UPDATE `#__faq_helpful_log` SET `type`=" . $this->db->quote('entry') . " WHERE `type`=" . $this->db->quote('article');
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasField('#__faq', 'section'))
			{
				$query = "ALTER TABLE `#__faq` ADD COLUMN `section` INT(11) NOT NULL DEFAULT '0';";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if (!$this->db->tableHasKey('#__faq', 'idx_section'))
			{
				$query = "ALTER TABLE `#__faq` ADD INDEX `idx_section` (`section`)";
				$this->db->setQuery($query);
				$this->db->query();
			}

			$query = "SELECT * FROM `#__categories` WHERE `extension`='com_kb'";
			$this->db->setQuery($query);
			$categories = $this->db->loadObjectList();

			$sub = array();
			$par = array();

			foreach ($categories as $category)
			{
				if ($category->parent_id)
				{
					$sub[] = $category;
					continue;
				}

				$par[$category->id] = $this->section($category);
			}

			foreach ($sub as $category)
			{
				//$parent = (isset($par[$category->parent_id]) ? $par[$category->parent_id] : 0);
				//$category->section = $parent;

				$par[$category->id] = $this->section($category);
			}

			$query = "SELECT id, category FROM `#__faq`";
			$this->db->setQuery($query);
			$articles = $this->db->loadObjectList();

			foreach ($articles as $article)
			{
				$article->section = (isset($par[$article->category]) ? $par[$article->category] : 0);

				$query = "UPDATE `#__faq` SET `section`=" . $this->db->quote($article->section) . " AND `category`=0 WHERE `id`=" . $this->db->quote($article->id);
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Make a #__categories entry
	 *
	 * @param   object  $category
	 * @return  integer
	 */
	public function category($category)
	{
		$id = 0;

		if (is_file(\Component::path('com_categories') . DS . 'models' . DS . 'category.php'))
		{
			include_once \Component::path('com_categories') . DS . 'models' . DS . 'category.php';

			// NOTE: We're using a model to do this as creating an entry involves
			// multiple queries due to the 'nested set' structure of the table
			$tbl = \Components\Categories\Models\Category::blank();
			$tbl->set('title', $category->title);
			$tbl->set('alias', $category->alias);
			$tbl->set('description', $category->description);
			$tbl->set('extension', 'com_kb');
			$tbl->set('published', $category->state);
			$tbl->set('access', $category->access);
			$tbl->set('parent_id', ($category->section ? $category->section : 1));
			$tbl->set('language', '*');
			$tbl->set('level', $category->level);
			$tbl->set('path', $category->path);
			$tbl->set('note', '');
			$tbl->set('metakey', '');
			$tbl->set('metadesc', '');
			$tbl->set('metadata', '');
			$tbl->set('params', '');

			$tbl->assetRules = new \Hubzero\Access\Rules(array());
			$tbl->setNameSpace('com_kb');

			$tbl->save();

			$id = $tbl->get('id');
		}

		return $id;
	}

	/**
	 * Make a #__faq_categories entry
	 *
	 * @param   object  $tbl
	 * @return  integer
	 */
	public function section($tbl)
	{
		$id = 0;

		if ($this->db->tableExists('#__faq_categories'))
		{
			$query = "INSERT INTO `#__faq_categories` (`id`, `title`, `alias`, `description`, `section`, `state`, `access`, `asset_id`)
					VALUES (NULL, " . $this->db->quote($tbl->title) . ", " . $this->db->quote($tbl->alias) . ", " . $this->db->quote($tbl->description) . ", " . $this->db->quote($tbl->parent_id) . ", " . $this->db->quote($tbl->published) . ", " . $this->db->quote($tbl->access) . ", NULL)";
			$this->db->setQuery($query);
			$this->db->query();

			$query = "SELECT id FROM `#__faq_categories` WHERE `title`=" . $this->db->quote($tbl->title) . " AND `alias`=" . $this->db->quote($tbl->alias) . " AND `section`=" . $this->db->quote($tbl->parent_id) . " LIMIT 1";
			$this->db->setQuery($query);
			$id = $this->db->loadResult();
		}

		return $id;
	}
}
