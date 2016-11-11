<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		if (!App::isSite())
		{
			return;
		}

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