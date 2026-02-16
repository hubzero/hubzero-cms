<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing search component's default settings.
  *
**/
class Migration20160729202716ComSearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__extensions')) {
            $params = '{"engine":"basic"}';
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['params' => $params])
                ->where('name', '=', 'com_search')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        // No down method
    }
}
