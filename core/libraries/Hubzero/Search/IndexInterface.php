<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search;

/**
 * IndexInterface  - The interface for Index Adapters
 *
 */
interface IndexInterface
{
    /**
     * getLogs - Returns an array of search engine query log entries
     *
     * @access public
     * @return void
     */
    public function getLogs();

    /**
     * lastInsert - Returns the timestamp of the last document indexed
     *
     * @access public
     * @return void
     */
    public function lastInsert();

    /**
     * status - Checks whether or not the search engine is responding
     *
     * @access public
     * @return void
     */
    public function status();

    /**
     * index - Stores a document within an index
     *
     * @param mixed $document
     * @access public
     * @return void
     */
    public function index($document);

    /**
     * delete
     *
     * @param string $id
     * @return void
     */
    public function delete($id);
}
