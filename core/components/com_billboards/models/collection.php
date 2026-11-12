<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Billboards\Models;

use Hubzero\Database\Relational;

/**
 * Billboard collections database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Collection extends Relational
{
    /**
     * The table namespace
     *
     * @var string
     **/
    protected $namespace = 'billboards';

    /**
     * Fields and their validation criteria
     *
     * @var array<string, string|callable|array<string, mixed>>
     **/
    protected $rules = [
        'name' => 'notempty',
    ];

    /**
     * Defines a one to many relationship with billboards
     *
     * @return \Hubzero\Database\Relationship\OneToMany
     * @since  1.3.2
     **/
    public function billboards()
    {
        return $this->oneToMany(__NAMESPACE__ . '\\Billboard');
    }
}
