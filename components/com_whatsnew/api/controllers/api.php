<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 Purdue University. All rights reserved.
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
 * @author    Chris Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2009-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/**
 * API controller class for What's New
 */
class WhatsnewControllerApi extends \Hubzero\Component\ApiController
{
	/**
	 * Execute a request
	 *
	 * @return  void
	 */
	public function execute()
	{
		//JLoader::import('joomla.environment.request');
		//JLoader::import('joomla.application.component.helper');

		switch ($this->segments[0])
		{
			case 'index': $this->index(); break;
			default:      $this->service();
		}
	}

	/**
	 * Documents available API tasks and their options
	 *
	 * @return  void
	 */
	private function service()
	{
		$response = new stdClass();
		$response->component = 'whatsnew';
		$response->tasks = array(
			'index' => array(
				'description' => Lang::txt('Get a list of new content.'),
				'parameters'  => array(
					'period' => array(
						'description' => Lang::txt('Time period to search for records.'),
						'type'        => 'string',
						'default'     => 'year',
						'accepts'     => array('year', 'quarter', 'month', 'week')
					),
					'order' => array(
						'description' => Lang::txt('Direction to sort results by.'),
						'type'        => 'string',
						'default'     => 'desc',
						'accepts'     => array('asc', 'desc')
					),
					'limit' => array(
						'description' => Lang::txt('Number of result to return.'),
						'type'        => 'integer',
						'default'     => '25'
					),
					'limitstart' => array(
						'description' => Lang::txt('Number of where to start returning results.'),
						'type'        => 'integer',
						'default'     => '0'
					),
				),
			),
		);

		$this->setMessageType(Request::getWord('format', 'json'));
		$this->setMessage($response);
	}

	/**
	 * Generates a list of new content
	 *
	 * @return  void
	 */
	protected function index()
	{
		// get the userid
		$userid = JFactory::getApplication()->getAuthn('user_id');

		// if we dont have a user return nothing
		if ($userid == null)
		{
			return $this->not_found();
		}

		// get the request vars
		$period     = Request::getVar("period", "year");
		$category   = Request::getVar("category", "all");
		$limit      = Request::getVar("limit", 25);
		$limitstart = Request::getVar("limitstart", 0);
		$content    = Request::getVar("content", 0);
		$order      = Request::getVar("order", "desc");

		// import joomla plugin helper
		jimport('joomla.plugin.helper');

		// Load plugins
		JPluginHelper::importPlugin('whatsnew');
		$dispatcher = JDispatcher::getInstance();

		// get the search areas
		$areas = array();
		$searchareas = $dispatcher->trigger('onWhatsNewAreas');
		foreach ($searchareas as $area)
		{
			$areas = array_merge($areas, $area);
		}

		// parse our categories
		// make sure we have a category
		$category = ($category == '') ? 'all' : $category;
		$category = array_filter(array_values(explode(',', $category)));

		// if we have an array of categories lets remove any areas not passed in
		if (!in_array('all', $category))
		{
			foreach ($areas as $k => $area)
			{
				if (!in_array($k, $category))
				{
					unset($areas[$k]);
				}
			}
		}

		//parse the period
		require_once(dirname(__DIR__) . DS . 'helpers' . DS . 'period.php');
		$p = new Components\Whatsnew\Helpers\Period($period);

		$results = $dispatcher->trigger(
			'onWhatsnew',
			array(
				$p,
				999,
				0,
				$areas
			)
		);

		$whatsnew = array();
		foreach ($results as $results_section)
		{
			foreach ($results_section as $result)
			{
				$item = array();
				$item['title'] = stripslashes($result->title);
				$item['link']  = $result->href;
				$item['date']  = @$result->created;
				switch ($result->section)
				{
					case "resources": $item['section'] = stripslashes($result->area);    break;
					case "content":   $item['section'] = "content articles";             break;
					default:          $item['section'] = stripslashes($result->section); break;
				}
				if ($content)
				{
					$item['text'] = $result->text;
				}
				$whatsnew[] = $item;
			}
		}

		// order by the date created
		if ($order == 'asc')
		{
			usort($whatsnew, array($this, "sorter_asc"));
		}
		else
		{
			usort($whatsnew, array($this, "sorter"));
		}

		$w = array();
		$count = 0;
		for ($i=$limitstart, $n=count($whatsnew); $i<$n; $i++)
		{
			if ($count < $limit)
			{
				$w[] = $whatsnew[$i];
			}
			$count++;
		}

		$obj = new stdClass();
		$obj->whatsnew = $w;

		$this->setMessageType("application/json");
		$this->setMessage($obj);
	}

	/**
	 * Sort items
	 *
	 * @param   array    $a
	 * @param   array    $b
	 * @return  integer
	 */
	public function sorter($a, $b)
	{
		return (strtotime($a['date']) < strtotime($b['date'])) ? 1 : -1;
	}

	/**
	 * Sort items desc
	 *
	 * @param   array    $a
	 * @param   array    $b
	 * @return  integer
	 */
	public function sorter_asc($a, $b)
	{
		return (strtotime($a['date']) < strtotime($b['date'])) ? -1 : 1;
	}
}
