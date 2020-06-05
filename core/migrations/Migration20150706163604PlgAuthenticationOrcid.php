<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

/**
 * Migration script for addung ORCID authentication plugin
 **/
class Migration20150706163604PlgAuthenticationOrcid extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addPluginEntry('authentication', 'orcid', 0);
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deletePluginEntry('authentication', 'orcid');
	}
}
