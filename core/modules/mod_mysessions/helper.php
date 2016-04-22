<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Modules\MySessions;

use Hubzero\Module\Module;
use Component;
use User;

/**
 * Module class for displaying a user's sessions
 */
class Helper extends Module
{
	/**
	 * Set the time when the session will tiemout
	 *
	 * @param   integer  $sess  Session ID
	 * @return  void
	 */
	private function _setTimeout($sess)
	{
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		$ms = new \Components\Tools\Tables\Session($mwdb);
		$ms->load($sess);
		$ms->timeout = 1209600;
		$ms->store();
	}

	/**
	 * Get the time when the session will tiemout
	 *
	 * @param   integer  $sess  Session ID
	 * @return  string
	 */
	private function _getTimeout($sess)
	{
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		$ms = new \Components\Tools\Tables\Session($mwdb);
		$remaining = $ms->getTimeout();

		$tl = 'unknown';

		if (is_numeric($remaining))
		{
			$days_left    = floor($remaining/60/60/24);
			$hours_left   = floor(($remaining - $days_left*60*60*24)/60/60);
			$minutes_left = floor(($remaining - $days_left*60*60*24 - $hours_left*60*60)/60);

			$left = array($days_left, $hours_left, $minutes_left);

			$tl  = '';
			$tl .= ($days_left > 0)    ? $days_left .' days, '    : '';
			$tl .= ($hours_left > 0)   ? $hours_left .' hours, '  : '';
			$tl .= ($minutes_left > 0) ? $minutes_left .' minute' : '';
			$tl .= ($minutes_left > 1) ? 's' : '';
		}
		return $tl;
	}

	/**
	 * Display module content
	 *
	 * @return  void
	 */
	public function display()
	{
		// Include mw libraries
		include_once(Component::path('com_tools') . DS . 'helpers' . DS . 'utils.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'job.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'view.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'viewperm.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'session.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'host.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'hosttype.php');
		include_once(Component::path('com_tools') . DS . 'tables' . DS . 'recent.php');
		require_once Component::path('com_members') . DS . 'models' . DS . 'member.php';

		// Get database object
		$this->database = \App::get('db');

		// Get a connection to the middleware database
		$mwdb = \Components\Tools\Helpers\Utils::getMWDBO();

		// Get tool paras
		$this->toolsConfig = Component::params('com_tools');

		// Set ACL for com_tools
		$authorized = User::authorise('core.manage', 'com_tools');

		// Ensure we have a connection to the middleware
		$this->error = false;
		if (!$mwdb || !$mwdb->connected() || !$this->toolsConfig->get('mw_on') || ($this->toolsConfig->get('mw_on') > 1 && !$authorized))
		{
			$this->error = true;
			return false;
		}

		// Run middleware command to create screenshots
		// only take snapshots if screenshots are on
		if ($this->params->get('show_screenshots', 1))
		{
			$cmd = "/bin/sh ". Component::path('com_tools') . "/scripts/mw screenshot " . User::get('username') . " 2>&1 </dev/null";
			exec($cmd, $results, $status);
		}

		// Get sessions
		$session = new \Components\Tools\Tables\Session($mwdb);
		$this->sessions = $session->getRecords(User::get('username'), '', false);

		// Output module
		require $this->getLayoutPath();
	}
}
