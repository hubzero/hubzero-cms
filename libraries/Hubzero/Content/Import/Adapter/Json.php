<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Content\Import\Adapter;

use Hubzero\Content\Import\Adapter;
use Hubzero\Content\Import\Model\Import;
use Hubzero\Content\Import\Adapter\Json\Reader;

/**
 * JSON Importer
 */
class Json implements Adapter
{
	/**
	 * Key that holds each item
	 *
	 * @var  string
	 */
	private $key = 'record';

	/**
	 * Array to hold processed data
	 *
	 * @var array
	 */
	private $data = array();

	/**
	 * Integer to hold count
	 *
	 * @var int
	 */
	private $data_count = 0;

	/**
	 * Does this adapter respond to a mime type
	 *
	 * @param  Mime type string
	 */
	public static function accepts($mime)
	{
		return in_array($mime, array(
			'json',
			'text/x-json',
			'text/json',
			'application/json'
		));
	}

	/**
	 * Count Import data
	 *
	 * @return int
	 */
	public function count(Import $import)
	{
		// create iterator
		$iterator = new Reader($import->getDatapath(), $this->key);

		// iterate over each row
		$this->data_count = iterator_count($iterator);

		// return count
		return $this->data_count;
	}

	/**
	 * Count Import data
	 *
	 * @return int
	 */
	public function headers(Import $import)
	{
		// create iterator
		$iterator = new Reader($import->getDataPath(), $this->key);
		$iterator->rewind();

		$headers = array();

		$row = $iterator->current();
		foreach ($row as $key => $val)
		{
			$headers[] = $key;
		}

		// return count
		return $headers;
	}

	/**
	 * Process Import data
	 *
	 * @param  Closure Object
	 */
	public function process(Import $import, array $callbacks, $dryRun)
	{
		// create new iterator
		$iterator = new Reader($import->getDatapath(), $this->key);

		// get the import params
		$options = new \JParameter($import->get('params'));

		// get the mode
		$mode = $import->get('mode', 'UPDATE');

		// loop through each item
		foreach ($iterator as $index => $record)
		{
			// make sure we have a record
			if ($record === null)
			{
				continue;
			}

			// do we have a post parse callback ?
			$record = $this->map($record, $callbacks['postparse'], $dryRun);

			// convert to objects
			$entry = $import->getRecord($record, $options->toArray(), $mode);

			// do we have a post map callback ?
			$entry = $this->map($entry, $callbacks['postmap'], $dryRun);

			// run check & store
			$entry->check()->store($dryRun);

			// do we have a post convert callback ?
			$entry = $this->map($entry, $callbacks['postconvert'], $dryRun);

			// add to data array
			array_push($this->data, $entry);

			// mark record processed
			$import->runs('current')->processed(1);
		}

		return $this->data;
	}

	/**
	 * Run Callbacks on Record
	 *
	 * @param  object $record    Resource Record
	 * @param  array  $callbacks Array of Callbacks
	 * @param  bool   $dryRun    Dry Run mode?
	 * @return object            Record object
	 */
	public function map($record, $callbacks, $dryRun)
	{
		foreach ($callbacks as $callback)
		{
			if (is_callable($callback))
			{
				$record = $callback($record, $dryRun);
			}
		}

		return $record;
	}
}