<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

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
		return (JFactory::getUser()->authorise('core.admin', $this->option) && parent::allowSave($data, $key));
	}

	/**
	 * Method to remove a record.
	 */
	public function delete()
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(Lang::txt('JInvalid_Token'));

		// Initialise variables.
		$user	= JFactory::getUser();
		$ids	= Request::getVar('cid', array(), '', 'array');

		if (!JFactory::getUser()->authorise('core.admin', $this->option)) {
			App::abort(500, Lang::txt('JERROR_ALERTNOAUTHOR'));
			jexit();
		}
		elseif (empty($ids)) {
			JError::raiseWarning(500, Lang::txt('COM_USERS_NO_LEVELS_SELECTED'));
		}
		else {
			// Get the model.
			$model = $this->getModel();

			\Hubzero\Utility\Arr::toInteger($ids);

			// Remove the items.
			if (!$model->delete($ids)) {
				JError::raiseWarning(500, $model->getError());
			}
			else {
				$this->setMessage(Lang::txts('COM_USERS_N_LEVELS_DELETED', count($ids)));
			}
		}

		$this->setRedirect('index.php?option=com_users&view=levels');
	}
}
