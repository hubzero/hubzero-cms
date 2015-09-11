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

use Component;
use Request;

/**
* Wiki page asset handler class
*/
class Wiki extends Content
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
		$this->asset['title']   = Request::getString('title', '');
		$this->asset['type']    = 'text';
		$this->asset['subtype'] = 'wiki';

		if (!Request::getString('title', false))
		{
			return array('error' => 'Please provide a title!');
		}

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
				require_once PATH_CORE . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'asset.php';
				$asset = new \Components\Courses\Models\Asset($this->assoc['asset_id']);
				$uploadDirectory = PATH_APP . DS . $asset->path($this->asset['course_id']);

				// Make sure upload directory exists and is writable
				if (!is_dir($uploadDirectory))
				{
					if (!\Filesystem::makeDirectory($uploadDirectory))
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