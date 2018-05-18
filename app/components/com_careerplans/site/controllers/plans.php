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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Careerplans\Site\Controllers;

use Components\Careerplans\Models\Careerplan;
use Components\Careerplans\Models\Fieldset;
use Components\Careerplans\Models\Field;
use Hubzero\Component\SiteController;
use Request;
use Event;
use Route;
use Lang;
use User;
use App;

/**
 * Applications controller class for career plans
 */
class Plans extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->registerTask('new', 'edit');

		parent::execute();
	}

	/**
	 * Redirect to the login page
	 *
	 * @return  void
	 */
	public function loginTask()
	{
		$return = base64_encode(Route::url('index.php?option=' . $this->_option, false, true));

		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . $return, false),
			Lang::txt('COM_CAREERPLANS_LOGIN_NOTICE')
		);
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		if (!User::get('id'))
		{
			App::abort(500, Lang::txt('No user found.'));
		}

		// Get the user's data
		$plan = Careerplan::oneByUser(User::get('id'));

		$fieldsets = $plan->summary();

		// Output HTML
		$this->view
			->set('config', $this->config)
			->set('plan', $plan)
			->set('fieldsets', $fieldsets)
			->setLayout('display')
			->display();
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function nextTask()
	{
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		// Get the user's data
		$plan = Careerplan::oneByUser(User::get('id'));

		// Incoming
		$page = Request::getInt('page', 1);
		$page = $page ?: 1;

		// Get all available pages
		$fieldsets = Fieldset::all()
			->ordered()
			->rows();

		if (!count($fieldsets))
		{
			$fieldset = Fieldset::blank()->set(array(
				'label'    => Lang::txt('COM_CAREERPLANS_PAGE_ONE'),
				'name'     => 'page1',
				'ordering' => 1
			));

			$fieldsets = array($fieldset);
		}

		$active = null;
		foreach ($fieldsets as $fieldset)
		{
			if ($fieldset->get('ordering') == $page)
			{
				$active = $fieldset;
				break;
			}
		}

		// No active page?
		// Default to the first
		if (!$active)
		{
			$fieldsets->reset();
			$active = $fieldsets->first();
		}

		// Get the form fields for the active page
		$fields = $active->fields()
			->ordered()
			->rows();

		if (Request::method() == 'POST')
		{
			// Incoming profile edits
			$answers = Request::getVar('questions', array(), 'post', 'none', 2);

			$plan = $this->save($plan, null, $answers);

			if ($this->getError())
			{
				$page--;
				$page = $page < 1 ? 1 : $page;

				$active = null;
				foreach ($fieldsets as $fieldset)
				{
					if ($fieldset->get('ordering') == $page)
					{
						$active = $fieldset;
						break;
					}
				}

				$fields = $active->fields()
					->ordered()
					->rows();
			}
		}

		// Are we starting a new plan?
		if (!$plan->get('id'))
		{
			// Initialize
			$plan->save();

			// Log activity
			Event::trigger('system.logActivity', [
				'activity' => [
					'action'      => 'created',
					'scope'       => 'careerplan',
					'scope_id'    => $plan->get('id'),
					'description' => Lang::txt(
						'COM_CAREERPLANS_ACTIVITY_CREATED',
						'<a href="' . Route::url('index.php?option=' . $this->_option) . '">' . Lang::txt('COM_CAREERPLANS_PLAN') . '</a>'
					),
					'details'     => array(
						'url' => Route::url('index.php?option=' . $this->_option)
					)
				],
				'recipients' => [
					$plan->get('created_by')
				]
			]);
		}

		// Output HTML
		$this->view
			->set('config', $this->config)
			->set('fieldsets', $fieldsets)
			->set('page', $active)
			->set('fields', $fields)
			->set('plan', $plan)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Display an entry
	 *
	 * @return  void
	 */
	public function submitTask()
	{
		if (User::isGuest())
		{
			return $this->loginTask();
		}

		// Incoming
		$page = Request::getInt('active', 1);
		$page = $page ?: 1;

		$fieldset = Fieldset::oneByOrdering($page);

		if (!$fieldset->get('id'))
		{
			Notify::error(Lang::txt('COM_CAREERPLANS_ERROR_PAGE_NOT_FOUND'));

			App::redirect(Route::url($fieldset->link(), false));
		}

		// Get the user's data
		$plan = Careerplan::oneByUser(User::get('id'));

		// Incoming answers
		$answers = Request::getVar('questions', array(), 'post', 'none', 2);

		$plan = $this->save($plan, $fieldset, $answers);

		if ($this->getError())
		{
			$error = array();
			foreach ($this->getErrors() as $err)
			{
				if (is_array($err))
				{
					$error[] = implode('<br />', $err);
				}
				else
				{
					$error = $err;
				}
			}
			Notify::error(implode('<br />', $error));

			App::redirect(Route::url($fieldset->link(), false));
		}

		$fieldsets = $plan->summary();

		// Output HTML
		$this->view
			->set('config', $this->config)
			->set('plan', $plan)
			->set('fieldsets', $fieldsets)
			->setLayout('display')
			->display();
	}

	/**
	 * Display an entry
	 *
	 * @param   object  $plan
	 * @param   object  $fieldset
	 * @return  void
	 */
	protected function save($plan, $fieldset, $answers = array())
	{
		// The model will automatically set the modified timestamp
		$plan->save();

		if (!($fieldset instanceof Fieldset))
		{
			$fieldset = Fieldset::oneByOrdering(Request::getInt('active'));
		}

		if (!$fieldset || !$fieldset->get('id'))
		{
			$this->setError(Lang::txt('COM_CAREERPLANS_ERROR_PAGE_NOT_FOUND'));
			return $plan;
		}

		$fields = $fieldset->fields()
			->including(['options', function ($option){
				$option
					->select('*');
			}])
			->ordered()
			->rows();

		$field_ids = $fields->fieldsByKey('id');

		// Get any previous data for this set of questions
		$prev = $plan->answers()
			->whereIn('field_id', $field_ids)
			->ordered()
			->rows();

		$old = Careerplan::collect($prev);
		$answers = array_merge($old, $answers);

		// Compile data
		foreach ($answers as $key => $data)
		{
			if (isset($answers[$key]) && is_array($answers[$key]))
			{
				$answers[$key] = array_filter($answers[$key]);
			}

			// If there's an 'other' value
			// set the main field's value to it and remove the 'other' entry
			if (isset($answers[$key . '_other']) && trim($answers[$key . '_other']))
			{
				if (is_array($answers[$key]))
				{
					$answers[$key][] = $answers[$key . '_other'];
				}
				else
				{
					$answers[$key] = $answers[$key . '_other'];
				}

				unset($answers[$key . '_other']);
			}
		}

		\Hubzero\Form\Form::addFieldPath(dirname(dirname(__DIR__)) . '/models/fields');
		\Hubzero\Form\Form::addRulePath(dirname(dirname(__DIR__)) . '/models/rules');

		// Validate profile data
		$form = new \Hubzero\Form\Form('careerplan', array('control' => 'questions'));
		$form->load(Field::toXml($fields, $answers));
		$form->bind(new \Hubzero\Config\Registry($answers));

		$errors = array(
			'_missing' => array(),
			'_invalid' => array()
		);

		if (!$form->validate($answers))
		{
			foreach ($form->getErrors() as $key => $error)
			{
				if ($error instanceof \Hubzero\Form\Exception\MissingData)
				{
					$errors['_missing'][$key] = (string)$error;
				}

				$errors['_invalid'][$key] = (string)$error;

				//$this->setError((string)$error);
				$this->setError($errors);
			}
		}

		if ($this->getError())
		{
			return $plan;
		}

		// Save profile data
		if (!$plan->saveAnswers($answers, $field_ids))
		{
			$this->setError($plan->getError());
		}

		return $plan;
	}
}
