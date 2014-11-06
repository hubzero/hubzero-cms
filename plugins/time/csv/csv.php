<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Plugin for time report csv download
 */
class plgTimeCsv extends \Hubzero\Plugin\Plugin
{
	/**
	 * List of accepted methods available by reports controller
	 *
	 * @var array
	 **/
	public static $accepts = array('download');

	/**
	 * Initial render view
	 *
	 * @return (string) view contents
	 */
	public static function render()
	{
		// Load language
		JFactory::getLanguage()->load('plg_time_csv', JPATH_ADMINISTRATOR);

		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'time',
				'element' => 'csv',
				'name'    => 'overview'
			)
		);

		$database        = JFactory::getDbo();
		$permissions     = new TimeModelPermissions('com_time');
		$recordTbl       = new TimeRecords($database);
		$hubTbl          = new TimeHubs($database);
		$view->hub_id    = JRequest::getInt('hub_id', null);
		$view->start     = JRequest::getCmd('start_date', JFactory::getDate(strtotime('today - 1 month'))->format('Y-m-d'));
		$view->end       = JRequest::getCmd('end_date', JFactory::getDate()->format('Y-m-d'));
		$view->hubsList  = $hubTbl->getRecords();
		$view->records   = $recordTbl->getRecords(
			array(
				'orderby'  => 'h.name',
				'orderdir' => 'ASC',
				'q'        => array(
					array(
						'column' => 'date',
						'o'      => '>=',
						'value'  => $view->start
					),
					array(
						'column' => 'date',
						'o'      => '<=',
						'value'  => $view->end
					),
					array(
						'column' => 'h.id',
						'o'      => '=',
						'value'  => (isset($view->hub_id) && $view->hub_id > 0) ? $view->hub_id : null
					)
				)
			)
		);

		// Pass permissions to view
		$view->permissions = $permissions;

		return $view->loadTemplate();
	}

	/**
	 * Download CSV
	 *
	 * @return void
	 */
	public static function download()
	{
		// Load language
		JFactory::getLanguage()->load('plg_time_csv', JPATH_ADMINISTRATOR);

		$database  = JFactory::getDbo();
		$recordTbl = new TimeRecords($database);
		$hub_id    = JRequest::getInt('hub_id', null);
		$start     = JRequest::getCmd('start_date', JFactory::getDate(strtotime('today - 1 month'))->format('Y-m-d'));
		$end       = JRequest::getCmd('end_date', JFactory::getDate()->format('Y-m-d'));
		$records   = $recordTbl->getRecords(
			array(
				'orderby'  => 'h.name',
				'orderdir' => 'ASC',
				'q'        => array(
					array(
						'column' => 'date',
						'o'      => '>=',
						'value'  => $start
					),
					array(
						'column' => 'date',
						'o'      => '<=',
						'value'  => $end
					),
					array(
						'column' => 'h.id',
						'o'      => '=',
						'value'  => (isset($hub_id) && $hub_id > 0) ? $hub_id : null
					)
				)
			)
		);

		$all  = true;
		foreach (JRequest::get('GET') as $key => $value)
		{
			if (strpos($key, 'fields-') !== false)
			{
				$all = false;
			}
		}

		if (isset($hub_id))
		{
			$hub = new TimeHubs($database);
			$hub->load($hub_id);
			if ($hub->get('name_normalized'))
			{
				$hubname = $hub->get('name_normalized');
			}
		}

		$filename  = 'time_report';
		$filename .= (isset($hubname)) ? '_' . $hubname : '';
		$filename .= '_' . JFactory::getDate($start)->format('Ymd');
		$filename .= '-' . JFactory::getDate($end)->format('Ymd');
		$filename .= '.csv';

		// Set content type headers
		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=$filename");
		header("Pragma: no-cache");
		header("Expires: 0");

		$row = array();
		if ($hub = JRequest::getInt('fields-hub', $all))
		{
			$row[] = JText::_('PLG_TIME_CSV_HUB');
		}
		if ($task = JRequest::getInt('fields-task', $all))
		{
			$row[] = JText::_('PLG_TIME_CSV_TASK');
		}
		if ($user = JRequest::getInt('fields-user', $all))
		{
			$row[] = JText::_('PLG_TIME_CSV_USER');
		}
		if ($date = JRequest::getInt('fields-date', $all))
		{
			$row[] = JText::_('PLG_TIME_CSV_DATE');
		}
		if ($time = JRequest::getInt('fields-time', $all))
		{
			$row[] = JText::_('PLG_TIME_CSV_TIME');
		}
		if ($description = JRequest::getInt('fields-description', $all))
		{
			$row[] = JText::_('PLG_TIME_CSV_DESCRIPTION');
		}
		echo implode(',', $row) . "\n";

		$permissions = new TimeModelPermissions('com_time');

		foreach ($records as $record)
		{
			if ($permissions->can('view.report', 'hubs', $record->hid))
			{
				$output = fopen('php://output','w');
				$row    = array();
				if ($hub)
				{
					$row[] = $record->hname;
				}
				if ($task)
				{
					$row[] = $record->pname;
				}
				if ($user)
				{
					$row[] = $record->uname;
				}
				if ($date)
				{
					$row[] = $record->date;
				}
				if ($time)
				{
					$row[] = $record->time;
				}
				if ($description)
				{
					$row[] = $record->description;
				}

				fputcsv($output, $row);
				fclose($output);
			}
		}

		exit();
	}
}