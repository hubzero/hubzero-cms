<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing improperly stored URLs from the plg_content_collect plugin
 **/
class Migration20141121144051ComCollections extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__collections_items'))
		{
			$query = "SELECT `id`, `url` FROM `#__collections_items` WHERE `type`='article' AND `url` LIKE '%&'";
			$this->db->setQuery($query);

			if ($articles = $this->db->loadObjectList())
			{
				foreach ($articles as $article)
				{
					$article->url = rtrim($article->url, '&');
					$query = "UPDATE `#__collections_items` SET `url`=" . $this->db->quote($article->url) . " WHERE `id`=" . $this->db->quote($article->id);
					$this->db->setQuery($query);
					$this->db->query();
				}
			}
		}
	}
}