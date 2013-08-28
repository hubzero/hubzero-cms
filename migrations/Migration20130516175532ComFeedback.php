<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130516175532ComFeedback extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "UPDATE `#__components` SET `params` = REPLACE(`params`,'/components/com_feedback/images/contributor.gif','/components/com_feedback/assets/img/contributor.gif') WHERE `option` = 'com_feedback';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `params` = REPLACE(`params`,'/components/com_feedback/images/contributor.gif','/components/com_feedback/assets/img/contributor.gif') WHERE `element` = 'com_feedback';";
		}

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
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query = "UPDATE `#__components` SET `params` = REPLACE(`params`,'/components/com_feedback/assets/img/contributor.gif','/components/com_feedback/images/contributor.gif') WHERE `option` = 'com_feedback';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `params` = REPLACE(`params`,'/components/com_feedback/assets/img/contributor.gif','/components/com_feedback/images/contributor.gif') WHERE `element` = 'com_feedback';";
		}

		if (!empty($query))
		{
			$db->setQuery($query);
			$db->query();
		}
	}
}
