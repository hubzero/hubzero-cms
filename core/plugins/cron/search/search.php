<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Hubzero\Search\Query;
use Hubzero\Search\Index;
use Components\Search\Models\Solr\QueueDB;
use Components\Search\Models\Solr\SearchComponent as SearchComponent;

/**
 * Cron plugin for Search indexing
 */
class plgCronSearch extends \Hubzero\Plugin\Plugin
{
	/**
	 * Return a list of events
	 *
	 * @return  array
	 */
	public function onCronEvents()
	{
		$this->loadLanguage();

		$obj = new stdClass();
		$obj->plugin = $this->_name;
		$obj->events = array(
			array(
				'name'   => 'processQueue',
				'label'  => Lang::txt('PLG_CRON_SEARCH_PROCESS_QUEUE'),
				'params' => ''
			),
			array(
				'name'	 => 'runFullIndex',
				'label'  => Lang::txt('Run Full Index'),
				'params' => ''
			)
		);

		return $obj;
	}

	public function runFullIndex()
	{
		$components = SearchComponent::all()
			->where('indexed', 'IS', null)
			->where('state', 'IS', null)
			->orWhereEquals('state', 0)
			->rows();
		$component = $components->first();
		$recordsIndexed = $component->indexSearchResults();
		if (!$recordsIndexed)
		{
			$component->set('state', 1);
			$component->set('indexed', Date::of()->toSql());
		}
		else
		{
			$component->set('indexed_records', $recordsIndexed);
		}
		$component->save();
	}

	/**
	 * Process the index queue
	 *
	 * @museDescription  Processes the index queue
	 *
	 * @return  bool
	 */
	public function processQueue()
	{
		$config = Component::params('com_search');

		// For now, we can only process the queue for Solr
		if ($config->get('engine', 'basic') != 'solr')
		{
			return true;
		}

		require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'indexqueue.php';
		require_once Component::path('com_search') . DS . 'models' . DS . 'solr' . DS . 'blacklist.php';

		// Get the type needed to be indexed;
		$items = QueueDB::all()
			->where('status', '=', 0)
			->limit(100)
			->rows();

		// Refresh indexed material if no work to do
		if ($items->count() <= 0)
		{
			$items = QueueDB::all()
				->where('status', '=', 1)
				->where('action', '=', 'index')
				->order('modified', 'ASC')
				->limit(100)
				->rows();
		}

		// Get the blacklist
		$sql = "SELECT doc_id FROM `#__search_blacklist`;";
		$db = App::get('db');
		$db->setQuery($sql);
		$blacklist = $db->query()->loadColumn();

		foreach ($items as $item)
		{
			$format = Event::trigger('search.onIndex', array($item->type, $item->type_id, true));
			if (isset($format[0]))
			{
				$this->processRows($format[0], $item->action, $blacklist);

				$timestamp = with(new \Hubzero\Utility\Date('now'))->toSql();
				$item->set('modified', $timestamp);
				$item->set('status', 1);
			}
			else
			{
				$item->set('status', 2);
			}

			$item->save();
		}

		return true;
	}

	/**
	 * processRows - Fires plugin events to facilitate indexing data 
	 * 
	 * @param   mixed    $item
	 * @param   string   $action
	 * @param   array    $blacklist 
	 * @access  private
	 * @return  void
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

			if (isset($item->title) || $item->title != '')
			{
				$index->index($item);
			}
		}
		elseif ($action == 'delete' || in_array($item->id, $blacklist))
		{
			$index->delete($item->id);
		}
	}
} //end plgCronSearch
