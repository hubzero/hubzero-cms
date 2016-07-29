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
		require_once PATH_CORE . DS . 'components' . DS .'com_search' . DS . 'models' . DS . 'noindex.php';

		// Get the type needed to be indexed;
		$item = \Components\Search\Models\IndexQueue::all()
			->where('complete', '=', 0)
			->where('lock', '=', 0)
			->order('created', 'ASC')
			->limit(1)
			->row();

		// Check to see if anything need to be processed
		$itemArr = $item->toArray();

		// Bail early if nothing next
		if (empty($itemArr))
		{
			// Create some work
			$item = \Components\Search\Models\IndexQueue::all()
				->where('complete', '=', 1)
				->where('lock', '=', 0)
				->order('modified', 'ASC')
				->limit(1)
				->row();

			// Prevent another process from working on it 
			$item->set('lock', 1);

			// Clear complete status
			$item->set('complete', 0);

			// Do another full-index
			$item->set('start', 0);
			$item->save();
		}

		if ($item->action == 'index')
		{
			$item->set('lock', 1);
			$item->save();
			$this->processRows($item);
		}

		// Remove lock. 
		$timestamp = \Hubzero\Utility\Date::of()->toSql();
		$item->set('modified', $timestamp);
		$item->set('lock', 0);
		$item->save();
	}

	/**
	 * processRows - Fires plugin events to facilitate indexing data 
	 * 
	 * @param mixed $item 
	 * @access private
	 * @return void
	 */
	private function processRows($item)
	{
		// @TODO dynamically determine blocksize? 
		// Size of chunk
		$blocksize = 5000;

		// Fire plugin event to get the model to process
		$models = Event::trigger('search.onGetModel', $item->hubtype);

		// We only process one model at a time
		if (count($models) > 0)
		{
			$model = $models[0];
		}
		else
		{
			$this->output->addLine('Check to see if Search - ' . $item->hubtype . ' plugin is enabled.', ['color' => 'yellow', 'format' => 'bold']);
			return false;
		}

		$total = $model->total();

		// Bail early
		if ($item->start > $total)
		{
			// Mark as complete
			$item->set('complete', 1);
			$item->save();

			// Move to next item
			$this->processQueue();
		}

		$rows = $model::all()->start($item->start)->limit($blocksize);

		// Used for ancillary querying
		$db = App::get('db');

		$config = Component::params('com_search');
		$index = new Index($config);

		// Process Rows
		foreach ($rows as $row)
		{
			// Instantiate a new Search Document
			$document = new stdClass;

			// Mandatory fields
			$document->hubid = $row->id;
			$document->hubtype = $item->hubtype;
			$document->id = $item->hubtype . '-' . $row->id;

			// Processed fields
			$processedFields = Event::trigger('search.onProcessFields', array($item->hubtype, $row, $db))[0];
			foreach ($processedFields as $key => $value)
			{
				$document->$key = $value;
			}

			// Index the document
			$index->index($document);
		} // end foreach

		// Are we done processing rows for this model?
		if ($item->get('start') + $blocksize >= $total)
		{
			$item->set('complete', 1);
			$item->set('start', $total);
		}
		else
		{
			$item->set('start', $item->start + $blocksize);
		}
		$item->save();
	} // end processRows()
} //end plgCronSearch
