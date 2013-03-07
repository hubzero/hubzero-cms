<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

/**
* Video Asset handler class
*/
class VideoFileAssetHandler extends FileAssetHandler
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
			'responds_to'    => array('zip'),
		);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create()
	{
		// Set the asset type to video
		$this->asset['type'] = 'video';

		// Call the primary create method on the file asset handler
		$return_info = parent::create();

		// Check for errors in response
		if(array_key_exists('error', $return_info))
		{
			$this->setMessage($return_info['error'], 500, 'Internal server error');
			return;
		}
		else
		{
			$asset = $return_info['assets'];

			// Exapand zip file if applicable - we're assuming zips are hubpresenter videos
			if(!array_key_exists('error', $asset) && $asset['asset_ext'] == 'zip')
			{
				// Set the timout so that PHP execution doesn't run out of time
				set_time_limit(60);

				// Make the path shell safe
				$escaped_file = escapeshellarg($asset['target_path']);

				// Exec the command to unzip things
				// @FIXME: check for symlinks and other potential security concerns
				if($result = shell_exec("unzip {$escaped_file} -d {$asset['upload_path']}"))
				{
					// Remove original archive
					JFile::delete($asset['target_path']);

					// Remove MACOSX dirs if there
					JFolder::delete($asset['upload_path'] . '__MACOSX');
				}
				else
				{
					$this->setMessage('Unzip failed.  Ensure that it is installed.', 500, 'Internal server error');
					return;
				}
			}
		}

		// Return info
		return $return_info;
	}
}