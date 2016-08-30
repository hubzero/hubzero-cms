<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing nanoHUB reference in wiki formatting page
 **/
class Migration20130702181838ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wiki_page') && $this->db->tableExists('#__wiki_version'))
		{
			$query  = "SELECT wv.* FROM `#__wiki_page` AS wp,";
			$query .= " `#__wiki_version` AS wv";
			$query .= " WHERE wp.id = wv.pageid";
			$query .= " AND wp.pagename = 'Help:WikiFormatting'";
			$query .= " ORDER BY version DESC";
			$query .= " LIMIT 1;";

			$this->db->setQuery($query);
			$result = $this->db->loadObject();

			if ($result)
			{
				$pagetext = preg_replace('/(nanoHUB)/', 'This site', $result->pagetext);
				$pagehtml = preg_replace('/(nanoHUB)/', 'This site', $result->pagehtml);

				$query = "UPDATE `#__wiki_version` SET `pagetext`=" . $this->db->quote($pagetext) . ", `pagehtml`=" . $this->db->quote($pagehtml) . " WHERE `id`=" . $result->id;
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}