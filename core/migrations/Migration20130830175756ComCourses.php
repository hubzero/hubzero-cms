<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for updating form info to correspond to code changes
 *
*/
class Migration20130830175756ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->db->getQuery(true)
            ->update('#__courses_assets')
            ->set(['url' => Expression::substring('url', 30, 50)])
            ->where('type', '=', 'form')
            ->where('url', 'LIKE', '/courses/form/complete?crumb=%')
            ->execute();

        $this->db->getQuery(true)
            ->update('#__courses_assets')
            ->set(['url' => Expression::substring('url', 22)])
            ->where('type', '=', 'form')
            ->where('url', 'LIKE', '/courses/form/layout/%')
            ->execute();
    }
}
