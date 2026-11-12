<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Model class for publication type
 */
class Type extends Relational
{
    /**
     * The table namespace
     *
     * @var  string
     */
    public $namespace = 'publication_master';

    /**
     * Fields and their validation criteria
     *
     * @var  array<string, string|callable|array<string, mixed>>
     */
    protected $rules = [
        'type'  => 'notempty',
        'alias' => 'notempty',
    ];

    /**
     * Automatic fields to populate every time a row is created
     *
     * @var  array<int, string>
     */
    public $initiate = [
        'created',
    ];

    /**
     * Get last order
     *
     * @return  integer
     */
    public function getLastOrder()
    {
        return self::all()
            ->order('ordering', 'desc')
            ->row()
            ->get('ordering', 0);
    }

    /**
     * Check usage
     *
     * @return  integer
     */
    public function checkUsage()
    {

        return Publication::all()
            ->whereEquals('master_type', $this->get('id'))
            ->total();
    }

    /**
     * Find one record by ordering
     *
     * @param   integer  $ordering
     * @return  object
     */
    public static function oneByOrdering($ordering)
    {
        return self::all()
            ->whereEquals('ordering', (int) $ordering)
            ->row();
    }
}
