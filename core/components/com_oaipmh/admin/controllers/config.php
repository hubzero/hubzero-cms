<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Oaipmh\Admin\Controllers;

use Components\Oaipmh\Models\Service;
use Hubzero\Component\AdminController;

/**
 * Controller class for OAIPMH config
 */
class Config extends AdminController
{
	/**
	 * Display config optins
	 * 
	 * @return  void
	 */
	public function displayTask()
	{
		// display panel
		$this->view->display();
	}

	/**
	 * Display available schemas
	 * 
	 * @return  void
	 */
	public function schemasTask()
	{
		require_once dirname(dirname(__DIR__)) . DS . 'models' . DS . 'service.php';

		// display panel
		$this->view
			->set('service', new Service())
			->display();
	}
}
