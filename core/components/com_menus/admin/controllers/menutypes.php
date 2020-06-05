<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Menus\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Menus\Models\Menutype;
use Request;

/**
 * The Menu types Controller
 */
class Menutypes extends AdminController
{
	/**
	 * Temporary method. This should go into the 1.5 to 1.6 upgrade routines.
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$recordId = Request::getInt('recordId');

		$model = new Menutype();
		$types = $model->getTypeOptions();

		// Output the HTML
		$this->view
			->set('recordId', $recordId)
			->set('types', $types)
			->display();
	}
}
