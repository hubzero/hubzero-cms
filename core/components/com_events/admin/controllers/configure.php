<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Admin\Controllers;

use Components\Events\Tables\Configs;
use Components\Events\Tables\Config as EventsConfig;
use Hubzero\Component\AdminController;
use Exception;

/**
 * Events controller for configuration
 */
class Configure extends AdminController
{
	/**
	 * Determines task and attempts to execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->config = new Configs($this->database);
		$this->config->load();

		parent::execute();
	}

	/**
	 * Show a form for editing configuration values
	 *
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->config = $this->config;

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Save configuration values
	 *
	 * @return     void
	 */
	public function saveTask()
	{
		// Get the configuration
		$config = Request::getArray('config', array(), 'post');
		foreach ($config as $n => $v)
		{
			$box = array(
				'param' => $n,
				'value' => $v
			);

			$row = new EventsConfig($this->database);
			if (!$row->bind($box))
			{
				throw new Exception($row->getError(), 500);
			}
			// Check content
			if (!$row->check())
			{
				throw new Exception($row->getError(), 500);
			}
			// Store content
			if (!$row->store())
			{
				throw new Exception($row->getError() . ' ' . $row->param . ': ' . $row->value, 500);
			}
		}

		// Get the custom fields
		$fields = Request::getArray('fields', array(), 'post');

		$box = array();
		$box['param'] = 'fields';
		$box['value'] = '';

		if (is_array($fields))
		{
			$txta = array();
			foreach ($fields as $val)
			{
				if ($val['title'])
				{
					$k = preg_replace("/[^a-zA-Z0-9]/", '', strtolower($val['title']));

					$t = str_replace('=', '-', $val['title']);
					$j = (isset($val['type']))     ? $val['type']     : 'text';
					$x = (isset($val['required'])) ? $val['required'] : '0';
					$z = (isset($val['show']))     ? $val['show']     : '0';
					$txta[] = $k . '=' . $t . '=' . $j . '=' . $x . '=' . $z;
				}
			}
			$field = implode("\n", $txta);
		}
		$box['value'] = $field;

		$row = new EventsConfig($this->database);
		if (!$row->bind($box))
		{
			throw new Exception($row->getError(), 500);
		}
		// Check content
		if (!$row->check())
		{
			throw new Exception($row->getError(), 500);
		}
		// Store content
		if (!$row->store())
		{
			throw new Exception($row->getError(), 500);
		}

		// Redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_EVENTS_CAL_LANG_CONFIG_SAVED')
		);
	}
}
