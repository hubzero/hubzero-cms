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
 * Migration script for incorrect link in default discover page content
 **/
class Migration20190327000000Discover extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__content'))
		{
			$query = "SELECT * FROM `#__content` WHERE `title`='Discover' AND `alias`='discover' AND `introtext` LIKE '%href=\"/store\"%'";
			$this->db->setQuery($query);
			$pages = $this->db->loadObjectList();

			if ($pages)
			{
				foreach ($pages as $page)
				{
					$content = $page->introtext;
					$content = str_replace('href="/store"', 'href="/storefront"', $content);

					$query = "UPDATE `#__content` SET `introtext`=" . $this->db->quote($content) . " WHERE `id`=" . $this->db->quote($page->id);
					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log('Updated Store link from /store to /storefront in default Discover page');
					}
					else
					{
						$this->log($query, 'warning');
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
		if ($this->db->tableExists('#__content'))
		{
			$query = "SELECT * FROM `#__content` WHERE `title`='Discover' AND `alias`='discover' AND `introtext` LIKE '%href=\"/storefront\"%'";
			$this->db->setQuery($query);
			$pages = $this->db->loadObjectList();

			if ($pages)
			{
				foreach ($pages as $page)
				{
					$content = $page->introtext;
					$content = str_replace('href="/storefront"', 'href="/store"', $content);

					$query = "UPDATE `#__content` SET `introtext`=" . $this->db->quote($content) . " WHERE `id`=" . $this->db->quote($page->id);
					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log('Updated Store link from /storefront to /store in default Discover page');
					}
					else
					{
						$this->log($query, 'warning');
					}
				}
			}
		}
	}
}
