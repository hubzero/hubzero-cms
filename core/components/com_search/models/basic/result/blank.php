<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Result as SearchResult;
use Exception;

/**
 * Empty result
 */
class Blank extends SearchResult
{
    /**
     * Return results as associative array
     *
     * @return  never
     * @throws  Exception  An empty result has nothing to convert
     */
    public function toAssociative()
    {
        throw new Exception('empty result -> to_associative');
    }
}
