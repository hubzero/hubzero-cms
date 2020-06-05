<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Document;
use Component;
use Request;
use Route;
use Lang;
use User;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'status.php';

/**
 * Controller class for contributing a tool
 */
class Status extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Check logged in status
		if (User::isGuest())
		{
			$return = base64_encode(Request::getString('REQUEST_URI', Route::url('index.php?option='
				. $this->_option . '&controller=' . $this->_controller . '&task='
				. Request::getWord('task', ''), false, true), 'server'));

			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . $return)
			);
			return;
		}

		$this->_authorize();

		// Set the default task
		$this->registerTask('__default', 'status');

		parent::execute();
	}

	/**
	 * Session status
	 *
	 * @return  void
	 */
	public function statusTask()
	{
		// Set the page title
		$this->view->title = Lang::txt(strtoupper($this->_option)) . ': ' . Lang::txt(strtoupper($this->_option . '_' . $this->_task));
		Document::setTitle($this->view->title);

		// Create a Table object
		$obj = new \Components\Tools\Tables\Status($this->database);

		$this->view->lastsession = $obj->getLastSession();
		$this->view->sessions = $obj->getSessionCount();
		$this->view->used_displays = $obj->getUsedDisplayCount();
		$this->view->ready_displays = $obj->getReadyDisplayCount();
		$this->view->absent_displays = $obj->getAbsentDisplayCount();
		$this->view->broken_displays = $obj->getBrokenDisplayCount();
		$this->view->config = $this->config;
		$this->view->admin = $this->config->get('access-admin-component');

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}

	/**
	 * Authorization checks
	 *
	 * @param   string  $assetType  Asset type
	 * @param   string  $assetId    Asset id to check against
	 * @return  void
	 */
	protected function _authorize($assetType='component', $assetId=null)
	{
		$this->config->set('access-view-' . $assetType, true);
		if (User::isGuest())
		{
			return;
		}

		// if no admin group is defined, allow superadmin to act as admin
		// otherwise superadmins can only act if they are also a member of the component admin group
		if (($admingroup = trim($this->config->get('admingroup', ''))))
		{
			// Check if they're a member of admin group
			$ugs = \Hubzero\User\Helper::getGroups(User::get('id'));
			if ($ugs && count($ugs) > 0)
			{
				$admingroup = strtolower($admingroup);
				foreach ($ugs as $ug)
				{
					if (strtolower($ug->cn) == $admingroup)
					{
						$this->config->set('access-manage-' . $assetType, true);
						$this->config->set('access-admin-' . $assetType, true);
						$this->config->set('access-create-' . $assetType, true);
						$this->config->set('access-delete-' . $assetType, true);
						$this->config->set('access-edit-' . $assetType, true);
					}
				}
			}
		}
		else
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
