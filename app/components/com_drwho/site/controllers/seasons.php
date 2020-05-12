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
		Pathway::append(
			Lang::txt(strtoupper($this->_option)),
			'index.php?option='.$this->_option
		);
		
		Pathway::append(
			Lang::txt(strtoupper($this->_task)),
			'index.php?option=' . $this->_options . '&task=' . $this->_task
		);
		
		$output = \Hubzero\Module\Helper::renderModule('breadcrumbs');
		$output = str_replace("\n", "", $output);
		$output = str_replace("\r", "", $output);
		$url = Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task);
		echo '<script type="text/javascript">',
				 "  $('.crumbs-wrap').html('" . $output . "');",
				 "  window.history.pushState('object or string', '', '" . $url . "');",
				 '</script>';
	}
	
	/**
	 * Default task. Displays a list of characters.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
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
		// Set the pathway
		$this->_buildPathway();

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
		// Set the pathway
		$this->_buildPathway();
		
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
		// Set the pathway
		$this->_buildPathway();
		
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
		// Set the pathway
		$this->_buildPathway();
		
		$this->view
			->display();
	}
}
