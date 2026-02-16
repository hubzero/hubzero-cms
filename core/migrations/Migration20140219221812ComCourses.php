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
 * Migration script for getting rid of duplicate section date entries
 *
 */
class Migration20140219221812ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $results = $this->db->getQuery(true)
            ->select(Expression::count('id'), 'num')
            ->select('section_id')
            ->select('scope')
            ->select('scope_id')
            ->from('#__courses_offering_section_dates')
            ->group('section_id')
            ->group('scope')
            ->group('scope_id')
            ->having('num', '>', 1)
            ->loadObjectList();

        if ($results && count($results) > 0) {
            foreach ($results as $result) {
                $rows = $this->db->getQuery(true)
                    ->select('*')
                    ->from('#__courses_offering_section_dates')
                    ->where('section_id', '=', $result->section_id)
                    ->where('scope', '=', $result->scope)
                    ->where('scope_id', '=', $result->scope_id)
                    ->loadObjectList();

                if ($rows && count($rows) > 1) {
                    // Leave the first one intact
                    unset($rows[0]);

                    foreach ($rows as $row) {
                        $this->db->getQuery(true)
                            ->delete('#__courses_offering_section_dates')
                            ->where('id', '=', $row->id)
                            ->execute();
                    }
                }
            }
        }
    }
}
