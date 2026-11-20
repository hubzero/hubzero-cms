<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;

/**
 * Citation sponsor model
 *
 * @uses \Hubzero\Database\Relational
 */
class Sponsor extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     **/
    protected $namespace = 'citations';

    /**
     * Default order by for model
     *
     * @var  string
     **/
    public $orderBy = 'sponsor';

    /**
     * Fields and their validation criteria
     *
     * @var  array<string, string|callable|array<string, mixed>>
     **/
    protected $rules = [
        'name' => 'sponsor',
    ];

    /**
     * Establish relationship to citations
     *
     * @return  \Hubzero\Database\Relationship\ManyToMany
     **/
    public function citations()
    {
        return $this->manyToMany('Citation', '#__citations_sponsors_assoc', 'sid', 'cid');
    }
}
