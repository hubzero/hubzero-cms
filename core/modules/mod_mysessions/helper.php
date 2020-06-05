<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		include_once Component::path('com_tools') . DS . 'helpers' . DS . 'utils.php';
		include_once Component::path('com_tools') . DS . 'tables' . DS . 'job.php';
		include_once Component::path('com_tools') . DS . 'tables' . DS . 'view.php';
		include_once Component::path('com_tools') . DS . 'tables' . DS . 'viewperm.php';
		include_once Component::path('com_tools') . DS . 'tables' . DS . 'session.php';
		include_once Component::path('com_tools') . DS . 'tables' . DS . 'host.php';
		include_once Component::path('com_tools') . DS . 'tables' . DS . 'hosttype.php';
		include_once Component::path('com_tools') . DS . 'tables' . DS . 'recent.php';

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
		// only take snapshots if screenshots are on and
		// it's a valid account
		if ($this->params->get('show_screenshots', 1)
		 && strstr(User::get('email'), '@') != '@invalid')
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
