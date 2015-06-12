<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for adding concept of 'attempts' to forms
 **/
class Migration20130819145850ComCourses extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if (!$this->db->tableHasField('#__courses_form_deployments', 'allowed_attempts') && $this->db->tableHasField('#__courses_form_deployments', 'user_id'))
		{
			$query = "ALTER TABLE `#__courses_form_deployments` ADD `allowed_attempts` INT(11) NOT NULL DEFAULT '1' AFTER `user_id`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if (!$this->db->tableHasField('#__courses_form_respondents', 'attempt') && $this->db->tableHasField('#__courses_form_respondents', 'finished'))
		{
			$query = "ALTER TABLE `#__courses_form_respondents` ADD `attempt` INT(11) NOT NULL DEFAULT '1' AFTER `finished`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableHasField('#__courses_form_deployments', 'allowed_attempts'))
		{
			$query = "ALTER TABLE `#__courses_form_deployments` DROP `allowed_attempts`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
		if ($this->db->tableHasField('#__courses_form_respondents', 'attempt'))
		{
			$query = "ALTER TABLE `#__courses_form_respondents` DROP `attempt`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}