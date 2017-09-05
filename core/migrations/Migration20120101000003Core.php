<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for 2011/12 drop tables
 **/
class Migration20120101000003Core extends Base
{
	public function up()
	{
		if ($this->db->tableExists('user_map'))
		{
			$query = "DROP TABLE `user_map`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('summary_user'))
		{
			$query = "DROP TABLE `summary_user`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('summary_simusage_vals'))
		{
			$query = "DROP TABLE `summary_simusage_vals`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('summary_simusage'))
		{
			$query = "DROP TABLE `summary_simusage`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('summary_misc_vals'))
		{
			$query = "DROP TABLE `summary_misc_vals`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('summary_misc'))
		{
			$query = "DROP TABLE `summary_misc`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('summary_andmore_vals'))
		{
			$query = "DROP TABLE `summary_andmore_vals`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('summary_andmore'))
		{
			$query = "DROP TABLE `summary_andmore`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('orgtypes'))
		{
			$query = "DROP TABLE `orgtypes`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__polls') && $this->db->tableExists('#__xpolls'))
		{
			$query = "DROP TABLE `#__polls`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__poll_menu') && $this->db->tableExists('#__xpoll_menu'))
		{
			$query = "DROP TABLE `#__poll_menu`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__poll_date') && $this->db->tableExists('#__xpoll_date'))
		{
			$query = "DROP TABLE `#__poll_date`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__poll_data') && $this->db->tableExists('#__xpoll_data'))
		{
			$query = "DROP TABLE `#__poll_data`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		/* Removing this as xgroups_modules is a needed table. Perhaps it was repurposed at some point?
		if ($this->db->tableExists('#__xgroups_modules'))
		{
			$query = "DROP TABLE `#__xgroups_modules`";
			$this->db->setQuery($query);
			$this->db->query();
		}*/

		if ($this->db->tableExists('#__xforum'))
		{
			$query = "DROP TABLE `#__xforum`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('#__modifications'))
		{
			$query = "DROP TABLE `#__modifications`";
			$this->db->setQuery($query);
			$this->db->query();
		}

		if ($this->db->tableExists('ipusers'))
		{
			$query = "DROP TABLE `ipusers`";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}
