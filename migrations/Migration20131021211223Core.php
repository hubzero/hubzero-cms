<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for converting joomla upload max units
 **/
class Migration20131021211223Core extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if ($db->tableExists('#__extensions'))
		{
			$query = "SELECT `extension_id`, `params` FROM `#__extensions` WHERE `element` = 'com_media'";
			$db->setQuery($query);
			$result = $db->loadObject();

			if ($result)
			{
				$params = json_decode($result->params);

				if ($params->upload_maxsize > 1000000)
				{
					$params->upload_maxsize = $params->upload_maxsize / 1000000;

					$query = "UPDATE `#__extensions` SET `params` = " . $db->quote(json_encode($params)) . " WHERE `extension_id` = " . $db->quote($result->extension_id);
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}
}