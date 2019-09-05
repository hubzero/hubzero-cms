<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Usage\Site\Controllers;

$componentPath = Component::path('com_usage');

require_once "$componentPath/helpers/helper.php";
require_once "$componentPath/helpers/monthsHelper.php";

use Hubzero\Component\SiteController;
use Components\Usage\Helpers\Helper;
use Components\Usage\Helpers\MonthsHelper;
use Exception;
use Document;
use Pathway;
use Request;
use Event;
use Lang;

/**
 * Usage controller class for results
 */
class Results extends SiteController
{

	protected $monthsHelper;

	/**
	 * Execute a task
	 *
	 * @return     void
	 */
	public function execute()
	{
		$this->registerTask('__default', 'default');
		$this->monthsHelper = new MonthsHelper();

		parent::execute();
	}

	/**
	 * Display usage data
	 *
	 * @return     void
	 */
	public function defaultTask()
	{
		$months = $this->monthsHelper->getAbbreviationMap();
		$monthsReverse = $this->monthsHelper->getAbbreviationMapReversed();

		$endDate = Request::getInt('selectedPeriod', 0, 'post');
		$noHtml = Request::getInt('no_html', 0);

		// Establish a connection to the usage database
		$udb = Helper::getUDBO();
		if (!is_object($udb))
		{
			throw new Exception(Lang::txt('COM_USAGE_ERROR_CONNECTING_TO_DATABASE'), 500);
		}

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
				$endDate
			)
		);

		// Build the page title
		$this->view->title  = Lang::txt(strtoupper($this->_option));
		$this->view->title .= ($this->_task) ? ': ' . Lang::txt('PLG_' . strtoupper($this->_name) . '_' . strtoupper($this->_task)) : '';
		$this->view->no_html = $noHtml;

		// Set the page title
		Document::setTitle($this->view->title);

		// Output HTML
		$this->view
			->setLayout('default')
			->setErrors($this->getErrors())
			->display();
	}
}
