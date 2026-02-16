<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding MP4 extension to params
  *
**/
class Migration20170124151456ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $params = $this->db->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('name', '=', 'com_support')
                ->value('params');

            if ($params) {
                $params = json_decode($params);
                $fileExt = explode(",", $params->file_ext);

                // Prevent duplicates
                if (!in_array('mp4', $fileExt)) {
                    array_push($fileExt, 'mp4');
                }

                $params->file_ext = implode(",", $fileExt);
                $params = json_encode($params);

                $this->db->getQuery(true)
                    ->update('#__extensions')
                    ->set(['params' => $params])
                    ->where('name', '=', 'com_support')
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $params = $this->db->getQuery(true)
            ->select('params')
            ->from('#__extensions')
            ->where('name', '=', 'com_support')
            ->value('params');

        if ($params) {
            $params = json_decode($params);

            $fileExt = explode(",", $params->file_ext);
            $index = array_search('mp4', $fileExt);

            // Prevents invalid array access
            if ($index !== false) {
                unset($fileExt[$index]);
            }

            $params->file_ext = implode(",", $fileExt);
            $params = json_encode($params);

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['params' => $params])
                ->where('name', '=', 'com_support')
                ->execute();
        }
    }
}
