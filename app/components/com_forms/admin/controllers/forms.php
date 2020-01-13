<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Forms\Admin\Controllers;

$componentPath = Component::path('com_forms');

require_once "$componentPath/admin/helpers/permissions.php";

use Components\Forms\Admin\Helpers\Permissions;
use Hubzero\Component\AdminController;

class Forms extends AdminController
{

	/*
	 * Task mapping
	 *
	 * @var  array
	 */
	protected $_taskMap = [
		'__default' => 'list'
	];

	/*
	 * Administrator toolbar title
	 *
	 * @var  string
	 */
	protected static $_toolbarTitle = 'Forms';

	/*
	 * Renders list view
	 *
	 * @return   void
	 */
	public function listTask()
	{
		$permissions = Permissions::getActions('form');

		$this->view
			->set('permissions', $permissions)
			->set('title', self::$_toolbarTitle)
			->display();
	}

}
