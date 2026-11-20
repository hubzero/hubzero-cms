<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Course certificate table class
 */
class Certificate extends Table
{
    /**
     * Constructor
     *
     * @param      \Hubzero\Database\Driver &$db Database
     * @return     void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__courses_certificates', 'id', $db);
    }

    /**
     * Populate the current object with a database record if found
     * Accepts either an alias or an ID
     *
     * @param      mixed $course_id Unique ID or alias of object to retrieve
     * @return     boolean True on success
     */
    public function loadByCourse($course_id)
    {
        return parent::load([
            'course_id' => (int) $course_id,
        ]);
    }
}
