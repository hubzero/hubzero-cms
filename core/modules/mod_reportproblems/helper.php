<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

		$this->referrer = Request::getVar('REQUEST_URI','','server');
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

		require $this->getLayoutPath();
	}
}
