<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for removing redundant mod_tagcloud module
 **/
class Migration20150114122012ModTagcloud extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->deleteModuleEntry('mod_tagcloud');

		if ($this->db->tableExists('#__modules'))
		{
			$this->db->setQuery("UPDATE `#__modules` SET `module`='mod_toptags', `params`=" . $this->db->quote('{"numtags":"20","exclude":"","message":"No tags found.","sortby":"popularity","morelnk":"0","cache":"0","cache_time":"900"}') . " WHERE `module`='mod_tagcloud'");
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addModuleEntry('mod_tagcloud', 1, '');
	}
}