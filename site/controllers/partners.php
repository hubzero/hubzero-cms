<?php
// Declare the namespace.
namespace Components\Partners\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Partners\Models\Partner;
use Components\Partners\Models\Partner_type;

use Request;
use Notify;
use Event;
use Lang;
use User;
use App;

/**
 * Partners controller for showing partners
 * 
 * Accepts an array of configuration values to the constructor. If no config 
 * passed, it will automatically determine the component and controller names.
 * Internally, sets the $database, $user, $view, and component $config.
 * 
 * Executable tasks are determined by method name. All public methods that end in 
 * "Task" (e.g., displayTask, editTask) are callable by the end user.
 * 
 * View name defaults to controller name with layout defaulting to task name. So,
 * a $controller of "One" and a $task of "two" will map to:
 *
 * /{component name}
 *     /{client name}
 *         /views
 *             /one
 *                 /tmpl
 *                     /two.php
 */
class Partners extends SiteController
{
	/**
	 * Determine task to perform and execute it.
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Here we're aliasing the task 'add' to 'edit'. When examing
		// this controller, you should not find any method called 'addTask'.
		// Instead, we're telling the controller to execute the 'edit' task
		// whenever a task of 'add' is called.
		$this->registerTask('add', 'edit');

		// Call the parent execute() method. Important! Otherwise, the
		// controller will never actually execute anything.
		parent::execute();
	}

	/**
	 * Default task. Displays a list of partners.
	 *
	 * @return	void
	 */
	public function displayTask()
	{
		// Get our model
		// This is the entry point to the database and the 
		// table of characters we'll be retrieving data from
		$this->view->model = new Partner();

		// NOTE:
		// A \Hubzero\Component\View object is auto-created when calling
		// execute() on the controller. By default, the view directory is 
		// set to the controller name and layout is set to task name.
		//
		// controller=foo&task=bar   loads a view from:
		//
		//   view/
		//     foo/
		//       tmpl/
		//         bar.php
		//
		// A new layout or name can be chosen by calling setLayout('newlayout')
		// or setName('newname') respectively.

		// Incoming filters:
		// Assumes URL has 'partner_type=#' - see views/partners/tmpl/display.php (in aside, value is set to '#')
		$this->view->filters = array(
			'partner_type' => Request::getInt('partner_type', 0)
		);

		$records = Partner::all();

		// If a partner_type's ID was passed in the URL, we load that partner_type and
		// retrieve the partners associated with only that partner_type.
		if ($partner_type = $this->view->filters['partner_type'])
		{
			// oneOrFail works on $id of object
			$records = Partner_type::oneOrFail($partner_type)->partners();
		}

		// Get a list of records
		$this->view->records = $records->paginated()->ordered();

		// Output the view
		// 
		// Make sure we load the correct view. This is for cases where 
		// we may be redirected from editTask(), which can happen if the
		// user is not logged in.
		$this->view
		     ->setLayout('display')
		     ->display();
	}

	/**
	 * Default task. Displays a list of partners in card form.
	 *
	 * @return	void
	 */
	public function cardsTask()
	{
		// Get our model
		// This is the entry point to the database and the 
		// table of characters we'll be retrieving data from
		$this->view->model = new Partner();

		// NOTE:
		// A \Hubzero\Component\View object is auto-created when calling
		// execute() on the controller. By default, the view directory is 
		// set to the controller name and layout is set to task name.
		//
		// controller=foo&task=bar   loads a view from:
		//
		//   view/
		//     foo/
		//       tmpl/
		//         bar.php
		//
		// A new layout or name can be chosen by calling setLayout('newlayout')
		// or setName('newname') respectively.

		// Incoming filters:
		// Assumes URL has 'partner_type=#' - see views/partners/tmpl/display.php (in aside, value is set to '#')
		$this->view->filters = array(
			'partner_type' => Request::getInt('partner_type', 0)
		);

		$records = Partner::all();

		// If a partner_type's ID was passed in the URL, we load that partner_type and
		// retrieve the partners associated with only that partner_type.
		if ($partner_type = $this->view->filters['partner_type'])
		{
			// oneOrFail works on $id of object
			$records = Partner_type::oneOrFail($partner_type)->partners();
		}

		// Get a list of records
		$this->view->records = $records->paginated()->ordered();

		// Output the view
		// 
		// Make sure we load the correct view. This is for cases where 
		// we may be redirected from editTask(), which can happen if the
		// user is not logged in.
		$this->view
		     ->setLayout('cards')
		     ->display();
	}
}