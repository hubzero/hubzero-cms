<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for removing redundant mod_tagcloud module
**/
class Migration20150114122012ModTagcloud extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $this->deleteModuleEntry('mod_tagcloud');

        if ($schema->tableExists('#__modules')) {
            $this->db->getQuery(true)
                ->update('#__modules')
                ->set(['module' => 'mod_toptags', 'params' => '{"numtags":"20","exclude":"","message":"No tags"
                . "found.","sortby":"popularity","morelnk":"0","cache":"0","cache_time":"900"}'])
                ->where('module', '=', 'mod_tagcloud')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $this->addModuleEntry('mod_tagcloud', 1, '');
    }
}
