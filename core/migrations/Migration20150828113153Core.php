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
 * Migration script for removing duplicate plugin entries while retaining proper parameters
 *
 */
class Migration20150828113153Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $query = $this->db->getQuery(true)
                ->select('MIN(extension_id)', 'min')
                ->select('MAX(extension_id)', 'max')
                ->select('folder')
                ->select('element')
                ->from('#__extensions')
                ->where('type', '=', 'plugin')
                ->group('folder')
                ->group('element')
                ->having(Expression::count(), '>', 1);
            $results = $query->loadObjectList();

            if ($results) {
                foreach ($results as $result) {
                    if (
                        empty($result)
                        || empty($result->min)
                        || empty($result->max)
                        || empty($result->folder)
                        || empty($result->element)
                    ) {
                        continue;
                    }

                    // Get params from the max ID
                    $query = $this->db->getQuery(true)
                        ->select('params')
                        ->from('#__extensions')
                        ->where('extension_id', '=', $result->max);
                    $params = $query->value('params');

                    if ($params) {
                        $this->db->getQuery(true)
                            ->update('#__extensions')
                            ->set(['params' => $params])
                            ->where('extension_id', '=', $result->min)
                            ->execute();
                    }

                    $this->db->getQuery(true)
                        ->delete('#__extensions')
                        ->where('folder', '=', $result->folder)
                        ->where('element', '=', $result->element)
                        ->where('extension_id', '!=', $result->min)
                        ->execute();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
    }
}
