<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing up some support ticket field data types
 *
*/
class Migration20160808143522ComSupport extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        // Note: DEFAULT '' for int is unusual, using 0 for proper typing
        $this->db->schema()->modifyColumn('#__support_tickets', 'owner')->integer()->notNull()->default(0)->execute();
    }
}
