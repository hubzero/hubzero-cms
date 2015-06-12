<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for feedback image update based on asset relocation
 **/
class Migration20130516175532ComFeedback extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__components'))
		{
			$query = "UPDATE `#__components` SET `params` = REPLACE(`params`,'/components/com_feedback/images/contributor.gif','/components/com_feedback/assets/img/contributor.gif') WHERE `option` = 'com_feedback';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `params` = REPLACE(`params`,'/components/com_feedback/images/contributor.gif','/components/com_feedback/assets/img/contributor.gif') WHERE `element` = 'com_feedback';";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__components'))
		{
			$query = "UPDATE `#__components` SET `params` = REPLACE(`params`,'/components/com_feedback/assets/img/contributor.gif','/components/com_feedback/images/contributor.gif') WHERE `option` = 'com_feedback';";
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `params` = REPLACE(`params`,'/components/com_feedback/assets/img/contributor.gif','/components/com_feedback/images/contributor.gif') WHERE `element` = 'com_feedback';";
		}

		if (!empty($query))
		{
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
