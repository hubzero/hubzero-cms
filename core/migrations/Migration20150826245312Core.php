<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for languages table addition
 *
*/
class Migration20150826245312Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__languages')) {
            $this->db->getQuery(true)
                ->update('#__languages')
                ->set(['access' => 1])
                ->where('lang_id', '=', 1)
                ->where('access', '=', 0)
                ->execute();
        }
    }
}
