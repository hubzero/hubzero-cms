<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating support attachments column
 *
*/
class Migration20140428094910ComSupport extends Base
{
    /**
     * Up
     *
     * @return  void
     **/
    public function up()
    {
        $this->db
            ->schema()
            ->modifyColumn('#__support_attachments', 'comment_id')
            ->integer()
            ->notNull()
            ->default(0)
            ->execute();
    }

    /**
     * Down
     *
     * @return  void
     **/
    public function down()
    {
        $this->db
            ->schema()
            ->modifyColumn('#__support_attachments', 'comment_id')
            ->integer()
            ->notNull()
            ->default(0)
            ->execute();
    }
}
