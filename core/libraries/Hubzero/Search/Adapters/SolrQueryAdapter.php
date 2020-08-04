<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search\Adapters;

use Hubzero\Search\QueryInterface;
use Solarium;
use GuzzleHttp\Client;

/**
 * SolrQueryAdapter - Adapter for Solr Querying
 *
 * @uses QueryInterface
 * @uses Solarium
 * @uses GuzzleHttp\Client
 */
class SolrQueryAdapter implements QueryInterface
{
	/**
	 * __construct
	 *
	 * @param mixed $config
	 * @access public
	 * @return void
	 */
	public function __construct($config)
	{
		// Some setup information
		$core = $config->get('solr_core');
		$port = $config->get('solr_port');
		$host = $config->get('solr_host');
		$path = $config->get('solr_path');

		$this->logPath = $config->get('solr_log_path');

		// Build the Solr config object
		$solrConfig = array( 'endpoint' =>
			array( $core  =>
				array('host' => $host,
							'port' => $port,
							'path' => $path,
							'core' => $core,
							)
						)
					);

		// Create the client
		$this->connection = new Solarium\Client($solrConfig);

		// Add plugin to accept bigger requests
		$this->connection->getPlugin('postbigrequest');

		// Make config accessible
		$this->config = $solrConfig;

		// Create the Solr Query object
		$this->query = $this->connection->createSelect();
	}

	/**
	 * Get MoreLikeThis
	 *
	 * @access public
	 * @return SolariumQuery
	 */
	public function getMoreLikeThis($terms)
	{
		// Get morelikethis settings
		$mltQuery = $this->connection->createSelect();
		$mltQuery->setQuery($terms)
			->getMoreLikeThis()
			->setFields('text');

		// Executes the query and returns the result
		$resultSet = $this->connection->select($mltQuery);
		$mlt = $resultSet->getMoreLikeThis();

		return $resultSet;
	}

	/**
	 * spellCheck Returns terms suggestions
	 *
	 * @param mixed $terms
	 * @access public
	 * @return dictionary
	 */
	public function spellCheck($terms)
	{
		// Set the spellCheck Query
	$scQuery = $this->connection->createSelect();
	$scQuery->setRows(0)
			->getSpellcheck()
			->setQuery($terms)
			->setCount('5');
		// This executes the query and returns the result
		$spellcheckResults = $this->connection->select($scQuery)->getSpellcheck();

		return $spellcheckResults;
	}

	/**
	 * getSuggestions Returns indexed terms
	 *
	 * @param mixed $terms
	 * @access public
	 * @return array
	 */
	public function getSuggestions($terms)
	{
		// Rewrite for easier keyboard typing
		$config = $this->config['endpoint']['hubsearch'];

		// Create the base URL
		$url = rtrim(Request::Root(), '/\\');

		// Use the correct port
		$url .= ':' . $config['port'];

		// Use the correct core
		$url .= '/solr/' . $config['core'];

		// Perform a select operation
		$url .= '/select?fl=id';

		// Derive user permission filters
		$this->restrictAccess();
		$userPerms = $this->query->getFilterQuery('userPerms')->getQuery();
		$url .= '&fq=' . $userPerms;

		// Limit rows, not interested in results, just facets
		$url .= '&rows=0';

		// Select all, honestly doesn't matter
		$url .= '&q=*:*';

		// Enable Facets, set the mandatory field
		$url .= '&facet=true&facet.field=author_auto&facet.field=tags_auto&facet.field=title_auto';

		// Set the minimum count, could tweak to only most popular things
		$url .= '&facet.mincount=1';

		//  The actual searching part
		$url .= '&facet.prefix=' . strtolower($terms);

		// Make it JSON
		$url .= '&wt=json';

		$client = new \GuzzleHttp\Client();
		$res = $client->get($url);
		$resultSet = $res->json()['facet_counts']['facet_fields'];

		$suggestions = array();

		foreach ($resultSet as $results)
		{
			$x = 0;
			foreach ($results as $i => $result)
			{
				if ($i % 2 == 0)
				{
					// Prevents too many results from being suggested
					if ($x >= 10)
					{
						break;
					}
					array_push( $suggestions, $result);
					$x++;
				}

			}
		}

		return $suggestions;
	}

	/**
	 * query
	 *
	 * @param mixed $terms
	 * @access public
	 * @return void
	 */
	public function query($terms)
	{
		$this->query->setQuery($terms);
		return $this;
	}

	/**
	 * run
	 *
	 * @access public
	 * @return void
	 */
	public function run()
	{
		$this->resultset = $this->connection->select($this->query);
		$this->numFound = $this->resultset->getNumFound();
		$this->results = $this->getResults();
		$this->resultsFacetSet = $this->resultset->getFacetSet();
		return $this;
	}

	/**
	 * getNumFound
	 *
	 * @access public
	 * @return void
	 */
	public function getNumFound()
	{
		return $this->numFound;
	}

	/**
	 * getFacetCount
	 *
	 * @param mixed $name
	 * @access public
	 * @return void
	 */
	public function getFacetCount($name)
	{
		$count = $this->resultset->getFacetSet()->getFacet($name)->getValue();
		return $count;
	}

