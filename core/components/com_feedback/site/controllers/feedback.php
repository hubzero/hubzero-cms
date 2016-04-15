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

namespace Components\Feedback\Site\Controllers;

use Components\Feedback\Models\Quote;
use Hubzero\Component\SiteController;
use Hubzero\User\Profile;
use Hubzero\Utility\Number;
use Hubzero\Utility\String;
use Hubzero\Utility\Sanitize;
use DirectoryIterator;
use Filesystem;
use Component;
use Pathway;
use Request;
use Config;
use Route;
use Lang;
use User;
use Date;

/**
 * Feedback controller class
 */
class Feedback extends SiteController
{
	/**
	 * Determine task and execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('success_story', 'story');

		parent::execute();
	}

	/**
	 * Set the pathway (breadcrumbs)
	 *
	 * @return  void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && in_array($this->_task, array('story', 'poll', 'sendstory', 'suggestions')))
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Set the page title
	 *
	 * @return  void
	 */
	protected function _buildTitle()
	{
		$this->_title = Lang::txt(strtoupper($this->_option));
		if ($this->_task && in_array($this->_task, array('story', 'poll', 'sendstory', 'suggestions')))
		{
			$this->_title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}

		\Document::setTitle($this->_title);
	}

	/**
	 * Display the main page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Check if wishlistcomponent entry is there
		$wishlist = Component::isEnabled('com_wishlist', true);

		// Check if poll component entry is there
		$poll = Component::isEnabled('com_poll', true);

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view
			->set('poll', $poll)
			->set('wishlist', $wishlist)
			->set('title', $this->_title)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Show a list of quotes
	 *
	 * @return  void
	 */
	public function quotesTask()
	{
		// Get quotes
		$quotes = Quote::all()
			->whereEquals('notable_quote', 1)
			->ordered()
			->rows();

		$quoteId = Request::getInt('quoteid');

		$this->view
			->set('quotes', $quotes)
			->set('quoteId', $quoteId)
			->display();
	}

