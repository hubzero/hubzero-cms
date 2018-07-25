<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

jimport('joomla.application.component.controllerform');

/**
 * User view level controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @since		1.6
 */
class UsersControllerLevel extends JControllerForm
{
	/**
	 * @var		string	The prefix to use with controller messages.
	 * @since	1.6
	 */
	protected $text_prefix = 'COM_USERS_LEVEL';

	/**
	 * Method to check if you can save a new or existing record.
	 *
	 * Overrides JControllerForm::allowSave to check the core.admin permission.
	 *
	 * @param	array	An array of input data.
	 * @param	string	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	protected function allowSave($data, $key = 'id')
	{
		return (User::authorise('core.admin', $this->option) && parent::allowSave($data, $key));
	}

	/**
	 * Method to remove a record.
	 */
	public function delete()
	{
		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JInvalid_Token'));

		// Initialise variables.
		$ids = Request::getArray('cid', array());

		if (!User::authorise('core.admin', $this->option))
		{
			throw new Exception(Lang::txt('JERROR_ALERTNOAUTHOR'), 403);
		}
		elseif (empty($ids))
		{
			throw new Exception(Lang::txt('COM_USERS_NO_LEVELS_SELECTED'), 500);
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			\Hubzero\Utility\Arr::toInteger($ids);

			// Remove the items.
			if (!$model->delete($ids))
			{
				throw new Exception($model->getError(), 500);
			}
			else
			{
				$this->setMessage(Lang::txts('COM_USERS_N_LEVELS_DELETED', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_users&view=levels');
	}
}
