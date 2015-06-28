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
		$query  = "SELECT * FROM `#__wiki_page` AS wp,";
		$query .= " `#__wiki_version` AS wv";
		$query .= " WHERE wp.id = wv.pageid";
		$query .= " AND pagename = 'Help:WikiFormatting'";
		$query .= " ORDER BY version DESC";
		$query .= " LIMIT 1;";

		$this->db->setQuery($query);
		$result = $this->db->loadObject();

		require_once PATH_CORE . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'revision.php';

		if ($result)
		{
			$cls = 'WikiPageRevision';
			if (class_exists('WikiTableRevision'))
			{
				$cls = 'WikiTableRevision';
			}
			else if (class_exists('\\Components\\Wiki\\Tables\\Revision'))
			{
				$cls = '\\Components\\Wiki\\Tables\\Revision';
			}

			$version = new $cls($this->db);
			$version->loadByVersion($result->pageid, $result->version);

			$hostname = php_uname('n');

			// No need to run this on nanoHUB
			if (stripos($hostname, 'nanohub') === false)
			{
				$pagetext = preg_replace('/(nanoHUB)/', 'This site', $result->pagetext);
				$pagehtml = preg_replace('/(nanoHUB)/', 'This site', $result->pagehtml);

				$version->save(array('pagetext'=>$pagetext, 'pagehtml'=>$pagehtml));
			}
		}
	}
}