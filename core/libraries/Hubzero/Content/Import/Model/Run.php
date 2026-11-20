<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Import\Model;

use Hubzero\Database\Relational;
use Hubzero\Utility\Date;
use Hubzero\Facades\User;

/**
 * Class for an import run
 */
class Run extends Relational
{
    /**
     * The table namespace
     *
     * @var string
     */
    protected $namespace = 'import';

    /**
     * Default order by for model
     *
     * @var  string
     */
    public $orderBy = 'ran_at';

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
        'import_id' => 'positive|nonzero',
    ];

    /**
     * Automatic fields to populate every time a row is created
     *
     * @var  array<int, string>
     **/
    public $initiate = [
        'ran_at',
        'ran_by',
    ];

    /**
     * Generates automatic created field value
     *
     * @return  string
     * @since   2.0.0
     **/
    public function automaticRanAt()
    {
        $dt = new Date('now');
        return $dt->toSql();
    }

    /**
     * Generates automatic created by field value
     *
     * @return  int
     * @since   2.0.0
     **/
    public function automaticRanBy()
    {
        return User::get('id');
    }

    /**
     * Get parent import record
     *
     * @return  \Hubzero\Database\Relationship\BelongsToOne
     */
    public function import()
    {
        return $this->belongsToOne('Hubzero\Content\Import\Model\Import', 'import_id');
    }

    /**
     * Add to the processed number on this run
     *
     * @param   integer  $number  Number to increpemnt by
     * @return  void
     */
    public function processed($number = 1)
    {
        $this->set('processed', (int) $this->get('processed', 0) + $number);
        $this->save();
    }
}
