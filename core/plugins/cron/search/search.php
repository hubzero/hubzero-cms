<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.	If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package	 hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license	 http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
use Hubzero\Search\Query;
use Hubzero\Search\Index;

/**
 * Cron plugin for support tickets
 */
class plgCronSearch extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return	array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'	 => 'processQueue',
				'label'	=> Lang::txt('PLG_CRON_SEARCH_PROCESS_QUEUE'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Process the index queue
	 *
	 * @museDescription  Processes the index queue
	 *
	 * @return  void
	 **/
	public function processQueue()
	{
		require_once PATH_CORE . DS . 'components' . DS .'com_search' . DS . 'models' . DS . 'indexqueue.php';
		require_once PATH_CORE . DS . 'components' . DS .'com_search' . DS . 'models' . DS . 'blacklist.php';

		// Get the type needed to be indexed;
		$items = \Components\Search\Models\QueueDB::all()
			->where('status', '=', 0)
			->limit(100)
			->rows();

		// Refresh indexed material if no work to do
		if ($items->count() <= 0)
		{
			$items = \Components\Search\Models\QueueDB::all()
				->where('status', '=', 1)
				->where('action', '=', 'index')
				->order('modified', 'ASC')
				->limit(100)
				->rows();
		}

		// Get the blacklist
		$sql = "SELECT doc_id FROM #__search_blacklist;";
		$db = App::get('db');
		$db->setQuery($sql);
		$blacklist = $db->query()->loadColumn();

		foreach ($items as $item)
		{
			$format = Event::trigger('search.onIndex', array($item->type, $item->type_id, true));
			if (isset($format[0]))
			{
				$this->processRows($format[0], $item->action, $blacklist);

				$timestamp = \Hubzero\Utility\Date::of()->toSql();
				$item->set('modified', $timestamp);
				$item->set('status', 1);
			}
			else
			{
				$item->set('status', '2');
			}

			$item->save();
		}
	}

	/**
	 * processRows - Fires plugin events to facilitate indexing data 
	 * 
	 * @param mixed $item 
	 * @access private
	 * @return void
	 */
	private function processRows($item, $action, $blacklist)
	{
		$config = Component::params('com_search');
		$index = new Index($config);

		if ($action == 'index' && !in_array($item->id, $blacklist))
		{
			// @TODO Fix the Solr schema to use 'path' instead of 'url' 
			if (isset($item->path))
			{
				$item->url = $item->path;
				unset($item->path);
			}
			$index->index($item);
		}
		elseif ($action == 'delete' || in_array($item->id, $blacklist))
		{
			$index->delete($item->id);
		}
	}
} //end plgCronSearch
