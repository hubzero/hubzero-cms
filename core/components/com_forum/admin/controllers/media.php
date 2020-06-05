<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forum\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Forum\Models\Post;
use Components\Forum\Models\Attachment;
use Filesystem;
use Request;
use Lang;
use App;

/**
 * Manage files for a group
 */
class Media extends AdminController
{
	/**
	 * Override Execute Method
	 *
	 * @return  void
	 */
	public function execute()
	{
		$id = Request::getInt('id');

		// Load the group page
		$this->post = Post::oneOrFail($id);

		//continue with parent execute method
		parent::execute();
	}

	/**
	 * Upload a file
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$file = Attachment::oneOrFail(Request::getInt('attachment'));

		$extension = Filesystem::extension($file->get('filename'));

		// new content server
		$contentServer = new \Hubzero\Content\Server();
		$contentServer->filename($file->path());
		$contentServer->disposition('attachment');
		$contentServer->acceptranges(false);

		// do we need to manually set mime type
		if ($extension == 'css')
		{
			$contentServer->setContentType('text/css');
		}

		if ($extension == 'php')
		{
			$contentServer->setContentType('text/plain');
		}

		// Serve up the file
		if (!$contentServer->serve())
		{
			App::abort(500, Lang::txt('COM_FORUM_SERVER_ERROR'));
		}

		exit();
	}

	/**
	 * Delete a file
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		// Incoming file to delete
		$file = Attachment::oneOrFail(Request::getInt('attachment'));

		if (!$file || !$file->get('id'))
		{
			$this->setError(Lang::txt('COM_FORUM_ERROR_NO_FILE'));

			return $this->displayTask();
		}

		// Check if the file even exists
		if (!$file->destroy())
		{
			$this->setError($file->getError());
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Display a file and its info
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Output the HTML
		$this->view
			->set('post', $this->post)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}
}
