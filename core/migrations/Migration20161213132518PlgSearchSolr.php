<?php

use Hubzero\Content\Migration\Base;

/**
 * Migration script for enabling the Solr search plugin
 **/
class Migration20161213132518PlgSearchSolr extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('search', 'solr', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->disablePluginEntry('search', 'solr');
	}
}
