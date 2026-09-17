<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search;

/**
 * Interface for Resource Importer
 */
interface Searchable
{
    /*
     * The hubtype of the searchable item.
     * @return string
     */
    public static function searchNamespace();

    /*
     * Generate a unique Id for solr to use.
     * @return string
     */
    public function searchId();

    /*
     * Convert object into solr searchable document.
     * @return stdClass
     */
    public function searchResult();

    /*
     * Provide batch of solr documents to be indexed.
     *
     * The limit comes first. Every implementation in the tree and both
     * callers have always passed it that way; the declaration said the
     * opposite, which cost nothing while the arguments were positional and
     * would have indexed the wrong window the moment somebody named them.
     *
     * @return array
     */
    public static function searchResults($limit, $offset = 0);

    /*
     * Grab total of objects that will be indexed during full index process
     * @return int
     */
    public static function searchTotal();
}
