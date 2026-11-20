<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Relationship;

/**
 * Database one to one relationship
 */
class OneToOne extends Relationship
{
    /**
     * Whether the relationship is to a single record (rows() gives a model, not a list)
     *
     * @return  bool
     **/
    protected function isSingular()
    {
        return true;
    }
}
