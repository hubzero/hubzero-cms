<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Site\Controllers;

use Components\Support\Models\Attachment;
use Components\Support\Models\Comment;
use Components\Support\Models\Ticket;
use Hubzero\Component\SiteController;
use Hubzero\Utility\Number;
use Hubzero\Component\View;
use Filesystem;
use Request;
use Lang;
use User;

require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'ticket.php';

/**
 * Collections controller class for media
 */
class Media extends SiteController
{
	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return  string
	 */
	public function ajaxUploadTask()
	{
		// Check if they're logged in
		/*if (User::isGuest())
		{
			echo json_encode(array('error' => Lang::txt('Must be logged in.')));
			return;
		}*/

		// Ensure we have an ID to work with
		$ticket  = Request::getInt('ticket', 0);
		$comment = Request::getInt('comment', 0);
		if (!$ticket)
		{
			echo json_encode(array('error' => Lang::txt('COM_SUPPORT_NO_ID'), 'ticket' => $ticket));
			return;
		}

		$mediaConfig = Component::params('com_media');

		$sizeLimit = $this->config->get('maxAllowed');
		if (!$sizeLimit)
		{
			// Size limit is in MB, so we need to turn it into just B
			$sizeLimit = $mediaConfig->get('upload_maxsize');
			$sizeLimit = $sizeLimit * 1024 * 1024;
		}

		// get the file
		if (isset($_GET['qqfile']) && isset($_SERVER["CONTENT_LENGTH"])) // make sure we actually have a file
		{
			$stream = true;
			$file = $_GET['qqfile'];
			$size = (int) $_SERVER["CONTENT_LENGTH"];
		}
		elseif (isset($_FILES['qqfile']) && isset($_FILES['qqfile']['size']))
		{
			$stream = false;
			$file = $_FILES['qqfile']['name'];
			$size = (int) $_FILES['qqfile']['size'];
		}
		else
		{
			$this->logUploadIssue('no file in request (no qqfile / missing CONTENT_LENGTH)', array(
				'ticket'  => $ticket,
				'has_get' => isset($_GET['qqfile']) ? 1 : 0,
				'has_files' => isset($_FILES['qqfile']) ? 1 : 0
			));
			echo json_encode(array('error' => Lang::txt('File not found')));
			return;
		}

		//define upload directory and make sure its writable
		$path = PATH_APP . DS . trim($this->config->get('webpath', '/site/tickets'), DS) . DS . $ticket;
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->logUploadIssue('could not create upload directory', array('ticket' => $ticket, 'path' => $path));
				echo json_encode(array('error' => Lang::txt('Error uploading. Unable to create path.')));
				return;
			}
		}

		if (!is_writable($path))
		{
			$this->logUploadIssue('upload directory not writable', array('ticket' => $ticket, 'path' => $path));
			echo json_encode(array('error' => Lang::txt('Server error. Upload directory isn\'t writable.')));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			$this->logUploadIssue('reported file size is 0', array('ticket' => $ticket, 'stream' => $stream ? 1 : 0));
			echo json_encode(array('error' => Lang::txt('File is empty')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$this->logUploadIssue('file exceeds size limit', array('ticket' => $ticket, 'size' => $size, 'limit' => $sizeLimit));
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => Lang::txt('File is too large. Max file upload size is %s', $max)));
			return;
		}

		// don't overwrite previous files that were uploaded
		$pathinfo = pathinfo($file);
		$filename = $pathinfo['filename'];

		// Make the filename safe
		$filename = urldecode($filename);
		$filename = Filesystem::clean($filename);
		$filename = str_replace(' ', '_', $filename);

		$ext = isset($pathinfo['extension']) ? $pathinfo['extension'] : '';
		while (file_exists($path . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		// Make sure that file is acceptable type
		$exts = $this->config->get('file_ext');
		$exts = $exts ?: $mediaConfig->get('upload_extensions');
		$allowed = array_values(array_filter(explode(',', $exts)));

		if (!in_array(strtolower($ext), $allowed))
		{
			$this->logUploadIssue('extension not in allowed list', array(
				'ticket' => $ticket,
				'file'   => $file,
				'ext'    => ($ext === '' || $ext === null) ? '(none)' : $ext
			));
			echo json_encode(array('error' => Lang::txt('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE')));
			return;
		}

		$file = $path . DS . $filename . '.' . $ext;

		if ($stream)
		{
			//read the php input stream to upload file
			$input = fopen("php://input", "r");
			$temp = tmpfile();
			$realSize = stream_copy_to_stream($input, $temp);
			fclose($input);

			//move from temp location to target location which is user folder
			$target = fopen($file, "w");
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
			// Always remove the rejected upload, then report - previously the
			// error was only returned when the delete happened to succeed,
			// which let a rejected file fall through and get saved anyway.
			Filesystem::delete($file);

			$this->logUploadIssue('antivirus scan rejected file', array(
				'ticket' => $ticket,
				'file'   => $filename . '.' . $ext,
				'size'   => $size
			));

			echo json_encode(array(
				'success' => false,
				'error'   => Lang::txt('ATTACHMENT: File rejected because the anti-virus scan failed.')
			));
			return;
		}

		// Create database entry
		$asset = Attachment::blank();
		$asset->set(array(
			'id'          => 0,
			'ticket'      => $ticket,
			'comment_id'  => $comment,
			'filename'    => $filename . '.' . $ext,
			'description' => Request::getString('description', '')
		));
		if (!$asset->save())
		{
			$this->logUploadIssue('attachment DB record failed to save', array(
				'ticket' => $ticket,
				'file'   => $filename . '.' . $ext,
				'error'  => $asset->getError()
			));
			echo json_encode(array(
				'success' => false,
				'error'   => $asset->getError()
			));
			return;
		}

		$view = new View(array(
			'name'   => 'media',
			'layout' => '_asset'
		));
		$view->set('option', $this->_option);
		$view->set('controller', $this->_controller);
		$view->asset      = $asset;
		$view->no_html    = 1;

		// Remove any abandoned uploads older than a day
		try
		{
			$old = time() - 86400;
			$uploads = PATH_APP . DS . trim($this->config->get('webpath', '/site/tickets'), DS);
			$dirs = Filesystem::directories($uploads);
			foreach ($dirs as $dir)
			{
				$dir = basename($dir);
				$dir = intval(trim($dir, DS));

				// Temp directories have a negative timestamp
				// So, if it's greater than zero, we can assume it's valid
				if ($dir > 0)
				{
					continue;
				}

				$name = abs($dir);

				// If it's older than a day ago...
				if ($name < $old)
				{
					Filesystem::deleteDirectory($uploads . DS . $dir);
				}
			}
		}
		catch (\Exception $e)
		{
			// Fail silently.
		}

		// echo result
		echo json_encode(array(
			'success'    => true,
			'file'       => $filename . '.' . $ext,
			'directory'  => str_replace(PATH_APP, '', $path),
			'ticket'     => $ticket,
			'comment_id' => $comment,
			'html'       => str_replace('>', '&gt;', $view->loadTemplate()) // Entities have to be encoded or IE 8 goes nuts
		));
	}

	/**
	 * Upload a file
	 *
	 * @return     void
	 */
	public function uploadTask()
	{
		// Check if they're logged in
		/*if (User::isGuest())
		{
			$this->displayTask();
			return;
		}*/

		if (Request::getInt('no_html', 0))
		{
			return $this->ajaxUploadTask();
		}

		// Ensure we have an ID to work with
		$ticket  = Request::getInt('ticket', 0, 'post');
		$comment = Request::getInt('comment', 0, 'post');
		if (!$ticket)
		{
			$this->setError(Lang::txt('COM_SUPPORT_NO_ID'));
			return $this->displayTask();
		}

		// Incoming file
		$file = Request::getArray('upload', array(), 'files');
		if (empty($file) || !$file['name'])
		{
			$this->setError(Lang::txt('COM_SUPPORT_NO_FILE'));
			return $this->displayTask();
		}

		// Build the upload path if it doesn't exist
		$path = PATH_APP . DS . trim($this->config->get('filepath', '/site/tickets'), DS) . DS . $ticket;

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('Error uploading. Unable to create path.'));
				return $this->displayTask();
			}
		}

		$mediaConfig = Component::params('com_media');

		$sizeLimit = $this->config->get('maxAllowed');
		if (!$sizeLimit)
		{
			// Size limit is in MB, so we need to turn it into just B
			$sizeLimit = $mediaConfig->get('upload_maxsize');
			$sizeLimit = $sizeLimit * 1024 * 1024;
		}

		if ($file['size'] > $sizeLimit)
		{
			$this->setError(Lang::txt('File is too large. Max file upload size is %s', Number::formatBytes($sizeLimit)));
			return $this->displayTask();
		}

		// Make the filename safe
		$file['name'] = urldecode($file['name']);
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		$ext = Filesystem::extension($file['name']);
		$filename = Filesystem::name($file['name']);
		while (file_exists($path . DS . $filename . '.' . $ext))
		{
			$filename .= rand(10, 99);
		}

		// Make sure that file is acceptable type
		$exts = $this->config->get('file_ext');
		$exts = $exts ?: $mediaConfig->get('upload_extensions');
		$allowed = array_values(array_filter(explode(',', $exts)));

		if (!in_array(strtolower($ext), $allowed))
		{
			$this->logUploadIssue('extension not in allowed list', array(
				'ticket' => $ticket,
				'file'   => $file['name'],
				'ext'    => ($ext === '' || $ext === null) ? '(none)' : $ext
			));
			$this->setError(Lang::txt('COM_SUPPORT_ERROR_INCORRECT_FILE_TYPE'));
			return $this->displayTask();
		}

		$filename .= '.' . $ext;

		// Upload new files
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $filename))
		{
			$this->setError(Lang::txt('ERROR_UPLOADING'));
		}
		// File was uploaded
		else
		{
			$fle = $path . DS . $filename;

			// Scan the file that was actually written ($fle), not the raw
			// $_FILES array ($file), and always remove it on rejection.
			if (!Filesystem::isSafe($fle))
			{
				Filesystem::delete($fle);

				$this->logUploadIssue('antivirus scan rejected file', array(
					'ticket' => $ticket,
					'file'   => $filename
				));

				$this->setError(Lang::txt('ATTACHMENT: File rejected because the anti-virus scan failed.'));
				echo $this->getError();
				return;
			}

			// Create database entry
			$asset = Attachment::blank();
			$asset->set(array(
				'id'          => 0,
				'ticket'      => $ticket,
				'comment_id'  => $comment,
				'filename'    => $filename,
				'description' => Request::getString('description', '')
			));

			if (!$asset->save())
			{
				$this->setError($asset->getError());
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Delete a file
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		if (Request::getInt('no_html', 0))
		{
			return $this->ajaxDeleteTask();
		}

		// Incoming asset
		$id = Request::getInt('asset', 0, 'get');

		$model = Attachment::oneOrFail($id);

		// Check if they're logged in when the ticket ID
		// is > 0. This means it's an attachment on a real
		// ticket, not a temp.
		if ($model->get('ticket') > 0 && User::isGuest())
		{
			return $this->displayTask();
		}
		$model->destroy();

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Display a form for uploading files
	 *
	 * @return     void
	 */
	public function ajaxDeleteTask()
	{
		// Incoming
		$id = Request::getInt('asset', 0);

		if ($id)
		{
			$model = Attachment::oneOrFail($id);

			if (!$model->destroy())
			{
				echo json_encode(array(
					'success' => false,
					'error'   => $model->getError()
				));
				return;
			}
		}

		//echo result
		echo json_encode(array(
			'success' => true,
			'asset'   => $id
		));
	}

	/**
	 * Display a list of files
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$ticket  = Request::getInt('ticket', 0);
		$comment = Request::getInt('comment', 0);

		if (!$ticket)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_NO_ID'));
		}

		if ($comment)
		{
			$model = Comment::oneOrFail($comment);
		}
		else
		{
			$model = Ticket::oneOrFail($ticket);
		}

		$this->view
			->set('model', $model)
			->set('config', $this->config)
			->set('ticket', $ticket)
			->set('comment', $comment)
			->setErrors($this->getErrors())
			->setLayout('list')
			->display();
	}

	/**
	 * Log a support-attachment upload problem to the PHP error log.
	 *
	 * These uploads fail intermittently for some users and are very hard to
	 * reproduce; recording which branch rejected the upload (and the relevant
	 * request facts) makes the next occurrence diagnosable instead of silent.
	 *
	 * @param   string  $reason   Short reason / branch identifier
	 * @param   array   $context  Extra key => value context
	 * @return  void
	 */
	protected function logUploadIssue($reason, $context = array())
	{
		$context = array_merge(array(
			'guest'          => User::isGuest() ? 1 : 0,
			'content_length' => isset($_SERVER['CONTENT_LENGTH']) ? $_SERVER['CONTENT_LENGTH'] : '-',
		), $context);

		$parts = array();
		foreach ($context as $k => $v)
		{
			$parts[] = $k . '=' . $v;
		}

		error_log(sprintf(
			' cms.WARNING support attachment upload failed: %s [%s]',
			$reason,
			implode(' ', $parts)
		));
	}
}
