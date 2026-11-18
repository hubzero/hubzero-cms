<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Adapter;

use Hubzero\Content\Import\Adapter;
use Hubzero\Content\Import\Model\Import;
use Hubzero\Content\Import\Adapter\Json\Reader;
use Hubzero\Html\Parameter;

/**
 * JSON Importer
 */
class Json implements Adapter
{
    /**
     * Array to hold processed data
     *
     * @var  array<mixed>
     */
    private $data = [];

    /**
     * Integer to hold count
     *
     * @var  int
     */
    private $data_count = 0;

    /**
     * Does this adapter respond to a mime type
     *
     * @param   string $mime  Mime type
     * @return  bool
     */
    public static function accepts($mime)
    {
        return in_array($mime, [
            'json',
            'text/x-json',
            'text/json',
            'application/json',
        ]);
    }

    /**
     * Count Import data
     *
     * @param   \Hubzero\Content\Import\Model\Import $import
     * @return  int
     */
    public function count(Import $import)
    {
        // create iterator
        $iterator = new Reader($import->getDatapath());

        // iterate over each row
        $this->data_count = iterator_count($iterator);

        // return count
        return $this->data_count;
    }

    /**
     * Get a list of headers
     *
     * @param   \Hubzero\Content\Import\Model\Import $import
     * @return  array<int, mixed>
     */
    public function headers(Import $import)
    {
        // create iterator
        $iterator = new Reader($import->getDataPath());
        $iterator->rewind();

        $headers = [];

        $row = $iterator->current();
        foreach ((array) $row as $key => $val) {
            $headers[] = $key;
        }

        // return count
        return $headers;
    }

    /**
     * Process Import data
     *
     * @param   \Hubzero\Content\Import\Model\Import $import     Import record
     * @param   array<mixed>   $callbacks  Array of Callbacks
     * @param   bool    $dryRun     Dry Run mode?
     * @return  array<mixed>
     */
    public function process(Import $import, array $callbacks, $dryRun)
    {
        // create new iterator
        $iterator = new Reader($import->getDatapath());

        // get the import params
        $options = new Parameter($import->get('params'));

        // get the mode
        $mode = $import->get('mode', 'UPDATE');

        // loop through each item
        foreach ($iterator as $index => $record) {
            // make sure we have a record
            if ($record === null) {
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
            $import->currentRun()->processed(1);
        }

        return $this->data;
    }

    /**
     * Run Callbacks on Record
     *
     * @template T of object
     * @param   T  $record    Resource Record (each callback returns it)
     * @param   array<mixed>   $callbacks Array of Callbacks
     * @param   bool    $dryRun    Dry Run mode?
     * @return  T  Record object
     */
    public function map($record, $callbacks, $dryRun)
    {
        foreach ($callbacks as $callback) {
            if (is_callable($callback)) {
                $record = $callback($record, $dryRun);
            }
        }

        return $record;
    }
}
