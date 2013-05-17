<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130517101308ComContent extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query  = "UPDATE `#__components` SET `params` = REPLACE(`params`,'show_pdf_icon=\n','') WHERE `option` = 'com_content';";
		$query .= "UPDATE `#__components` SET `params` = REPLACE(`params`,'show_pdf_icon=0\n','') WHERE `option` = 'com_content';";
		$query .= "UPDATE `#__components` SET `params` = REPLACE(`params`,'show_pdf_icon=1\n','') WHERE `option` = 'com_content';";
		$query .= "UPDATE `#__menu` SET `params` = REPLACE(`params`,'show_pdf_icon=\n','') WHERE `link` LIKE '%com_content%';";
		$query .= "UPDATE `#__menu` SET `params` = REPLACE(`params`,'show_pdf_icon=0\n','') WHERE `link` LIKE '%com_content%';";
		$query .= "UPDATE `#__menu` SET `params` = REPLACE(`params`,'show_pdf_icon=1\n','') WHERE `link` LIKE '%com_content%';";
		$query .= "UPDATE `#__content` SET `params` = REPLACE(`params`,'show_pdf_icon=\n','') WHERE `option` = 'com_content';";
		$query .= "UPDATE `#__content` SET `params` = REPLACE(`params`,'show_pdf_icon=1\n','') WHERE `option` = 'com_content';";
		$query .= "UPDATE `#__content` SET `params` = REPLACE(`params`,'show_pdf_icon=0\n','') WHERE `option` = 'com_content';";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		$query = "";

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}
