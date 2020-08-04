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
	 * @return array
	 */
	public static function searchResults($offset, $limit);

	/*
	 * Grab total of objects that will be indexed during full index process
	 * @return int
	 */
	public static function searchTotal();
}
