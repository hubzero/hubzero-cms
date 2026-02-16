<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for extending filed from 100 to 255 chars
**/
class Migration20160715100931PlgHubzeroComments extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__item_comment_files') && $schema->hasColumn('#__item_comment_files', 'filename')) {
            $schema->modifyColumn('#__item_comment_files', 'filename')
                ->string(255)
                ->nullable()
                ->default(null)
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__item_comment_files') && $schema->hasColumn('#__item_comment_files', 'filename')) {
            $schema->modifyColumn('#__item_comment_files', 'filename')
                ->string(100)
                ->nullable()
                ->default(null)
                ->execute();
        }
    }
}
