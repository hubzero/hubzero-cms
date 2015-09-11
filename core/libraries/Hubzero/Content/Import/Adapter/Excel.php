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

namespace Hubzero\Content\Import\Adapter;

use Hubzero\Content\Import\Adapter;
use Hubzero\Content\Import\Model\Import;
use Hubzero\Content\Import\Adapter\Excel\Reader;
use Hubzero\Html\Parameter;

/**
 * Excel Importer
 */
class Excel implements Adapter
{
	/**
	 * Not really used in this case
	 *
	 * @var  integer
	 */
	private $key = '';

	/**
	 * Array to hold processed data
	 *
	 * @var  array
	 */
	private $data = array();

	/**
	 * Integer to hold count
	 *
	 * @var  int
	 */
	private $data_count = 0;

	/**
	 * Does this adapter respond to a mime type
	 *
	 * @param   string  Mime type
	 * @return  bool
	 */
	public static function accepts($mime)
	{
		return in_array($mime, array(
			'xls',
			'xlsx',
			'application/zip', // WTF?
			'application/vnd.ms-excel',
			'application/vnd.ms-excel.addin.macroEnabled.12',
			'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'application/vnd.ms-excel.sheet.macroEnabled.12',
			'application/vnd.ms-excel.template.macroEnabled.12',
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		));
	}

	/**
	 * Count Import data
	 *
	 * @param   object  $import
	 * @return  int
	 */
	public function count(Import $import)
	{
		// create iterator
		$iterator = new Reader($import->getDatapath(), $this->key);

		// return count
		return $iterator->total();
	}

	/**
	 * Get a list of headers
	 *
	 * @param   object  $import
	 * @return  array
	 */
	public function headers(Import $import)
	{
		// create iterator
		$iterator = new Reader($import->getDataPath(), $this->key);

		return $iterator->headers();
	}

	/**
	 * Process Import data
	 *
	 * @param   object  $import     Import record
	 * @param   array   $callbacks  Array of Callbacks
	 * @param   bool    $dryRun     Dry Run mode?
	 * @return  object
	 */
	public function process(Import $import, array $callbacks, $dryRun)
	{
		// create new iterator
		$iterator = new Reader($import->getDatapath(), $this->key);

		// get the import params
		$options = new Parameter($import->get('params'));

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
	 * @param   object  $record    Resource Record
	 * @param   array   $callbacks Array of Callbacks
	 * @param   bool    $dryRun    Dry Run mode?
	 * @return  object  Record object
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