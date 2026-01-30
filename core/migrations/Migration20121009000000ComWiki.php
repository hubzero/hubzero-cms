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
    public function up()
    {
        if ($this->db->tableExists('#__plugins')) {
            $query = "UPDATE `#__plugins` SET element='wiki' WHERE element='topics';\n";
        } elseif ($this->db->tableExists('#__extensions')) {
            $query = "UPDATE `#__extensions` SET element='wiki' WHERE element='topics';\n";
        }

        $this->db->setQuery($query);
        $this->db->query();
    }
}
