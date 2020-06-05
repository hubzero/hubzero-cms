<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
		$tmpl = Request::getCmd('tmpl', '');

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
