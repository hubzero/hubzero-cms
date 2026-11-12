<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for help to correct default value for GeoDB
 *
*/
class Migration20161214180653ComSystem extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $params = $this->db
            ->getQuery()
            ->select('params')
            ->from('#__extensions')
            ->where('name', '=', 'com_system')
            ->value('params');

        $params = json_decode($params);
        $params->geodb_driver = 'pdo';
        $params = json_encode($params);

        $this->db
            ->getQuery()
            ->update('#__extensions')
            ->set(['params' => $params])
            ->where('name', '=', 'com_system')
            ->execute();
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $params = $this->db
            ->getQuery()
            ->select('params')
            ->from('#__extensions')
            ->where('name', '=', 'com_system')
            ->value('params');

        $params = json_decode($params);
        $params->geodb_driver = 'mysql';
        $params = json_encode($params);

        $this->db
            ->getQuery()
            ->update('#__extensions')
            ->set(['params' => $params])
            ->where('name', '=', 'com_system')
            ->execute();
    }
}
