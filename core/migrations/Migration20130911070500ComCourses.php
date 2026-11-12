<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding params field to asset groups
**/
class Migration20130911070500ComCourses extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__courses_asset_groups')
            && !$schema->hasColumn('#__courses_asset_groups', 'params')
        ) {
            $schema->addColumn('#__courses_asset_groups', 'params')->text()->notNull();

            $query = $this->db->getQuery();
            $query
                ->select('id')
                ->from('#__courses_asset_groups')
                ->where('alias', '=', 'lectures');
            $results = $query->loadObjectList();

            if ($results && count($results) > 0) {
                foreach ($results as $r) {
                    $this->db
                        ->getQuery()
                        ->update('#__courses_asset_groups')
                        ->set(['params' => 'discussions_category=1'])
                        ->where('parent', '=', $r->id)
                        ->execute();
                }
            }
        }
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__courses_asset_groups', 'params')) {
            $schema->dropColumn('#__courses_asset_groups', 'params');
        }
    }
}
