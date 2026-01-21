<?php

// phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Content\Migration\Base;

// No direct access
defined('_HZEXEC_') or die();

/**
 * Migration script for allowing null scores in gradebook for unfinished forms
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 **/
class Migration20130423213901ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $query = "ALTER TABLE `#__courses_grade_book` CHANGE `score` `score` DECIMAL(5,2)  NULL;";

        if (!empty($query)) {
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
