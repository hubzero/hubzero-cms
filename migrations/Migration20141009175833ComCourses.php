<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for fixing engine type on some courses tables
 **/
class Migration20141009175833ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__courses_form_answers') && strtolower($this->db->getEngine('#__courses_form_answers')) != 'myisam')
		{
			$query = "ALTER TABLE `#__courses_form_answers` ENGINE = MyISAM";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_deployments') && strtolower($this->db->getEngine('#__courses_form_deployments')) != 'myisam')
		{
			$query = "ALTER TABLE `#__courses_form_deployments` ENGINE = MyISAM";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_questions') && strtolower($this->db->getEngine('#__courses_form_questions')) != 'myisam')
		{
			$query = "ALTER TABLE `#__courses_form_questions` ENGINE = MyISAM";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_respondent_progress') && strtolower($this->db->getEngine('#__courses_form_respondent_progress')) != 'myisam')
		{
			$query = "ALTER TABLE `#__courses_form_respondent_progress` ENGINE = MyISAM";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_respondents') && strtolower($this->db->getEngine('#__courses_form_respondents')) != 'myisam')
		{
			$query = "ALTER TABLE `#__courses_form_respondents` ENGINE = MyISAM";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_form_responses') && strtolower($this->db->getEngine('#__courses_form_responses')) != 'myisam')
		{
			$query = "ALTER TABLE `#__courses_form_responses` ENGINE = MyISAM";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_forms') && strtolower($this->db->getEngine('#__courses_forms')) != 'myisam')
		{
			$query = "ALTER TABLE `#__courses_forms` ENGINE = MyISAM";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__courses_offering_section_badge_criteria') && strtolower($this->db->getCharacterSet('#__courses_offering_section_badge_criteria')) != 'utf8')
		{
			$query = "ALTER TABLE `#__courses_offering_section_badge_criteria` CHARACTER SET = utf8";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}