<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die;

/**
 * System Logging Plugin
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
		if (!App::has('log'))
		{
			return;
		}

		$errorlog = array();

		switch ($response['status'])
		{
			case Hubzero\Auth\Status::SUCCESS:
				$errorlog['status']  = $response['type'] . ' CANCELED: ';
				$errorlog['comment'] = $response['error_message'];
			break;

			case Hubzero\Auth\Status::FAILURE:
				$errorlog['status']  = $response['type'] . ' FAILURE: ';
				$errorlog['comment'] = $response['error_message'];

				if ($this->params->get('log_username', 0))
				{
					$errorlog['comment'] .= ' ("' . $response['username'] . '")';
				}
			break;

			default:
				$errorlog['status']  = $response['type'] . ' UNKNOWN ERROR: ';
				$errorlog['comment'] = $response['error_message'];
			break;
		}

		App::get('log')
			->logger('auth')
			->info(implode('', $errorlog));
	}
}
