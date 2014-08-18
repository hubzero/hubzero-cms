<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Migration script for ...
 **/
class Migration20140818160255ComCitations extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		// lang field
		if (!$this->db->tableHasField('#__citations', 'language'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN language VARCHAR(100) AFTER notes;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// accession number field
		if (!$this->db->tableHasField('#__citations', 'accession_number'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN accession_number VARCHAR(100) AFTER language;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// short title field
		if (!$this->db->tableHasField('#__citations', 'short_title'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN short_title VARCHAR(250) AFTER accession_number;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// author address
		if (!$this->db->tableHasField('#__citations', 'author_address'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN author_address TEXT AFTER short_title;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// keywords
		if (!$this->db->tableHasField('#__citations', 'keywords'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN keywords TEXT AFTER author_address;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// abstract
		if (!$this->db->tableHasField('#__citations', 'abstract'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN abstract TEXT AFTER keywords;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// call #
		if (!$this->db->tableHasField('#__citations', 'call_number'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN call_number VARCHAR(100) AFTER abstract;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// label
		if (!$this->db->tableHasField('#__citations', 'label'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN label VARCHAR(100) AFTER call_number;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// research notes
		if (!$this->db->tableHasField('#__citations', 'research_notes'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN research_notes TEXT AFTER label;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// params field
		if (!$this->db->tableHasField('#__citations', 'params'))
		{
			$query = "ALTER TABLE `#__citations` ADD COLUMN params TEXT AFTER research_notes;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// remove old full text index name
		if ($this->db->tableHasKey('#__citations', 'jos_citations_search_ftidx'))
		{
			$query = "ALTER TABLE `#__citations` DROP INDEX jos_citations_search_ftidx;";
			$this->db->setQuery($query);
			$this->db->query();
		}

		// new full text index for searching
		if (!$this->db->tableHasKey('#__citations', 'ftidx_search'))
		{
			$query = "ALTER TABLE `#__citations` ADD FULLTEXT ftidx_search (`title`,`isbn`,`doi`,`abstract`,`author`,`publisher`);";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}