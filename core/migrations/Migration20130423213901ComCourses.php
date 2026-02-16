<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for allowing null scores in gradebook for unfinished forms
**/
class Migration20130423213901ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_grade_book', 'score')) {
            $schema->modifyColumn('#__courses_grade_book', 'score')->decimal(5, 2)->nullable();
        }
    }
}
