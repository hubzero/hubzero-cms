<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Site\Controllers;

/**
 * AJAX actions controller class (TODO, not yet implemented)
 */
class CartControllerDo extends ComponentController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Get the task
		$this->_task  = Request::getCmd('task', '');

		if (empty($this->_task))
		{
			$this->_task = 'home';
			$this->registerTask('__default', $this->_task);
		}

		parent::execute();
	}

	/**
	 * Default task
	 *
	 * @return     void
	 */
	public function homeTask()
	{
		App::abort(404);
	}

	/**
	 * Default task
	 *
	 * @return     void
	 */
	public function deleteTask()
	{
		$sId = $this->getParams('sId');
	}
}
