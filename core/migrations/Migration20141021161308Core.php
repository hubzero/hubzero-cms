<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing 'jos_' references
 *
*/
class Migration20141021161308Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $this->db->schema()->modifyColumn('#__categories', 'asset_id')->integer(10)->unsigned()->notNull()->default(0);
        $this->db->schema()->modifyColumn('#__content', 'asset_id')->integer(10)->unsigned()->notNull()->default(0);
        $this->db->schema()->modifyColumn('#__menu', 'component_id')->integer(10)->unsigned()->notNull()->default(0);
        $this->db
            ->schema()
            ->modifyColumn('#__user_usergroup_map', 'user_id')
            ->integer(10)
            ->unsigned()
            ->notNull()
            ->default(0);
        $this->db
            ->schema()
            ->modifyColumn('#__user_usergroup_map', 'group_id')
            ->integer(10)
            ->unsigned()
            ->notNull()
            ->default(0);
    }
}
