<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for upping access values to be consistent with #__viewlevels
 **/
class Migration20150216140100ComKb extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		if ($this->db->tableExists('#__faq') && $this->db->tableHasField('#__faq', 'access'))
		{
			$query = "SELECT COUNT(*) FROM `#__faq` WHERE `access`=0";
			$this->db->setQuery($query);
			if ($this->db->loadResult())
			{
				$query = "UPDATE `#__faq` SET `access`=(`access` + 1)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__faq_categories') && $this->db->tableHasField('#__faq_categories', 'access'))
		{
			$query = "SELECT COUNT(*) FROM `#__faq_categories` WHERE `access`=0";
			$this->db->setQuery($query);
			if ($this->db->loadResult())
			{
				$query = "UPDATE `#__faq_categories` SET `access`=(`access` + 1)";
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
		if ($this->db->tableExists('#__faq') && $this->db->tableHasField('#__faq', 'access'))
		{
			$query = "SELECT COUNT(*) FROM `#__faq` WHERE `access`=0";
			$this->db->setQuery($query);
			if (!$this->db->loadResult())
			{
				$query = "UPDATE `#__faq` SET `access`=(`access` - 1)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}

		if ($this->db->tableExists('#__faq_categories') && $this->db->tableHasField('#__faq_categories', 'access'))
		{
			$query = "SELECT COUNT(*) FROM `#__faq_categories` WHERE `access`=0";
			$this->db->setQuery($query);
			if (!$this->db->loadResult())
			{
				$query = "UPDATE `#__faq_categories` SET `access`=(`access` - 1)";
				$this->db->setQuery($query);
				$this->db->query();
			}
		}
	}
}