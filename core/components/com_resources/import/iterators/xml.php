<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Import\Iterators;

/**
 *  XML Reader Iterator Class implemeting interator
 *
 * @implements \Iterator<int, \stdClass|null>
 */
class Xml implements \Iterator
{
    /**
     * @var  string
     */
    private $file;
    /**
     * @var  string
     */
    private $key;
    /**
     * @var  \XMLReader
     */
    private $reader;
    /**
     * @var  int
     */
    private $position;

    /**
     * XML Reader Iterator Constructor
     *
     * @param  string  $file  XML file we want to use
     * @param  string  $key   XML node we are looking to iterate over
     */
    public function __construct($file, $key)
    {
        $this->reader   = new \XMLReader();
        $this->position = 0;
        $this->file     = $file;
        $this->key      = $key;
    }

    /**
     * Get the current XML node
     *
     * @return  \stdClass|null  XML node as a stdClass; null when it cannot be read
     */
    #[\ReturnTypeWillChange]
    public function current()
    {
        $node = $this->reader->expand();
        if ($node === false) {
            return null;
        }

        $doc = new \DOMDocument();
        $object = simplexml_import_dom($doc->importNode($node, true));
        $record = json_decode((string) json_encode($object));

        return $record instanceof \stdClass ? $record : null;
    }

    /**
     * Get our current position while iterating
     *
     * @return  int  Current position
     */
    #[\ReturnTypeWillChange]
    public function key()
    {
        return $this->position;
    }

    /**
     * Go to the next Node that matches our key
     *
     * @return  void
     */
    #[\ReturnTypeWillChange]
    public function next()
    {
        if ($this->reader->next($this->key)) {
            ++$this->position;
        }
    }

    /**
     * Move to the first node that matches our key
     *
     * @return  void
     */
    #[\ReturnTypeWillChange]
    public function rewind()
    {
        // open file with reader
        // force UTF-8, validate XML, & substitute entities while reading
        $this->reader->open($this->file, 'UTF-8', LIBXML_NONET);

        // fast forward to first record
        while ($this->reader->read() && $this->reader->name !== $this->key) {
            // Um...
        }
    }

    /**
     * Is our current node valid
     *
     * @return  bool  Is valid?
     */
    #[\ReturnTypeWillChange]
    public function valid()
    {
        return $this->reader->name === $this->key;
    }
}
