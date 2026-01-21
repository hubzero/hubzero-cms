<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for creating publication categories table (if it doesn't exist)
  *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20130828201526ComPublications extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        if (!$this->db->tableExists('#__publication_categories')) {
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
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

            $this->db->setQuery($query);
            $this->db->query();

            $customFields = 'bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0'
                . '\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0'
                . '\npublications=Publications=textarea=0';
            $customFieldsTool = 'poweredby=Powered by=textarea=0\nbio=Bio=textarea=0'
                . '\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0'
                . '\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0'
                . '\npublications=Publications=textarea=0';
            $params = 'plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1';
            $paramsExt = 'plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'
                . '\nplg_wishlist=1\nplg_citations=1\nplg_usage = 1';

            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('1','Datasets','Dataset','dataset','datasets','A collection of research data',"
                . "'1','1','$customFields','$paramsExt');";
            $this->db->setQuery($query);
            $this->db->query();

            $desc2 = 'A collection of lectures, seminars, and materials that were presented at a workshop.';
            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('2','Workshops','Event','workshop','workshops','$desc2',"
                . "'0','0','$customFields','$params');";
            $this->db->setQuery($query);
            $this->db->query();

            $desc3 = 'A publication is a paper relevant to the community that has been published in some manner.';
            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('3','Publications','Dataset','publication','publications','$desc3',"
                . "'0','0','$customFields','$params');";
            $this->db->setQuery($query);
            $this->db->query();

            $desc4 = 'A combination of presentations, tools, assignments, etc. '
                . 'geared toward teaching a specific concept.';
            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('4','Learning Modules','InteractiveResource','learning module','learningmodules',"
                . "'$desc4','0','0','$customFields','$params');";
            $this->db->setQuery($query);
            $this->db->query();

            $desc5 = 'An animation is a Flash-based demo or short movie that illustrates some concept.';
            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('5','Animations','MovingImage','animation','animations','$desc5',"
                . "'0','0','$customFields','$params');";
            $this->db->setQuery($query);
            $this->db->query();

            $desc6 = 'University courses that make videos of lectures and associated teaching materials available.';
            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('6','Courses','Collection','course','courses','$desc6',"
                . "'0','0','$customFields','$params');";
            $this->db->setQuery($query);
            $this->db->query();

            $desc7 = 'A simulation tool is software that allows users to run a specific type of calculation.';
            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('7','Tools','Software','tool','tools','$desc7',"
                . "'0','1','$customFieldsTool','$params');";
            $this->db->setQuery($query);
            $this->db->query();

            $desc9 = 'A download is a type of resource that users can download and use on their own computer.';
            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('9','Downloads','PhysicalObject','download','downloads','$desc9',"
                . "'0','0','$customFields','$params');";
            $this->db->setQuery($query);
            $this->db->query();

            $desc10 = 'Notes are typically a category for any resource that might not fit any of the other categories.';
            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('10','Notes','Text','note','notes','$desc10',"
                . "'0','0','$customFields','$params');";
            $this->db->setQuery($query);
            $this->db->query();

            $desc11 = 'Series are collections of other resources, typically online presentations, '
                . 'that cover a specific topic.';
            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('11','Series','Collection','series','series','$desc11',"
                . "'0','0','$customFields','$params');";
            $this->db->setQuery($query);
            $this->db->query();

            $desc12 = 'Supplementary materials (study notes, guides, etc.) '
                . 'that don\'t quite fit into any of the other categories.';
            $query = "INSERT INTO `#__publication_categories` "
                . "(`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,"
                . "`state`,`customFields`,`params`) VALUES "
                . "('12','Teaching Materials','Text','teaching material','teachingmaterials','$desc12',"
                . "'0','0','$customFields','$params');";

            $this->db->setQuery($query);
            $this->db->query();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        if ($this->db->tableExists(`#__publication_categories`)) {
            $query = "DROP TABLE `#__publication_categories`";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
