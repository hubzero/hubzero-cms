<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Publications\Tables;
use Route;
use App;

/**
 * Publication administrative support
 */
class Admin extends AdminController
{
	/**
	 * List available admin tasks
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Redirect to Publication Manager for now
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=items', false)
		);
		return;
	}
}
