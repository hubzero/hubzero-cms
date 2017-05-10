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
 * @copyright Copyright 2005-20115 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Admin\Controllers;

use Components\Collections\Models\Orm\Item;
use Components\Collections\Models\Orm\Asset;
use Components\Collections\Models\Orm\Post;
use Hubzero\Component\AdminController;
use Hubzero\Content\Server;
use Filesystem;
use Exception;
use Request;
use Lang;
use User;

/**
 * Collections controller class for media
 */
class Media extends AdminController
{
	/**
	 * Download a file
	 *
	 * @return  void
	 */
	public function downloadTask()
	{
		$file = Request::getVar('file', '');
		$item = Request::getInt('post', 0);

		$post = Post::oneOrFail($item);

		// Instantiate an attachment object
		$asset = Asset::getInstance($file, $post->get('item_id'));

		// Ensure we have a path
		if (!$asset->get('filename'))
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_FILE_NOT_FOUND'), 404);
		}

		// Get the configured upload path
		$filename = $asset->filespace() . DS . $asset->get('item_id') . DS . ltrim($asset->get('filename'), DS);

		// Ensure the file exist
		if (!file_exists($filename))
		{
			throw new Exception(Lang::txt('COM_COLLECTIONS_FILE_NOT_FOUND') . ' ' . $filename, 404);
		}

		// Initiate a new content server and serve up the file
		$server = new Server();
		$server->filename($filename);
		$server->disposition('attachment');
		if ($asset->isImage())
		{
			$server->disposition('inline');
		}
		$server->acceptranges(false); // @TODO fix byte range support

		if (!$server->serve())
		{
			// Should only get here on error
			throw new Exception(Lang::txt('COM_COLLECTIONS_SERVER_ERROR'), 500);
		}

