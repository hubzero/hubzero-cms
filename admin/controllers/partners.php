<?php
namespace Components\Partners\Admin\Controllers;
use Hubzero\Component\AdminController;
use JRequest;
use Config;
use Notify;
use Route;
use User;
use Lang;
use Date;
use App;
use Components\Partners\Models\Partner;
use Components\Partners\Models\Partner_type;
use Hubzero\User\Group;

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
class Partners extends AdminController
{
	/**
	 * Execute a task
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
		$this->registerTask('apply', 'save');
		$this->registerTask('publish', 'state');
		$this->registerTask('unpublish', 'state');

		// Call the parent execute() method. Important! Otherwise, the
		// controller will never actually execute anything.
		parent::execute();
	}

	/**
	 * Display a list of entries
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Get some incoming filters to apply to the entries list
		//
		// The Request::getState() method makes it easy to retain values of 
		// certain variables across page accesses. This makes development much 
		// simpler because we no longer has to worry about losing variable values 
		// if it is left out of a form. The best example is that the form will
		// retain the proper filters even after navigating to the edit entry form
		// and back.
		$this->view->filters = array(
			'search' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			)),
			'state' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.state',
				'state',
				-1
			)),

			'partner_type' => urldecode(Request::getState(
				$this->_option . '.' . $this->_controller . '.partner_type',
				'partner_type',
				0
			)),
			// Get sorting variables
			//
			// "sort" is the name of the table column to sort by
			// "sort_Dir" is the direction to sort by [ASC, DESC]
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'name'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		// Get our model
		// This is the entry point to the database and the 
		// table of partners we'll be retrieving data from
		
		$record = Partner::all();

		$this->view->partner_types = Partner_type::all()->ordered();	
		if ($this->view->filters['state'] >= 0)
		{
			$record->whereEquals('state', $this->view->filters['state']);
		}

		if ($search = $this->view->filters['search'])
		{
			$record->whereLike('name', $search);
		}

		$this->view->rows = $record->ordered('filter_order', 'filter_order_Dir')->paginated();

		// Output the view
		$this->view->display();
	}

	/**
	 * Show a form for editing an entry
	 *
	 * @param   object  $row
	 * @return  void
	 */
	public function editTask($row=null)
	{
		// This is a flag to disable the main menu. This makes sure the user
		// doesn't navigate away while int he middle of editing an entry.
		// To leave the form, one must explicitely call the "cancel" task.
		Request::setVar('hidemainmenu', 1);

		// If we're being passed an object, use it instead
		// This means we came from saveTask() and some error occurred.
		// Most likely a missing or incorrect field.
		if (!is_object($row))
		{
			// Grab the incoming ID and load the record for editing
			//
			// IDs can come arrive in two formts: single integer or 
			// an array of integers. If it's the latter, we'll only take 
			// the first ID in the list.
			$id = Request::getVar('id', array(0));
			if (is_array($id) && !empty($id))
			{
				$id = $id[0];
			}

			// Load the article
			$row = Partner::oneOrNew($id);
		}

		$grouprows = \Hubzero\User\Group::find(array(
			'type'       => array('all'),
			'limit'      => 'all',
			'search'     => '',
			'fields'     => array('cn', 'description', 'published', 'gidNumber', 'type'),
			'sortby'     => 'title',
			'authorized' => 'admin'
		));
		
		
		$member_names = $this->userSelect('QUBES_liason', 0, 0);
		//pass rows
		$this->view->row = $row;
		$this->view->partner_types = Partner_type::all()->ordered();

		// Output the view
		// 
		// Make sure we load the edit view.
		// This is for cases where saveTask() might encounter a data
		// validation error and fall through to editTask(). Since layout 
		// is auto-assigned the task name, the layout will be 'save' but
		// saveTask has no layout!
		$this->view
			->set('member_names',$member_names)
			->set('grouprows', $grouprows)
			->setLayout('edit')
			->display();
	}



