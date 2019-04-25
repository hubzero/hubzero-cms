<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for setting the com_update asset rules to only allow supers
 **/
class Migration20141105142938ComUpdate extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$rules = array(
			'core.admin' => array(
				'Super Users' => 1
			)
		);

		$this->setAssetRules('com_update', $rules);
	}
}
