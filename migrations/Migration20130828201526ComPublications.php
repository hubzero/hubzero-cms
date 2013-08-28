<?php

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for creating publication categories table (if it doesn't exist)
 **/
class Migration20130828201526ComPublications extends Hubzero_Migration
{
	/**
	 * Up
	 **/
	protected static function up($db)
	{
		if (!$db->tableExists('#__publication_categories'))
		{
			$query = "CREATE TABLE IF NOT EXISTS `#__publication_categories` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`name` varchar(200) NOT NULL DEFAULT '',
				`dc_type` varchar(200) NOT NULL DEFAULT 'Dataset',
				`alias` varchar(200) NOT NULL DEFAULT '',
				`url_alias` varchar(200) NOT NULL DEFAULT '',
				`description` tinytext,
				`contributable` int(2) DEFAULT '1',
				`state` tinyint(1) DEFAULT '1',
				`customFields` text,
				`params` text,
				PRIMARY KEY (`id`),
				UNIQUE KEY `type` (`name`),
				UNIQUE KEY `alias` (`alias`),
				UNIQUE KEY `url_alias` (`url_alias`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('1','Datasets','Dataset','dataset','datasets','A collection of research data','1','1','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1\nplg_wishlist=1\nplg_citations=1\nplg_usage = 1');

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('2','Workshops','Event','workshop','workshops','A collection of lectures, seminars, and materials that were presented at a workshop.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('3','Publications','Dataset','publication','publications','A publication is a paper relevant to the community that has been published in some manner.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('4','Learning Modules','InteractiveResource','learning module','learningmodules','A combination of presentations, tools, assignments, etc. geared toward teaching a specific concept.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('5','Animations','MovingImage','animation','animations','An animation is a Flash-based demo or short movie that illustrates some concept.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('6','Courses','Collection','course','courses','University courses that make videos of lectures and associated teaching materials available.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('7','Tools','Software','tool','tools','A simulation tool is software that allows users to run a specific type of calculation.','0','1','poweredby=Powered by=textarea=0\nbio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('9','Downloads','PhysicalObject','download','downloads','A download is a type of resource that users can download and use on their own computer.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('10','Notes','Text','note','notes','Notes are typically a category for any resource that might not fit any of the other categories.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('11','Series','Collection','series','series','Series are collections of other resources, typically online presentations, that cover a specific topic.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');

			INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) VALUES ('12','Teaching Materials','Text','teaching material','teachingmaterials','Supplementary materials (study notes, guides, etc.) that don\'t quite fit into any of the other categories.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1');";

			$db->setQuery($query);
			$db->query();
		}
	}

	/**
	 * Down
	 **/
	protected static function down($db)
	{
		if ($db->tableExists(`#__publication_categories`))
		{
			$query = "DROP TABLE `#__publication_categories`";
			$db->setQuery($query);
			$db->query();
		}
	}
}