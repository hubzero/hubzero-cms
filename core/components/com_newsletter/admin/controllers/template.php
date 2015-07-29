<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Newsletter\Admin\Controllers;

use Components\Newsletter\Tables\Template as NewsletterTemplate;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Route;
use Lang;
use App;

/**
 * Newsletter stories Controller
 */
class Template extends AdminController
{
	/**
	 * Display Newsletter Templates Task
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		//get the templates
		$newsletterTemplate = new NewsletterTemplate($this->database);
		$this->view->templates = $newsletterTemplate->getTemplates();

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->setLayout('display')->display();
	}

	/**
	 * Add Newsletter Template Task
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit Newsletter Template Task
	 *
	 * @return 	void
	 */
	public function editTask()
	{
		//default object
		$this->view->template = new stdClass;
		$this->view->template->id = null;
		$this->view->template->editable = null;
		$this->view->template->name = null;
		$this->view->template->primary_title_color = null;
		$this->view->template->primary_text_color = null;
		$this->view->template->secondary_title_color = null;
		$this->view->template->secondary_text_color = null;
		$this->view->template->template = null;

		//get request vars
		$id = Request::getVar('id', array(0));
		if (is_array($id))
		{
			$id = (isset($id[0]) ? $id[0] : null);
		}

		//are we editing or adding a new tempalte
		if ($id)
		{
			$newsletterTemplate = new NewsletterTemplate($this->database);
			$this->view->template = $newsletterTemplate->getTemplates($id);
		}

		//check to see if tempalte is editable
		if ($this->view->template->editable == 0 && $this->view->template->editable != null)
		{
			$this->setError(Lang::txt('COM_NEWSLETTER_TEMPLATE_NOT_EDITABLE'));
			$this->displayTask();
			return;
		}

		//make sure were not passing in a template from save or duplicate
		if ($this->template)
		{
			$this->view->template = $this->template;
		}

		//set errors if we have any
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		//set vars for view
		$this->view->config = $this->config;

		// Output the HTML
		$this->view->setLayout('edit')->display();
	}

	/**
	 * Save Newsletter Template Task
	 *
	 * @return 	void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		//get request vars
		$template = Request::getVar('template', array(), 'post', 'ARRAY', JREQUEST_ALLOWHTML);

		//instantiate newsletter template object
		$newsletterTemplate = new NewsletterTemplate($this->database);

		//save the story
		if (!$newsletterTemplate->save($template))
		{
			//send back template object
			$this->template = $newsletterTemplate;

			$this->setError($newsletterTemplate->getError());
			$this->editTask();
			return;
		}

		//inform user of successful save and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_NEWSLETTER_TEMPLATE_SAVED_SUCCESS')
		);
	}

	/**
	 * Delete Task
	 *
	 * @return 	void
	 */
	public function deleteTask()
	{
		//get the request vars
		$ids = Request::getVar("id", array());

		//make sure we have ids
		if (isset($ids) && count($ids) > 0)
		{
			//delete each newsletter
			foreach ($ids as $id)
			{
				//instantiate template object
				$newsletterTemplate = new NewsletterTemplate($this->database);
				$newsletterTemplate->load($id);

				//check to make sure this isnt our default templates
				if ($newsletterTemplate->editable == 0)
				{
					$this->setError(Lang::txt('COM_NEWSLETTER_TEMPLATE_DELETE_FAILED'));
					$this->displayTask();
					return;
				}

				//mark as deleted
				$newsletterTemplate->deleted = 1;

				//save template marking as deleted
				if (!$newsletterTemplate->save($newsletterTemplate))
				{
					$this->setError(Lang::txt('COM_NEWSLETTER_TEMPLATE_DELETE_FAILED'));
					$this->displayTask();
					return;
				}
			}
		}

		//redirect back to campaigns list
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_NEWSLETTER_TEMPLATE_DELETE_SUCCESS')
		);
	}

	/**
	 * Duplicate Task
	 *
	 * @return 	void
	 */
	public function duplicateTask()
	{
		//get request vars
		$ids = Request::getVar('id', array());
		$id = (isset($ids[0])) ? $ids[0] : null;

		//are we editing or adding a new tempalte
		if ($id)
		{
			//get template we want to duplicate
			$newsletterTemplate = new NewsletterTemplate($this->database);
			$template = $newsletterTemplate->getTemplates($id);

			//set var so edit task can use
			$new_template = new stdClass;
			$new_template->id = null;
			$new_template->name = $template->name . ' (copy)';
			$new_template->editable = 1;
			$new_template->primary_title_color = $template->primary_title_color;
			$new_template->primary_text_color = $template->primary_text_color;
			$new_template->secondary_title_color = $template->secondary_title_color;
			$new_template->secondary_text_color = $template->secondary_text_color;
			$new_template->template = $template->template;
		}

		//save copied template
		$newsletterTemplate = new NewsletterTemplate($this->database);
		if (!$newsletterTemplate->save($new_template))
		{
			$this->setError(Lang::txt('COM_NEWSLETTER_TEMPLATE_DUPLICATE_FAILED'));
			$this->displayTask();
			return;
		}

		//set success message & redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_NEWSLETTER_TEMPLATE_DUPLICATE_SUCCESS')
		);
	}
}