		exit;
	}

	/**
	 * Upload a file to the wiki
	 *
	 * @return  void
	 */
	public function createTask()
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		if (Request::getVar('no_html', 0))
		{
			return $this->ajaxCreateTask();
		}

		// Ensure we have an ID to work with
		$listdir = Request::getInt('dir', 0, 'post');
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_NO_ID'));
			$this->displayTask();
			return;
		}

		if (substr($listdir, 0, 3) == 'tmp')
		{
			$item = Item::oneOrNew($listdir);
			if ($item->isNew())
			{
				$item->set('state', $item::STATE_UNPUBLISHED);
				$item->set('title', $listdir);
				if (!$item->save())
				{
					$this->setError($item->getError());
				}
			}
			$listdir = $item->get('id');
		}

		// Create database entry
		$asset = Asset::blank();
		$asset->set('item_id', intval($listdir));
		$asset->set('filename', 'http://');
		$asset->set('description', Request::getVar('description', '', 'post'));
		$asset->set('state', $asset::STATE_PUBLISHED);
		$asset->set('type', 'link');

		if (!$asset->save())
		{
			$this->setError($asset->getError());
		}

		$this->displayTask();
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return  string
	 */
	public function ajaxCreateTask()
	{
		// Ensure we have an ID to work with
		$listdir = strtolower(Request::getVar('dir', ''));
		if (!$listdir)
		{
			echo json_encode(array('error' => Lang::txt('COM_COLLECTIONS_NO_ID')));
			return;
		}

		if (substr($listdir, 0, 3) == 'tmp')
		{
			$item = Item::oneOrNew($listdir);
			if ($item->isNew())
			{
				$item->set('id', 0);
				$item->set('state', $item::STATE_UNPUBLISHED);
				$item->set('title', $listdir);
				if (!$item->save())
				{
					echo json_encode(array(
						'success'   => false,
						'errors'    => $item->getErrors(),
						'file'      => 'http://',
						'directory' => '',
						'id'        => $listdir
					));
					return;
				}
			}
			$listdir = $item->get('id');
		}

		// Create database entry
		$asset = Asset::blank();
		$asset->set('item_id', intval($listdir));
		$asset->set('filename', 'http://');
		$asset->set('description', Request::getVar('description', '', 'post'));
		$asset->set('state', $asset::STATE_PUBLISHED);
		$asset->set('type', 'link');

		if (!$asset->save())
		{
			echo json_encode(array(
				'success'   => false,
				'errors'    => $asset->getErrors(),
				'file'      => 'http://',
				'directory' => '',
				'id'        => $listdir
			));
			return;
		}

		//echo result
		echo json_encode(array(
			'success'   => true,
			'file'      => 'http://',
			'directory' => '',
			'id'        => $listdir
		));
	}

	/**
	 * Upload a file to the wiki via AJAX
	 *
	 * @return  string
	 */
	public function ajaxUploadTask()
	{
		// Ensure we have an ID to work with
		$listdir = Request::getInt('dir', 0);

		if (!$listdir)
		{
			echo json_encode(array('error' => $listdir . ' ' . Lang::txt('COM_COLLECTIONS_NO_ID')));
			return;
		}

		if (substr($listdir, 0, 3) == 'tmp')
		{
			$item = Item::oneOrNew($listdir);
			if ($item->isNew())
			{
				$item->set('state', $item::STATE_UNPUBLISHED);
				$item->set('title', $listdir);
				if (!$item->save())
				{
					echo json_encode(array(
						'error' => $item->getError()
					));
					return;
				}
			}
			$listdir = $item->get('id');
		}

		//max upload size
		$sizeLimit = $this->config->get('maxAllowed', 40000000);

		// get the file
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
			echo json_encode(array('error' => Lang::txt('COM_COLLECTIONS_FILE_NOT_FOUND')));
			return;
		}

		$asset = Asset::blank();

		//define upload directory and make sure its writable
		$path = $asset->filespace() . DS . $listdir;
		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				echo json_encode(array('error' => Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_CREATE_UPLOAD_DIR')));
				return;
			}
		}

		if (!is_writable($path))
		{
			echo json_encode(array('error' => Lang::txt('COM_COLLECTIONS_ERROR_UPLOAD_DIR_NOT_WRITABLE')));
			return;
		}

		//check to make sure we have a file and its not too big
		if ($size == 0)
		{
			echo json_encode(array('error' => Lang::txt('COM_COLLECTIONS_ERROR_EMPTY_FILE')));
			return;
		}
		if ($size > $sizeLimit)
		{
			$max = preg_replace('/<abbr \w+=\\"\w+\\">(\w{1,3})<\\/abbr>/', '$1', \Hubzero\Utility\Number::formatBytes($sizeLimit));
			echo json_encode(array('error' => Lang::txt('COM_COLLECTIONS_ERROR_FILE_TOO_LARGE', $max)));
			return;
		}

		// don't overwrite previous files that were uploaded
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

		// Create database entry
		$asset->set('item_id', intval($listdir));
		$asset->set('filename', $filename . '.' . $ext);
		$asset->set('description', Request::getVar('description', '', 'post'));
		$asset->set('state', $asset::STATE_PUBLISHED);
		$asset->set('type', 'file');

		if (!$asset->save())
		{
			echo json_encode(array(
				'error' => intval($listdir) . ' ' . $asset->getError()
			));
			return;
		}

		$view = new \Hubzero\Component\View(array(
			'name'   => 'media',
			'layout' => '_asset'
		));
		$view->i          = Request::getInt('i', 0);
		$view->option     = $this->_option;
		$view->controller = $this->_controller;
		$view->asset      = $asset;
		$view->no_html    = 1;

		//echo result
		echo json_encode(array(
			'success'   => true,
			'file'      => $filename . '.' . $ext,
			'directory' => str_replace(PATH_APP, '', $path),
			'id'        => $listdir,
			'html'      => str_replace('>', '&gt;', $view->loadTemplate()) // Entities have to be encoded or IE 8 goes nuts
		));
	}

	/**
	 * Upload a file to the wiki
	 *
	 * @return  void
	 */
	public function uploadTask()
	{
		if (!User::authorise('core.edit', $this->_option)
		 && !User::authorise('core.create', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		if (Request::getVar('no_html', 0))
		{
			return $this->ajaxUploadTask();
		}

		// Ensure we have an ID to work with
		$listdir = Request::getInt('dir', 0, 'post');
		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_NO_ID'));
			$this->displayTask();
			return;
		}

		// Incoming file
		$file = Request::getVar('upload', '', 'files', 'array');
		if (!$file['name'])
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_NO_FILE'));
			$this->displayTask();
			return;
		}

		$asset = Asset::blank();

		// Build the upload path if it doesn't exist
		$path = $asset->filespace() . DS . $listdir;

		if (!is_dir($path))
		{
			if (!Filesystem::makeDirectory($path))
			{
				$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_CREATE_UPLOAD_DIR'));
				$this->displayTask();
				return;
			}
		}

		// Make the filename safe
		$file['name'] = urldecode($file['name']);
		$file['name'] = Filesystem::clean($file['name']);
		$file['name'] = str_replace(' ', '_', $file['name']);

		// Upload new files
		if (!Filesystem::upload($file['tmp_name'], $path . DS . $file['name']))
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_UNABLE_TO_UPLOAD'));
		}
		// File was uploaded
		else
		{
			// Create database entry
			$asset->set('item_id', intval($listdir));
			$asset->set('filename', $file['name']);
			$asset->set('description', Request::getVar('description', '', 'post'));
			$asset->set('state', $asset::STATE_PUBLISHED);
			$asset->set('type', 'file');

			if (!$asset->save())
			{
				$this->setError($asset->getError());
			}
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Delete a file in the wiki
	 *
	 * @return  void
	 */
	public function deleteTask()
	{
		if (!User::authorise('core.delete', $this->_option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		if (Request::getVar('no_html', 0))
		{
			return $this->ajaxDeleteTask();
		}

		// Incoming asset
		$id = Request::getInt('asset', 0, 'get');

		$model = Asset::oneOrFail($id);

		if (!$model->destroy())
		{
			$this->setError($model->getError());
		}

		// Push through to the media view
		$this->displayTask();
	}

	/**
	 * Display a form for uploading files
	 *
	 * @return  void
	 */
	public function ajaxDeleteTask()
	{
		// Incoming
		$id = Request::getInt('asset', 0);

		if ($id)
		{
			$model = Asset::oneOrFail($id);

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
	 * Display a form for uploading files
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Incoming
		$listdir = Request::getInt('dir', 0, 'request');

		$this->view
			->set('config', $this->config)
			->set('listdir', $listdir)
			->setLayout('display')
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Display a list of files
	 *
	 * @return  void
	 */
	public function listTask()
	{
		// Incoming
		$listdir = Request::getInt('dir', 0, 'get');

		if (!$listdir)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_NO_ID'));
		}
		else
		{
			$item = Item::getInstance($listdir);

			if (!$item->get('id'))
			{
				$this->setError(Lang::txt('COM_COLLECTIONS_NO_ID'));
			}
		}

		$this->view
			->set('config', $this->config)
			->set('listdir', $listdir)
			->setErrors($this->getErrors())
			->display();
	}
}
