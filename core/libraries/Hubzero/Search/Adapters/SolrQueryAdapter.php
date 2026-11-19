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
use Hubzero\Facades\Event;
use Hubzero\Facades\Request;
use Hubzero\Facades\User;

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
     * @var  mixed
     */
    public $logPath = null;

    /**
     * @var  \Solarium\Client
     */
    public $connection;

    /**
     * @var  array<string, mixed>|null
     */
    public $config = null;

    /**
     * @var  \Solarium\QueryType\Select\Query\Query
     */
    public $query;

    /**
     * @var  mixed
     */
    public $adapter = null;

    /**
     * @var  mixed
     */
    public $resultset = null;

    /**
     * @var  mixed
     */
    public $numFound = null;

    /**
     * @var  array<mixed>|null
     */
    public $results = null;

    /**
     * @var  mixed
     */
    public $resultsFacetSet = null;

    /**
     * @var  \Solarium\Component\Debug|bool  The query's debug component once run; false before
     */
    public $debug = false;

    /**
     * @var  mixed
     */
    public $facetSet = null;

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
        $core = $config->get('solr_core', 'hubzero-solr-core');
        $port = $config->get('solr_port', '2090');
        $host = $config->get('solr_host', 'localhost');
        $path = $config->get('solr_path', '/');
        $context = $config->get('solr_context', 'solr');

        $this->logPath = $config->get('solr_log_path');

        // Build the Solr config object
        $solrConfig = [
            'endpoint' => [
                $core => [
                    'host' => $host,
                    'port' => $port,
                    'path' => $path,
                    'context' => $context,
                    'core' => $core,
                ],
            ],
        ];

        // Create the client
        $adapter = new Solarium\Core\Client\Adapter\Curl();
        $eventDispatcher = new \Symfony\Component\EventDispatcher\EventDispatcher();

        $this->connection = new Solarium\Client($adapter, $eventDispatcher, $solrConfig);

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
     * @param   mixed  $terms
     * @return \Solarium\QueryType\Select\Result\Result  the result set
     */
    public function getMoreLikeThis($terms)
    {
        // Get morelikethis settings
        $mltQuery = $this->connection->createSelect();
        $mltQuery
            ->setQuery($terms)
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
     * @return mixed  the spellcheck results
     */
    public function spellCheck($terms)
    {
        // Set the spellCheck Query
        $scQuery = $this->connection->createSelect();
        $scQuery
            ->setRows(0)
            ->getSpellcheck()
            ->setQuery($terms)
            ->setCount(5);
        // This executes the query and returns the result
        $spellcheckResults = $this->connection->select($scQuery)->getSpellcheck();

        return $spellcheckResults;
    }

    /**
     * getSuggestions Returns indexed terms
     *
     * @param mixed $terms
     * @access public
     * @return array<mixed>
     */
    public function getSuggestions($terms)
    {
        // The endpoint is keyed by the configured core name
        $endpoints = $this->config['endpoint'] ?? [];
        $config = reset($endpoints);

        // Talk to the configured Solr host, not to whatever host this request named
        $url = 'http://' . $config['host'];

        // Use the correct port
        $url .= ':' . $config['port'];

        // Use the correct core
        $url .= '/solr/' . $config['core'];

        // Perform a select operation
        $url .= '/select?fl=id';

        // Derive user permission filters
        $this->restrictAccess();
        $userPerms = $this->query->getFilterQuery('userPerms')?->getQuery();
        $url .= '&fq=' . urlencode((string) $userPerms);

        // Limit rows, not interested in results, just facets
        $url .= '&rows=0';

        // Select all, honestly doesn't matter
        $url .= '&q=*:*';

        // Enable Facets, set the mandatory field
        $url .= '&facet=true&facet.field=author_auto&facet.field=tags_auto&facet.field=title_auto';

        // Set the minimum count, could tweak to only most popular things
        $url .= '&facet.mincount=1';

        //  The actual searching part
        $url .= '&facet.prefix=' . urlencode(strtolower($terms));

        // Make it JSON
        $url .= '&wt=json';

        $client = new \GuzzleHttp\Client();
        $res = $client->get($url);
        $body = json_decode((string) $res->getBody(), true);
        $resultSet = isset($body['facet_counts']['facet_fields']) ? $body['facet_counts']['facet_fields'] : [];

        $suggestions = [];

        foreach ($resultSet as $results) {
            $x = 0;
            foreach ($results as $i => $result) {
                if ($i % 2 == 0) {
                    // Prevents too many results from being suggested
                    if ($x >= 10) {
                        break;
                    }
                    array_push($suggestions, $result);
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
     * @return static
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
     * @return static
     */
    public function run()
    {
        $this->resultset = $this->connection->select($this->query);
        $this->numFound = $this->resultset->getNumFound();
        $this->results = $this->getResults();
        $this->resultsFacetSet = $this->resultset->getFacetSet();
        $this->debug = $this->query->getDebug();
        return $this;
    }

    /**
     * getDebug
     *
     * returns Solarium query debug object
     *
     * @access public
     * @return mixed
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * getNumFound
     *
     * @access public
     * @return mixed
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
     * @return mixed
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
     * @return \Solarium\Component\Facet\MultiQuery
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
     * @param array<mixed> $query
     * @access public
     * @return static
     */
    public function addFacet($name, $query = [])
    {
        $this->facetSet = $this->query->getFacetSet();

        $string = $this->makeQueryString($query);
        $this->facetSet->createFacetQuery($name)->setQuery((string) $string);

        return $this;
    }

    /**
     * addFilter
     *
     * @param mixed $name
     * @param mixed $query
     * @param   string|array<string>  $tag  The filter's tag, or tags
     * @access public
     * @return static
     */
    public function addFilter($name, $query = [], $tag = 'root_type')
    {
        $string = null;

        if (is_array($query)) {
            $string = $this->makeQueryString($query);
        } elseif (is_string($query)) {
            $string = $query;
            if ($name == 'BoundingBox') {
                $this->query->setOptions(['geo' => true]);
            }
        }
        $filterParams = [];
        $filterParams['key'] = $name;
        $filterParams['query'] = $string;
        if ($tag) {
            $filterParams['local_tag'] = $tag;
        }

        $this->query->createFilterQuery($filterParams);
        return $this;
    }

    /**
     * fields
     *
     * @param mixed $fieldArray
     * @access public
     * @return static
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
     * @return static
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
     * @return static
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
     * @return static
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

    /**
     * @return  string
     */
    public function getAccessString()
    {
        $accessFilter = '';
        if (User::isGuest()) {
            $accessFilter = "(access_level:public)";
        } else {
            $user = User::get('id');
            $userFilter = 'OR (access_level:private AND owner_type:user AND owner:' . $user . ')';
            $accessFilter = "(access_level:public) OR (access_level:registered) " . $userFilter;

            $userGroups = \Hubzero\User\Helper::getGroups($user);
            if (!$userGroups) {
                $userGroups = [];
            }
            $userGroups = array_map(function ($group) {
                return $group->gidNumber;
            }, $userGroups);
            $userGroups = array_unique($userGroups);
            if (!empty($userGroups)) {
                $userGroupString = implode(' OR ', $userGroups);
                $groupFilter = 'OR (access_level:private AND owner_type:group AND owner:(' . $userGroupString . '))';
                $accessFilter .= ' ' . $groupFilter;
            }

            $addon = Event::trigger('search.onAddPermissionSet');
            foreach ($addon as $add) {
                $accessFilter .= $add;
            }
        }
        return $accessFilter;
    }

    /**
     * getResults
     *
     * @access public
     * @return array<mixed>
     */
    public function getResults()
    {
        if (!isset($this->resultset)) {
            $this->run();
        }

        $documents = [];
        foreach ($this->resultset as $document) {
            array_push($documents, $document);
        }

        foreach ($documents as &$document) {
            $document = $document->getFields();
        }

        return $documents;
    }

    /**
     * makeQueryString
     *
     * @param array<mixed> $query
     * @access private
     * @return string|null  Null for an operator other than '='
     */
    private function makeQueryString($query = [])
    {
        $subject = $query[0];
        $operator = $query[1];
        $operand = $query[2];

        if ($operator == '=') {
            return $subject . ':' . $operand;
        }

        return null;
    }

    /**
     * lastInsert - Returns the timestamp of the latest indexed document
     *
     * @access public
     * @return mixed
     */
    public function lastInsert()
    {
        $query = $this->connection->createSelect();
        $query->setQuery('*:*');
        $query->setFields(['timestamp']);
        $query->addSort('timestamp', 'DESC');
        $query->setRows(1);
        $query->setStart(0);

        $results = $this->connection->select($query);
        foreach ($results as $document) {
            foreach ($document as $field => $value) {
                $result = $value;
                return $result;
            }
        }

        return null;
    }
}
