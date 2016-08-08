<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for removing embedded default passwords and excess escaping
 **/
class Migration201508281528321Core extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__extensions'))
		{

			$query[] = 'UPDATE jos_extensions SET manifest_cache = REPLACE(manifest_cache, "2013 Open Source Matters", "2014 Open Source Matters") WHERE extension_id < 10000;';
			$query[] = 'UPDATE jos_extensions SET params = REPLACE(params, "_HUB0_nW_", "") WHERE type="component" AND element="com_system";';
			$query[] = 'UPDATE jos_extensions SET params = REPLACE(params, "hubzero_network", "") WHERE type="component" AND element="com_system";';
			$query[] = 'UPDATE jos_extensions SET params = REPLACE(params, "hubzero.org", "") WHERE type="component" AND element="com_system";';
    		$query[] = 'UPDATE jos_extensions SET params = REPLACE(params, "ABQIAAAAPq8QOefNUw20Lc6RX2gKqhQkcPnh--THxGDMaCLza-8u_rvH7hQmdZgwooOYuoIkEqFAtrnkoY4ElA","") WHERE type="component" AND element="com_usage";';
    		$query[] = 'UPDATE jos_extensions SET manifest_cache = "" WHERE type="file" AND element="joomla";';		
			$query[] = 'UPDATE jos_extensions SET params = REPLACE(params, ":10,", ":\"10\",") WHERE type="component" AND element="com_media";';
			$query[] = 'UPDATE jos_extensions SET params = REPLACE(params, "site\\\\/media", "site/media") WHERE type="component" AND element="com_media" ;';
			$query[] = 'UPDATE jos_extensions SET params = REPLACE(params, "media\\\\/images", "media/images") WHERE type="component" AND element="com_media";';
			$query[] = 'UPDATE jos_extensions SET params = REPLACE(params, "image\\\\/", "image/") WHERE type="component" AND element="com_media";';
			$query[] = 'UPDATE jos_extensions SET params = REPLACE(params, "application\\\\/", "application/") WHERE type="component" AND element="com_media";';
			$query[] = 'UPDATE jos_extensions SET params = REPLACE(params, "text\\\\/", "text/") WHERE type="component" AND element="com_media";';

			foreach($query as $q)
			{
				$this->db->setQuery($q);
    			$this->db->query();
    		}
    	}
	}

	/**
	 * Down
	 **/
	public function down()
	{

	}
}
