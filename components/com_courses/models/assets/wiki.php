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
* Wiki page asset handler class
*/
class WikiAssetHandler extends ContentAssetHandler
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
		'action_message' => 'A textual wiki page',
		'responds_to'    => array('wiki')
	);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create()
	{
		$this->asset['title']   = JRequest::getString('title', '');
		$this->asset['type']    = 'text';
		$this->asset['subtype'] = 'wiki';

		if (!JRequest::getString('title', false))
		{
			return array('error' => 'Please provide a title!');
		}

		if (!JRequest::getInt('id', false))
		{
			// Create asset
			$this->asset['course_id'] = JRequest::getInt('course_id');
			$return = parent::create();
		}
		else
		{
			$this->asset['course_id'] = JRequest::getInt('course_id');
			$this->assoc['asset_id']  = JRequest::getInt('id');
			$this->assoc['scope_id']  = JRequest::getInt('scope_id');

			// Save asset
			$return = parent::save();
		}

		// If files are included, save them as well
		// @FIXME: share this with file upload if possible
		if (isset($_FILES['files']))
		{
			jimport('joomla.filesystem.folder');
			jimport('joomla.filesystem.file');

			// @FIXME: should these come from the global settings, or should they be courses specific
			// Get config
			$config = JComponentHelper::getParams('com_media');

			// Max upload size
			$sizeLimit = $config->get('upload_maxsize');
			$sizeLimit = $sizeLimit * 1024 * 1024;

			// Get courses config
			$cconfig = JComponentHelper::getParams('com_courses');

			// Loop through files and save them (they will potentially be coming in together, in a single request)
			for ($i=0; $i < count($_FILES['files']['name']); $i++)
			{
				$file = $_FILES['files']['name'][$i];
				$size = (int) $_FILES['files']['size'][$i];

				// Get the file extension
				$pathinfo = pathinfo($file);
				$filename = $pathinfo['filename'];
				$ext      = $pathinfo['extension'];

				// Check to make sure we have a file and its not too big
				if ($size == 0)
				{
					return array('error' => 'File is empty');
				}
				if ($size > $sizeLimit)
				{
					$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
					return array('error' => "File is too large. Max file upload size is $max");
				}

				// Build the upload path if it doesn't exist
				require_once JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php';
				$asset = new CoursesModelAsset($this->assoc['asset_id']);
				$uploadDirectory = JPATH_ROOT . DS . $asset->path($this->asset['course_id']);

				// Make sure upload directory exists and is writable
				if (!is_dir($uploadDirectory))
				{
					if (!JFolder::create($uploadDirectory))
					{
						return array('error' => 'Server error. Unable to create upload directory');
					}
				}
				if (!is_writable($uploadDirectory))
				{
					return array('error' => 'Server error. Upload directory isn\'t writable');
				}

				// Get the final file path
				$target_path = $uploadDirectory . $filename . '.' . $ext;

				// Move the file to the site folder
				set_time_limit(60);
				if (!$move = move_uploaded_file($_FILES['files']['tmp_name'][$i], $target_path))
				{
					return array('error' => 'Move file failed');
				}
			}
		}

		// Return info
		return $return;
	}

	/**
	 * Edit method for this handler
	 *
	 * @param  object $asset - asset
	 * @return array((string) type, (string) text)
	 **/
	public function edit($asset)
	{
		$options = array('scope'=>'wiki');
		return array('type'=>'default', 'options'=>$options);
	}
}