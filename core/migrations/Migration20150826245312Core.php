<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__languages')) {
            $this->db
                ->getQuery()
                ->update('#__languages')
                ->set(['access' => 1])
                ->where('lang_id', '=', 1)
                ->where('access', '=', 0)
                ->execute();
        }
    }
}
