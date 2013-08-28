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
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query  = "UPDATE `#__components` SET `params` = REPLACE(`params`,'show_pdf_icon=\n','') WHERE `option` = 'com_content';";
			$query .= "UPDATE `#__components` SET `params` = REPLACE(`params`,'show_pdf_icon=0\n','') WHERE `option` = 'com_content';";
			$query .= "UPDATE `#__components` SET `params` = REPLACE(`params`,'show_pdf_icon=1\n','') WHERE `option` = 'com_content';";
			$query .= "UPDATE `#__menu` SET `params` = REPLACE(`params`,'show_pdf_icon=\n','') WHERE `link` LIKE '%com_content%';";
			$query .= "UPDATE `#__menu` SET `params` = REPLACE(`params`,'show_pdf_icon=0\n','') WHERE `link` LIKE '%com_content%';";
			$query .= "UPDATE `#__menu` SET `params` = REPLACE(`params`,'show_pdf_icon=1\n','') WHERE `link` LIKE '%com_content%';";

			$db->setQuery($query);
			$db->query();
		}
		else
		{
			$query = "SELECT `extension_id`, `params` from `#__extensions` WHERE `element` = 'com_content'";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					$params = json_decode($r->params);
					unset($params->show_pdf_icon);

					$query = "UPDATE `#__extensions` SET `params` = " . $db->quote(json_encode($params)) . " WHERE `extension_id` = " . $db->quote($r->id);
					$db->setQuery($query);
					$db->query();
				}
			}

			$query = "SELECT `id`, `params` from `#__menu` WHERE `link` LIKE '%com_content%'";
			$db->setQuery($query);
			$results = $db->loadObjectList();

			if (count($results) > 0)
			{
				foreach ($results as $r)
				{
					$params = json_decode($r->params);
					unset($params->show_pdf_icon);

					$query = "UPDATE `#__menu` SET `params` = " . $db->quote(json_encode($params)) . " WHERE `id` = " . $db->quote($r->id);
					$db->setQuery($query);
					$db->query();
				}
			}
		}
	}
}
