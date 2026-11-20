<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm\Page;

use Hubzero\Database\Relational;

/**
 * Group page category model
 */
class Category extends Relational
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
        'gidNumber' => 'positive|nonzero',
        'title'     => 'notempty',
    ];

    /**
     * Get parent group
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function group()
    {
        return $this->belongsToOne('Components\Groups\Models\Orm\Group', 'gidNumber');
    }

    /**
     * Get pages assigned to this category
     *
     * @return  \Hubzero\Database\Relationship\OneToMany
     */
    public function pages()
    {
        return $this->oneToMany('Components\Groups\Models\Orm\Page', 'category');
    }
}
