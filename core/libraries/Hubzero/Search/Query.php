<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search;

use Hubzero\Search\Adapters;

/**
 * Query - Class to provide search engine query functionality
 */
class Query
{
	/**
	 * Set the adapter
	 *
	 * @param   object  $config  Configuration object
	 * @return  void
	 */
	public function __construct($config)
	{
		$engine = $config->get('engine');

		$adapter = "\\Hubzero\\Search\\Adapters\\" . ucfirst($engine) . 'QueryAdapter';

		$this->adapter = new $adapter($config);
	}

	/**
	 * Get MoreLikeThis
	 *
	 * @param   mixed  $terms
	 * @return  array
	 */
	public function getMoreLikeThis($terms)
	{
		return $this->adapter->getMoreLikeThis($terms);
	}

	/**
	 * Returns terms suggestions
	 *
	 * @param   mixed  $terms
	 * @return  array
	 */
	public function spellCheck($terms)
	{
		return $this->adapter->spellCheck($terms);
	}

	/**
	 * Returns indexed terms
	 *
	 * @param   mixed  $terms
	 * @return  array
	 */
	public function getSuggestions($terms)
	{
		return $this->adapter->getSuggestions($terms);
	}

	/**
	 * Sets the query string
	 *
	 * @param   mixed   $terms
	 * @return  object
	 */
	public function query($terms)
	{
		$this->adapter->query($terms);
		return $this;
	}

	/**
	 * Sets the fields to be returned by the query.
	 *
	 * @param   array  $fields
	 * @return  object
	 */
	public function fields($fields)
	{
		$this->adapter->fields($fields);
		return $this;
	}

	/**
	 * Adds a filter to the query
	 *
	 * @param   mixed   $name
	 * @param   array   $query
	 * @param   string  $tag
	 * @return  object
	 */
	public function addFilter($name, $query = array(), $tag = 'root_type')
	{
		$this->adapter->addFilter($name, $query, $tag);
		return $this;
	}

	/**
	 * Adds a facet to the query object.
	 *
	 * @param   string  $name   Used to identify facet when result is returned.
	 * @param   array   $query  The query array with a indexes of name, operator, and value
	 * @return  object
	 */
	public function addFacet($name, $query = array())
	{
		$this->adapter->addFacet($name, $query);
		return $this;
	}

	/**
	 * Returns an integer value of a defined facet.
	 *
	 * @param   mixed  $name
	 * @return  int
	 */
	public function getFacetCount($name)
	{
		return $this->adapter->getFacetCount($name);
	}

	/**
	 * limit - Set the number of results to be returned
	 *
	 * @param   int     $limit
	 * @return  object
	 */
	public function limit($limit)
	{
		$this->adapter->limit($limit);
		return $this;
	}

	/**
	 * Executes the query and returns an array of results.
	 *
	 * @return  array
	 */
	public function getResults()
	{
		return $this->adapter->getResults();
	}

	/**
	 * Returns the total number of matching results, even outside of limit.
	 *
	 * @return  integer
	 */
	public function getNumFound()
	{
		return $this->adapter->getNumFound();
	}

	/**
	 * Offset of search index results. Warning: non-deterministic.
	 *
	 * @param   mixed   $start
	 * @return  object
	 */
	public function start($start)
	{
		$this->adapter->start($start);
		return $this;
	}

	/**
	 * Order results by a field in a given direction.
	 *
	 * @param   mixed  $field      name of a field
	 * @param   mixed  $direction  (ASC or DESC)
	 * @return  object
	 */
	public function sortBy($field, $direction)
	{
		$this->adapter->sortBy($field, $direction);
		return $this;
	}

	/**
	 * Performs the query, does not return results.
	 *
	 * @return void
	 */
	public function run()
	{
		return $this->adapter->run();
	}

	/**
	 * Applies CMS permissions for the current user.
	 *
	 * @return  object
	 */
	public function restrictAccess()
	{
		$this->adapter->restrictAccess();
		return $this;
	}
}
