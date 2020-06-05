<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Import\Adapters;

// include xml iterator
require_once dirname(__DIR__) . DS . 'iterators' . DS . 'xml.php';

/**
 * Xml Resource Importer
 */
class Xml implements \Components\Resources\Import\Interfaces\Adapter
{
	/**
	 * XML key that holds each resource item
	 *
	 * @var  string
	 */
	private $key = 'record';

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
	 * @param   string  $mime  Mime type string
	 * @return  bool
	 */
	public static function accepts($mime)
	{
		return $mime == 'application/xml' ? true : false;
	}

	/**
	 * Count Import data
	 *
	 * @param   object  $import
	 * @return  int
	 */
	public function count(\Components\Resources\Models\Import $import)
	{
		// instantiate iterator
		$xmlIterator = new \Components\Resources\Import\Iterators\Xml($import->getDatapath(), $this->key);

		// count records
		$this->data_count = iterator_count($xmlIterator);

		// return count
		return $this->data_count;
	}

	/**
	 * Process Import data
	 *
	 * @param   object   $import
	 * @param   array    $callbacks
	 * @param   integer  $dryrun
	 * @return  array
	 */
	public function process(\Components\Resources\Models\Import $import, array $callbacks, $dryRun)
	{
		// create new xml reader
		$iterator = new \Components\Resources\Import\Iterators\Xml($import->getDataPath(), $this->key);

		// get the import params
		$options = new \Hubzero\Config\Registry($import->get('params'));

		// get the mode
		$mode = $import->get('mode', 'UPDATE');

		// loop through each item
		foreach ($iterator as $index => $record)
		{
			// do we have a post parse callback ?
			$record = $this->map($record, $callbacks['postparse'], $dryRun);

			// convert to resource objects
			$resource = new \Components\Resources\Models\Import\Record($record, $options->toArray(), $mode);

			// do we have a post map callback ?
			$resource = $this->map($resource, $callbacks['postmap'], $dryRun);

			// run resource check & store
			if (!$dryRun)
			{
				$resource->check()->store($dryRun);
			}

			// do we have a post convert callback ?
			$resource = $this->map($resource, $callbacks['postconvert'], $dryRun);
			$resource->record->resource = $resource->record->resource->toObject();
			$resource->record->type = $resource->record->type->toObject();

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
	 * @param   object  $record     Resource Record
	 * @param   array   $callbacks  Array of Callbacks
	 * @param   bool    $dryRun     Dry Run mode?
	 * @return  object              Record object
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
