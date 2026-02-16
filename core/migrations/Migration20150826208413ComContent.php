<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing excessiving escaping of content originating for early hub upgrades (before 1.2.0)
**/
class Migration20150826208413ComContent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__content')) {
            $query = $this->db->getQuery(true)
                ->select(['id', 'attribs'])
                ->from('#__content');
            $results = $query->loadObjectList();

            if (count($results) > 0) {
                foreach ($results as $r) {
                    if (empty($r->attribs)) {
                        $attribs = "{}";
                    } else {
                        $attribs = $r->attribs;
                        $attribs = preg_replace("/^{\"{\\\\\"{/", "{", $attribs);
                        $attribs = preg_replace("/^{\"{/", "{", $attribs);
                        $attribs = preg_replace("/}\\\\\":\\\\\\\"\\\\\"}\":\"\"}$/", "}", $attribs);
                        $attribs = preg_replace("/}\":\"\"}$/", "}", $attribs);
                        $attribs = preg_replace("/\\\\\\\\\\\\\"/", "\"", $attribs);
                        $attribs = preg_replace("/\\\\\\\\\\\\\\\\\\\\\\\\\\\\\"/", "\"", $attribs);
                        $attribs = preg_replace("/\\\\\"/", "\"", $attribs);
                    }

                    $attribs = json_decode($attribs);

                    if (json_last_error() === JSON_ERROR_NONE) {
                        $attribs = json_encode($attribs);
                        $this->db->getQuery(true)
                            ->update('#__content')
                            ->set(['attribs' => $attribs])
                            ->where('id', '=', $r->id)
                            ->execute();
                    }
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
