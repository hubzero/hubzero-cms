<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing nanoHUB reference in wiki formatting page
 **/
class Migration20130708082138ComWiki extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query  = "SELECT * FROM `#__wiki_page` AS wp,";
		$query .= " `#__wiki_version` AS wv";
		$query .= " WHERE wp.id = wv.pageid";
		$query .= " AND wp.pagename = 'MainPage' AND (wp.group_cn='' OR wp.group_cn IS NULL)";
		$query .= " ORDER BY wv.version DESC";
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
				$pagetext = preg_replace('/(xsearch)/', 'search', $result->pagetext);
				$pagehtml = preg_replace('/(xsearch)/', 'search', $result->pagehtml);

				$version->save(array('pagetext'=>$pagetext, 'pagehtml'=>$pagehtml));
			}
		}
	}
}