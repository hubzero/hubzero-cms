<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Tables;

use Hubzero\Database\Table;

/**
 * Courses grade book table
 */
class GradePolicies extends Table
{
    /**
     * Constructor
     *
     * @param      \Hubzero\Database\Driver &$db Database
     * @return     void
     */
    public function __construct(&$db)
    {
        parent::__construct('#__courses_grade_policies', 'id', $db);
    }
}
