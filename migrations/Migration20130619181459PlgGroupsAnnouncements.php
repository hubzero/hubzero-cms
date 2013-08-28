<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20130619181459PlgGroupsAnnouncements extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		$query = "CREATE TABLE IF NOT EXISTS `jos_announcements` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`scope` varchar(100) DEFAULT NULL,
						`scope_id` int(11) DEFAULT NULL,
						`content` text,
						`priority` tinyint(2) NOT NULL DEFAULT '0',
						`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
						`created_by` int(11) NOT NULL DEFAULT '0',
						`state` tinyint(2) NOT NULL DEFAULT '0',
						`publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
						`publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
						`sticky` tinyint(2) NOT NULL DEFAULT '0',
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";
		$db->setQuery($query);
		$db->query();

		$params = array(
			'plugin_access' => 'members',
			'display_tab'   => 1
		);

		self::addPluginEntry('groups', 'announcements', 1, $params);

		//get citation params
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$sql = "SELECT `params` FROM `#__extensions` WHERE `type`='plugin' AND `element`='messages' AND `folder` = 'groups'";
		}
		else
		{
			$sql = "SELECT `params` FROM `#__plugins` WHERE `element`='messages' AND `folder`='groups'";
		}

		$db->setQuery($sql);
		$p = $db->loadResult();

		//load params object
		$params = new JParameter( $p );

		//set param to hide messages tab
		$params->set('display_tab', 0);

		//save new params
		if (version_compare(JVERSION, '1.6', 'ge'))
		{
			$query = "UPDATE `#__extensions` SET `params`=".$db->quote(json_encode($params->toArray()))." WHERE `element`='messages' AND `folder`='groups'";
		}
		else
		{
			$query = "UPDATE `#__plugins` SET `params`='" . $params->toString() . "' WHERE `element`='messages' AND `folder`='groups'";
		}
		$db->setQuery($query);
		$db->query();
	}
}