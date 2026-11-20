<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Model class for a member
 */
class Member extends Relational
{
    /**
     * The table namespace
     *
     * @var string
     */
    protected $namespace = 'courses';

    /**
     * Default order by for model
     *
     * @var string
     */
    public $orderBy = 'publish_up';

    /**
     * Default order direction for select queries
     *
     * @var  string
     */
    public $orderDir = 'desc';

    /**
     * Registry
     *
     * @var  object
     */
    public $params;

    /**
     * Get parent course
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function course()
    {
        return $this->belongsToOne('course');
    }

    /**
     * Get role
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function role()
    {
        return $this->belongsToOne('role');
    }

    /**
     * Get user profile object
     *
     * @return \Hubzero\Database\Relationship\BelongsToOne
     */
    public function user()
    {
        return $this->belongsToOne('Hubzero\User\User');
    }
}
