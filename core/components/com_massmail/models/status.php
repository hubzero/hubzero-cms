<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Models;

use Hubzero\Database\Relational;

/**
 * Mass mail: single-row snapshot of the sender/queue monitor state.
 */
class Status extends Relational
{
    /**
     * The table name (single-row status snapshot, id = 1)
     *
     * @var  string
     */
    protected $table = '#__massmail_status';

    /**
     * Fetch (or create) the single status row.
     *
     * @return  object
     */
    public static function instance()
    {
        $row = self::oneOrNew(1);
        if (!$row->get('id')) {
            $row->set('id', 1);
        }
        return $row;
    }
}
