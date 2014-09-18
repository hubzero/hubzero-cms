<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing nanoHUB reference in wiki formatting page
 **/
class Migration20130708082138ComWiki extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query  = "SELECT * FROM `#__wiki_page` AS wp,";
		$query .= " `#__wiki_version` AS wv";
		$query .= " WHERE wp.id = wv.pageid";
		$query .= " AND wp.pagename = 'MainPage' AND (wp.group_cn='' OR wp.group_cn IS NULL)";
		$query .= " ORDER BY wv.version DESC";
		$query .= " LIMIT 1;";

		$db->setQuery($query);
		$result = $db->loadObject();

		require_once JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'revision.php';

		if ($result)
		{
			$cls = 'WikiPageRevision';
			if (class_exists('WikiTableRevision'))
			{
				$cls = 'WikiTableRevision';
			}

			$version = new $cls($db);
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
