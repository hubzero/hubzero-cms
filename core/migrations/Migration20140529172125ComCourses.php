<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for making sure manual grade entries are of the proper type and subtype
  *
**/
class Migration20140529172125ComCourses extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // Get the problem assets
        $results = $this->db->getQuery(true)
            ->select('ca.id')
            ->from('#__courses_assets', 'ca')
            ->leftJoin('#__courses_asset_associations AS caa', 'ca.id', 'caa.asset_id')
            ->leftJoin('#__courses_forms AS cf', 'ca.id', 'cf.asset_id')
            ->whereIsNull('caa.id')
            ->whereIsNull('cf.id')
            ->where('ca.type', '=', 'form')
            ->loadObjectList();

        if ($results && count($results) > 0) {
            foreach ($results as $result) {
                $this->db->getQuery(true)
                    ->update('#__courses_assets')
                    ->set(['type' => 'gradebook', 'subtype' => 'auxiliary'])
                    ->where('id', '=', $result->id)
                    ->execute();
            }
        }
    }
}
