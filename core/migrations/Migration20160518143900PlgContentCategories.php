<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for renaming Joomla content plugin
 **/
class Migration20160518143900PlgContentCategories extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `name`='plg_content_categories', `element`='categories' WHERE `folder`='content' AND `element`='joomla';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `name`='plg_content_joomla', `element`='joomla' WHERE `folder`='content' AND `element`='categories';";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}