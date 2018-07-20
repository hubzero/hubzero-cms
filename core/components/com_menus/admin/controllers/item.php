<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_HZEXEC_') or die();

jimport('joomla.application.component.controllerform');

/**
 * The Menu Item Controller
 *
 * @package     Joomla.Administrator
 * @subpackage  com_menus
 * @since       1.6
 */
class MenusControllerItem extends JControllerForm
{
	/**
	 * Method to add a new menu item.
	 *
	 * @return  mixed  True if the record can be added, a JError object if not.
	 *
	 * @since   1.6
	 */
	public function add()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$context = 'com_menus.edit.item';

		$result = parent::add();
		if ($result)
		{
			$app->setUserState($context . '.type', null);
			$app->setUserState($context . '.link', null);

			$menuType = $app->getUserStateFromRequest($this->context . '.filter.menutype', 'menutype', 'mainmenu', 'cmd');

			$this->setRedirect(Route::url('index.php?option=com_menus&view=item&menutype=' . $menuType . $this->getRedirectToItemAppend(), false));
		}

		return $result;
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean	 True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.6
	 */
	public function batch($model = null)
	{
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$model = $this->getModel('Item', '', array());

		// Preset the redirect
		$this->setRedirect(Route::url('index.php?option=com_menus&view=items' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   1.6
	 */
	public function cancel($key = null)
	{
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$context = 'com_menus.edit.item';
		$result = parent::cancel();

		if ($result)
		{
			// Clear the ancillary data from the session.
			User::setState($context . '.type', null);
			User::setState($context . '.link', null);
		}
	}

	/**
	 * Method to edit an existing record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key
	 * (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if access level check and checkout passes, false otherwise.
	 *
	 * @since   1.6
	 */
	public function edit($key = null, $urlVar = null)
	{
		// Initialise variables.
		$result = parent::edit();

		if ($result)
		{
			// Push the new ancillary data into the session.
			User::setState('com_menus.edit.item.type', null);
			User::setState('com_menus.edit.item.link', null);
		}

		return true;
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   1.6
	 */
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel('Item', '', array());
		$data = Request::getArray('jform', array(), 'post');
		$task = $this->getTask();
		$context = 'com_menus.edit.item';
		$recordId = Request::getInt('id');

		if (!$this->checkEditId($context, $recordId))
		{
			// Somehow the person just went to the form and saved it - we don't allow that.
			$this->setError(Lang::txt('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
			$this->setMessage($this->getError(), 'error');
			$this->setRedirect(Route::url('index.php?option=com_menus&view=items' . $this->getRedirectToListAppend(), false));

			return false;
		}

		// Populate the row id from the session.
		$data['id'] = $recordId;

		// The save2copy task needs to be handled slightly differently.
		if ($task == 'save2copy')
		{
			// Check-in the original row.
			if ($model->checkin($data['id']) === false)
			{
				// Check-in failed, go back to the item and display a notice.
				$this->setMessage(Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'warning');
				return false;
			}

			// Reset the ID and then treat the request as for Apply.
			$data['id'] = 0;
			$data['associations'] = array();
			$task = 'apply';
		}

		// Validate the posted data.
		// This post is made up of two forms, one for the item and one for params.
		$form = $model->getForm($data);

		if (!$form)
		{
			throw new Exception($model->getError(), 500);

			return false;
		}

		if ($data['type'] == 'url')
		{
			 $data['link'] = str_replace(array('"', '>', '<'), '', $data['link']);

			if (strstr($data['link'], ':') && substr($data['link'], 0, 1) != '/')
			{
				$segments = explode(':', $data['link']);
				$protocol = strtolower($segments[0]);
				$scheme = array('http', 'https', 'ftp', 'ftps', 'gopher', 'mailto', 'news', 'prospero', 'telnet', 'rlogin', 'tn3270', 'wais', 'url',
					'mid', 'cid', 'nntp', 'tel', 'urn', 'ldap', 'file', 'fax', 'modem', 'git');

				if (!in_array($protocol, $scheme))
				{
					$app->enqueueMessage(Lang::txt('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'), 'warning');
					$this->setRedirect(Route::url('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

					return false;
				}
			}
		}

		$data = $model->validate($form, $data);

		// Check for the special 'request' entry.
		if ($data['type'] == 'component' && isset($data['request']) && is_array($data['request']) && !empty($data['request']))
		{
			// Parse the submitted link arguments.
			$args = array();
			parse_str(parse_url($data['link'], PHP_URL_QUERY), $args);

			// Merge in the user supplied request arguments.
			$args = array_merge($args, $data['request']);
			$data['link'] = 'index.php?' . urldecode(http_build_query($args, '', '&'));
			unset($data['request']);
		}

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}

			// Save the data in the session.
			$app->setUserState('com_menus.edit.item.data', $data);

			// Redirect back to the edit screen.
			$this->setRedirect(Route::url('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

			return false;
		}

		// Attempt to save the data.
		if (!$model->save($data))
		{
			// Save the data in the session.
			$app->setUserState('com_menus.edit.item.data', $data);

			// Redirect back to the edit screen.
			$this->setMessage(Lang::txt('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(Route::url('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

			return false;
		}

		// Save succeeded, check-in the row.
		if ($model->checkin($data['id']) === false)
		{
			// Check-in failed, go back to the row and display a notice.
			$this->setMessage(Lang::txt('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()), 'warning');
			$this->setRedirect(Route::url('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));

			return false;
		}

		$this->setMessage(Lang::txt('COM_MENUS_SAVE_SUCCESS'));

		// Redirect the user and adjust session state based on the chosen task.
		switch ($task)
		{
			case 'apply':
				// Set the row data in the session.
				$recordId = $model->getState($this->context . '.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState('com_menus.edit.item.data', null);
				$app->setUserState('com_menus.edit.item.type', null);
				$app->setUserState('com_menus.edit.item.link', null);

				// Redirect back to the edit screen.
				$this->setRedirect(Route::url('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
				break;

			case 'save2new':
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_menus.edit.item.data', null);
				$app->setUserState('com_menus.edit.item.type', null);
				$app->setUserState('com_menus.edit.item.link', null);
				$app->setUserState('com_menus.edit.item.menutype', $model->getState('item.menutype'));

				// Redirect back to the edit screen.
				$this->setRedirect(Route::url('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend(), false));
				break;

			default:
				// Clear the row id and data in the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState('com_menus.edit.item.data', null);
				$app->setUserState('com_menus.edit.item.type', null);
				$app->setUserState('com_menus.edit.item.link', null);

				// Redirect to the list screen.
				$this->setRedirect(Route::url('index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend(), false));
				break;
		}
	}

	/**
	 * Sets the type of the menu item currently being edited.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	function setType()
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the posted values from the request.
		$data = Request::getArray('jform', array(), 'post');
		$recordId = Request::getInt('id');

		// Get the type.
		$type = $data['type'];

		$type = json_decode(base64_decode($type));
		$title = isset($type->title) ? $type->title : null;
		$recordId = isset($type->id) ? $type->id : 0;

		if ($title != 'alias' && $title != 'separator' && $title != 'url')
		{
			$title = 'component';
		}

		$app->setUserState('com_menus.edit.item.type', $title);
		if ($title == 'component')
		{
			if (isset($type->request))
			{
				$component = Component::load($type->request->option);
				$data['component_id'] = $component->id;

				$app->setUserState('com_menus.edit.item.link', 'index.php?' . JURI::buildQuery((array) $type->request));
			}
		}
		// If the type is alias you just need the item id from the menu item referenced.
		elseif ($title == 'alias')
		{
			$app->setUserState('com_menus.edit.item.link', 'index.php?Itemid=');
		}

		unset($data['request']);
		$data['type'] = $title;
		if (Request::getCmd('fieldtype') == 'type')
		{
			$data['link'] = $app->getUserState('com_menus.edit.item.link');
		}

		//Save the data in the session.
		$app->setUserState('com_menus.edit.item.data', $data);

		$this->type = $type;
		$this->setRedirect(Route::url('index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend($recordId), false));
	}
}
