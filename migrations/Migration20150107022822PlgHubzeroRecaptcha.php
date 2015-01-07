<?php

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

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

		$this->savePluginParams('hubzero','recaptcha', $params);
	}
}