<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

// Check to ensure this file is included in Hubzero!
/**
 * Migration script for removing unused/deprecated mod_megamenu module
 *
*/
class Migration20150508103212ModMegamenu extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->deleteModuleEntry('mod_megamenu');
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->addModuleEntry('mod_megamenu', 1, '');
    }
}
