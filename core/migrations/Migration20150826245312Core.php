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
        if ($this->db->tableExists('#__languages')) {
            $query = "UPDATE `#__languages` SET access=1 WHERE lang_id=1 AND access=0;";
            $this->db->setQuery($query);
            $this->db->query();
        }
    }
}
