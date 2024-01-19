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

require_once Component::path('com_search') . '/models/solr/searchcomponent.php';

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
				'label'  => Lang::txt('PLG_CRON_SEARCH_RUN_FULL_INDEX'),
				'params' => ''
			)
		);

		return $obj;
	}

	/**
	 * Fully reindex all currently indexed components
	 *
	 * @museDescription  Reindex all indexed components
	 *
	 * Modeled on the searchable controller: com_search/admin/controllers/searchable.php:activateIndexTask()
	 *
	 * Database table is: jos_solr_search_searchcomponents
	 *
	 * Clearly, the admin search view uses solarium, not our db table, for its counts:
	 * The 'indexed' (datetime) and 'indexed_records' (record count) contents are untrue.
	 * Do not use the 'custom' db field for arbitrary contents.
	 *
	 * Hubzero com_cron expects TRUE returned from this function.
	 *
	 * @return  bool
	 */
	public function runFullIndex()
	{
		// Start indexing at first record:
		$offset = 0;

		// For all currently indexed Searchable components:
		$components = SearchComponent::all()
			->whereEquals('state', SearchComponent::STATE_INDEXED)
			->rows();

		foreach ($components as $component)
		{
			$componentModel = $component->getSearchableModel();
			if (class_exists($componentModel))
			{
				$recordsIndexed = $component->indexSearchResults($offset);
			}
			else
			{
				$recordsIndexed = null;
			}

			// If the indexing process is complete:
			if (!$recordsIndexed)
			{
				// update the solr search table's state field for the component,
				$component->set('state', SearchComponent::STATE_INDEXED);

				// JMS has added these to hopefully update the db record:
				// Note: this is unsuccessful, TODO, investigate component.
				// Also TODO: use local time?
				$component->set('indexed', Date::of()->toSql());
				$component->set('indexed_records', $component->getSearchCount());
			}
			// If the indexing process threw an error:
			elseif (isset($recordsIndexed['error']))
			{
				// TODO: log me
				$error = $recordsIndexed['error'];
			}
			// If the indexing process returns null, or an in-process count,
			// or it's still running:
			else
			{
				// as in the searchable controller:
				if (isset($recordsIndexed['offset']))
				{
					$component->set('indexed_records', $recordsIndexed['offset']);
				}

				// current state of indexing is incomplete:
				$recordsIndexed['state'] = SearchComponent::STATE_NOTINDEXED;
			}
			$component->save();
		}
		// cron expects 'true' retval.
		return true;
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
