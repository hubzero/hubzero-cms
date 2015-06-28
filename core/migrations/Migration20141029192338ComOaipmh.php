<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for fixing 'jos_' refernces in OAIPMH content
 **/
class Migration20141029192338ComOaipmh extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$query = "UPDATE `#__oaipmh_dcspecs` SET `query` = REPLACE(`query`, 'jos_', '#__')";
		$this->db->setQuery($query);
		$this->db->query();
	}
}