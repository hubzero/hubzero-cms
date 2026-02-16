<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for properly associating gradebook items with an offering
 *
 */
class Migration20140529192810ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $results = $this->db->getQuery(true)
            ->select('ca.*')
            ->from('#__courses_assets', 'ca')
            ->leftJoin('#__courses_asset_associations AS caa', 'ca.id', 'caa.asset_id')
            ->where('type', '=', 'gradebook')
            ->where('subtype', '=', 'auxiliary')
            ->whereIsNull('caa.id')
            ->loadObjectList();

        $ordering = array();

        if ($results && count($results) > 0) {
            foreach ($results as $result) {
                $offering = $this->db->getQuery(true)
                    ->select(['id', 'title'])
                    ->from('#__courses_offerings')
                    ->where('course_id', '=', $result->course_id)
                    ->where('created', '<', $result->created)
                    ->where('state', '=', 1)
                    ->order('id', 'ASC')
                    ->first();

                if ($offering) {
                    $ordering[$offering->id] = (!isset($ordering[$offering->id])) ? 0 : $ordering[$offering->id] + 1;

                    $this->db->getQuery(true)
                        ->insert('#__courses_asset_associations')
                        ->set([
                            'asset_id' => $result->id,
                            'scope_id' => $offering->id,
                            'scope'    => 'offering',
                            'ordering' => $ordering[$offering->id]
                        ])
                        ->execute();
                }
            }
        }
    }
}
