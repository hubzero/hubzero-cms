<?php
/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
defined( '_JEXEC' ) or die;

/**
 * Messages Component Message Model
 *
 * @package		Joomla.Administrator
 * @subpackage	com_messages
 * @since		1.6
 */
class MessagesControllerConfig extends JControllerLegacy
{
	/**
	 * Method to save a record.
	 */
	public function save()
	{
		// Check for request forgeries.
		Session::checkToken() or exit(Lang::txt('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = JFactory::getApplication();
		$model = $this->getModel('Config', 'MessagesModel');
		$data  = Request::getVar('jform', array(), 'post', 'array');

		// Validate the posted data.
		$form = $model->getForm();
		if (!$form)
		{
			throw new Exception($model->getError(), 500);
		}
		$data = $model->validate($form, $data);

		// Check for validation errors.
		if ($data === false)
		{
			// Get the validation messages.
			$errors	= $model->getErrors();

			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception)
				{
					Notify::warning($errors[$i]->getMessage());
				}
				else
				{
					Notify::warning($errors[$i]);
				}
			}

			// Redirect back to the main list.
			$this->setRedirect(Route::url('index.php?option=com_messages&view=messages', false));
			return false;
		}

		// Attempt to save the data.
		if (!$model->save($data))
		{
			// Redirect back to the main list.
			$this->setMessage(Lang::txt('JERROR_SAVE_FAILED', $model->getError()), 'warning');
			$this->setRedirect(Route::url('index.php?option=com_messages&view=messages', false));
			return false;
		}

		// Redirect to the list screen.
		$this->setMessage(Lang::txt('COM_MESSAGES_CONFIG_SAVED'));
		$this->setRedirect(Route::url('index.php?option=com_messages&view=messages', false));

		return true;
	}
}
