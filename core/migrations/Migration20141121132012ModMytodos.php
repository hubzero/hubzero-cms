<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding a "My To-Do Items" module contributed by Shaun Einolf <einolfs@mail.nih.gov>
 *
*/
class Migration20141121132012ModMytodos extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->addModuleEntry('mod_mytodos', 1, '');
    }

    /**
     * Up
     *
     * @return  void
     **/
    public function down()
    {
        $this->deleteModuleEntry('mod_mytodos');
    }
}
