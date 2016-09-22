<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing some dated references to topics, rather than wiki
 **/
class Migration20131017133750ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wiki_page')
		 && $this->db->tableExists('#__wiki_version')
		 && $this->db->tableHasField('#__wiki_version', 'pageid'))
		{
			$query  = "SELECT * FROM `#__wiki_page` AS wp,";
			$query .= " `#__wiki_version` AS wv";
			$query .= " WHERE wp.id = wv.pageid";
			$query .= " AND wp.pagename = 'MainPage' AND (wp.group_cn='' OR wp.group_cn IS NULL)";
			$query .= " ORDER BY wv.version DESC";
			$query .= " LIMIT 1;";

			$this->db->setQuery($query);
			$result = $this->db->loadObject();

			if ($result)
			{
				$pagetext = preg_replace('/(Topic)/', 'Wiki', $result->pagetext);
				$pagehtml = preg_replace('/(Topic)/', 'Wiki', $result->pagehtml);
				$pagetext = preg_replace('/(topic)/', 'wiki', $pagetext);
				$pagehtml = preg_replace('/(topic)/', 'wiki', $pagehtml);

				$this->db->setQuery("UPDATE `#__wiki_version` SET `pagetext`=" . $this->db->quote($pagetext) . ", `pagehtml`=" . $this->db->quote($pagehtml) . " WHERE `pageid`=" . $result->pageid . " AND `version`=" . $result->version);
				$this->db->query();

				$title = preg_replace('/(Topic)/', 'Wiki', $result->title);

				$this->db->setQuery("UPDATE `#__wiki_page` SET `title`=" . $this->db->quote($title) . " WHERE `id`=" . $result->pageid);
				$this->db->query();
			}
		}
	}
}