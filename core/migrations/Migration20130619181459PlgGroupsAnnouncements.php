<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding general announcements table and hiding group messaging tab
 **/
class Migration20130619181459PlgGroupsAnnouncements extends Base
{
	/**
	 * Up
	 **/
	public function up()
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
		$this->db->setQuery($query);
		$this->db->query();

		$params = array(
			'plugin_access' => 'members',
			'display_tab'   => 1
		);

		$this->addPluginEntry('groups', 'announcements', 1, $params);

		//get citation params
		if ($this->db->tableExists('#__extensions'))
		{
			$sql = "SELECT `params` FROM `#__extensions` WHERE `type`='plugin' AND `element`='messages' AND `folder` = 'groups'";
		}
		else
		{
			$sql = "SELECT `params` FROM `#__plugins` WHERE `element`='messages' AND `folder`='groups'";
		}

		$this->db->setQuery($sql);
		$p = $this->db->loadResult();

		//load params object
		$params = new \JParameter( $p );

		//set param to hide messages tab
		$params->set('display_tab', 0);

		//save new params
		if ($this->db->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `params`=".$this->db->quote(json_encode($params->toArray()))." WHERE `element`='messages' AND `folder`='groups'";
		}
		else
		{
			$query = "UPDATE `#__plugins` SET `params`='" . $params->toString() . "' WHERE `element`='messages' AND `folder`='groups'";
		}
		$this->db->setQuery($query);
		$this->db->query();
	}
}