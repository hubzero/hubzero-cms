<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Admin\Controllers;

use Hubzero\Component\AdminController;
use Route;
use Lang;
use App;

/**
 * System controller class for info
 */
class Geodb extends AdminController
{
	/**
	 * Default view
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Output the HTML
		$this->view->display();
	}

	/**
	 * Import the hub configuration
	 *
	 * @return  void
	 */
	public function importHubconfigTask()
	{
		if (file_exists(PATH_ROOT . DS . 'hubconfiguration.php'))
		{
			include_once PATH_ROOT . DS . 'hubconfiguration.php';
		}
		elseif (file_exists(PATH_APP . DS . 'hubconfiguration.php'))
		{
			include_once PATH_APP . DS . 'hubconfiguration.php';
		}

		if (class_exists('HubConfig'))
		{
			$db = App::get('db');
			$db->setQuery("SELECT * FROM `#__extensions` WHERE `type`='component' AND `element`=" . $db->quote($this->_option));
			$table = $db->loadObject();

			$hub_config = new \HubConfig();

			$this->config->set('geodb_driver', $hub_config->ipDBDriver);
			$this->config->set('geodb_host', $hub_config->ipDBHost);
			$this->config->set('geodb_port', $hub_config->ipDBPort);
			$this->config->set('geodb_user', $hub_config->ipDBUsername);
			$this->config->set('geodb_password', $hub_config->ipDBPassword);
			$this->config->set('geodb_database', $hub_config->ipDBDatabase);
			$this->config->set('geodb_prefix', $hub_config->ipDBPrefix);

			$db->setQuery("UPDATE `#__extensions` SET `params`=" . $db->quote($this->config->toString()) . " WHERE `extension_id`=" . $db->quote($table->extension_id));
			$db->query();

			Notify::success(Lang::txt('COM_SYSTEM_GEO_IMPORT_COMPLETE'));
		}

		$this->cancelTask();
	}
}
