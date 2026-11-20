<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for converting topics plugins to wiki
 *
*/
class Migration20121009000000ComWiki extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__plugins')) {
            $this->db
                ->getQuery()
                ->update('#__plugins')
                ->set(['element' => 'wiki'])
                ->where('element', '=', 'topics')
                ->execute();
        } elseif ($schema->tableExists('#__extensions')) {
            $this->db
                ->getQuery()
                ->update('#__extensions')
                ->set(['element' => 'wiki'])
                ->where('element', '=', 'topics')
                ->execute();
        }
    }
}
