<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Module class for displaying a report problems form
 */
class modReportProblems extends \Hubzero\Module\Module
{
	/**
	 * Display module content
	 *
	 * @return     void
	 */
	public function display()
	{
		$this->juser = JFactory::getUser();

		$this->verified = 0;
		if (!$this->juser->get('guest'))
		{
			$profile = \Hubzero\User\Profile::getInstance($this->juser->get('id'));
			if ($profile->get('emailConfirmed') == 1 || $profile->get('emailConfirmed') == 3)
			{
				$this->verified = 1;
			}
		}

		// Figure out whether this is a guess or temporary account created during the auth_link registration process
		if ($this->juser->get('guest') || (is_numeric($this->juser->get('username')) && $this->juser->get('username') < 0))
		{
			$this->guestOrTmpAccount = true;
		}
		else
		{
			$this->guestOrTmpAccount = false;
		}

		$this->referrer = JRequest::getVar('REQUEST_URI','','server');
		$this->referrer = str_replace('&amp;', '&', $this->referrer);
		$this->referrer = str_replace('&', '&amp;', $this->referrer);

		$browser = new \Hubzero\Browser\Detector();

		$this->os = $browser->platform();
		$this->os_version = $browser->platformVersion();
		$this->browser = $browser->name();
		$this->browser_ver = $browser->version();

		$this->css();
		$this->js();
		$this->js('jQuery(document).ready(function(jq) { HUB.Modules.ReportProblems.initialize("' . $this->params->get('trigger', '#tab') . '"); });');

		$this->supportParams = JComponentHelper::getParams('com_support');

		require(JModuleHelper::getLayoutPath($this->module->module));
	}
}
