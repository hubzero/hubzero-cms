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
 * @copyright Copyright 2011-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

use Filesystem;

/**
* Video Asset handler class
*/
class Video extends File
{
	/**
	 * Class info
	 *
	 * Action message - what the user will see if presented with multiple handlers for this extension
	 * Responds to    - what extensions this handler responds to
	 *
	 * @var array
	 **/
	protected static $info = array(
		'action_message' => 'As an HTML5/HUBpresenter Video',
		'responds_to'    => array('zip', 'mp4', 'mov', 'm4v', 'json'),
	);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create()
	{
		// Set the asset type to video
		$this->asset['type']    = 'video';
		$this->asset['subtype'] = 'video';

		// @FIXME: if extension is .zip, go ahead and set subtype (html5/hubpresenter) accordingly

		// Call the primary create method on the file asset handler
		$return_info = parent::create();

		// Check for errors in response
		if (array_key_exists('error', $return_info))
		{
			return array('error' => $return_info['error']);
		}
		else
		{
			$asset = $return_info['assets'];

			// Exapand zip file if applicable - we're assuming zips are hubpresenter videos
			if ($asset['asset_ext'] == 'zip')
			{
				// Set the timout so that PHP execution doesn't run out of time
				set_time_limit(60);

				// Make the path shell safe
				$escaped_file = escapeshellarg($asset['target_path']);

				// Exec the command to unzip things
				// @FIXME: check for symlinks and other potential security concerns
				if ($result = shell_exec("unzip -o {$escaped_file} -d {$asset['upload_path']}"))
				{
					// Remove original archive
					Filesystem::delete($asset['target_path']);

					// Remove MACOSX dirs if there
					if (Filesystem::exists($asset['upload_path'] . '__MACOSX'))
					{
						Filesystem::deleteDirectory($asset['upload_path'] . '__MACOSX');
					}

					// Set permissions
					Filesystem::setPermissions($asset['upload_path'], '0664', '0775');
				}
				else
				{
					return array('error' => 'Unzip failed. Ensure that it is installed.');
				}
			}
		}

		// Return info
		return $return_info;
	}
}