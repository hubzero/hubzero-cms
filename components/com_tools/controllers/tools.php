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
 * Controller class for tools (default)
 */
class ToolsControllerTools extends \Hubzero\Component\SiteController
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
		$task = JRequest::getVar('task', 'display');

		// Check if middleware is enabled
		if ($task != 'image'
		 && $task != 'css'
		 && $task != 'assets'
		 && (!$this->config->get('mw_on') || ($this->config->get('mw_on') > 1 && !$this->config->get('access-admin-component'))))
		{
			// Redirect to home page
			$this->setRedirect(
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
		$this->_title = ($title) ? $title : JText::_(strtoupper($this->_option));
		if ($this->_task && $this->_task != 'display')
		{
			$this->_title .= ': ' . JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task));
		}
		$document = JFactory::getDocument();
		$document->setTitle($this->_title);
	}

	/**
	 * Method to set the document path
	 *
	 * @return	void
	 */
	protected function _buildPathway()
	{
		$pathway = JFactory::getApplication()->getPathway();

		if (count($pathway->getPathWay()) <= 0)
		{
			$pathway->addItem(
				(isset($this->_title) ? $this->_title : JText::_(strtoupper($this->_option))),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task && $this->_task != 'display')
		{
			$pathway->addItem(
				JText::_(strtoupper($this->_option) . '_' . strtoupper($this->_task)),
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
		include_once(JPATH_COMPONENT . DS . 'models' . DS . 'tools.php');
		$model = new ToolsModelTools();

		// Get the tool list
		$this->view->apps = $model->getApplicationTools();

		// Get the forge image
		$this->view->image = \Hubzero\Document\Assets::getComponentImage($this->_option, 'forge.png', 1);

		// Get some vars to fill in text
		$this->view->title = $this->_title;

		$jconfig = JFactory::getConfig();

		$live_site = rtrim(JURI::base(),'/');
		$slive_site = preg_replace('/^http:\/\//', 'https://', $live_site, 1);

		$this->view->forgeName = $jconfig->getValue('config.sitename') . ' FORGE';

		// Set the page title
		$this->_buildTitle($this->view->forgeName);

		// Set the pathway
		$this->_buildPathway();

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
	 * Tool asset delivery function.
 	 * Original purpose was to deliver a template overrideable css file for the filexfer package
	 *
	 * @return    exit
	 */
	public function assetsTask()
	{
		$type = JRequest::getVar('type', 'css');
		$file = JRequest::getVar('file', '');

		if (($type != 'css') || empty($file))
		{
			ob_clean();
			header("HTTP/1.1 404 Not Found");
			ob_end_flush();
			exit;
		}

		if ($type == 'css')
		{
			$file = JPATH_SITE . \Hubzero\Document\Assets::getComponentStylesheet($this->_option, $file);

			if (is_readable($file))
			{
				ob_clean();
				header("Content-Type: text/css");
				readfile($file);
				ob_end_flush();
				exit;
			}
			else
			{
				ob_clean();
				header("HTTP/1.1 404 Not Found");
				ob_end_flush();
				exit;
			}
		}
	}

	/**
	 * Display the FORGE logo
	 *
	 * @return     void
	 */
	public function imageTask()
	{
		$image = JPATH_SITE . \Hubzero\Document\Assets::getComponentImage($this->_option, 'forge.png', 1);

		if (is_readable($image))
		{
			ob_clean();
			header("Content-Type: image/png");
			readfile($image);
			ob_end_flush();
			exit;
		}
	}

	/**
	 * Display CSS
	 *
	 * @return     void
	 */
	public function cssTask()
	{
		$file = JPATH_SITE . \Hubzero\Document\Assets::getComponentStylesheet($this->_option, 'site_css.css');

		if (is_readable($file))
		{
			ob_clean();
			header("Content-Type: text/css");
			readfile($file);
			ob_end_flush();
			exit;
		}
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
		if (!$this->juser->get('guest'))
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
			$this->config->set('access-admin-' . $assetType, $this->juser->authorise('core.admin', $asset));
			$this->config->set('access-manage-' . $assetType, $this->juser->authorise('core.manage', $asset));
			// Permissions
			$this->config->set('access-create-' . $assetType, $this->juser->authorise('core.create' . $at, $asset));
			$this->config->set('access-delete-' . $assetType, $this->juser->authorise('core.delete' . $at, $asset));
			$this->config->set('access-edit-' . $assetType, $this->juser->authorise('core.edit' . $at, $asset));
			$this->config->set('access-edit-state-' . $assetType, $this->juser->authorise('core.edit.state' . $at, $asset));
			$this->config->set('access-edit-own-' . $assetType, $this->juser->authorise('core.edit.own' . $at, $asset));
		}
	}
}

