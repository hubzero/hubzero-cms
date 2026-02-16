<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes on #__collections_following
 *
*/
class Migration20140822155500ComCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections_following')) {
            $schema->addIndex('#__collections_following', 'idx_follower_type_follower_id', [
                'follower_type',
                'follower_id',
            ]);

            $schema->addIndex('#__collections_following', 'idx_following_type_following_id', [
                'following_type',
                'following_id',
            ]);
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections_following')) {
            $schema->dropIndex('#__collections_following', 'idx_following_type_following_id');

            $schema->dropIndex('#__collections_following', 'idx_follower_type_follower_id');
        }
    }
}
