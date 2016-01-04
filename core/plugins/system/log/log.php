<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die;

/**
 * Joomla! System Logging Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	System.log
 */
class plgSystemLog extends \Hubzero\Plugin\Plugin
{
	/**
	 * Method to log login failures
	 *
	 * @param   array  $response
	 * @return  void
	 */
	public function onUserLoginFailure($response)
	{
		$errorlog = array();

		switch ($response['status'])
		{
			case \Hubzero\Auth\Status::SUCCESS:
				$errorlog['status']  = $response['type'] . ' CANCELED: ';
				$errorlog['comment'] = $response['error_message'];
			break;

			case \Hubzero\Auth\Status::FAILURE:
				$errorlog['status']  = $response['type'] . ' FAILURE: ';
				if ($this->params->get('log_username', 0))
				{
					$errorlog['comment'] = $response['error_message'] . ' ("' . $response['username'] . '")';
				}
				else
				{
					$errorlog['comment'] = $response['error_message'];
				}
			break;

			default:
				$errorlog['status']  = $response['type'] . ' UNKNOWN ERROR: ';
				$errorlog['comment'] = $response['error_message'];
			break;
		}

		App::get('log')->logger('auth')->info(implode('', $errorlog));
	}
}
