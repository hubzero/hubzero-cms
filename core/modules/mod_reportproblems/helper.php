<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Modules\ReportProblems;

use Hubzero\Module\Module;
use Hubzero\Browser\Detector;
use Component;
use User;
use Request;

/**
 * Module class for displaying a report problems form
 */
class Helper extends Module
{
	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		$this->verified = 0;
		if (!User::isGuest())
		{
			if (User::get('activation') == 1 || User::get('activation') == 3)
			{
				$this->verified = 1;
			}
		}

		// Figure out whether this is a guess or temporary account created during the auth_link registration process
		if (User::isGuest() || (is_numeric(User::get('username')) && User::get('username') < 0))
		{
			$this->guestOrTmpAccount = true;
		}
		else
		{
			$this->guestOrTmpAccount = false;
		}

		$this->referrer = Request::getString('REQUEST_URI', '', 'server');
		$this->referrer = str_replace('&amp;', '&', $this->referrer);
		$this->referrer = base64_encode($this->referrer);

		$browser = new Detector();
		$this->os          = $browser->platform();
		$this->os_version  = $browser->platformVersion();
		$this->browser     = $browser->name();
		$this->browser_ver = $browser->version();

		$this->css()
		     ->js()
		     ->js('jQuery(document).ready(function(jq) { HUB.Modules.ReportProblems.initialize("' . $this->params->get('trigger', '#tab') . '"); });');

		$this->supportParams = Component::params('com_support');

		$allowed = Component::params('com_media')->get('upload_extensions');
		if (!$allowed)
		{
			$allowed = $this->supportParams->get('file_ext', 'jpg,jpeg,jpe,bmp,tif,tiff,png,gif');
		}

		$this->allowed = $allowed;

		require $this->getLayoutPath();
	}
}
