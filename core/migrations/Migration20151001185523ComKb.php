<?php

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

			if ($this->db->tableHasField('#__faq', 'section'))
			{
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
		include_once(PATH_CORE . DS . 'libraries' . DS . 'joomla' . DS . 'database' . DS . 'table' . DS . 'category.php');

		$tbl = new \JTableCategory($this->db);
		$tbl->title       = $category->title;
		$tbl->alias       = $category->alias;
		$tbl->description = $category->description;
		$tbl->extension   = 'com_kb';
		$tbl->published   = $category->state;
		$tbl->access      = $category->access;
		$tbl->parent_id   = ($category->section ? $category->section : 1);
		$tbl->language    = '*';
		$tbl->level       = $category->level;
		$tbl->path        = $category->path;
		$tbl->store();

		return $tbl->id;
	}

	/**
	 * Make a #__faq_categories entry
	 *
	 * @param   object  $tbl
	 * @return  integer
	 */
	public function section($tbl)
	{
		$category = new \JTableCategory($this->db);
		$category->title   = $tbl->title;
		$category->alias   = $tbl->alias;
		$category->description = $tbl->description;
		$category->state   = $tbl->published;
		$category->access  = $tbl->access;
		$category->section = $tbl->parent_id;
		$category->store();

		return $category->id;
	}
}