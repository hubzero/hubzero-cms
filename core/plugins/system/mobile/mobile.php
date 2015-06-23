<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2012 Purdue University. All rights reserved.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin for Mobile template
 */
class plgSystemMobile extends \Hubzero\Plugin\Plugin
{
	/**
	 * Method to carry template setting in user session if
	 * using the mobile template.
	 *
	 * @return  void
	 */
	public function onAfterDispatch()
	{
		$session = App::get('session');
		$tmpl = Request::getVar('tmpl', '');

		if ($tmpl == 'mobile')
		{
			$session->set('mobile', true);
		}
		else
		{
			if ($session->get('mobile'))
			{
				Request::setVar('tmpl', 'mobile');
			}
		}


		// Are we requesting to view full site again?
		if ($tmpl == 'fullsite')
		{
			$session->set('mobile', false);

			Request::setVar('tmpl', '');

			App::redirect($_SERVER['SCRIPT_URI'] . '?' . str_replace('tmpl=fullsite', '', $_SERVER['QUERY_STRING']));
		}
	}
}