<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$config = Request::getVar('config', array(), 'post');
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
		$fields = Request::getVar('fields', array(), 'post');

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

