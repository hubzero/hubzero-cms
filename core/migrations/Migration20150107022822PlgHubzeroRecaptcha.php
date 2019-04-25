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
 * Migration script for adding key to recaptcha
 **/
class Migration20150107022822PlgHubzeroRecaptcha extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = array(
			'private' => '6Lf9IgATAAAAAAs_fYlomzK_HO6gbUVpSkGkDTRl',
			'public'  => '6Lf9IgATAAAAAAl3WEw0hwpbsG9O2_EXY_-NH7xd'
		);

		$this->savePluginParams('hubzero', 'recaptcha', $params);
	}
}
