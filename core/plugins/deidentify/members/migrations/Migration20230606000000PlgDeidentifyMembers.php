<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for adding Deidentify - Members plugin
 **/
class Migration20230606000000PlgDeidentifyMembers extends Base
{
	/** Up **/
	public function up() {
		$this->addPluginEntry('deidentify', 'members');
	}

	/** Down **/
	public function down(){
		$this->deletePluginEntry('deidentify', 'members');
	}
}