	/**
	 * Show a form for sending a success story
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function storyTask($row=null)
	{
		if (User::isGuest())
		{
			$here = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($here)),
				Lang::txt('COM_FEEDBACK_STORY_LOGIN'),
				'warning'
			);
		}

		// Check to see if the user temp folder for holding pics is there, if so then remove it
		if (is_dir($this->tmpPath() . DS . User::get('id')))
		{
			Filesystem::deleteDirectory($this->tmpPath() . DS . User::get('id'));
		}

		// Incoming
		$quote = array(
			'long'  => Request::getVar('quote', '', 'post'),
			'short' => Request::getVar('short_quote', '', 'post')
		);

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Get the curent user's profile
		$user = Profile::getInstance(User::get('id'));

		// Create the object if we weren't passed one
		if (!is_object($row))
		{
			$row = Quote::oneOrNew(0);
			$row->set('org', $user->get('organization'));
			$row->set('fullname', $user->get('name'));
		}

		// Output HTML
		$this->view
			->set('title', $this->_title)
			->set('quote', $quote)
			->set('row', $row)
			->set('user', $user)
			->setErrors($this->getErrors())
			->setLayout('story')
			->display();
	}

	/**
	 * Show the latest poll
	 *
	 * @return  void
	 */
	public function pollTask()
	{
		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view
			->set('title', $this->_title)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Save a success story and show a thank you message
	 *
	 * @return  void
	 */
	public function sendstoryTask()
	{
		if (User::isGuest())
		{
			$here = Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task);
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($here)),
				Lang::txt('COM_FEEDBACK_STORY_LOGIN'),
				'warning'
			);
		}

		Request::checkToken();

		$fields = Request::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$fields['user_id'] = User::get('id');

		// Initiate class and bind posted items to database fields
		$row = Quote::oneOrNew(0)->set($fields);

		// Check that a story was entered
		if (!$row->get('quote'))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_ERROR_MISSING_STORY'));
			return $this->storyTask($row);
		}

		// Check for an author
		if (!$row->get('fullname'))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_ERROR_MISSING_AUTHOR'));
			return $this->storyTask($row);
		}

		// Check for an organization
		if (!$row->get('org'))
		{
			$this->setError(Lang::txt('COM_FEEDBACK_ERROR_MISSING_ORGANIZATION'));
			return $this->storyTask($row);
		}

		// Code cleaner for xhtml transitional compliance
		$row->set('quote', Sanitize::stripAll($row->get('quote')));
		$row->set('quote', str_replace('<br>', '<br />', $row->get('quote')));
		$row->set('date', Date::toSql());

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->storyTask($row);
		}

		$addedPictures = array();

		$path = $row->filespace() . DS . $row->get('id');
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_FEEDBACK_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
			}
		}

		// If there is a temp dir for this user then copy the contents to the newly created folder
		$tempDir = $this->tmpPath() . DS . User::get('id');

		if (is_dir($tempDir))
		{
			$dirIterator = new DirectoryIterator($tempDir);

			foreach ($dirIterator as $file)
			{
				if ($file->isDot() || $file->isDir())
				{
					continue;
				}

				$name = $file->getFilename();

				if ($file->isFile())
				{
					if ('cvs' == strtolower($name)
					 || '.svn' == strtolower($name))
					{
						continue;
					}

					if (Filesystem::move($tempDir . DS . $name, $path . DS . $name))
					{
						array_push($addedPictures, $name);
					}
				}
			}

			// Remove temp folder
			Filesystem::deleteDirectory($tempDir);
		}

		$path = substr($row->filespace(), strlen(PATH_ROOT)) . DS . $row->get('id');

		// Set page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		$this->view
			->set('row', $row)
			->set('path', $path)
			->set('addedPictures', $addedPictures)
			->set('title', $this->_title)
			->setErrors($this->getErrors())
			->setLayout('thanks')
			->display();
	}

	/**
	 * Show a form for submitting suggestions
	 *
	 * @return  void
	 */
	public function suggestionsTask()
	{
		App::redirect(
			Route::url('index.php?option=com_wishlist')
		);
	}

	/**
	 * Takes recieved files and saves them to a temporary directory specific
	 * directory then returns a json object with those file names.
	 *
	 * @return  void
	 */
	public function uploadImageTask()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			echo json_encode(array('error' => Lang::txt('COM_FEEDBACK_STORY_LOGIN')));
			return;
		}

		// Max upload size
		$sizeLimit = $this->config->get('maxAllowed', 40000000);

		// Get the file
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
			echo json_encode(array('error' => Lang::txt('COM_FEEDBACK_ERROR_FILE_NOT_FOUND')));
			return;
		}

		// Define upload directory and make sure its writable
		$path = rtrim($this->tmpPath(), DS) . DS . User::get('id');

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				echo json_encode(array('error' => Lang::txt('COM_FEEDBACK_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH')));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array('error' => Lang::txt('COM_FEEDBACK_ERROR_UPLOAD_PATH_IS_NOT_WRITABLE')));
			return;
		}

		// Check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => Lang::txt('COM_FEEDBACK_ERROR_EMPTY_FILE')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => Lang::txt('COM_FEEDBACK_ERROR_FILE_TOO_LARGE', $max)));
			return;
		}

		// Don't overwrite previous files that were uploaded
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];

		// Make the filename safe
		$filename = urldecode($filename);
		$filename = Filesystem::clean($filename);
		$filename = str_replace(' ', '_', $filename);

		$ext = $pathinfo['extension'];
		while (file_exists($path . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		$file = $path . DS . $filename . '.' . $ext;

		if ($stream)
		{
			// Read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			// Move from temp location to target location which is user folder
			$target = fopen($file , "w");
			fseek($temp, 0, SEEK_SET);
			stream_copy_to_stream($temp, $target);
			fclose($target);
		}
		else
		{
			move_uploaded_file($_FILES['qqfile']['tmp_name'], $file);
		}

		if (!Filesystem::isSafe($file))
		{
			if (Filesystem::delete($file))
			{
				echo json_encode(array(
					'success' => false,
					'error'  => Lang::txt('COM_FEEDBACK_ERROR_FILE_FAILED_VIRUS_SCAN')
				));
				return;
			}
		}

		// Output result
		echo json_encode(array(
			'success'    => true,
			'file'       => $filename . '.' . $ext,
			'directory'  => str_replace(PATH_ROOT, '', $path),
		));
	}

	/**
	 * Path to the temp directory
	 *
	 * @return  string
	 */
	protected function tmpPath()
	{
		return Config::get('tmp_path', PATH_APP . DS . '/tmp') . DS . 'feedback';
	}
}
