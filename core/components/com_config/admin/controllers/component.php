<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Config\Admin\Controllers;

use Components\Config\Models;
use Hubzero\Component\AdminController;
use Exception;
use Request;
use Notify;
use Lang;
use User;
use App;
use Route;

include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'component.php';

/**
 * Controller class for a component's config
 */
class Component extends AdminController
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
	 * Displa the configuration.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$model = new Models\Component();

		// Access check.
		if (!User::authorise('core.admin', $model->get('component.option')))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		$form = $model->getForm();
		$component = $model->getComponent();

		// Check for errors.
		if (count($errors = $this->getErrors()))
		{
			App::abort(500, implode("\n", $errors));
		}

		// Bind the form to the data.
		if ($form && $component->params)
		{
			$form->bind($component->params->toArray());
		}

		// Get the document object.
		App::get('document')->setTitle(Lang::txt('JGLOBAL_EDIT_PREFERENCES'));

		Request::setVar('hidemainmenu', true);

		$this->view
			->set('model', $model)
			->set('form', $form)
			->set('component', $component)
			->setLayout('default')
			->display();
	}

	/**
	 * Save the configuration
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries.
		Request::checkToken();

		// Initialise variables.
		$model  = new Models\Component();
		$form   = $model->getForm();
		$data   = Request::getArray('hzform', array(), 'post');
		$id     = Request::getInt('id');
		$option = Request::getCmd('component');

		// Check if the user is authorized to do this.
		if (!User::authorise('core.admin', $option))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// The row the params are written to is named by the posted id, and the
		// check above is on the posted component; they have to be the same
		// extension, or core.admin on one component rewrites another's config.
		$extension = Models\Extension::oneOrNew($id);
		if ($extension->isNew()
		 || $extension->get('type') != 'component'
		 || $extension->get('element') != $option)
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		// No config.xml means no form to validate against
		if (!$form)
		{
			App::abort(500, $model->getError() ?: Lang::txt('JERROR_LOADFILE_FAILED'));
		}

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
				Route::url('index.php?option=' . $this->_option . '&view=component&component=' . $option . '&tmpl=component&path=' . $model->get('component.path'), false)
			);
			return false;
		}

		// A usergroup option (com_members' new_usertype, the group every
		// registration and every admin-created user is put in) must not name a
		// group that carries core.admin unless the saver is a super admin. The
		// field lists every group, and core.admin on a component is not core.admin
		// on the site: the stock Administrator group has the first, not the second.
		if (!User::authorise('core.admin'))
		{
			foreach ($form->getFieldset() as $field)
			{
				if (strtolower($field->type) != 'usergroup' || !isset($return[$field->fieldname]))
				{
					continue;
				}

				foreach ((array) $return[$field->fieldname] as $gid)
				{
					if (\Hubzero\Access\Access::checkGroup((int) $gid, 'core.admin'))
					{
						Notify::error(Lang::txt('JLIB_USER_ERROR_NOT_SUPERADMIN'));

						User::setState($this->_option . '.config.global.data', $data);

						App::redirect(
							Route::url('index.php?option=' . $this->_option . '&view=component&component=' . $option . '&tmpl=component&path=' . $model->get('component.path'), false)
						);
						return false;
					}
				}
			}
		}

		// Attempt to save the configuration.
		$data = array(
			'params' => $return,
			'id'     => $id,
			'option' => $option
		);
		$return = $model->save($data);

		// Check the return value.
		if ($return === false)
		{
			// Save the data in the session.
			User::setState($this->_option . '.config.global.data', $data);

			// Save failed, go back to the screen and display a notice.
			Notify::error(Lang::txt('JERROR_SAVE_FAILED', $model->getError()));

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&view=component&component=' . $option . '&tmpl=component&path=' . $model->get('component.path'), false)
			);
			return false;
		}

		User::setState($this->_option . '.config.global.data', null);

		// Set the redirect based on the task.
		switch (Request::getCmd('task'))
		{
			case 'apply':
				Notify::success(Lang::txt('COM_CONFIG_SAVE_SUCCESS'));

				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&view=component&component=' . $option . '&tmpl=component&path=' . $model->get('component.path') . '&refresh=1', false)
				);
				break;

			case 'save':
			default:
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&view=close&tmpl=component&path=' . $model->get('component.path'), false)
				);
				break;
		}
	}
}
