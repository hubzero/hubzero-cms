<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Storefront\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Storefront\Models\Archive;

/**
 * Controller class for knowledge base categories
 */
class Meta extends AdminController
{
	/**
	 * Display a list of all categories
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get filters
		$this->view->filters = array(
			'access' => -1
		);
		$this->view->filters['sort'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sort',
			'filter_order',
			'title'
		));
		$this->view->filters['sort_Dir'] = trim($app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.sortdir',
			'filter_order_Dir',
			'ASC'
		));

		// Get paging variables
		$this->view->filters['limit'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limit',
			'limit',
			Config::get('list_limit'),
			'int'
		);
		$this->view->filters['start'] = $app->getUserStateFromRequest(
			$this->_option . '.' . $this->_controller . '.limitstart',
			'limitstart',
			0,
			'int'
		);

		$obj = new Archive();

		// Get record count
		$this->view->total = $obj->products('count', $this->view->filters);

		// Get records
		$this->view->rows  = $obj->products('list', $this->view->filters);

		// Set any errors
		if ($this->getError())
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Create a new category
	 *
	 * @return  void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Edit a category
	 *
	 * @return  void
	 */
	public function editTask($row=null)
	{
		Request::setVar('hidemainmenu', 1);

		$obj = new Archive();

		if (is_object($row))
		{
			$this->view->row = $row;
			$this->view->task = 'edit';

			$id = $row->getId();
		}
		else
		{
			// Incoming
			$id = Request::getArray('id', 0);

			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load product
			$this->view->row = $obj->product($id);
		}

		// Load meta
		$this->view->meta = $this->view->row->getMeta();

		// Set any errors
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		// See what layout to load
		if ($this->view->row->getTypeInfo()->name == 'Software Download')
		{
			// Software
			$layout = 'edit-software';
		}
		else
		{
			App::abort(404, Lang::txt('No meta for this product'));
		}

		// Output the HTML
		$this->view
			->setLayout($layout)
			->display();
	}

	/**
	 * Save a category and come back to the edit form
	 *
	 * @return  void
	 */
	public function applyTask()
	{
		$this->saveTask(false);
	}

	/**
	 * Save a product
	 *
	 * @param   boolean  $redirect  Redirect the page after saving
	 * @return  void
	 */
	public function saveTask($redirect=true)
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$fields = Request::getArray('fields', array(), 'post');

		$obj = new Archive();
		// Get the product
		$product = $obj->product($fields['pId']);

		$meta = $fields;
		unset($meta['pId']);

		// Save product meta
		$product->setMeta($meta);

		if ($redirect)
		{
			// Redirect
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=products&task=edit&id=' . Request::getInt('id', 0)),
				Lang::txt('COM_STOREFRONT_PRODUCT_SAVED')
			);
			return;
		}
		Notify::success(Lang::txt('COM_STOREFRONT_PRODUCT_SAVED'));

		$this->editTask($product);
	}

	/**
	 * Cancel a task (redirects to default task)
	 *
	 * @return     void
	 */
	public function cancelTask()
	{
		// Set the redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=products&task=edit&id=' . Request::getInt('id', 0))
		);
	}
}
