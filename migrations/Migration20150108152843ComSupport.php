<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for changing the format of a com_support param value
 **/
class Migration20150108152843ComSupport extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$params = \JComponentHelper::getParams('com_support');

			$defs = str_replace("\r", '', $params->get('emails', '{config.mailfrom}'));
			$defs = str_replace('\n', "\n", $defs);
			$defs = explode("\n", $defs);
			$defs = array_map('trim', $defs);

			$params->set('emails', implode(', ', $defs));

			$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($params->toString()) . " WHERE `element`=" . $this->db->quote('com_support');
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
			$params = \JComponentHelper::getParams('com_support');

			$defs = explode(',', $params->get('emails', '{config.mailfrom}'));
			$defs = array_map('trim', $defs);

			$params->set('emails', implode("\n", $defs));

			$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($params->toString()) . " WHERE `element`=" . $this->db->quote('com_support');
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}