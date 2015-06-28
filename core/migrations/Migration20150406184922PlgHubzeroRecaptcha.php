<?php

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for delete hubzero recaptcha keys from non-hubzero machines
 **/
class Migration20150406184922PlgHubzeroRecaptcha extends Base
{
	/**
	 * Up
	 **/
	public function up()
	{
		$params = $this->getParams('plg_hubzero_recaptcha');

		if (!file_exists('/etc/apache2/apache.block')
			&& $params->get('private') == '6Lf9IgATAAAAAAs_fYlomzK_HO6gbUVpSkGkDTRl'
			&& $params->get('public') == '6Lf9IgATAAAAAAl3WEw0hwpbsG9O2_EXY_-NH7xd')
		{
			$params->set('public', '');
			$params->set('private', '');

			$this->saveParams('plg_hubzero_recaptcha', $params);
			$this->disablePlugin('hubzero', 'recaptcha');
		}
	}
}