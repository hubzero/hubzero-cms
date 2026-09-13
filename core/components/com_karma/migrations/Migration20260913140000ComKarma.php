<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Register com_karma
 *
 * The tables it administers belong to Hubzero\Karma and are created by the
 * core karma migrations. This component is their user interface, and can be
 * removed without touching them.
 **/
class Migration20260913140000ComKarma extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$this->addComponentEntry('Karma');
	}

	/**
	 * Down
	 **/
	public function down()
	{
		$this->deleteComponentEntry('Karma');
	}
}
