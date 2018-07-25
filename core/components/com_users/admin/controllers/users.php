<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

jimport('joomla.application.component.controlleradmin');

/**
 * Users list controller class.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_users
 * @since       1.6
 */
class UsersControllerUsers extends JControllerAdmin
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_USERS_USERS';

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @return  UsersControllerUsers
	 *
	 * @since   1.6
	 * @see     JController
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		$this->registerTask('block', 'changeBlock');
		$this->registerTask('unblock', 'changeBlock');
	}

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  object  The model.
	 *
	 * @since	1.6
	 */
	public function getModel($name = 'User', $prefix = 'UsersModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	/**
	 * Method to change the block status on a record.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function changeBlock()
	{
		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$ids    = Request::getArray('cid', array());
		$values = array('block' => 1, 'unblock' => 0);
		$task   = $this->getTask();
		$value  = \Hubzero\Utility\Arr::getValue($values, $task, 0, 'int');

		if (empty($ids))
		{
			throw new Exception(Lang::txt('COM_USERS_USERS_NO_ITEM_SELECTED'), 500);
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Change the state of the records.
			if (!$model->block($ids, $value))
			{
				throw new Exception($model->getError(), 500);
			}
			else
			{
				if ($value == 1)
				{
					$this->setMessage(Lang::txts('COM_USERS_N_USERS_BLOCKED', count($ids)));
				}
				elseif ($value == 0)
				{
					$this->setMessage(Lang::txts('COM_USERS_N_USERS_UNBLOCKED', count($ids)));
				}
			}
		}

		$this->setRedirect('index.php?option=com_users&view=users');
	}

	/**
	 * Method to activate a record.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	public function activate()
	{
		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$ids = Request::getArray('cid', array());

		if (empty($ids))
		{
			throw new Exception(Lang::txt('COM_USERS_USERS_NO_ITEM_SELECTED'), 500);
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Change the state of the records.
			if (!$model->activate($ids))
			{
				throw new Exception($model->getError(), 500);
			}
			else
			{
				$this->setMessage(Lang::txts('COM_USERS_N_USERS_ACTIVATED', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_users&view=users');
	}

	/**
	 * Method to approve users
	 *
	 * @return  void
	 */
	public function approve()
	{
		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$ids = Request::getArray('cid', array());

		if (empty($ids))
		{
			throw new Exception(Lang::txt('COM_USERS_USERS_NO_ITEM_SELECTED'), 500);
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Change the state of the records.
			if (!$model->approve($ids))
			{
				throw new Exception($model->getError(), 500);
			}
			else
			{
				$this->setMessage(Lang::txts('COM_USERS_N_USERS_APPROVED', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_users&view=users');
	}
}