	/**
	 *
	 *
	 * @param string $name name provided for the multiFacet set.
	 * @access public
	 * @return \Solarium\QueryType\Select\Query\Component\Facet\MultiQuery
	 */
	public function getFacetMultiQuery($name)
	{
		$facet = $this->query->getFacetSet()->createFacetMultiQuery($name);
		return $facet;
	}

	/**
	 * addFacet
	 *
	 * @param mixed $name
	 * @param array $query
	 * @access public
	 * @return void
	 */
	public function addFacet($name, $query = array())
	{
		$this->facetSet = $this->query->getFacetSet();

		$string = $this->makeQueryString($query);
		$this->facetSet->createFacetQuery($name)->setQuery($string);

		return $this;
	}

	/**
	 * addFilter
	 *
	 * @param mixed $name
	 * @param mixed $query
	 * @access public
	 * @return void
	 */
	public function addFilter($name, $query = array(), $tag = 'root_type')
	{
		if (is_array($query))
		{
			$string = $this->makeQueryString($query);
		}
		elseif (is_string($query))
		{
			$string = $query;
			if ($name == 'BoundingBox')
			{
				$this->query->setOptions(array('geo'=> true));
			}
		}
		$filterParams = array();
		$filterParams['key'] = $name;
		$filterParams['query'] = $string;
		if ($tag)
		{
			$filterParams['tag'] = $tag;
		}

		$this->query->createFilterQuery($filterParams);
		return $this;
	}

	/**
	 * fields
	 *
	 * @param mixed $fieldArray
	 * @access public
	 * @return void
	 */
	public function fields($fieldArray)
	{
		$this->query->setFields($fieldArray);
		return $this;
	}

	/**
	 * sortBy
	 *
	 * @param mixed $field
	 * @param mixed $direction
	 * @access public
	 * @return void
	 */
	public function sortBy($field, $direction)
	{
		$this->query->addSort($field, $direction);
		return $this;
	}

	/**
	 * limit
	 *
	 * @param mixed $limit
	 * @access public
	 * @return void
	 */
	public function limit($limit)
	{
		$this->query->setRows($limit);
		return $this;
	}

	/**
	 * start
	 *
	 * @param mixed $offset
	 * @access public
	 * @return void
	 */
	public function start($offset)
	{
		$this->query->setStart($offset);
		return $this;
	}

	/**
	 * restrictAccess
	 *
	 * @access public
	 * @return void
	 */
	public function restrictAccess()
	{
		$accessFilter = $this->getAccessString();
		$this->addFilter('userPerms', $accessFilter, 'root_type');
	}

	public function getAccessString()
	{
		$accessFilter = '';
		if (User::isGuest())
		{
			$accessFilter = "(access_level:public)";
		}
		else
		{
			$user = User::get('id');
			$userFilter = 'OR (access_level:private AND owner_type:user AND owner:' . $user . ')';
			$accessFilter = "(access_level:public) OR (access_level:registered) " . $userFilter;

			$userGroups = \Hubzero\User\Helper::getGroups($user);
			if (!$userGroups)
			{
				$userGroups = array();
			}
			$userGroups = array_map(function($group){
				return $group->gidNumber;
			}, $userGroups);
			$userGroups = array_unique($userGroups);
			if (!empty($userGroups))
			{
				$userGroupString = implode(' OR ', $userGroups);
				$groupFilter = 'OR (access_level:private AND owner_type:group AND owner:(' . $userGroupString . '))';
				$accessFilter .= ' ' . $groupFilter;
			}

			$addon = Event::trigger('search.onAddPermissionSet');
			foreach ($addon as $add)
			{
				$accessFilter .= $add;
			}
		}
		return $accessFilter;
	}

	/**
	 * getResults
	 *
	 * @access public
	 * @return void
	 */
	public function getResults()
	{
		if (!isset($this->resultset))
		{
			$this->run();
		}

		$documents = array();
		foreach ($this->resultset as $document)
		{
			array_push($documents, $document);
		}

		foreach ($documents as &$document)
		{
			$document = $document->getFields();
		}

		return $documents;
	}

	/**
	 * makeQueryString
	 *
	 * @param array $query
	 * @access private
	 * @return void
	 */
	private function makeQueryString($query = array())
	{
		$subject = $query[0];
		$operator = $query[1];
		$operand = $query[2];

		switch ($operator)
		{
			case '=':
				$string = $subject . ':' . $operand;
			break;
		}

		return $string;
	}


	/**
	 * lastInsert - Returns the timestamp of the latest indexed document
	 *
	 * @access public
	 * @return void
	 */
	public function lastInsert()
	{
		$query = $this->connection->createSelect();
		$query->setQuery('*:*');
		$query->setFields(array('timestamp'));
		$query->addSort('timestamp', 'DESC');
		$query->setRows(1);
		$query->setStart(0);

		$results = $this->connection->execute($query);
		foreach ($results as $document)
		{
			foreach ($document as $field => $value)
			{
				$result = $value;
				return $result;
			}
		}
	}
}
