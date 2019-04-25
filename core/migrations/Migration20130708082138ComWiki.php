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
 * Migration script for fixing 'xsearch' reference in wiki formatting page
 **/
class Migration20130708082138ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__wiki_page') && $this->db->tableExists('#__wiki_version') && $this->db->tableHasField('#__wiki_version', 'pageid'))
		{
			$query  = "SELECT wv.* FROM `#__wiki_page` AS wp,";
			$query .= " `#__wiki_version` AS wv";
			$query .= " WHERE wp.id = wv.pageid";
			$query .= " AND wp.pagename = 'MainPage' AND (wp.group_cn='' OR wp.group_cn IS NULL)";
			$query .= " ORDER BY wv.version DESC";
			$query .= " LIMIT 1;";

			$this->db->setQuery($query);
			$result = $this->db->loadObject();

			if ($result)
			{
				$pagetext = preg_replace('/(xsearch)/', 'search', $result->pagetext);
				$pagehtml = preg_replace('/(xsearch)/', 'search', $result->pagehtml);

				$query = "UPDATE `#__wiki_version` SET `pagetext`=" . $this->db->quote($pagetext) . ", `pagehtml`=" . $this->db->quote($pagehtml) . " WHERE `id`=" . $result->id;
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}
