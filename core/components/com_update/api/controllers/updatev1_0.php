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

namespace Components\Update\Api\Controllers;

use Hubzero\Component\ApiController;
use Exception;
use stdClass;
use Request;
use Route;
use User;
use Lang;


/**
 * API controller class for blog entries
 */
class Updatev1_0 extends ApiController
{
	/**
	 * Calls the  update script for QA auto-update
	 *
	 * @apiMethod GET
	 * @apiUri    /update/gitpullupdate
	 * @apiParameter {
	 * 		"name":          "payload",
	 * 		"description":   "The GitHub Webhook's payload",
	 * 		"type":          "string",
	 * 		"required":      true,
	 * }
	 **/
	public function gitPullUpdateTask()
	{
		// Get the component parameters
		$params = Component::params('com_update');

		$currentBranch = $params->get('github_branch', '');
		$secret = $params->get('github_secret');
		$updateScriptPath = $params->get('update_script_path', '/var/www/hub/githookpullscript.sh');
		$success = false;

		// Get the debug logwriter.
		$log = App::get('log')->logger('debug');

		// Read the raw request
		$request = file_get_contents('php://input');

		// Generate the checksum
		$checksum = 'sha1=' . hash_hmac('sha1', $request, $secret);

		// Verify the signature
		if ($checksum === Request::header('x-hub-signature'))
		{
			$log->info('Update hook received.');
			
			// Grab the payload
			$payload = Request::getVar('payload');
			$payload = json_decode($payload);

			// Make sure the push is for the correct branch
			$branch = ltrim($payload->ref, '/refs/heads/');
			if ($branch == $currentBranch)
			{
				// Script should be ran as www-data/apache
				if (file_exists($updateScriptPath))
				{
					$output = shell_exec($updateScriptPath);
					$log->info('Git Update Script ran via webhook.');
					if (strpos($output, 'failed') !== FALSE)
					{
						$log->error('Failed to update CMS via webhook. Check the /tmp/gitpull log.');
					}
					else
					{
						$success = true;
					}
				}
				else
				{
					$log->error('Unable to locate script. ' . $updateScriptPath . ' not found.');
				}
			}
		}
		else
		{
			$log->error('Webhook signature not valid.');
		}

		$response = new stdClass;
		$response->success = $success;
		$this->send($response);
	}
}
