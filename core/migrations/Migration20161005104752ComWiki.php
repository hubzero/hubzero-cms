<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating old 'new page' links in group wikis
 **/
class Migration20161005104752ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wiki_versions') && $this->db->tableExists('#__wiki_pages'))
		{
			$query = "SELECT v.id, v.pagetext, v.pagehtml
					FROM `#__wiki_versions` AS v
					INNER JOIN `#__wiki_pages` AS p ON p.`id`=v.`page_id`
					WHERE p.`scope`='group' AND v.`pagetext` LIKE " . $this->db->quote('%[?task=new Create a new article]%');
			$this->db->setQuery($query);
			$versions = $this->db->loadObjectList();

			foreach ($versions as $version)
			{
				$version->pagetext = str_replace('[?task=new Create a new article]', '[?action=new Create a new article]', $version->pagetext);
				$version->pagehtml = str_replace('/wiki/tasknew">Create a new article</a>', '/wiki/?action=new">Create a new article</a>', $version->pagehtml);

				$query = "UPDATE `#__wiki_versions` SET `pagetext`=" . $this->db->quote($version->pagetext) . " WHERE `id`=" . $version->id;
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
		if ($this->db->tableExists('#__wiki_versions') && $this->db->tableExists('#__wiki_pages'))
		{
			$query = "SELECT v.id, v.pagetext, v.pagehtml
					FROM `#__wiki_versions` AS v
					INNER JOIN `#__wiki_pages` AS p ON p.`id`=v.`page_id`
					WHERE p.`scope`='group' AND v.`pagetext` LIKE " . $this->db->quote('%[?action=new Create a new article]%');
			$this->db->setQuery($query);
			$versions = $this->db->loadObjectList();

			foreach ($versions as $version)
			{
				$version->pagetext = str_replace('[?action=new Create a new article]', '[?task=new Create a new article]', $version->pagetext);
				$version->pagehtml = str_replace('/wiki/?action=new">Create a new article</a>', '/wiki/tasknew">Create a new article</a>', $version->pagehtml);

				$query = "UPDATE `#__wiki_versions` SET `pagetext`=" . $this->db->quote($version->pagetext) . " WHERE `id`=" . $version->id;
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
