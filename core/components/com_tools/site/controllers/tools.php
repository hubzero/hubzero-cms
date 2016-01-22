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

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Document;
use Pathway;
use Request;
use Route;
use Lang;
use User;
use App;

/**
 * Controller class for tools (default)
 */
class Tools extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->_authorize();

		// Get the task
		$task = Request::getVar('task', 'display');

		// Check if middleware is enabled
		if ($task != 'image'
		 && $task != 'css'
		 && $task != 'assets'
		 && (!$this->config->get('mw_on') || ($this->config->get('mw_on') > 1 && !$this->config->get('access-admin-component'))))
		{
			// Redirect to home page
			App::redirect(
				$this->config->get('mw_redirect', '/home')
			);
			return;
		}

		parent::execute();
	}

	/**
	 * Method to build and set the document title
	 *
	 * @return	void
	 */
	protected function _buildTitle($title=null)
	{
		$this->_title = ($title) ? $title : Lang::txt(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'display')
		{
			$this->_title .= ': ' . Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		Document::setTitle($this->_title);
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				(isset($this->_title) ? $this->_title : Lang::txt(strtoupper($this->_option))),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && $this->_task != 'display')
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}
	}

	/**
	 * Display the landing page
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'tools.php');
		$model = new \Components\Tools\Models\Tools();

		// Get the tool list
		$this->view->apps = $model->getApplicationTools();

		// Get the forge image
		$this->view->image = \Hubzero\Document\Assets::getComponentImage($this->_option, 'forge.png', 1);

		// Get some vars to fill in text
		$this->view->title = $this->_title;

		$live_site = rtrim(Request::base(),'/');
		$slive_site = preg_replace('/^http:\/\//', 'https://', $live_site, 1);

		$this->view->forgeName = Config::get('sitename') . ' FORGE';

		// Set the page title
		$this->_buildTitle($this->view->forgeName);

		// Set the pathway
		$this->_buildPathway();

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Tool asset delivery function.
 	 * Original purpose was to deliver a template overrideable css file for the filexfer package
	 *
	 * @return    exit
	 */
	public function assetsTask()
	{
		$type = Request::getVar('type', 'css');
		$file = Request::getVar('file', '');

		if (($type != 'css') || empty($file))
		{
			ob_clean();
			header("HTTP/1.1 404 Not Found");
			ob_end_flush();
			exit;
		}

		if ($type == 'css')
		{
			$this->cssTask($file);
		}
	}

	/**
	 * Display the FORGE logo
	 *
	 * @return  void
	 */
	public function imageTask()
	{
		$file = 'forge.png';

		$paths = array(
			\App::get('template')->path . DS . 'html' . DS . $this->_option . DS . 'images' . DS . $file,
			dirname(__DIR__) . DS . 'assets' . DS . 'img' . DS . $file,
			dirname(__DIR__) . DS . 'images' . DS . $file
		);

		// Run through each path until we find one that works
		$image = null;
		foreach ($paths as $path)
		{
			if (file_exists($path))
			{
				$image = $path;
			}
		}

		if (is_readable($image))
		{
			ob_clean();
			header("Content-Type: image/png");
			readfile($image);
			ob_end_flush();
			exit;
		}

		ob_clean();
		header("HTTP/1.1 404 Not Found");
		ob_end_flush();
		exit;
	}

	/**
	 * Display CSS
	 *
	 * @param   string  $css
	 * @return  void
	 */
	public function cssTask($css = 'site_css.css')
	{
		$paths = array(
			\App::get('template')->path . DS . 'html' . DS . $this->_option . DS . $css,
			dirname(__DIR__) . DS . 'assets' . DS . 'css' . DS . $css,
			dirname(__DIR__) . DS . 'css' . DS . $css
		);

		// Run through each path until we find one that works
		$file = null;
		foreach ($paths as $path)
		{
			if (file_exists($path))
			{
				$file = $path;
			}
		}

		if (is_readable($file))
		{
			ob_clean();
			header("Content-Type: text/css");
			readfile($file);
			ob_end_flush();
			exit;
		}

		ob_clean();
		header("HTTP/1.1 404 Not Found");
		ob_end_flush();
		exit;
	}

	/**
	 * Authorization checks
	 *
	 * @param      string $assetType Asset type
	 * @param      string $assetId   Asset id to check against
	 * @return     void
	 */
	public function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (!User::isGuest())
		{
			$asset  = $this->_option;
			if ($assetId)
			{
				$asset .= ($assetType != 'component') ? '.' . $assetType : '';
				$asset .= ($assetId) ? '.' . $assetId : '';
			}

			$at = '';
			if ($assetType != 'component')
			{
				$at .= '.' . $assetType;
			}

			// Admin
			$this->config->set('access-admin-' . $assetType, User::authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, User::authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, User::authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, User::authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, User::authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, User::authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, User::authorise('core.edit.own' . $at, $asset));
		}
	}
}

