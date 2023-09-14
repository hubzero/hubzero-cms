<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Assets;

use Component;
use Request;

/**
 * External App asset handler class
 */
class Xapp extends Content
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
		'action_message' => 'External App',
		'responds_to'    => array('xapp')
	);

	/**
	 * Create method for this handler
	 *
	 * @return array of assets created
	 **/
	public function create()
	{
		$title = Request::getString('title', '');
		$xappAlias = Request::getString('xapp-alias', '');
		$title = empty($title) ?  $xappAlias : $title;
		$this->asset['xapp-alias'] = $xappAlias;
		$this->asset['title']   = $title;
		$this->asset['type']    = 'xapp';
		$this->asset['subtype'] = 'xapp';

		if (!Request::getInt('id', false))
		{
			// Create asset
			$this->asset['course_id'] = Request::getInt('course_id');
			$return = parent::create();
		}
		else
		{
			$this->asset['course_id'] = Request::getInt('course_id');
			$this->assoc['asset_id']  = Request::getInt('id');
			$this->assoc['scope_id']  = Request::getInt('scope_id');

			// Save asset
			$return = parent::save();
		}
		$asset = new \Components\Courses\Models\Asset($this->assoc['asset_id']);
		$uploadDirectory = self::getXappDirectory() . $asset->get('path') . '/';

		// Make sure upload directory exists and is writable
		if (!is_dir($uploadDirectory))
		{
			if (!\Filesystem::makeDirectory($uploadDirectory))
			{
				return array('error' => 'Server error. Unable to create upload directory');
			}
			// Set the right permissions on the folder for the external apps to access
			\Filesystem::setPermissions($uploadDirectory, '0664', '02775');
		}
		if (!is_writable($uploadDirectory))
		{
			return array('error' => 'Server error. Upload directory isn\'t writable');
		}


		// If files are included, save them as well
		// @FIXME: share this with file upload if possible
		if (isset($_FILES['files']))
		{
			// @FIXME: should these come from the global settings, or should they be courses specific
			// Get config
			$config = Component::params('com_media');

			// Max upload size
			$sizeLimit = $config->get('upload_maxsize');
			$sizeLimit = $sizeLimit * 1024 * 1024;

			// Get courses config
			$cconfig = Component::params('com_courses');

			// Build the upload path if it doesn't exist
			require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php';

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


				// Get the final file path
				$target_path = $uploadDirectory . $filename . '.' . $ext;

				// Move the file to the site folder
				set_time_limit(60);

				// Scan for viruses
				if (!\Filesystem::isSafe($_FILES['files']['tmp_name'][$i]))
				{
					// Scan failed, return an error
					return array('error' => 'File rejected because the anti-virus scan failed.');
				}

				if (!$move = move_uploaded_file($_FILES['files']['tmp_name'][$i], $target_path))
				{
					return array('error' => 'Move file failed');
				}
				// Set the file permissions for the external app to be able to access it
				chmod($target_path, 0664);
			}
		}
		return;
	}

	/**
	 * Check for base external app directory
	 *
	 * @return mixed  string of external app path or false
	 */
	public static function getXappDirectory()
	{
		$courseParams = Component::params('com_courses');
		$xappPath = $courseParams->get('xapp_path');
		$xappPath = trim($xappPath, '/');
		$xappPath = '/' . $xappPath . '/';
		if (!empty($xappPath) && is_writable($xappPath))
		{
			return $xappPath;
		}
		return false;
	}

	/**
	 * Edit method for this handler
	 *
	 * @param  object $asset - asset
	 * @return array((string) type, (string) text)
	 */
	public function edit($asset)
	{
		$options = array('scope'=>'xapp');
		return array('type' => 'default', 'options' => $options);
	}

	/**
	 * List of files currently added to asset
	 * @param object 	$asset object of current asset
	 *
	 * @return array list of files currently included in asset
	 */
	public function files($asset)
	{
		$path = self::getXappDirectory() . $asset->get('path') . '/';
		$files = array();
		if ($path && is_dir($path))
		{
			$files = array_diff(scandir($path), array('..', '.', '.DS_Store'));
		}
		return $files;
	}
}
