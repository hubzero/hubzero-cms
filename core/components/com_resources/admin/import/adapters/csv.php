<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Import\Adapters;

// include csv iterator
require_once dirname(__DIR__) . DS . 'iterators' . DS . 'csv.php';

/**
 * Xml Resource Importer
 */
class Csv implements \Components\Resources\Import\Interfaces\Adapter
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
	public function count(\Components\Resources\Models\Import $import)
	{
		// create iterator
		$iterator = new \Components\Resources\Import\Iterators\Csv($import->getDatapath(), $this->delimiter);

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
	public function process(\Components\Resources\Models\Import $import, array $callbacks, $dryRun)
	{
		// create new xml reader
		$iterator = new \Components\Resources\Import\Iterators\Csv($import->getDatapath(), $this->delimiter);

		// get the import params
		$options = new \Hubzero\Config\Registry($import->get('params'));

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
			$resource = new \Components\Resources\Models\Import\Record($record, $options->toArray(), $mode);

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