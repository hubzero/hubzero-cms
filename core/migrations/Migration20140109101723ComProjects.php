<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script to change column type to TEXT to allow extended blog entry
 *
*/
class Migration20140109101723ComProjects extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__project_microblog', 'blogentry')) {
            $schema->modifyColumn('#__project_microblog', 'blogentry')->text()->nullable();
        }
    }
}
