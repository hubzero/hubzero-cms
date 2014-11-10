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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Feedback controller class
 */
class FeedbackControllerFeedback extends \Hubzero\Component\SiteController
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
		$pathway = JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && in_array($this->_task, array('story', 'poll', 'sendstory', 'suggestions')))
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
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
		$this->_title = JText::_(strtoupper($this->_option));
		if ($this->_task && in_array($this->_task, array('story', 'poll', 'sendstory', 'suggestions')))
		{
			$this->_title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}

		JFactory::getDocument()->setTitle($this->_title);
	}

	/**
	 * Display the main page
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Check if wishlistcomponent entry is there
		$this->view->wishlist = JComponentHelper::isEnabled('com_wishlist', true);

		// Check if poll component entry is there
		$this->view->poll = JComponentHelper::isEnabled('com_poll', true);

		// Set page title
		$this->_buildTitle();
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Set any messages
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Show a list of quotes
	 *
	 * @return  void
	 */
	public function quotesTask()
	{
		// Get quotes
		$filters = array(
			'notable_quote' => 1
		);

		$sq = new FeedbackQuotes($this->database);
		$this->view->quotes = $sq->find('list', $filters);

		$this->view->path    = trim($this->config->get('uploadpath', '/site/quotes'), DS) . DS;
		$this->view->quoteId = JRequest::getInt('quoteid', null);

		$this->view->display();
	}

	/**
	 * Show a form for sending a success story
	 *
	 * @return  void
	 */
	public function storyTask($row=null)
	{
		// Check to see if the user temp folder for holding pics is there, if so then remove it
		if (is_dir(JPATH_ROOT . '/tmp/feedback/' . $this->juser->get('id')))
		{
			JFolder::delete(JPATH_ROOT . '/tmp/feedback/' . $this->juser->get('id'));
		}

		if ($this->juser->get('guest'))
		{
			$here = JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task);
			$this->setRedirect(
				JRoute::_('index.php?option=com_users&view=login&return=' . base64_encode($here)),
				JText::_('COM_FEEDBACK_STORY_LOGIN'),
				'warning'
			);
			return;
		}

		// Incoming
		$this->view->quote = array(
			'long'  => JRequest::getVar('quote', '', 'post'),
			'short' => JRequest::getVar('short_quote', '', 'post')
		);

		// Set page title
		$this->_buildTitle();
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		$this->view->user = \Hubzero\User\Profile::getInstance($this->juser->get('id'));

		if (!is_object($row))
		{
			$row = new FeedbackQuotes($this->database);
			$row->org      = $this->view->user->get('organization');
			$row->fullname = $this->view->user->get('name');
		}
		$row->user_id   = $this->view->user->get('uidNumber');
		$row->useremail = $this->view->user->get('email');

		$this->view->row = $row;

		// Set error messages
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view
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
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Set error messages
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output HTML
		$this->view->display();
	}

	/**
	 * Save a success story and show a thank you message
	 *
	 * @return  void
	 */
	public function sendstoryTask()
	{
		if ($this->juser->get('guest'))
		{
			$this->setRedirect(
				JRoute::_('index.php?option=' . $this->_option . '&controller=' . $this->_controller . '&task=' . $this->_task)
			);
			return;
		}

		$fields = JRequest::getVar('fields', array(), 'post');
		$fields = array_map('trim', $fields);

		$dir  = \Hubzero\Utility\String::pad($fields['user_id']);
		$path = DS . trim($this->config->get('uploadpath', '/site/quotes'), DS) . DS . $dir;

		// Initiate class and bind posted items to database fields
		$row = new FeedbackQuotes($this->database);
		if (!$row->bind($fields))
		{
			$this->setError($row->getError());
			$this->storyTask($row);
			return;
		}

		// Check that a story was entered
		if (!$row->quote)
		{
			$this->setError(JText::_('COM_FEEDBACK_ERROR_MISSING_STORY'));
			$this->storyTask($row);
			return;
		}

		// Code cleaner for xhtml transitional compliance
		$row->quote = \Hubzero\Utility\Sanitize::stripAll($row->quote);
		$row->quote = str_replace('<br>', '<br />', $row->quote);
		$row->date  = JFactory::getDate()->toSql();

		// Check content
		if (!$row->check())
		{
			$this->setError($row->getError());
			$this->storyTask($row);
			return;
		}

		// Store new content
		if (!$row->store())
		{
			$this->setError($row->getError());
			$this->storyTask($row);
			return;
		}

		$files = $_FILES;
		$addedPictures = array();

		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/quotes'), DS) . DS . $row->id;
		if (!is_dir($path))
		{
			if (!JFolder::create($path))
			{
				$this->setError(JText::_('COM_FEEDBACK_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
			}
		}

		// If there is a temp dir for this user then copy the contents to the newly created folder
		$tempDir = JPATH_ROOT . '/tmp/feedback/' . $this->juser->get('id');

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

					if (JFile::move($tempDir . '/' . $name, $path . '/' . $name))
					{
						array_push($addedPictures, $name);
					}
				}
			}

			// Remove temp folder
			JFolder::delete($tempDir);
		}

		$this->view->addedPictures = $addedPictures;
		$this->view->path   = trim($this->config->get('uploadpath', '/site/quotes'), DS) . DS . $row->id;

		// Output HTML
		$this->view->user   = $this->juser;
		$this->view->row    = $row;
		$this->view->config = $this->config;

		// Set page title
		$this->_buildTitle();
		$this->view->title = $this->_title;

		// Set the pathway
		$this->_buildPathway();

		// Set error messages
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// Output HTML
		$this->view
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
		$this->setRedirect(
			JRoute::_('index.php?option=com_wishlist')
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
		if ($this->juser->get('guest'))
		{
			echo json_encode(array('error' => JText::_('COM_FEEDBACK_STORY_LOGIN')));
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
			echo json_encode(array('error' => JText::_('COM_FEEDBACK_ERROR_FILE_NOT_FOUND')));
			return;
		}

		// Define upload directory and make sure its writable
		$path = 'tmp/feedback/' . $this->juser->get('id');

		if (!is_dir($path))
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path))
			{
				echo json_encode(array('error' => JText::_('COM_FEEDBACK_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH')));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array('error' => JText::_('COM_FEEDBACK_ERROR_UPLOAD_PATH_IS_NOT_WRITABLE')));
			return;
		}

		// Check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => JText::_('COM_FEEDBACK_ERROR_EMPTY_FILE')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => JText::sprintf('COM_FEEDBACK_ERROR_FILE_TOO_LARGE', $max)));
			return;
		}

		// Don't overwrite previous files that were uploaded
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$filename = urldecode($filename);
		$filename = JFile::makeSafe($filename);
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

		if (!JFile::isSafe($file))
		{
			if (JFile::delete($file))
			{
				echo json_encode(array(
					'success' => false,
					'error'  => JText::_('COM_FEEDBACK_ERROR_FILE_FAILED_VIRUS_SCAN')
				));
				return;
			}
		}

		// Output result
		echo json_encode(array(
			'success'    => true,
			'file'       => $filename . '.' . $ext,
			'directory'  => str_replace(JPATH_ROOT, '', $path),
		));
	}
}

