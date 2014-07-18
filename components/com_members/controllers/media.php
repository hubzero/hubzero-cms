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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Members controller class for media
 */
class MembersControllerMedia extends \Hubzero\Component\SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$parts = explode('/', $_SERVER['REQUEST_URI']);

		$file = array_pop($parts);

		if (substr(strtolower($file), 0, 5) == 'image'
		 || substr(strtolower($file), 0, 4) == 'file')
		{
			JRequest::setVar('task', 'download');
		}

		$this->registerTask('deleteimg', 'delete');

		parent::execute();
	}

	/**
	 * Show a form for uploading a file
	 *
	 * @return     void
	 */
	public function ajaxuploadTask()
	{
		//get the id
		$id = JRequest::getInt('id', 0);
		if (!$id)
		{
			return;
		}

		//load profile from id
		$this->view->profile = \Hubzero\User\Profile::getInstance($id);

		//instantiate view and pass needed vars
		$this->view->setLayout('upload');
		$this->view->config = $this->config;
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Upload a file to the profile via AJAX
	 *
	 * @return     string
	 */
	public function doajaxuploadTask()
	{
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
		$id = JRequest::getVar('id', 0);
		$profile = \Hubzero\User\Profile::getInstance($id);
		if (!$profile)
		{
			return;
		}

		//define upload directory and make sure its writable
		$uploadDirectory = JPATH_ROOT . DS . trim($this->config->get('webpath', '/site/members'), DS) . DS . \Hubzero\Utility\String::pad($id) . DS;

		if (!is_dir($uploadDirectory))
		{
			if (!JFolder::create($uploadDirectory))
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
		$final_file = $uploadDirectory . 'profile.png';
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
			'directory' => str_replace(JPATH_ROOT, '', $uploadDirectory)
		));
	}

	/**
	 * Set the picture of a profile
	 *
	 * @return     string JSON
	 */
	public function ajaxuploadsaveTask()
	{
		//get the user id
		$id = JRequest::getInt('id', 0);
		if (!$id)
		{
			echo json_encode(array('error' => 'Missing required user ID.'));
		}

		//load the user profile
		$profile = \Hubzero\User\Profile::getInstance($id);
		if (!$profile)
		{
			echo json_encode(array('error' => 'Unable to locate user profile.'));
		}

		//update the user pic
		$p = JRequest::getVar('profile', array());
		$profile->set('picture', $p['picture']);

		//save
		if ($profile->update())
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
	 * @return     string JSON
	 */
	public function getfileattsTask()
	{
		$file = JRequest::getVar('file', '');
		$dir  = JRequest::getVar('dir', '');

		if (!$file || !$dir)
		{
			return;
		}

		$size = filesize(JPATH_ROOT . $dir . $file);
		list($width, $height) = getimagesize(JPATH_ROOT . $dir . $file);

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
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			return false;
		}

		// Incoming member ID
		$id = JRequest::getInt('id', 0);
		if (!$id)
		{
			$this->setError(JText::_('MEMBERS_NO_ID'));
			$this->displayTask('', $id);
			return;
		}

		// Incoming file
		$file = JRequest::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(JText::_('MEMBERS_NO_FILE'));
			$this->displayTask('', $id);
			return;
		}

		// Build upload path
		$dir  = \Hubzero\Utility\String::pad($id);
		$path = JPATH_ROOT . DS . trim($this->config->get('webpath', '/site/members'), DS) . DS . $dir;

		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				$this->setError(JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
				$this->displayTask('', $id);
				return;
			}
		}

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Do we have an old file we're replacing?
		$curfile = JRequest::getVar('currentfile', '');

		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(JText::_('ERROR_UPLOADING'));
			$file = $curfile;
		}
		else
		{
			$ih = new MembersImgHandler();

			if ($curfile != '')
			{
				// Yes - remove it
				if (file_exists($path . DS . $curfile))
				{
					if (!JFile::delete($path . DS . $curfile))
					{
						$this->setError(JText::_('UNABLE_TO_DELETE_FILE'));
						$this->displayTask($file['name'], $id);
						return;
					}
				}

				$curthumb = $ih->createThumbName($curfile);
				if (file_exists($path . DS . $curthumb))
				{
					if (!JFile::delete($path . DS . $curthumb))
					{
						$this->setError(JText::_('UNABLE_TO_DELETE_FILE'));
						$this->displayTask($file['name'], $id);
						return;
					}
				}
			}

			// Instantiate a profile, change some info and save
			$profile = \Hubzero\User\Profile::getInstance($id);
			$profile->set('picture', $file['name']);
			if (!$profile->update())
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
	 * @return     void
	 */
	public function deleteTask()
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			return false;
		}

		// Incoming member ID
		$id = JRequest::getInt('id', 0);
		if (!$id)
		{
			$this->setError(JText::_('MEMBERS_NO_ID'));
			$this->displayTask('', $id);
		}

		// Incoming file
		$file = JRequest::getVar('file', '');
		if (!$file)
		{
			$this->setError(JText::_('MEMBERS_NO_FILE'));
			$this->displayTask('', $id);
		}

		// Build the file path
		$dir  = \Hubzero\Utility\String::pad($id);
		$path = JPATH_ROOT . DS . trim($this->config->get('webpath', '/site/members'), DS) . DS . $dir;

		if (!file_exists($path . DS . $file) or !$file)
		{
			$this->setError(JText::_('FILE_NOT_FOUND'));
		}
		else
		{
			$ih = new MembersImgHandler();

			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete($path . DS . $file))
			{
				$this->setError(JText::_('UNABLE_TO_DELETE_FILE'));
				$this->displayTask($file, $id);
				return;
			}

			$curthumb = $ih->createThumbName($file);
			if (file_exists($path . DS . $curthumb))
			{
				if (!JFile::delete($path . DS . $curthumb))
				{
					$this->setError(JText::_('UNABLE_TO_DELETE_FILE'));
					$this->displayTask($file, $id);
					return;
				}
			}

			// Instantiate a profile, change some info and save
			$profile = \Hubzero\User\Profile::getInstance($id);
			$profile->set('picture', '');
			if (!$profile->update())
			{
				$this->setError($profile->getError());
			}

			$file = '';
		}

		// Push through to the image view
		$this->displayTask($file, $id);
	}

	/**
	 * Display a user's profile picture
	 *
	 * @param      string  $file File name
	 * @param      integer $id   User ID
	 * @return     void
	 */
	public function displayTask($file='', $id=0)
	{
		// Incoming
		if (!$id)
		{
			$id = JRequest::getInt('id', 0, 'get');
		}
		$this->view->id = $id;

		if (!$file)
		{
			$file = JRequest::getVar('file', '', 'get');
		}
		$this->view->file = $file;

		// Build the file path
		$dir  = \Hubzero\Utility\String::pad($id);
		$path = JPATH_ROOT . DS . trim($this->config->get('webpath', '/site/members'), DS) . DS . $dir;

		// Output HTML
		$this->view->webpath = DS . trim($this->config->get('webpath', '/site/members'), DS);
		$this->view->default_picture = $this->config->get('defaultpic', '/components/com_members/images/profile.gif');
		$this->view->path = $dir;

		$this->view->file_path = $path;

		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->setLayout('display');
		$this->view->display();
	}

	/**
	 * Method to check admin access permission
	 *
	 * @param      integer $uid       User ID
	 * @param      string  $assetType Asset type
	 * @param      string  $assetId   Asset ID
	 * @return     boolean True on success
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		// Check if they are logged in
		if ($this->juser->get('guest'))
		{
			return false;
		}

		// Check if they're a site admin (from Joomla)
		// Admin
		$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
		$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));

		if ($this->config->get('access-admin-' . $assetType))
		{
			return 'admin';
		}

		return false;
	}

	/**
	 * Download a file
	 *
	 * @return     void
	 */
	public function downloadTask()
	{
		//get vars
		$id = JRequest::getInt('id', 0);

		//check to make sure we have an id
		if (!$id || $id == 0)
		{
			return;
		}

		//Load member profile
		$member = \Hubzero\User\Profile::getInstance($id);

		// check to make sure we have member profile
		if (!$member)
		{
			return;
		}

		$parts = explode('/', $_SERVER['REQUEST_URI']);
		$filename = array_pop($parts);

		//get the file name
		if (substr(strtolower($filename), 0, 5) == 'image')
		{
			$file = substr($filename, 6);
		}
		elseif (substr(strtolower($filename), 0, 4) == 'file')
		{
			$file = substr($filename, 5);
		}

		//decode file name
		$file = urldecode($file);

		//if we are on the blog
		if (JRequest::getVar('active', 'profile') == 'blog')
		{
			// @FIXME Check still needs to occur for non-public entries
			//authorize checks
			/*if ($this->_authorize() != 'admin')
			{
				if ($this->juser->get('id') != $member->get('uidNumber'))
				{
					JError::raiseError(403, JText::_('You are not authorized to download the file: ') . ' ' . $file);
					return;
				}
			}*/

			//get the params from the members blog plugin
			$blog_config = JPluginHelper::getPlugin('members', 'blog');
			$blog_params = new JRegistry($blog_config->params);

			//build the base path to file based of upload path param
			$base_path = str_replace('{{uid}}', \Hubzero\User\Profile\Helper::niceidformat($member->get('uidNumber')), $blog_params->get('uploadpath'));
		}

		//build file path
		$file_path = $base_path . DS . $file;

		// Ensure the file exist
		if (!file_exists(JPATH_ROOT . DS . $file_path))
		{
			JError::raiseError(404, JText::_('The requested file could not be found: ') . ' ' . $file);
			return;
		}

		// Serve up the image
		$xserver = new \Hubzero\Content\Server();
		$xserver->filename(JPATH_ROOT . DS . $file_path);
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support

		//serve up file
		if (!$xserver->serve())
		{
			// Should only get here on error
			JError::raiseError(404, JText::_('An error occured while trying to output the file'));
		}
		else
		{
			exit;
		}
		return;
	}
}

