<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search;

/**
 * QueryInterface - Interface for Query Adapters
 *
 */
interface QueryInterface
{
	/**
	 * getSuggestions  - Returns an array of suggested terms given terms
	 *
	 * @param mixed $terms
	 * @access public
	 * @return void
	 */
	public function getSuggestions($terms);

	/**
	 * query - Sets the query string
	 *
	 * @param mixed $terms
	 * @access public
	 * @return void
	 */
	public function query($terms);

	/**
	 * fields - Sets the fields to be returned by the query.
	 *
	 * @param  array $fields
	 * @access public
	 * @return void
	 */
	public function fields($fields);

	/**
	 * addFilter - Adds a filter to the query
	 *
	 * @param mixed $name
	 * @param array $query
	 * @access public
	 * @return void
	 */
	public function addFilter($name, $query = array());

	/**
	 * addFacet - Adds a facet to the query object.
	 *
	 * @param string $name - Used to identify facet when result is returned.
	 * @param array $query - The query array with a indexes of name, operator, and value
	 * @access public
	 * @return void
	 */
	public function addFacet($name, $query = array());

	/**
	 * getFacetCount
	 *
	 * @param mixed $name - Returns an integer value of a defined facet.
	 * @access public
	 * @return void
	 */
	public function getFacetCount($name);

	/**
	 * limit - Set the number of results to be returned
	 *
	 * @param int $limit
	 * @access public
	 * @return void
	 */
	public function limit($limit);

	/**
	 * getResults  - Executes the query and returns an array of results.
	 *
	 * @access public
	 * @return void
	 */
	public function getResults();

	/**
	 * getNumFound - Returns the total number of matching results, even outside of limit.
	 *
	 * @access public
	 * @return void
	 */
	public function getNumFound();

	/**
	 * start - Offset of search index results. Warning: non-deterministic.
	 *
	 * @param mixed $start
	 * @access public
	 * @return void
	 */
	public function start($start);

	/**
	 * sortBy - Order results by a field in a given direction.
	 *
	 * @param mixed $field  name of a field
	 * @param mixed $direction  (ASC or DESC)
	 * @access public
	 * @return void
	 */
	public function sortBy($field, $direction);

	/**
	 * run  - Performs the query, does not return results.
	 *
	 * @access public
	 * @return void
	 */
	public function run();

	/**
	 * restrictAccess - Applies CMS permissions for the current user.
	 *
	 * @access public
	 * @return void
	 */
	public function restrictAccess();

}
