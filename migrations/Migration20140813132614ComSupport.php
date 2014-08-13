<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing a typo in default support message
 **/
class Migration20140813132614ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__support_messages'))
		{
			//$query = "SELECT id FROM `#__support_messages` WHERE `message`=" . $this->db->quote('We haven not heard back from you so we will assume that you are no\r\nlonger experiencing the problem, or that you\\\'ve worked around it, and we\\\'ll consider the matter closed.  You can reopen the matter at any time by sending email or by submitting another problem report on our web site.\r\n\r\nThanks again for your support!\r\n--the {sitename} team');
			$query = 'SELECT id FROM `#__support_messages` WHERE `message`=' . $this->db->quote("We haven not heard back from you so we will assume that you are no\r\nlonger experiencing the problem, or that you\'ve worked around it, and we\'ll consider the matter closed.  You can reopen the matter at any time by sending email or by submitting another problem report on our web site.\r\n\r\nThanks again for your support!\r\n--the {sitename} team");
			$this->db->setQuery($query);
			if ($id = $this->db->loadResult())
			{
				$query = 'UPDATE `#__support_messages` SET `message`=' . $this->db->quote("We have not heard back from you so we will assume that you are no longer experiencing the problem, or that you\'ve worked around it, and we\'ll consider the matter closed.  You can reopen the matter at any time by sending email or by submitting another problem report on our web site.\r\n\r\nThanks again for your support!\r\n--the {sitename} team") . ' WHERE `id`=' . $this->db->quote($id);
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// No down.
	}
}