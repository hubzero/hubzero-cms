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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
