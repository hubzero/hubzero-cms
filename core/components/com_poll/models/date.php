<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Poll\Models;

use Hubzero\Database\Relational;

/**
 * Poll date model
 */
class Date extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     */
    protected $namespace = 'poll';

    /**
     * Default order by for model
     *
     * @var  string
     */
    public $orderBy = 'id';

    /**
     * Default order direction for select queries
     *
     * @var  string
     */
    public $orderDir = 'asc';

    /**
     * Fields and their validation criteria
     *
     * @var  array<string, string|callable|array<string, mixed>>
     */
    protected $rules = [
        'vote_id' => 'positive|nonzero',
        'poll_id' => 'positive|nonzero',
    ];

    /**
     * Defines a belongs to one relationship between option and poll
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function poll()
    {
        return $this->belongsToOne('Poll', 'poll_id');
    }
}
