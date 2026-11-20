<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Models\Quota;

use Hubzero\Database\Relational;

/**
 * Quota class group model
 */
class Group extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     */
    protected $namespace = 'users_quotas_classes';

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
        'class_id' => 'nonzero|positive',
    ];

    /**
     * Get parent group
     *
     * @return  \Hubzero\User\Group|false  False when there is no such group
     */
    public function group()
    {
        return \Hubzero\User\Group::getInstance($this->get('group_id'));
    }

    /**
     * Get parent class
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function category()
    {
        return $this->belongsToOne('Category', 'class_id');
    }
}
