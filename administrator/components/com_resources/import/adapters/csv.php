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

namespace Resources\Import\Adapters;

// include csv iterator
require_once JPATH_COMPONENT_ADMINISTRATOR . DS . 'import' . DS . 'iterators' . DS . 'csv.php';

/**
 * Xml Resource Importer
 */
class Csv implements \Resources\Import\Interfaces\Adapter
{
	/**
	 * Field Delimiter
	 * @var string
	 */
	private $delimiter = ',';

	/**
	 * Array to hold processed data
	 * @var array
	 */
	private $data = array();

	/**
	 * Integer to hold count
	 * @var int
	 */
	private $data_count = 0;

	/**
	 * Does this adapter respond to a mime type
	 *
	 * @access public
	 * @param  Mime type string
	 */
	public static function accepts($mime)
	{
		$acceptable = array('text/plain', 'text/csv', 'application/vnd.ms-excel');
		return in_array($mime, $acceptable);
	}

	/**
	 * Count Import data
	 *
	 * @access public
	 * @return int
	 */
	public function count(\Resources\Model\Import $import)
	{
		// create iterator
		$iterator = new \Resources\Import\Iterators\Csv($import->getDatapath(), $this->delimiter);

		// iterate over each row
		foreach ($iterator as $row => $data)
		{
			// if we got back null for a row dont count
   			if ($data !== null)
   			{
   				$this->data_count++;
   			}
		}

		// return count
		return $this->data_count;
	}

	/**
	 * Process Import data
	 *
	 * @access public
	 * @param  Closure Object
	 */
	public function process(\Resources\Model\Import $import, array $callbacks, $dryRun)
	{
		// create new xml reader
		$iterator = new \Resources\Import\Iterators\Csv($import->getDatapath(), $this->delimiter);

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

			// convert to resource objects
			$resource = new \Resources\Model\Import\record($record, $options->toArray(), $mode);

			// do we have a post map callback ?
			$resource = $this->map($resource, $callbacks['postmap'], $dryRun);

			// run resource check & store
			$resource->check()->store($dryRun);

			// do we have a post convert callback ?
			$resource = $this->map($resource, $callbacks['postconvert'], $dryRun);

			// add to data array
			array_push($this->data, $resource);

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