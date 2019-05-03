<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Request;
use Notify;
use Lang;
use App;

/**
 * Controller class for registration configuration
 */
class Registration extends AdminController
{
	/**
	 * Display configurations for registration
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$config = new \Hubzero\Form\Form('com_members.registration');
		$config->loadFile(dirname(dirname(__DIR__)) . DS . 'config' . DS . 'config.xml', true, '/config');
		$config->bind($this->config->toArray());

		$params = $config->getFieldset('registration');

		// Output the HTML
		$this->view
			->set('params', $params)
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Save changes to the registration
	 *
	 * @return  void
	 */
	public function saveTask()
	{
		// Check for request forgeries
		Request::checkToken();

		$settings = Request::getArray('settings', array(), 'post');

		if (!is_array($settings) || empty($settings))
		{
			Notify::error(Lang::txt('COM_MEMBERS_REGISTRATION_ERROR_MISSING_DATA'));

			return $this->cancelTask();
		}

		$arr = array();

		$database = App::get('db');
		$database->setQuery(
			"SELECT *
			FROM `#__extensions`
			WHERE `type`='component'
			AND `element`=" . $database->quote($this->_option) . "
			LIMIT 1"
		);
		$component = $database->loadObject();

		$params = new \Hubzero\Config\Registry($component->params);

		foreach ($settings as $name => $value)
		{
			$r = $value['create'] . $value['proxy'] . $value['update'] . $value['edit'];

			$params->set('registration' . trim($name), trim($r));
		}

		$component->params = $params->toString();

		$database->setQuery(
			"UPDATE `#__extensions`
			SET `params`=" . $database->quote($component->params) . "
			WHERE `extension_id`=" . $database->quote($component->extension_id)
		);
		$database->query();

		if (App::get('config')->get('caching'))
		{
			$handler = App::get('config')->get('cache_handler');

			App::get('config')->set($handler, array(
				'cachebase' => PATH_APP . '/cache/site'
			));

			$cache = new \Hubzero\Cache\Manager(\App::getRoot());
			$cache->storage($handler);
			$cache->clean('_system');
		}

		Notify::success(Lang::txt('COM_MEMBERS_REGISTRATION_SAVED'));

		$this->cancelTask();
	}
}