		/**
	 * Builds a select list of users
	 *
	 * @param      string  $name       Name of the select element
	 * @param      string  $active     Selected value
	 * @param      integer $nouser     Display an empty start option
	 * @param      string  $javascript Any JS to attach to the select element
	 * @param      string  $order      Field to order the users by
	 * @return     string
	 */
	private function userSelect($name, $active, $nouser=0, $javascript=NULL, $order='a.name')
	{
		$database = \App::get('db');

		$group_id = 'g.id';
		$aro_id = 'aro.id';

		$query = "SELECT a.id AS value, a.name AS text, g.title AS groupname"
			. "\n FROM #__users AS a"
			. "\n INNER JOIN #__user_usergroup_map AS gm ON gm.user_id = a.id"	// map aro to group
			. "\n INNER JOIN #__usergroups AS g ON g.id = gm.group_id"
			. "\n WHERE a.block = '0' AND g.title='Registered'" //options are = Public, Registered, Author, Editor, Publisher, Manager, Administrator, Super Users
			. "\n ORDER BY ". $order;

		$database->setQuery($query);
		$result = $database->loadObjectList();

		if (!$result)
		{
			$result = array();
		}

		return $result;
	}



	/**
	 * Save changes to an entry
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// [SECURITY] Check for request forgeries
		//
		// We're currently only checking POST, so if someone tries
		// to access this task via a querystring (... &task=delete&id[]=1)
		// it will be denied. This helps ensure the deletion process is
		// *only* coming in through the submitted edit form.
		Request::checkToken();




		// Incoming
		$fields = Request::getVar('fields', array(), 'post', 'none', 2);


		// Initiate model and bind the incoming data to it
		$row = Partner::oneOrNew($fields['id'])->set($fields);
		
				// Get the partner type has been assigned to.
		//
		// Here we're grabbing the array of partner_types (radio buttons) and
		// assigning the model to the partner_type
		$partner_type = Request::getVar('partner_type', array(), 'post');

		// See if we have an image coming in as well
		$logo_image = Request::getVar('logo-image', false, 'files', 'array');

		// If so, proceed with saving the image
		if (isset($logo_image['name']) && $logo_image['name'])
		{
			// Build the upload path if it doesn't exist, THIS IS WHERE IMAGE WILL BE STORED
			$image_location  = $this->config->get('image_location', 'app' . DS . 'site' . DS . 'media' . DS . 'images' . DS .'partners');
			$uploadDirectory = PATH_ROOT . DS . trim($image_location, DS) . DS;

			// Make sure upload directory exists and is writable
			if (!is_dir($uploadDirectory))
			{
				if (!\Filesystem::makeDirectory($uploadDirectory))
				{
					Notify::error(Lang::txt('COM_PARTNERS_ERROR_UNABLE_TO_CREATE_UPLOAD_PATH'));
					return $this->editTask($row);
				}
			}

			// Scan for viruses
			if (!\Filesystem::isSafe($logo_image['tmp_name']))
			{
				Notify::error(Lang::txt('COM_PARTNERS_ERROR_FAILED_VIRUS_SCAN'));
				return $this->editTask($row);
			}

			if (!move_uploaded_file($logo_image['tmp_name'], $uploadDirectory . $logo_image['name']))
			{
				Notify::error(Lang::txt('COM_PARTNERS_ERROR_FILE_MOVE_FAILED'));
				return $this->editTask($row);
			}
			else
			{
				if ($old = $row->get('logo_img'))
				{
					if (file_exists($uploadDirectory . $old))
					{
						\Filesystem::delete($uploadDirectory . $old);
					}
				}
				// Move successful, save the image url to the billboard entry
				$row->set('logo_img', $logo_image['name']);
				if (!$row->save())
				{
					Notify::error($row->getError());
					return $this->editTask($row);
				}
			}
		}

		// Validate and save the data
		//
		// If save() returns false for any reason, me pass the error
		// message from the model to the controller and fall through
		// to the edit form. We pass the existing model to the edit form
		// so it can repopulate the form with the user-submitted data.
		if (!$row->save())
		{
			foreach ($row->getErrors() as $error)
			{
				Notify::error($error);
			}

			return $this->editTask($row);
		}


		// Get the partner type has been assigned to.
		//
		// Here we're grabbing the array of partner_types (radio buttons) and
		// assigning the model to the partner_type

		//doing same thing with groups_cn
		

		// Notify the user that the entry was saved
		Notify::success(Lang::txt('COM_PARTNER_ENTRY_SAVED'));

		if ($this->_task == 'apply')
		{
			// Display the edit form. This will happen if the user clicked
			// the "save" or "apply" button.
			return $this->editTask($row);
		}

		// Are we redirecting?
		// This will happen if a user clicks the "save & close" button.
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Delete one or more entries, didnt change any code from DRWHO here, except for the successful deletion message
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// [SECURITY] Check for request forgeries
		//
		// We're currently only checking POST, so if someone tries
		// to access this task via a querystring (... &task=delete&id[]=1)
		// it will be denied. This helps ensure the deletion process is
		// *only* coming in through the main listing, which submits a form.
		Request::checkToken();

		// Incoming
		// We're expecting an array of incoming IDs from the
		// entries listing. But, we'll force the data into an
		// array just to be extra sure.
		$ids = JRequest::getVar('id', array());
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Do we actually have any entries?
		if (count($ids) > 0)
		{
			$removed = 0;

			// Loop through all the IDs
			foreach ($ids as $id)
			{
				// Get the model for this entry
				$entry = Partner::oneOrFail(intval($id));

				// Delete the entry
				//
				// NOTE: It's generally preferred to use the model to delete
				// entries instead of direct SQL statements as the model will
				// typically take care of associated data and clean up after itself.
				if (!$entry->destroy())
				{
					// If the deletion process fails for any reason, we'll take the 
					// error message passed from the model and assign it to the message
					// handler to be displayed by the template after we redirect back
					// to the main listing.
					Notify::error($entry->getError());
					continue;
				}

				$removed++;
			}
		}

		// Set the redirect URL to the main entries listing.
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			($removed ? Lang::txt('COM_PARTNERS_ENTRIES_DELETED') : null)
		);
	}

	/**
	 * Sets the state of one or more entries, no change from DRWHO
	 *
	 * @return  void
	 */
	public function stateTask()
	{
		// [SECURITY] Check for request forgeries
		//
		// Unlike deleteTask() above, we're allowing requests from both
		// GET and POST. This allows us to have single click "toggle" buttons
		// on the entries list as well as handle checkboxes+toolbar button presses
		// which submit a form. This is a little less secure but state change
		// is fairly innocuous.
		Request::checkToken(['get', 'post']);

		$state = $this->_task == 'publish' ? 1 : 0;

		// Incoming
		//
		// We're expecting an array of incoming IDs from the
		// entries listing. But, we'll force the data into an
		// array just to be extra sure.
		$ids = Request::getVar('id', array(0));
		$ids = (!is_array($ids) ? array($ids) : $ids);

		// Check for a resource
		if (count($ids) < 1)
		{
			// No entries found, so go back to the entries list with
			// a message scolding the user for not selecting anything. Tsk, tsk.
			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
				Lang::txt('COM_PARTNERS_SELECT_ENTRY_TO', $this->_task),
				'error'
			);
			return;
		}

		// Loop through all the IDs
		$success = 0;
		foreach ($ids as $id)
		{
			// Load the entry and set its state
			$row = Partner::oneOrNew(intval($id))->set(array('state' => $state));

			// Store new content
			if (!$row->save())
			{
				// If the save() process fails for any reason, we'll take the 
				// error message passed from the model and assign it to the message
				// handler to be displayed by the template after we redirect back
				// to the main listing.
				Notify::error($row->getError());
				continue;
			}

			// Here, we're countign the number of successful state changes
			// so we can display that number in a message when we're done.
			$success++;
		}

		// Get the appropriate message for the task called. We're
		// passing in the number of successful state changes so it
		// can be displayed in the message.
		switch ($this->_task)
		{
			case 'publish':
				$message = Lang::txt('COM_PARTNERS_ITEMS_PUBLISHED', $success);
			break;
			case 'unpublish':
				$message = Lang::txt('COM_PARTNERS_ITEMS_UNPUBLISHED', $success);
			break;
			case 'archive':
				$message = Lang::txt('COM_PARTNERS_ITEMS_ARCHIVED', $success);
			break;
		}

		// Set the redirect URL to the main entries listing.
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			$message
		);
	}
}