<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing deprecaed disablecache plugin
 **/
class Migration20161111173852PlgSystemCache extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "SELECT `params` FROM `#__extensions` WHERE `type`='plugin' AND `folder`='system' AND `element`='disablecache'";
			$this->db->setQuery($query);
			if ($params = $this->db->loadResult())
			{
				$params = $this->params($params);

				if (isset($params->definitions))
				{
					$query = "SELECT `params` FROM `#__extensions` WHERE `type`='plugin' AND `folder`='system' AND `element`='cache'";
					$this->db->setQuery($query);
					$cparams = $this->db->loadResult();
					$cparams = $this->params($cparams);

					$cparams->cacheexempt = $params->definitions;
					$cparams = json_encode($cparams);

					$query = "UPDATE `#__extensions` SET `params`=" . $this->db->quote($cparams) . " WHERE `type`='plugin' AND `folder`='system' AND `element`='cache'";
					$this->db->setQuery($query);
					$this->db->execute();
				}
			}
		}

		$this->deletePluginEntry('system', 'disablecache');
	}

	/**
	 * Convert string to object
	 *
	 * @param   string  $params
	 * @return  object
	 **/
	private function params($params)
	{
		$params = json_decode($params);
		if (json_last_error() !== JSON_ERROR_NONE)
		{
			$params = @parse_ini_string($params);
		}
		if (!isset($params) || !$params)
		{
			$params = new \stdClass;
		}
		return $params;
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->addPluginEntry('system', 'disablecache');
	}
}
