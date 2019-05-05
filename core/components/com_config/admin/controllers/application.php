<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Config\Admin\Controllers;

use Components\Config\Models;
use Hubzero\Component\AdminController;
use Hubzero\Error;
use Exception;
use Component;
use Notify;
use Route;
use User;
use App;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'application.php';

/**
 * Controller class for the Application config
 */
class Application extends AdminController
{
	/**
	 * Execute a task
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Map the apply task to the save method.
		$this->registerTask('apply', 'save');

		parent::execute();
	}

	/**
	 * Method to save the configuration.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Access check.
		if (!User::authorise('core.admin', $this->_option))
		{
			App::abort(404, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$model = new Models\Application();

		$form = $model->getForm();
		$data = $model->getData();

		// Check for model errors.
		if ($errors = $model->getErrors())
		{
			App::abort(500, implode('<br />', $errors));
		}

		// Bind the form to the data.
		if ($form && $data)
		{
			$form->bind($data);
		}

		// Get the params for com_users.
		$usersParams = Component::params('com_users');

		// Get the params for com_media.
		$mediaParams = Component::params('com_media');

		$this->view
			->set('model', $model)
			->set('form', $form)
			->set('data', $data)
			->set('usersParams', $usersParams)
			->set('mediaParams', $mediaParams)
			->setLayout('default')
			->display();
	}

	/**
	 * Method to save the configuration.
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries.
		Request::checkToken();

		// Check if the user is authorized to do this.
		if (!User::authorise('core.admin'))
		{
			App::redirect(
				Route::url('index.php', false),
				Lang::txt('JERROR_ALERTNOAUTHOR')
			);
			return;
		}

		// Initialise variables.
		$model = new Models\Application();
		$form  = $model->getForm();
		$data  = Request::getArray('hzform', array(), 'post');

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();

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

			// Save the data in the session.
			User::setState($this->_option . '.config.global.data', $data);

			// Redirect back to the edit screen.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&view=application', false)
			);
			return false;
		}

		// 'other' is passed as a separate array because form validation
		// above will strip it out,
		$other = Request::getArray('hzother', array(), 'post');
		$data  = array_merge($return, $other);

		// Attempt to save the configuration.
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			User::setState($this->_option . '.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&view=application', false),
				Lang::txt('JERROR_SAVE_FAILED', $model->getError()),
				'error'
			);
			return false;
		}

		// Clean the session data.
		User::setState('com_config.config.global.data', null);

		// Set the success message.
		Notify::success(Lang::txt('COM_CONFIG_SAVE_SUCCESS'));

		// Set the redirect based on the task.
		switch (Request::getCmd('task'))
		{
			case 'apply':
				App::redirect(Route::url('index.php?option=' . $this->_option, false));
				break;

			case 'save':
			default:
				App::redirect(Route::url('index.php', false));
				break;
		}
	}

	/**
	 * Cancel operation
	 *
	 * @return  void
	 */
	public function cancelTask()
	{
		// Check if the user is authorized to do this.
		if (!User::authorise('core.admin', $this->_option))
		{
			App::redirect(
				Route::url('index.php', false),
				Lang::txt('JERROR_ALERTNOAUTHOR')
			);
			return;
		}

		// Clean the session data.
		User::setState('com_config.config.global.data', null);

		App::redirect(Route::url('index.php', false));
	}

	/**
	 * Refresh the help
	 *
	 * @return  void
	 */
	public function refreshHelp()
	{
		if (($data = file_get_contents('http://help.hubzero.org/helpsites.xml')) === false)
		{
			App::redirect(Route::url('index.php?option=com_config', false), Lang::txt('COM_CONFIG_ERROR_HELPREFRESH_FETCH'), 'error');
		}
		elseif (!\Filesystem::write(PATH_APP . '/help/helpsites.xml', $data))
		{
			App::redirect(Route::url('index.php?option=com_config', false), Lang::txt('COM_CONFIG_ERROR_HELPREFRESH_ERROR_STORE'), 'error');
		}
		else
		{
			App::redirect(Route::url('index.php?option=com_config', false), Lang::txt('COM_CONFIG_HELPREFRESH_SUCCESS'));
		}
	}

	/**
	 * Method to remove the root property from the configuration.
	 *
	 * @return  bool  True on success, false on failure.
	 */
	public function removerootTask()
	{
		// Check for request forgeries.
		Request::checkToken(['get']);

		// Check if the user is authorized to do this.
		if (!User::authorise('core.admin'))
		{
			App::redirect(
				Route::url('index.php', false),
				Lang::txt('JERROR_ALERTNOAUTHOR')
			);
			return;
		}

		// Initialise model.
		$model = new Models\Application();

		// Attempt to save the configuration and remove root.
		$return = $model->removeroot();

		// Check the return value.
		if ($return === false)
		{
			// Save failed, go back to the screen and display a notice.
			Notify::error(Lang::txt('JERROR_SAVE_FAILED', $model->getError()));
		}
		else
		{
			Notify::success(Lang::txt('COM_CONFIG_SAVE_SUCCESS'));
		}

		// Set the redirect based on the task.
		App::redirect(
			Route::url('index.php', false)
		);
	}
}
