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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

jimport('joomla.application.component.view');

/**
 * End single sign on view class
 */
class UsersViewEndsinglesignon extends JViewLegacy
{
	function display($tpl = null)
	{
		// Assign variables to the view
		$authenticator = \Request::getWord('authenticator', false);

		\Hubzero\Document\Assets::addComponentStylesheet('com_user', 'login.css');

		// Get the site name
		$sitename = \Config::get('sitename');

		// Get the display name for the current plugin being used
		$plugin       = Plugin::byType('authentication', $authenticator);
		$pparams      = new \Hubzero\Config\Registry($plugin->params);
		$display_name = $pparams->get('display_name', ucfirst($plugin->name));

		$this->assign('authenticator', $authenticator);
		$this->assign('sitename', $sitename);
		$this->assign('display_name', $display_name);

		parent::display($tpl);
	}

	function attach()
	{
	}
}