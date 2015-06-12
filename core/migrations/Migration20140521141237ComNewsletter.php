<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for separating html & plain parts for newsletter emails.
 **/
class Migration20140521141237ComNewsletter extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// rename content to html content
		if ($this->db->tableHasField('#__newsletters', 'content'))
		{
			$query = "ALTER TABLE `#__newsletters` CHANGE `content` `html_content` MEDIUMTEXT;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add plain text col
		if (!$this->db->tableHasField('#__newsletters', 'plain_content'))
		{
			$query = "ALTER TABLE `#__newsletters` ADD COLUMN `plain_content` MEDIUMTEXT AFTER `html_content`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// rename content to html content
		if ($this->db->tableHasField('#__newsletter_mailings', 'body'))
		{
			$query = "ALTER TABLE `#__newsletter_mailings` CHANGE `body` `html_body` LONGTEXT;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// add plain text col
		if (!$this->db->tableHasField('#__newsletter_mailings', 'plain_body'))
		{
			$query = "ALTER TABLE `#__newsletter_mailings` ADD COLUMN `plain_body` LONGTEXT AFTER `html_body`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Down
	 **/
	public function down()
	{
		// rename html content to content
		if ($this->db->tableHasField('#__newsletters', 'html_content'))
		{
			$query = "ALTER TABLE `#__newsletters` CHANGE `html_content` `content` MEDIUMTEXT;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// remove plain text col
		if ($this->db->tableHasField('#__newsletters', 'plain_content'))
		{
			$query = "ALTER TABLE `#__newsletters` DROP COLUMN `plain_content`;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// rename html content to content
		if ($this->db->tableHasField('#__newsletter_mailings', 'html_body'))
		{
			$query = "ALTER TABLE `#__newsletter_mailings` CHANGE `html_body` `body` LONGTEXT;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// remove plain text col
		if ($this->db->tableHasField('#__newsletter_mailings', 'plain_body'))
		{
			$query = "ALTER TABLE `#__newsletter_mailings` DROP COLUMN `plain_body`;";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}