<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for usage
 */
class plgResourcesUsage extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesAreas($model)
	{
		$areas = array();

		if ($model->type->params->get('plg_' . $this->_name) && $model->isTool())
		{
			// Only show tab for tools
			$areas['usage'] = Lang::txt('PLG_RESOURCES_USAGE');
		}

		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model))))
			{
				$rtrn = 'metadata';
			}
		}
		if (!$model->type->params->get('plg_usage'))
		{
			return $arr;
		}

		// Display only for tools
		if (!$model->isTool())
		{
			//return $arr;
			$rtrn == 'metadata';
		}

		// Check if we have a needed database table
		$database = App::get('db');

		$tables = $database->getTableList();
		$table  = $database->getPrefix() . 'resource_stats_tools';

		$url = Route::url('index.php?option=' . $option . '&' . ($model->resource->alias ? 'alias=' . $model->resource->alias : 'id=' . $model->resource->id) . '&active=' . $this->_name);

		if (!in_array($table, $tables))
		{
			$arr['html'] = '<p class="error">'. Lang::txt('PLG_RESOURCES_USAGE_MISSING_TABLE') . '</p>';
			$arr['metadata'] = '<p class="usage"><a href="' . $url . '">' . Lang::txt('PLG_RESOURCES_USAGE_DETAILED') . '</a></p>';
			return $arr;
		}

		// Get/set some variables
		$dthis  = Request::getVar('dthis', date('Y') . '-' . date('m'));
		$period = Request::getInt('period', $this->params->get('period', 14));

		include_once(PATH_CORE . DS . 'components' . DS . $option . DS . 'tables' . DS . 'stats.php');
		if ($model->isTool())
		{
			$stats = new \Components\Resources\Tables\Stats\Tools($database);
		}
		else
		{
			$stats = new \Components\Resources\Tables\Stats($database);
		}
		$stats->loadStats($model->resource->id, $period); //, $dthis);

		$clusters = new \Components\Resources\Tables\Stats\Clusters($database);
		$clusters->loadStats($model->resource->id);

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			$action = Request::getVar('action', '');
			if ($action == 'top')
			{
				$dtm = Request::getVar('datetime', '0000-00-00 00:00:00');
				if (!preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $dtm))
				{
					$dtm = '0000-00-00 00:00:00';
				}
				$this->getTopValues($model->resource->id, $dtm);
				return;
			}
			if ($action == 'overview')
			{
				$this->getValues($model->resource->id, Request::getInt('period', 13));
				return;
			}

			include_once(\Component::path('com_members') . DS . 'models' . DS . 'profile' . DS . 'field.php');

			$types = array();

			$field = \Components\Members\Models\Profile\Field::all()
				->whereEquals('name', 'orgtype')
				->row();

			if ($field->get('id'))
			{
				$options = $field->options()->ordered()->rows();

				foreach ($options as $opt)
				{
					$type = new stdClass;
					$type->id    = $opt->get('id');
					$type->type  = $opt->get('value');
					$type->title = $opt->get('label');

					$types[] = $type;
				}
			}

			// Instantiate a view
			$view = $this->view('default', 'browse');

			// Pass the view some info
			$view->option     = $option;
			$view->resource   = $model->resource;
			$view->stats      = $stats;
			$view->chart_path = $this->params->get('chart_path','');
			$view->map_path   = $this->params->get('map_path','');
			$view->dthis      = $dthis;
			$view->period     = $period;
			$view->params     = $this->params;
			$view->organizations = $types;
			if ($this->getError())
			{
				$view->setError($this->getError());
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			if (!$stats->users)
			{
				$stats->users = 0;
			}
			if ($model->isTool())
			{
				$arr['metadata'] = '<p class="usage"><a href="' . $url . '">' . Lang::txt('PLG_RESOURCES_USAGE_NUM_USERS_DETAILED', $stats->users) . '</a></p>';
			}
			else
			{
				$arr['metadata'] = '<p class="usage">' . Lang::txt('PLG_RESOURCES_USAGE_NUM_USERS', $stats->users) . '</p>';
			}
			if (isset($clusters->users) && $clusters->users && isset($clusters->classes) && $clusters->classes)
			{
				$arr['metadata'] .= '<p class="usage">' . Lang::txt('PLG_RESOURCES_USAGE_NUM_USERS_IN_CLASSES', $clusters->users, $clusters->classes) . '</p>';
			}
		}

		return $arr;
	}

	/**
	 * Round time into nearest second/minutes/hours/days
	 *
	 * @param      integer $time Time
	 * @return     string
	 */
	public static function timeUnits($time)
	{
		if ($time < 60)
		{
			$data = Lang::txt('PLG_RESOURCES_USAGE_SECONDS', round($time, 2));
		}
		else if ($time > 60 && $time < 3600)
		{
			$data = Lang::txt('PLG_RESOURCES_USAGE_MINUTES', round(($time/60), 2));
		}
		else if ($time >= 3600 && $time < 86400)
		{
			$data = Lang::txt('PLG_RESOURCES_USAGE_HOURS', round(($time/3600), 2));
		}
		else if ($time >= 86400)
		{
			$data = Lang::txt('PLG_RESOURCES_USAGE_DAYS', round(($time/86400), 2));
		}

		return $data;
	}

	/**
	 * Get overview data
	 *
	 * @param      integer $id  Resource ID
	 * @return     array
	 */
	public static function getOverview($id, $period=1)
	{
		$database = App::get('db');

		$sql = "SELECT *
				FROM `#__resource_stats_tools`
				WHERE resid = '$id'
				AND period = '$period'
				ORDER BY `datetime` ASC";
		$database->setQuery($sql);
		return $database->loadObjectList();
	}

	/**
	 * Check for data for a given time period
	 *
	 * @param      integer $id  Resource ID
	 * @param      integer $top Value type (1 = country, 2 = domain, 3 = org)
	 * @param      integer $tid Stats ID for that tool
	 * @param      string  $datetime Timestamp YYYY-MM-DD
	 * @return     array
	 */
	public static function getTopValue($id, $top, $tid, $datetime, $prd=14)
	{
		$database = App::get('db');

		if (!$id || !$tid)
		{
			return array();
		}

		$sql = "SELECT v.*, t.datetime, t.`processed_on`
				FROM `#__resource_stats_tools` AS t
				LEFT JOIN `#__resource_stats_tools_topvals` AS v ON v.id=t.id
				WHERE t.resid = '$id'
				AND t.period = '$prd'
				AND t.datetime = '" . $datetime . "-00 00:00:00'
				AND t.id = $tid
				AND v.top = '$top'
				ORDER BY v.id, v.rank";

		$database->setQuery($sql);
		return $database->loadObjectList();
	}

	/**
	 * Get the stats ID for a specific resource
	 * Getting this now allows for faster data pulling later on
	 *
	 * @param      integer $id       Resource ID
	 * @param      string  $datetime Timestamp YYYY-MM-DD
	 * @return     array
	 */
	public static function getTid($id, $datetime, $period=14)
	{
		$database = App::get('db');

		$sql = "SELECT t.id FROM `#__resource_stats_tools` AS t WHERE t.resid = " . $database->quote($id) . " AND t.period = " . $database->quote($period) . " AND t.datetime = '" . $datetime . "-00 00:00:00' ORDER BY t.id LIMIT 1";
		$database->setQuery($sql);
		return $database->loadResult();
	}

	/**
	 * Get data for orgs, countries, domains for a given time period
	 * (1 = country, 2 = domain, 3 = org)
	 *
	 * @param      integer $id       Resource ID
	 * @param      string  $datetime Timestamp YYYY-MM-DD
	 * @return     array
	 */
	public function getValues($id, $period)
	{
		$results = $this->getOverview($id, $period);

		$users = array();
		//$interactive = array();
		//$sessions = array();
		$runs = array();

		$data = new stdClass;
		$data->points = array();
		//$data->runs = array();

		foreach ($results as $result)
		{
			//$point = new stdClass;
			$result->datetime = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . ' 00:00:00';
			//$point->users = $result->users;
			//$point->users = $result->users;

			//$data->users[]       = "[Date.parse('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . " 00:00:00')," . $result->users . "]";
			//$interactive[] = "[Date.parse('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . " 00:00:00')," . $result->sessions . "]";
			//$sessions[]    = "[Date.parse('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . " 00:00:00')," . $result->simulations . "]";
			//$data->runs[]        = "[Date.parse('" . str_replace('-', '/', str_replace('-00 00:00:00', '-01', $result->datetime)) . " 00:00:00')," . $result->jobs . "]";
			$data->points[] = $result;

			//$usersTop = ($result->users > $usersTop) ? $result->users : $usersTop;
			//$runsTop = ($result->jobs > $runsTop) ? $result->jobs : $runsTop;
		}

		ob_clean();

		echo json_encode($data);
		die();
	}

	/**
	 * Get data for orgs, countries, domains for a given time period
	 * (1 = country, 2 = domain, 3 = org)
	 *
	 * @param      integer $id       Resource ID
	 * @param      string  $datetime Timestamp YYYY-MM-DD
	 * @return     array
	 */
	public function getTopValues($id, $datetime)
	{
		$period = Request::getInt('period', 14);

		$colors = array(
			$this->params->get('pie_chart_color1', '#7c7c7c'),
			$this->params->get('pie_chart_color2', '#515151'),
			$this->params->get('pie_chart_color3', '#d9d9d9'),
			$this->params->get('pie_chart_color4', '#3d3d3d'),
			$this->params->get('pie_chart_color5', '#797979'),
			$this->params->get('pie_chart_color6', '#595959'),
			$this->params->get('pie_chart_color7', '#e5e5e5'),
			$this->params->get('pie_chart_color8', '#828282'),
			$this->params->get('pie_chart_color9', '#404040'),
			$this->params->get('pie_chart_color10', '#6a6a6a'),
			$this->params->get('pie_chart_color1', '#bcbcbc'),
			$this->params->get('pie_chart_color2', '#515151'),
			$this->params->get('pie_chart_color3', '#d9d9d9'),
			$this->params->get('pie_chart_color4', '#3d3d3d'),
			$this->params->get('pie_chart_color5', '#797979'),
			$this->params->get('pie_chart_color6', '#595959'),
			$this->params->get('pie_chart_color7', '#e5e5e5'),
			$this->params->get('pie_chart_color8', '#828282'),
			$this->params->get('pie_chart_color9', '#404040'),
			$this->params->get('pie_chart_color10', '#3a3a3a'),
		);

		$json = new stdClass;

		$database = App::get('db');

		$tid = $this->getTid($id, $datetime, $period);

		$orgs = $this->getTopValue($id, 3, $tid, $datetime, $period);
		$r = array();
		if ($orgs)
		{
			$i = 0;
			foreach ($orgs as $row)
			{
				$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
				if ($row->datetime && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $row->datetime, $regs))
				{
					$ky = $regs[1] . '/' . $regs[2] . '/01'; //mktime($regs[4], $regs[5], $regs[6], , $regs[3], );
				}
				if (!isset($r[$ky]))
				{
					$i = 0;
					$r[$ky] = array();
				}

				if (!isset($colors[$i]))
				{
					$i = 0;
				}

				$obj = new stdClass;
				$obj->label = $row->name;
				$obj->data  = (int) $row->value;
				$obj->color = $colors[$i];
				$obj->code  = '';

				$r[$ky][] = $obj; //'{label: \'' . addslashes($row->name) . '\', data: ' . number_format($row->value) . ', color: \'' . $colors[$i] . '\'}';
				$i++;
			}
		}
		$json->orgs = $r;

		$countries = $this->getTopValue($id, 1, $tid, $datetime, $period);
		$r = array();
		if ($countries)
		{
			$names = array();
			foreach ($countries as $row)
			{
				$names[] = $row->name;
			}

			$codes = \Hubzero\Geocode\Geocode::getCodesByNames($names);

			$i = 0;
			foreach ($countries as $row)
			{
				$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
				if ($row->datetime && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $row->datetime, $regs))
				{
					$ky = $regs[1] . '/' . $regs[2] . '/01'; //mktime($regs[4], $regs[5], $regs[6], , $regs[3], );
				}

				if (!isset($r[$ky]))
				{
					$i = 0;
					$r[$ky] = array();
				}

				if (!isset($colors[$i]))
				{
					$i = 0;
				}

				$obj = new stdClass;
				$obj->label = $row->name;
				$obj->data  = (int) $row->value;
				$obj->color = $colors[$i];
				$obj->code  = (isset($codes[$row->name]) ? strtolower($codes[$row->name]['code']) : '');

				$r[$ky][] = $obj; //'{label: \'' . addslashes($row->name) . '\', data: ' . number_format($row->value) . ', color: \'' . $colors[$i] . '\'}';
				$i++;
			}
		}
		$json->countries = $r;

		$domains = $this->getTopValue($id, 2, $tid, $datetime, $period);
		$r = array();
		if ($domains)
		{
			$i = 0;
			foreach ($domains as $row)
			{
				$ky = str_replace('-', '/', str_replace('-00 00:00:00', '-01', $row->datetime));
				if ($row->datetime && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $row->datetime, $regs))
				{
					$ky = $regs[1] . '/' . $regs[2] . '/01'; //mktime($regs[4], $regs[5], $regs[6], , $regs[3], );
				}
				if (!isset($r[$ky]))
				{
					$i = 0;
					$r[$ky] = array();
				}

				if (!isset($colors[$i]))
				{
					$i = 0;
				}

				$obj = new stdClass;
				$obj->label = $row->name;
				$obj->data  = (int) $row->value;
				$obj->color = $colors[$i];
				$obj->code  = '';

				$r[$ky][] = $obj; //'{label: \'' . addslashes($row->name) . '\', data: ' . number_format($row->value) . ', color: \'' . $colors[$i] . '\'}';
				$i++;
			}
		}
		$json->domains = $r;

		ob_clean();

		echo json_encode($json);
		die();
	}
}

