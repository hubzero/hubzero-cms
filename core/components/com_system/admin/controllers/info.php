<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Admin\Controllers;

use Components\System\Models\Info as KnowItAll;
use Hubzero\Component\AdminController;
use Lang;
use User;
use App;

/**
 * System controller class for info
 */
class Info extends AdminController
{
	/**
	 * Outputs a list of available scripts
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		if (!User::authorise('core.admin'))
		{
			App::abort(403, Lang::txt('JERROR_ALERTNOAUTHOR'));
		}

		include_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'info.php';

		$model = new KnowItAll();

		$this->view
			->set('php_settings', $model->getPhpSettings())
			->set('config', $model->getConfig())
			->set('info', $model->getInfo())
			->set('php_info', $model->getPhpInfo())
			->set('directory', $model->getDirectory())
			->setLayout('default')
			->display();
	}
}
