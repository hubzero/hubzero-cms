<?php
// Declare the namespace.
namespace Components\Drwho\Site\Controllers;

use Hubzero\Component\SiteController;
use Pathway;
use Request;

/**
 * Drwho controller for show seasons
 */
class Seasons extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		$this->no_html = Request::getInt('no_html');
		
		// Make sure call display when no_html is 0 for display subtasks
		$this->_task = trim(Request::getCmd('task', 'display'));
		$this->_task = ($this->_task == 'display') ? 'one' : $this->_task; // Set default subtask
		$subtasks = array('one', 'two', 'three', 'four');
		if (in_array($this->_task, $subtasks) && !$this->no_html)
		{
			Request::setVar('action', $this->_task);
			$this->_action = $this->_task;
					
			Request::setVar('task', 'display');
		}
		
		parent::execute();
	}
	
	/**
	 * Build the "trail"
	 *
	 * @return  void
	 */
	protected function _buildPathway()
	{
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option='.$this->_option
			);
		}
	}
	
	/**
	 * Default task. Displays a list of characters.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set the pathway
		$this->_buildPathway();
		
		$this->view
			->display();

		// Subtask - call ajax
		//   Can we use $this->_route to push existing URL forward?
		if (Request::has('action'))
		{
			$ajax = Route::url('index.php?option=' . $this->_option . '&task=' . $this->_action . '?no_html=1');
			echo '<script type="text/javascript">',
     			 '$.get("' . $ajax . '", function(result) {',
					 '  $("#section-content").html(result);',
					 '});',
           '</script>';
		}
	}

	/**
	 * Display one
	 *
	 * @return  void
	 */
	public function oneTask()
	{
		// Output the view
		$this->view
			->display();
	}

	/**
	 * Display two
	 *
	 * @return  void
	 */
	public function twoTask()
	{
		$this->view
			->display();
	}

	/**
	 * Display three
	 *
	 * @return  void
	 */
	public function threeTask()
	{
		$this->view
			->display();
	}

	/**
	 * Display four
	 *
	 * @return  void
	 */
	public function fourTask()
	{
		$this->view
			->display();
	}
}
