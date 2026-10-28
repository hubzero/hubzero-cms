<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Models;

use Hubzero\Database\Relational;

/**
 * Mass mail: one recipient of a mailing, with per-recipient send status.
 */
class Recipient extends Relational
{
    const PENDING = 0;
    const SENT    = 1;
    const FAILED  = 2;
    const SKIPPED = 3;

    /**
     * The table namespace
     *
     * @var  string
     */
    protected $namespace = 'massmail';

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
     * @var  array
     */
    protected $rules = array(
        'email'      => 'notempty',
        'mailing_id' => 'positive|nonzero'
    );

    /**
     * The mailing this recipient belongs to
     *
     * @return  object
     */
    public function mailing()
    {
        return $this->belongsToOne('Mailing', 'mailing_id');
    }
}
