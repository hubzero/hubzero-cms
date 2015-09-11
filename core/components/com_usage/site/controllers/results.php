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

namespace Components\Usage\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Usage\Helpers\Helper;
use Exception;
use Document;
use Pathway;
use Request;
use Event;
use Lang;

require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'helper.php');

/**
 * Usage controller class for results
 */
class Results extends SiteController
{
	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'default');

		parent::execute();
	}

	/**
	 * Display usage data
	 *
	 * @return     void
	 */
	public function defaultTask()
	{
		// Set some common variables
		$thisyear = date("Y");
		$months = array(
			'01' => 'Jan',
			'02' => 'Feb',
			'03' => 'Mar',
			'04' => 'Apr',
			'05' => 'May',
			'06' => 'Jun',
			'07' => 'Jul',
			'08' => 'Aug',
			'09' => 'Sep',
			'10' => 'Oct',
			'11' => 'Nov',
			'12' => 'Dec'
		);
		$monthsReverse = array_reverse($months, TRUE);

		// Incoming
		$enddate = Request::getVar('selectedPeriod', 0, 'post');

		// Establish a connection to the usage database
		$udb = Helper::getUDBO();
		if (!is_object($udb))
		{
			throw new Exception(Lang::txt('COM_USAGE_ERROR_CONNECTING_TO_DATABASE'), 500);
		}

		$this->view->no_html = Request::getVar('no_html', 0);

		// Trigger the functions that return the areas we'll be using
		$this->view->cats = Event::trigger('usage.onUsageAreas');

		if (is_array($this->view->cats))
		{
			if (!$this->_task || $this->_task == 'default')
			{
				$this->_task = (isset($this->view->cats[0]) && is_array($this->view->cats[0])) ? key($this->view->cats[0]) : 'overview';
			}
		}
		$this->_task = ($this->_task) ? $this->_task : 'overview';
		$this->view->task = $this->_task;

		// Set the pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(
				Lang::txt(strtoupper($this->_option)),
				'index.php?option=' . $this->_option
			);
		}
		if ($this->_task)
		{
			Pathway::append(
				Lang::txt('PLG_' . strtoupper($this->_name) . '_' . strtoupper($this->_task)),
				'index.php?option=' . $this->_option . '&task=' . $this->_task
			);
		}

		// Get the sections
		$this->view->sections = Event::trigger('usage.onUsageDisplay', array(
				$this->_option,
				$this->_task,
				$udb,
				$months,
				$monthsReverse,
				$enddate
			)
		);

		// Build the page title
		$this->view->title  = Lang::txt(strtoupper($this->_option));
		$this->view->title .= ($this->_task) ? ': ' . Lang::txt('PLG_' . strtoupper($this->_name) . '_' . strtoupper($this->_task)) : '';

		// Set the page title
		Document::setTitle($this->view->title);

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view
			->setLayout('default')
			->display();
	}
}

