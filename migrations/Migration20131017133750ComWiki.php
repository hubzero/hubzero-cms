<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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
			require_once JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'revision.php';
			require_once JPATH_ROOT . DS . 'components' . DS . 'com_wiki' . DS . 'tables' . DS . 'page.php';

			$cls = 'WikiPage';
			$cls2 = 'WikiPageRevision';
			if (class_exists('WikiTablePage'))
			{
				$cls = 'WikiTablePage';
				$cls2 = 'WikiTableRevision';
			}
			else if (class_exists('\\Components\\Wiki\\Tables\\Page'))
			{
				$cls = '\\Components\\Wiki\\Tables\\Page';
				$cls2 = '\\Components\\Wiki\\Tables\\Revision';
			}

			$version = new $cls2($this->db);
			$version->loadByVersion($result->pageid, $result->version);

			$page = new $cls($this->db);
			$page->load($result->pageid);

			$hostname = php_uname('n');

			// No need to run this on nanoHUB
			if (stripos($hostname, 'nanohub') === false)
			{
				$pagetext = preg_replace('/(Topic)/', 'Wiki', $result->pagetext);
				$pagehtml = preg_replace('/(Topic)/', 'Wiki', $result->pagehtml);
				$pagetext = preg_replace('/(topic)/', 'wiki', $pagetext);
				$pagehtml = preg_replace('/(topic)/', 'wiki', $pagehtml);
				$version->save(array('pagetext'=>$pagetext, 'pagehtml'=>$pagehtml));

				$page->set('title', preg_replace('/(Topic)/', 'Wiki', $page->title));
				$page->store();
			}
		}
	}
}