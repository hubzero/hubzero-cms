<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm\Page;

use Hubzero\Database\Relational;

/**
 * Group page version model
 */
class Version extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     **/
    protected $namespace = 'xgroups_pages';

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
        'pageid'  => 'positive|nonzero',
        'content' => 'notempty',
    ];

    /**
     * Automatic fields to populate every time a row is created
     *
     * @var  array<int, string>
     */
    public $initiate = [
        'created',
        'created_by',
    ];

    /**
     * Get parent page
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function page()
    {
        return $this->belongsToOne('Components\Groups\Models\Orm\Page', 'pageid');
    }

    /**
     * Defines a belongs to one relationship between category and creator
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function creator()
    {
        return $this->belongsToOne('Hubzero\User\User', 'created_by');
    }
}
