<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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
	public function create($localPath = null)
	{
		// Set the asset type to video
		$this->asset['type']    = 'video';
		$this->asset['subtype'] = 'video';

		// @FIXME: if extension is .zip, go ahead and set subtype (html5/hubpresenter) accordingly

		// Call the primary create method on the file asset handler
		$return_info = parent::create($localPath);

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
