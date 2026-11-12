<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Database\Relational;

/**
 * Abuse Report model
 *
 * @property  int|string|null  $id
 */
class Report extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     */
    public $namespace = 'abuse';

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
     * Automatic fields to populate every time a row is created
     *
     * @var  array<int, string>
     */
    public $initiate = [
        'created',
        'created_by',
    ];
}
