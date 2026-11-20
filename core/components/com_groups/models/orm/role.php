<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Group role model
 */
class Role extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     **/
    protected $namespace = 'xgroups';

    /**
     * Default order by for model
     *
     * @var  string
     */
    public $orderBy = 'name';

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
        'gidNumber' => 'positive|nonzero',
        'name'      => 'notempty',
    ];

    /**
     * Get parent group
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function group()
    {
        return $this->belongsToOne('Group', 'gidNumber');
    }
}
