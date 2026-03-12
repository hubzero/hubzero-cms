<?php
/**
 * BasicQueryAdapter — No-op query adapter for the "basic" search engine.
 *
 * The "basic" engine was the original HubZero search implementation that
 * searched directly via component plugins using
 * Components\Search\Models\Basic\Result\Set.  That approach does not use
 * the adapter pattern, so this stub exists solely to prevent a fatal error
 * when the engine config is set to "basic" and code instantiates
 * \Hubzero\Search\Query.
 *
 * @package    framework
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Search\Adapters;

use Hubzero\Search\QueryInterface;

class BasicQueryAdapter implements QueryInterface
{
    protected $terms   = '';
    protected $limit   = 10;
    protected $offset  = 0;
    protected $results = [];
    protected $numFound = 0;

    public function __construct($config = null)
    {
        // No external service to connect to
    }

    public function getSuggestions($terms)
    {
        return [];
    }

    public function query($terms)
    {
        $this->terms = $terms;
        return $this;
    }

    public function fields($fields)
    {
        return $this;
    }

    public function addFilter($name, $query = [])
    {
        return $this;
    }

    public function addFacet($name, $query = [])
    {
        return $this;
    }

    public function getFacetCount($name)
    {
        return 0;
    }

    public function limit($limit)
    {
        $this->limit = (int) $limit;
        return $this;
    }

    public function getResults()
    {
        return $this->results;
    }

    public function getNumFound()
    {
        return $this->numFound;
    }

    public function start($start)
    {
        $this->offset = (int) $start;
        return $this;
    }

    public function sortBy($field, $direction)
    {
        return $this;
    }

    public function run()
    {
        return $this;
    }

    public function getDebug()
    {
        return (object) ['message' => 'Basic engine: no external search service configured'];
    }

    public function restrictAccess()
    {
        return $this;
    }

    /**
     * Return a no-op multi-facet query object.
     *
     * The Solr adapter returns a Solarium facet helper here; the basic
     * engine has no faceting, so we return a tiny stub whose createQuery()
     * method is a silent no-op.
     *
     * @param  string $name
     * @return object
     */
    public function getFacetMultiQuery($name)
    {
        return new class {
            public function createQuery()
            {
                // no-op
            }
        };
    }
}
