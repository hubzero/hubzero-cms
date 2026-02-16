<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__plugins')) {
            $this->db->getQuery(true)
                ->update('#__plugins')
                ->set(['element' => 'wiki'])
                ->where('element', '=', 'topics')
                ->execute();
        } elseif ($schema->tableExists('#__extensions')) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['element' => 'wiki'])
                ->where('element', '=', 'topics')
                ->execute();
        }
    }
}
