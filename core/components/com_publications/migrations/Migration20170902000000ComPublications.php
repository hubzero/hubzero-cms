<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding default Publications content
 **/
class Migration20170902000000ComPublications extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__publication_audience_levels'))
		{
			$query = "SELECT COUNT(*) FROM `#__publication_audience_levels`";

			$this->db->setQuery($query);
			$total = $this->db->loadResult();

			if (!$total)
			{
				$audiences = array(
					array('level0','K12','Middle/High School'),
					array('level1','Easy','Freshmen/Sophomores'),
					array('level2','Intermediate','Juniors/Seniors'),
					array('level3','Advanced','Graduate Students'),
					array('level4','Expert','PhD Experts'),
					array('level5','Professional','Beyond PhD')
				);
				foreach ($audiences as $audience)
				{
					$query = "INSERT INTO `#__publication_audience_levels` (`label`,`title`,`description`) VALUES (" . $this->db->quote($audience[0]) . "," . $this->db->quote($audience[1]) . "," . $this->db->quote($audience[2]) . ")";

					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log(sprintf('Created publication audience "%s"', $this->db->quote($audience[0])));
					}
				}
			}
		}

		if ($this->db->tableExists('#__publication_licenses'))
		{
			$query = "SELECT COUNT(*) FROM `#__publication_licenses`";

			$this->db->setQuery($query);
			$total = $this->db->loadResult();

			if (!$total)
			{
				$types = array(
					array('File(s)','files','uploaded material','1','1','1','peer_review=1'),
					array('Link','links','external content','0','0','3',''),
					array('Wiki','notes','from project notes','0','0','5',''),
					array('Application','apps','simulation tool','0','0','4',''),
					array('Series','series','publication collection','0','0','6',''),
					array('Gallery','gallery','image/photo gallery','0','0','7',''),
					array('Databases','databases','project database','0','0','2','')
				);
				foreach ($types as $type)
				{
					$query = "INSERT INTO `#__publication_master_types` (`type`,`alias`,`description`,`contributable`,`supporting`,`ordering`,`params`)
							VALUES " . $this->db->quote($type[0]) . "," . $this->db->quote($type[1]) . "," . $this->db->quote($type[2]) . "," . $this->db->quote($type[3]) . "," . $this->db->quote($type[4]) . "," . $this->db->quote($type[5]) . "," . $this->db->quote($type[6]) . ")";

					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log(sprintf('Created publication type "%s"', $this->db->quote($type[0])));
					}
				}
			}
		}

		if ($this->db->tableExists('#__publication_master_types'))
		{
			$query = "SELECT COUNT(*) FROM `#__publication_master_types`";

			$this->db->setQuery($query);
			$total = $this->db->loadResult();

			if (!$total)
			{
				$query = "INSERT INTO `#__publication_licenses` (`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('custom','[ONE LINE DESCRIPTION]\r\nCopyright (C) [YEAR] [OWNER]','Custom','http://creativecommons.org/about/cc0','Custom license','3','1','0','0','0','1','/components/com_publications/assets/img/logos/license.gif');";
				$this->db->setQuery($query);
				if ($this->db->query())
				{
					$this->log('Created publication license "custom"');
				}

				$query = "INSERT INTO `#__publication_licenses` (`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('cc','','CC0 - Creative Commons','http://creativecommons.org/about/cc0','CC0 enables scientists, educators, artists and other creators and owners of copyright- or database-protected content to waive those interests in their works and thereby place them as completely as possible in the public domain, so that others may freely build upon, enhance and reuse the works for any purposes without restriction under copyright or database law.','2','1','0','1','1','0','/components/com_publications/assets/img/logos/cc.gif');";
				$this->db->setQuery($query);
				if ($this->db->query())
				{
					$this->log('Created publication license "cc"');
				}

				$query = "INSERT INTO `#__publication_licenses` (`name`,`text`,`title`,`url`,`info`,`ordering`,`active`,`apps_only`,`main`,`agreement`,`customizable`,`icon`) VALUES ('standard','All rights reserved.','Standard HUB License','http://nanohub.org','Standard HUB license.','1','0','0','0','0','0','/components/com_publications/images/logos/license.gif');";

				$this->db->setQuery($query);
				if ($this->db->query())
				{
					$this->log('Created publication license "standard"');
				}
			}
		}

		if ($this->db->tableExists('#__publication_categories'))
		{
			$query = "SELECT COUNT(*) FROM `#__publication_categories`";

			$this->db->setQuery($query);
			$total = $this->db->loadResult();

			if (!$total)
			{
				$cats = array(
					array('1','Datasets','Dataset','dataset','datasets','A collection of research data','1','1','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1\nplg_wishlist=1\nplg_citations=1\nplg_usage = 1'),
					array('2','Workshops','Event','workshop','workshops','A collection of lectures, seminars, and materials that were presented at a workshop.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'),
					array('3','Publications','Dataset','publication','publications','A publication is a paper relevant to the community that has been published in some manner.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'),
					array('4','Learning Modules','InteractiveResource','learning module','learningmodules','A combination of presentations, tools, assignments, etc. geared toward teaching a specific concept.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'),
					array('5','Animations','MovingImage','animation','animations','An animation is a Flash-based demo or short movie that illustrates some concept.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'),
					array('6','Courses','Collection','course','courses','University courses that make videos of lectures and associated teaching materials available.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'),
					array('7','Tools','Software','tool','tools','A simulation tool is software that allows users to run a specific type of calculation.','0','1','poweredby=Powered by=textarea=0\nbio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'),
					array('8','Downloads','PhysicalObject','download','downloads','A download is a type of resource that users can download and use on their own computer.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'),
					array('9','Notes','Text','note','notes','Notes are typically a category for any resource that might not fit any of the other categories.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'),
					array('10','Series','Collection','series','series','Series are collections of other resources, typically online presentations, that cover a specific topic.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1'),
					array('11','Teaching Materials','Text','teaching material','teachingmaterials','Supplementary materials (study notes, guides, etc.) that don\'t quite fit into any of the other categories.','0','0','bio=Bio=textarea=0\ncredits=Credits=textarea=0\ncitations=Citations=textarea=0\nsponsoredby=Sponsored by=textarea=0\nreferences=References=textarea=0\npublications=Publications=textarea=0','plg_reviews=1\nplg_questions=1\nplg_supportingdocs=1\nplg_versions=1')
				);
				foreach ($cats as $cat)
				{
					$query = "INSERT INTO `#__publication_categories` (`id`,`name`,`dc_type`,`alias`,`url_alias`,`description`,`contributable`,`state`,`customFields`,`params`) 
							VALUES " . $this->db->quote($cat[0]) . "," . $this->db->quote($cat[1]) . "," . $this->db->quote($cat[2]) . "," . $this->db->quote($cat[3]) . "," . $this->db->quote($cat[4]) . "," . $this->db->quote($cat[5]) . "," . $this->db->quote($cat[6]) . "," . $this->db->quote($cat[7]) . "," . $this->db->quote($cat[8]) . ")";

					$this->db->setQuery($query);
					if ($this->db->query())
					{
						$this->log(sprintf('Created publication category "%s"', $this->db->quote($cat[0])));
					}
				}
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__publication_audience_levels'))
		{
			$query = "DELETE FROM `#__publication_audience_levels` WHERE `label` IN ('level0', 'level1', 'level2', 'level3', 'level4', 'level5')";

			$this->db->setQuery($query);
			if ($this->db->query())
			{
				$this->log('Deleted publication audiences "level0, level1, level2, level3, level4, level5"');
			}
		}

		if ($this->db->tableExists('#__publication_master_types'))
		{
			$query = "DELETE FROM `#__publication_master_types` WHERE `alias` IN ('files', 'links', 'notes', 'apps', 'series', 'gallery', 'databases')";

			$this->db->setQuery($query);
			if ($this->db->query())
			{
				$this->log('Deleted publication types "files, links, notes, apps, series, gallery, databases"');
			}
		}

		if ($this->db->tableExists('#__publication_licenses'))
		{
			$query = "DELETE FROM `#__publication_licenses` WHERE `name` IN ('custom', 'cc', 'standard')";

			$this->db->setQuery($query);
			if ($this->db->query())
			{
				$this->log('Deleted publication licenses "custom, cc, standard"');
			}
		}

		if ($this->db->tableExists('#__publication_categories'))
		{
			$query = "DELETE FROM `#__publication_categories` WHERE `name` IN ('Datasets', 'Workshops', 'Publications', 'Learning Modules', 'Animations', 'Courses', 'Tools', 'Downloads', 'Notes', 'Series', 'Teaching Materials')";

			$this->db->setQuery($query);
			if ($this->db->query())
			{
				$this->log('Deleted publication categories "Datasets, Workshops, Publications, Learning Modules, Animations, Courses, Tools, Downloads, Notes, Series, Teaching Materials"');
			}
		}
	}
}
