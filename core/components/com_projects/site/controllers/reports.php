<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Site\Controllers;

use Components\Projects\Tables;
use Exception;
use Request;
use Route;
use Lang;
use User;
use Date;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'stats.php';

/**
 * Projects Reports controller class
 */
class Reports extends Base
{
	/**
	 * Display reports
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		$tblStats = new Tables\Stats($this->database);

		$monthly = $tblStats->monthlyStats(2, true);
		$monthly = ($monthly && count($monthly) > 1) ? $monthly : null;

		$stats = $tblStats->getStats($this->model, false, $this->_publishing);

		// Output HTML
		$this->view
			->set('title', $this->title)
			->set('task', $this->_task)
			->set('admin', $this->model->reviewerAccess('admin'))
			->set('option', $this->_option)
			->set('config', $this->config)
			->set('publishing', $this->_publishing)
			->set('monthly', $monthly)
			->set('stats', $stats)
			->set('msg', (isset($this->_msg) ? $this->_msg : ''))
			->setErrors($this->getErrors())
			->display();
	}

	/**
	 * Generate report
	 *
	 * @return  void
	 */
	public function generateTask()
	{
		// Incoming
		$data   = Request::getArray('data', array(), 'post');
		$from   = Request::getString('fromdate', Date::of('-1 month')->toLocal('Y-m'));
		$to     = Request::getString('todate', Date::of('now')->toLocal('Y-m'));
		$filter = Request::getString('searchterm', '');

		if (empty($data))
		{
			$this->setError(Lang::txt('Please pick at least one information field to report'));
		}

		$date_regex = '/^(19|20)\d\d[\-\/.](0[1-9]|1[012])$/';

		if (!preg_match($date_regex, $from) || !preg_match($date_regex, $to))
		{
			$this->setError(Lang::txt('Please use yyyy-mm format for the date'));
		}

		if (strtotime($from) > strtotime($to))
		{
			$this->setError(Lang::txt('The start date of report should be earlier than the end date'));
		}

		// Project table class
		$obj = $this->model->table();

		// Check authorization
		if (!$this->model->reviewerAccess('admin') && !$this->model->reviewerAccess('reports'))
		{
			if (User::isGuest())
			{
				$this->_msg = Lang::txt('COM_PUBLICATIONS_REPORTS_LOGIN');
				$this->_login();
				return;
			}

			throw new Exception(Lang::txt('COM_PUBLICATIONS_REPORTS_ERROR_UNAUTHORIZED'), 403);
		}

		// Get stats
		if (!$this->getError())
		{
			require_once \Component::path('com_publications') . DS . 'tables' . DS . 'logs.php';

			$objLog = new \Components\Publications\Tables\Log($this->database);

			// Get all test projects
			$exclude = $obj->getProjectsByTag('test', true, 'id');

			$stats = $objLog->getCustomStats($from, $to, $exclude, $filter);

			$filename = 'from_' . $from . '_to_' . $to .'_report.csv';

			if ($stats)
			{
				// Output to CSV
				header('Content-Type: text/csv; charset=utf-8');
				header('Content-Disposition: attachment; filename=' . $filename);
				$output = fopen('php://output', 'w');

				// output the column headings
				fputcsv($output, $data);

				foreach ($stats as $record)
				{
					$sorted = array();
					foreach ($data as $field)
					{
						$input = $record->$field;
						if ($field == 'doi')
						{
							$input = $input ? 'https://doi.org/' . $input : 'N/A';
						}

						$sorted[] = $input;
					}

					fputcsv($output, $sorted);
				}

				fclose($output);
			}
			else
			{
				$this->setError(Lang::txt('Nothing to report for selected date range and/or search term'));
			}
		}

		// Redirect on error
		if ($this->getError())
		{
			Notify::error($this->getError());

			App::redirect(
				Route::url('index.php?option=' . $this->_option . '&controller=reports&task=custom&searchterm=' . $filter, false)
			);
		}

		return;
	}

	/**
	 * Custom reports
	 *
	 * @return  void
	 */
	public function customTask()
	{
		// Set the pathway
		$this->_buildPathway();

		// Set the page title
		$this->_buildTitle();

		// Check authorization
		if (!$this->model->reviewerAccess('admin') && !$this->model->reviewerAccess('reports'))
		{
			if (User::isGuest())
			{
				$this->_msg = Lang::txt('COM_PUBLICATIONS_REPORTS_LOGIN');
				$this->_login();
				return;
			}

			throw new Exception(Lang::txt('COM_PUBLICATIONS_REPORTS_ERROR_UNAUTHORIZED'), 403);
		}

		// Output HTML
		$this->view
			->set('title', $this->title)
			->set('task', $this->_task)
			->set('option', $this->_option)
			->set('config', $this->config)
			->set('msg', (isset($this->_msg) ? $this->_msg : ''))
			->setLayout('custom')
			->setErrors($this->getErrors())
			->display();
	}
}
