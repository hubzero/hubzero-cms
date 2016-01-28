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

namespace Components\Config\Controllers;

use Components\Config\Models;
use Hubzero\Component\AdminController;
use Exception;
use Request;
use Notify;
use User;
use App;

include_once(dirname(__DIR__) . DS . 'models' . DS . 'component.php');

/**
 * Controller class for a component's config
 */
class Component extends AdminController
{
	/**
	 * Class Constructor
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 * @return  void
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Map the apply task to the save method.
		$this->registerTask('apply', 'save');
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
		if (!User::authorise('core.admin', $model->getState('component.option')))
		{
			App::abort(404, \Lang::txt('JERROR_ALERTNOAUTHOR'));
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
		\Session::checkToken();

		// Set FTP credentials, if given.
		\JClientHelper::setCredentialsFromRequest('ftp');

		// Initialise variables.
		$model  = new Models\Component();
		$form   = $model->getForm();
		$data   = Request::getVar('jform', array(), 'post', 'array');
		$id     = Request::getInt('id');
		$option = Request::getCmd('component');

		// Check if the user is authorized to do this.
		if (!User::authorise('core.admin', $option))
		{
			App::redirect('index.php', \Lang::txt('JERROR_ALERTNOAUTHOR'));
			return;
		}

		// Validate the posted data.
		$return = $model->validate($form, $data);

		// Check for validation errors.
		if ($return === false)
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

			// Save the data in the session.
			User::setState($this->_option . '.config.global.data', $data);

			// Redirect back to the edit screen.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&view=component&component=' . $option . '&tmpl=component&path=' . $model->getState('component.path'), false)
			);
			return false;
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
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&view=component&component=' . $option . '&tmpl=component&path=' . $model->getState('component.path'), false),
				Lang::txt('JERROR_SAVE_FAILED', $model->getError()),
				'error'
			);
			return false;
		}

		// Set the redirect based on the task.
		switch (Request::getCmd('task'))
		{
			case 'apply':
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&view=component&component=' . $option . '&tmpl=component&path=' . $model->getState('component.path') . '&refresh=1', false),
					Lang::txt('COM_CONFIG_SAVE_SUCCESS')
				);
				break;

			case 'save':
			default:
				App::redirect(
					Route::url('index.php?option=' . $this->_option . '&view=close&tmpl=component&path=' . $model->getState('component.path'), false)
				);
				break;
		}
	}
}
