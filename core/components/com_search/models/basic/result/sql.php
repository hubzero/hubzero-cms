<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Result as SearchResult;
use Exception;
use Hubzero\Facades\App;

/**
 * Search result SQL
 */
class Sql extends SearchResult
{
    /**
     * @var  string|null
     */
    protected $sql;

    /**
     * Constructor
     *
     * @param   string|null  $sql
     * @return  void
     */
    public function __construct($sql = null)
    {
        $this->sql = $sql;
    }

    /**
     * Get the SQL
     *
     * @return  string
     */
    public function getSql()
    {
        return (string) $this->sql;
    }

    /**
     * Return results as associative array
     *
     * @return  AssocList
     * @throws  Exception
     */
    public function toAssociative()
    {
        $db = App::get('db');
        $db->setQuery((string) $this->sql);

        if (!($rows = $db->loadAssocList())) {
            $rows = [];
        }

        return new AssocList($rows, $this->getPlugin());
    }
}
