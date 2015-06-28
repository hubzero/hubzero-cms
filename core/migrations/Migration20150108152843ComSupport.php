<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

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
			if (class_exists('\\Component'))
			{
				$params = \Component::params('com_support');
			}
			else
			{
				$params = \JComponentHelper::getParams('com_support');
			}

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
			if (class_exists('\\Component'))
			{
				$params = \Component::params('com_support');
			}
			else
			{
				$params = \JComponentHelper::getParams('com_support');
			}

			$defs = explode(',', $params->get('emails', '{config.mailfrom}'));
			$defs = array_map('trim', $defs);

			$params->set('emails', implode("\n", $defs));

			$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($params->toString()) . " WHERE `element`=" . $this->db->quote('com_support');
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}