<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for changing field lengths on Newsletter Template table to accomodate more styles
 **/
class Migration20160216115900ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__newsletter_templates'))
		{
			if ($this->db->tableHasField('#__newsletter_templates', 'primary_title_color'))
			{
				$query = "ALTER TABLE `#__newsletter_templates` CHANGE `primary_title_color` `primary_title_color` VARCHAR(255) DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__newsletter_templates', 'primary_text_color'))
			{
				$query = "ALTER TABLE `#__newsletter_templates` CHANGE `primary_text_color` `primary_text_color` VARCHAR(255) DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__newsletter_templates', 'secondary_title_color'))
			{
				$query = "ALTER TABLE `#__newsletter_templates` CHANGE `secondary_title_color` `secondary_title_color` VARCHAR(255) DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__newsletter_templates', 'secondary_text_color'))
			{
				$query = "ALTER TABLE `#__newsletter_templates` CHANGE `secondary_text_color` `secondary_text_color` VARCHAR(255) DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		if ($this->db->tableExists('#__newsletter_templates'))
		{
			if ($this->db->tableHasField('#__newsletter_templates', 'primary_title_color'))
			{
				$query = "ALTER TABLE `#__newsletter_templates` CHANGE `primary_title_color` `primary_title_color` VARCHAR(100) DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__newsletter_templates', 'primary_text_color'))
			{
				$query = "ALTER TABLE `#__newsletter_templates` CHANGE `primary_text_color` `primary_text_color` VARCHAR(100) DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__newsletter_templates', 'secondary_title_color'))
			{
				$query = "ALTER TABLE `#__newsletter_templates` CHANGE `secondary_title_color` `secondary_title_color` VARCHAR(100) DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}

			if ($this->db->tableHasField('#__newsletter_templates', 'secondary_text_color'))
			{
				$query = "ALTER TABLE `#__newsletter_templates` CHANGE `secondary_text_color` `secondary_text_color` VARCHAR(100) DEFAULT NULL;";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}