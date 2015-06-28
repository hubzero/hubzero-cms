<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding billboards component
 **/
class Migration20130930160944ComBillboards extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableExists('#__billboards'))
		{
			$query = "CREATE TABLE `#__billboards` (
						`id` int(11) unsigned NOT NULL auto_increment,
						`collection_id` int(11) default NULL,
						`name` varchar(255) default NULL,
						`header` varchar(255) default NULL,
						`text` text,
						`learn_more_text` varchar(255) default NULL,
						`learn_more_target` varchar(255) default NULL,
						`learn_more_class` varchar(255) default NULL,
						`learn_more_location` varchar(255) default NULL,
						`background_img` varchar(255) default NULL,
						`padding` varchar(255) default NULL,
						`alias` varchar(255) default NULL,
						`css` text,
						`published` tinyint(1) default '0',
						`ordering` int(11) default NULL,
						`checked_out` int(11) default '0',
						`checked_out_time` datetime default '0000-00-00 00:00:00',
						PRIMARY KEY  (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();

			$query = "INSERT INTO `#__billboards` (`collection_id`, `name`, `header`, `text`, `learn_more_text`, `learn_more_target`, `learn_more_class`, `learn_more_location`, `background_img`, `padding`, `alias`, `css`, `published`, `ordering`, `checked_out`, `checked_out_time`)
						VALUES (1,'Powered by HUBzero','Powered by HUBzero','HUBzero is a platform used to create dynamic web sites for scientific research and educational activities. With HUBzero, you can easily publish your research software and related educational materials on the web.','Learn more &rsaquo;','http://hubzero.org/about','learnmore','relative','slideone.png','15px','poweredbyhubzero','',1,1,0,'0000-00-00 00:00:00');

						INSERT INTO `#__billboards` (`collection_id`, `name`, `header`, `text`, `learn_more_text`, `learn_more_target`, `learn_more_class`, `learn_more_location`, `background_img`, `padding`, `alias`, `css`, `published`, `ordering`, `checked_out`, `checked_out_time`)
						VALUES (1,'Interactive simulation tools','Interactive simulation tools','The signature service of a hub is its ability to deliver interactive, graphical simulation tools through an ordinary web browser. In the world of portals and cyber-environments, this capability is completely unique.','Learn more &rsaquo;','http://hubzero.org/tour/features/#tools','learnmore','bottomright','slidetwo.png','0 0 0 225px','interactivesimulationtools','',1,2,0,'0000-00-00 00:00:00');

						INSERT INTO `#__billboards` (`collection_id`, `name`, `header`, `text`, `learn_more_text`, `learn_more_target`, `learn_more_class`, `learn_more_location`, `background_img`, `padding`, `alias`, `css`, `published`, `ordering`, `checked_out`, `checked_out_time`)
						VALUES (1,'Electronic library of resources','Electronic library of resources','Each hub is a place for users to come together and share information. One important way to accomplish this is by encouraging all users to upload their own tools, presentations, and other materials onto the hub.<br />','Learn more &rsaquo;','/contribute','learnmore','relative','slidethree.png','0 0 0 190px','electroniclibraryofresources','#electroniclibraryofresources h3 {\r\nline-height:2em;\r\n}',1,3,0,'0000-00-00 00:00:00');

						INSERT INTO `#__billboards` (`collection_id`, `name`, `header`, `text`, `learn_more_text`, `learn_more_target`, `learn_more_class`, `learn_more_location`, `background_img`, `padding`, `alias`, `css`, `published`, `ordering`, `checked_out`, `checked_out_time`)
						VALUES (1,'User groups for collaboration','User groups for collaboration','Groups are an easy way to share content and conversation, either privately or with the world. Many times, a group already exist for a specific interest or topic. If you can\'t find one you like, feel free to start your own.','Learn more &rsaquo;','/groups','learnmore','bottomright','slidefour.png','0 0 0 170px','usergroupsforcollaboration','#usergroupsforcollaboration h3 {\r\nline-height:2em;\r\n}',1,4,0,'0000-00-00 00:00:00');";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if (!$this->db->tableExists('#__billboard_collection'))
		{
			$query = "CREATE TABLE `#__billboard_collection` (
						`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						`name` varchar(255) DEFAULT NULL,
						PRIMARY KEY (`id`)
						) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			$this->db->setQuery($query);
			$this->db->query();

			$query = "INSERT INTO `#__billboard_collection` (`name`) VALUES ('Home Default Billboard');";
			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->addComponentEntry('Billboards');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__billboards'))
		{
			$query = "DROP TABLE `#__billboards`";

			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__billboard_collection'))
		{
			$query = "DROP TABLE `#__billboard_collection`";

			$this->db->setQuery($query);
			$this->db->query();
		}

		$this->deleteComponentEntry('Billboards');
	}
}