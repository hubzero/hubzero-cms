<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

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
