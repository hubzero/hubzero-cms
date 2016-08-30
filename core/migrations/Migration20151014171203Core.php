<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for ensuring plugins are enabled for site login by default
 **/
class Migration20151014171203Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{
			// If a plugin is enabled, we'll assume that it should also be enabled for site login if not already saved
			// We changed the CMS to enforce the configuration option of site_login enabled.  Prior to this, it was
			// assuming true.  We need to default the database to emulate current behavior.
			$query = "SELECT `element`, `params` FROM `#__extensions` WHERE `type` = 'plugin' AND `folder` = 'authentication'";
			$this->db->setQuery($query);
			$plugins = $this->db->loadObjectList();

			if (count($plugins) > 0)
			{
				foreach ($plugins as $plugin)
				{
					$params = json_decode($plugin->params);

					if (is_null($params))
					{
						$params = new \stdClass();
					}

					if (!isset($params->site_login))
					{
						$params->site_login = "1";

						$this->saveParams('plg_authentication_' . $plugin->element, (array) $params);
					}
				}
			}
		}
	}
}