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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Site\Controllers;

use Components\Members\Models\Member;
use Hubzero\Component\SiteController;
use Filesystem;
use Request;
use Route;
use Lang;
use User;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'member.php');

/**
 * Members controller class for media
 */
class Media extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		$parts = explode('/', $_SERVER['REQUEST_URI']);

		$file = array_pop($parts);

		if (substr(strtolower($file), 0, 5) == 'image'
		 || substr(strtolower($file), 0, 4) == 'file')
		{
			Request::setVar('task', 'download');
		}

		$this->registerTask('deleteimg', 'delete');

		parent::execute();
	}

	/**
	 * Show a form for uploading a file
	 *
	 * @return  void
	 */
	public function ajaxuploadTask()
	{
		// get the id
		$id = Request::getInt('id', 0);

		// load profile from id
		$profile = Member::oneOrFail($id);

		if (!$profile->get('id'))
		{
			App::abort(404, Lang::txt('MEMBERS_NO_ID'));
		}

		// instantiate view and pass needed vars
		$this->view
			->set('config', $this->config)
			->set('profile', $profile)
			->setErrors($this->getErrors())
			->setLayout('upload')
			->display();
	}

	/**
	 * Upload a file to the profile via AJAX
	 *
	 * @return  string
	 */
	public function doajaxuploadTask()
	{
		Request::checkToken(['get', 'post']);

		//allowed extensions for uplaod
		$allowedExtensions = array('png', 'jpe', 'jpeg', 'jpg', 'gif');

		//max upload size
		$sizeLimit = $this->config->get('maxAllowed', '40000000');

		//get the file
		if (isset($_GET['qqfile']))
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['qqfile']))
		{
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			return;
		}

		//get the id and load profile
		$id = Request::getVar('id', 0);
		$profile = Member::oneOrFail($id);

		if (!$profile->get('id'))
		{
			return;
		}

		//define upload directory and make sure its writable
		$uploadDirectory = PATH_APP . DS . $this->filespace() . DS . \Hubzero\Utility\String::pad($id) . DS;

		if (!is_dir($uploadDirectory))
		{
			if (!Filesystem::makeDirectory($uploadDirectory))
			{
				echo json_encode(array('error' => 'Server error. Unable to create upload directory.'));
				return;
			}
		}

		if (!is_writable($uploadDirectory))
		{
			echo json_encode(array('error' => "Server error. Upload directory isn't writable."));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => 'File is empty'));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => 'File is too large. Max file upload size is ' . $max));
			return;
		}

		//check to make sure we have an allowable extension
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];
		$ext      = $pathinfo['extension'];
		if ($allowedExtensions && !in_array(strtolower($ext), $allowedExtensions))
		{
			$these = implode(', ', $allowedExtensions);
			echo json_encode(array('error' => 'File has an invalid extension, it should be one of '. $these . '.'));
			return;
		}

		// don't overwrite previous files that were uploaded
		while (file_exists($uploadDirectory . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		$file = $uploadDirectory . $filename . '.' . $ext;
		$final_file  = $uploadDirectory . 'profile.png';
		$final_thumb = $uploadDirectory . 'thumb.png';

		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			//move from temp location to target location which is user folder
			$target = fopen($file , "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		//resize image to max 400px and rotate in case user didnt before uploading
		$hi = new \Hubzero\Image\Processor($file);
		if (count($hi->getErrors()) == 0)
		{
			$hi->autoRotate();
			$hi->resize(400);
			$hi->setImageType(IMAGETYPE_PNG);
			$hi->save($final_file);
		}
		else
		{
			echo json_encode(array('error' => $hi->getError()));
			return;
		}

		// create thumb
		$hi = new \Hubzero\Image\Processor($final_file);
		if (count($hi->getErrors()) == 0)
		{
			$hi->resize(50, false, true, true);
			$hi->save($final_thumb);
		}
		else
		{
			echo json_encode(array('error' => $hi->getError()));
			return;
		}

		// remove orig
		unlink($file);

		echo json_encode(array(
			'success'   => true,
			'file'      => str_replace($uploadDirectory, '', $final_file),
			'directory' => str_replace(PATH_ROOT, '', $uploadDirectory)
		));
	}

	/**
	 * Set the picture of a profile
	 *
	 * @return  string  JSON
	 */
	public function ajaxuploadsaveTask()
	{
		Request::checkToken(['get', 'post']);

		//get the user id
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			echo json_encode(array('error' => 'Missing required user ID.'));
		}

		//load the user profile
		$profile = Member::oneOrFail($id);
		if (!$profile->get('id'))
		{
			echo json_encode(array('error' => 'Unable to locate user profile.'));
		}

		//update the user pic
		$p = Request::getVar('profile', array());
		$profile->set('picture', $p['picture']);

		//save
		if ($profile->save())
		{
			echo json_encode(array('success' => true));
		}
		else
		{
			echo json_encode(array('error' => 'An error occurred while trying to save you profile picture.'));
		}
	}

	/**
	 * Get the size, width, height, and src attributes for a file
	 *
	 * @return  string  JSON
	 */
	public function getfileattsTask()
	{
		$file = Request::getVar('file', '');
		$dir  = Request::getVar('dir', '');

		if (!$file || !$dir)
		{
			return;
		}

		$dir  = '/' . trim($dir, '/') . '/';
		$file = ltrim($file, '/');

		if (!file_exists(PATH_ROOT . $dir . $file))
		{
			return;
		}

		$size = filesize(PATH_ROOT . $dir . $file);
		list($width, $height) = getimagesize(PATH_ROOT . $dir . $file);

		$result = array();
		$result['src']    = $dir . $file;
		$result['name']   = $file;
		$result['size']   = \Hubzero\Utility\Number::formatBytes($size);
		$result['width']  = $width . ' <abbr title="pixels">px</abbr>';
		$result['height'] = $height . ' <abbr title="pixels">px</abbr>';

		echo json_encode($result);
	}

	/**
	 * Upload a file to the wiki
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		Request::checkToken();

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('MEMBERS_NO_ID'));
			return $this->displayTask('', $id);
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('MEMBERS_NO_FILE'));
			return $this->displayTask('', $id);
		}

		// Build upload path
		$dir  = \Hubzero\Utility\String::pad($id);
		$path = PATH_APP . DS . $this->filespace() . DS . $dir;

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('UNABLE_TO_CREATE_UPLOAD_PATH'));
				return $this->displayTask('', $id);
			}
		}

		// Make the filename safe
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Do we have an old file we're replacing?
		$curfile = Request::getVar('currentfile', '');

		// Perform the upload
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('ERROR_UPLOADING'));
			$file = $curfile;
		}
		else
		{
			$ih = new \Components\Members\Helpers\ImgHandler();

			if ($curfile != '')
			{
				// Yes - remove it
				if (file_exists($path . DS . $curfile))
				{
					if (!Filesystem::delete($path . DS . $curfile))
					{
						$this->setError(Lang::txt('UNABLE_TO_DELETE_FILE'));
						return $this->displayTask($file['name'], $id);
					}
				}

				$curthumb = $ih->createThumbName($curfile);
				if (file_exists($path . DS . $curthumb))
				{
					if (!Filesystem::delete($path . DS . $curthumb))
					{
						$this->setError(Lang::txt('UNABLE_TO_DELETE_FILE'));
						return $this->displayTask($file['name'], $id);
					}
				}
			}

			// Instantiate a profile, change some info and save
			$profile = Member::oneOrFail($id);
			$profile->set('picture', $file['name']);
			if (!$profile->save())
			{
				$this->setError($profile->getError());
			}

			// Resize the image if necessary
			$ih->set('image',$file['name']);
			$ih->set('path', $path . DS);
			$ih->set('maxWidth', 186);
			$ih->set('maxHeight', 186);
			if (!$ih->process())
			{
				$this->setError($ih->getError());
			}

			// Create a thumbnail image
			$ih->set('maxWidth', 50);
			$ih->set('maxHeight', 50);
			$ih->set('cropratio', '1:1');
			$ih->set('outputName', $ih->createThumbName());
			if (!$ih->process())
			{
				$this->setError($ih->getError());
			}

			$file = $file['name'];
		}

		// Push through to the image view
		$this->displayTask($file, $id);
	}

	/**
	 * Delete a file in the wiki
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		Request::checkToken(['get', 'post']);

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_ID'));
			return $this->displayTask('', $id);
		}

		$profile = Member::oneOrFail($id);

		if (!$profile->get('id'))
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_ID'));
			return $this->displayTask('', $id);
		}

		// Build the file path
		$files = array(
			'profile.png',
			'thumb.png'
		);

		$dir  = \Hubzero\Utility\String::pad($id);
		$path = PATH_APP . DS . $this->filespace() . DS . $dir;

		foreach ($files as $file)
		{
			if (!file_exists($path . DS . $file))
			{
				$this->setError(Lang::txt('FILE_NOT_FOUND'));
				continue;
			}

			// Attempt to delete the file
			if (!Filesystem::delete($path . DS . $file))
			{
				$this->setError(Lang::txt('UNABLE_TO_DELETE_FILE'));
				continue;
			}
		}

		// Push through to the image view
		$this->displayTask($file, $id);
	}

	/**
	 * Display a user's profile picture
	 *
	 * @param   string   $file  File name
	 * @param   integer  $id    User ID
	 * @return  void
	 */
	public function displayTask($file='', $id=0)
	{
		// Incoming
		if (!$id)
		{
			$id = Request::getInt('id', 0, 'get');
		}

		if (!$file)
		{
			$file = Request::getVar('file', '', 'get');
		}

		// Build the file path
		$dir  = \Hubzero\Utility\String::pad($id);
		$path = PATH_APP . DS . $this->filespace() . DS . $dir;

		// Output HTML
		$this->view
			->set('webpath', '/' . $this->filespace())
			->set('default_picture', $this->config->get('defaultpic', '/core/components/com_members/site/assets/img/profile.gif'))
			->set('path', $dir)
			->set('file', $file)
			->set('id', $id)
			->set('file_path', $path)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}

	/**
	 * Method to check admin access permission
	 *
	 * @param   integer  $uid        User ID
	 * @param   string   $assetType  Asset type
	 * @param   string   $assetId    Asset ID
	 * @return  boolean  True on success
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		// Check if they are logged in
		if (User::isGuest())
		{
			return false;
		}

		// Check if they're a site admin
		// Admin
		$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
		$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));

		if ($this->config->get('access-admin-' . $assetType))
		{
			return 'admin';
		}

		return false;
	}

	/**
	 * Download a file
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		//get vars
		$id = Request::getInt('id', 0);

		//check to make sure we have an id
		if (!$id || $id == 0)
		{
			return;
		}

		//Load member profile
		$member = Member::oneOrFail($id);

		// check to make sure we have member profile
		if (!$member)
		{
			return;
		}

		//get the file name
		// make sure to leave out any query params (ex. ?v={timestamp})
		$uri = Request::getVar('SCRIPT_URL', '', 'server');
		if (strstr($uri, 'Image:'))
		{
			$file = str_replace('Image:', '', strstr($uri, 'Image:'));
		}
		elseif (strstr($uri, 'File:'))
		{
			$file = str_replace('File:', '', strstr($uri, 'File:'));
		}

		//decode file name
		$file = urldecode($file);

		// build base path
		$base_path = $this->filespace() . DS . \Hubzero\Utility\String::pad($member->get('id'), 5);

		//if we are on the blog
		if (Request::getVar('active', 'profile') == 'blog')
		{
			// @FIXME Check still needs to occur for non-public entries
			//authorize checks
			/*if ($this->_authorize() != 'admin')
			{
				if (User::get('id') != $member->get('id'))
				{
					App::abort(403, Lang::txt('You are not authorized to download the file: ') . ' ' . $file);
				}
			}*/

			//get the params from the members blog plugin
			$blog_params = Plugin::params('members', 'blog');

			//build the base path to file based of upload path param
			$base_path = str_replace('{{uid}}', \Hubzero\Utility\String::pad($member->get('id'), 5), $blog_params->get('uploadpath'));
		}

		//build file path
		$file_path = $base_path . DS . $file;

		// Ensure the file exist
		if (!file_exists(PATH_APP . DS . $file_path))
		{
			App::abort(404, Lang::txt('The requested file could not be found: ') . ' ' . $file);
			return;
		}

		// Serve up the image
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename(PATH_APP . DS . $file_path);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support

		//serve up file
		if (!$xserver->serve())
		{
			// Should only get here on error
			App::abort(404, Lang::txt('An error occured while trying to output the file'));
		}

		exit;
	}

	/**
	 * Get file path
	 *
	 * @return  void
	 */
	private function filespace()
	{
		return trim($this->config->get('webpath', '/site/members'), DS);
	}
}
