<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * System plugin for activity
 */
class plgSystemActivity extends \Hubzero\Plugin\Plugin
{
	/**
	 * Log an activity
	 *
	 * @param   array  $activity
	 * @param   array  $recipients
	 * @return  void
	 */
	public function logActivity($activity, $recipients = array())
	{
		if (!App::isSite() && !App::isApi())
		{
			return;
		}

		if (!$activity)
		{
			return;
		}

		Hubzero\Activity\Log::log($activity, $recipients);
	}
}
