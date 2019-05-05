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
 * Migration script for migrating old com_sef data into com_redirect
 **/
class Migration20150206191525ComRedirect extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__redirection'))
		{
			$query = "SELECT * FROM `#__redirection`";
			$this->db->setQuery($query);
			if ($links = $this->db->loadObjectList())
			{
				include_once PATH_CORE . DS . 'components' . DS . 'com_redirect' . DS . 'tables' . DS . 'link.php';

				foreach ($links as $link)
				{
					$query = "SELECT id FROM `#__redirect_links` WHERE `old_url`=" . $this->db->quote($link->oldurl);
					$this->db->setQuery($query);
					if ($this->db->loadResult())
					{
						continue;
					}

					$tbl = new \Components\Redirect\Tables\Link($this->db);
					$tbl->old_url      = $link->oldurl;
					$tbl->new_url      = $link->newurl;
					$tbl->created_date = $link->dateadd;
					$tbl->store();
				}
			}

			$query = "DROP TABLE `#__redirection`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->deleteComponentEntry('com_sef');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if (!$this->db->tableExists('#__redirection'))
		{
			$query = "CREATE TABLE `#__redirection` (
				  `id` int(11) NOT NULL AUTO_INCREMENT,
				  `cpt` int(11) NOT NULL DEFAULT '0',
				  `oldurl` varchar(100) NOT NULL DEFAULT '',
				  `newurl` varchar(150) NOT NULL DEFAULT '',
				  `dateadd` date NOT NULL DEFAULT '0000-00-00',
				  PRIMARY KEY (`id`),
				  KEY `newurl` (`newurl`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
