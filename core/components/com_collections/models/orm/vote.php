<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Collection vote model
 */
class Vote extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     */
    protected $namespace = 'collections';

    /**
     * Default order by for model
     *
     * @var  string
     */
    public $orderBy = 'created';

    /**
     * Default order direction for select queries
     *
     * @var  string
     */
    public $orderDir = 'desc';

    /**
     * Fields and their validation criteria
     *
     * @var  array<string, string|callable|array<string, mixed>>
     */
    protected $rules = [
        'user_id' => 'positive|nonzero',
        'item_id' => 'positive|nonzero',
    ];

    /**
     * Automatic fields to populate every time a row is created
     *
     * @var  array<int, string>
     */
    public $initiate = [
        'voted',
    ];

    /**
     * Get represented item
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function item()
    {
        return $this->belongsToOne(__NAMESPACE__ . '\\Item', 'item_id');
    }

    /**
     * Get represented item
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function user()
    {
        return $this->belongsToOne('Hubzero\User\User', 'user_id');
    }
}
