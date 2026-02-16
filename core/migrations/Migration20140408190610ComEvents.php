<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing how event rules are tracked
**/
class Migration20140408190610ComEvents extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        // remove bad fields
        if ($schema->hasColumn('#__events', 'sid')) {
            $schema->dropColumn('#__events', 'sid');
            $schema->dropColumn('#__events', 'color_bar');
            $schema->dropColumn('#__events', 'useCatColor');
            $schema->dropColumn('#__events', 'mask');
            $schema->dropColumn('#__events', 'created_by_alias');
            $schema->dropColumn('#__events', 'images');
            $schema->dropColumn('#__events', 'reccurtype');
            $schema->dropColumn('#__events', 'reccurday');
            $schema->dropColumn('#__events', 'reccurweekdays');
            $schema->dropColumn('#__events', 'reccurweeks');
            $schema->dropColumn('#__events', 'announcement');
            $schema->dropColumn('#__events', 'ordering');
            $schema->dropColumn('#__events', 'archived');
            $schema->dropColumn('#__events', 'access');
            $schema->dropColumn('#__events', 'hits');
        }

        // add new repeating rule
        if (!$schema->hasColumn('#__events', 'repeating_rule')) {
            $schema->addColumn('#__events', 'repeating_rule')
                ->string(150)
                ->after('time_zone')
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        // add bad fields
        if (!$schema->hasColumn('#__events', 'sid')) {
            $schema->addColumn('#__events', 'sid')->integer(11)->notNull()->default(0)->after('id')->execute();
            $schema->addColumn('#__events', 'color_bar')
                ->string(8)
                ->notNull()
                ->default('')
                ->after('extra_info')
                ->execute();
            $schema->addColumn('#__events', 'useCatColor')
                ->tinyInteger(1)
                ->notNull()
                ->default(0)
                ->after('color_bar')
                ->execute();
            $schema->addColumn('#__events', 'mask')
                ->integer(11)
                ->unsigned()
                ->notNull()
                ->default(0)
                ->after('state')
                ->execute();
            $schema->addColumn('#__events', 'created_by_alias')
                ->string(100)
                ->notNull()
                ->default('')
                ->after('created_by')
                ->execute();
            $schema->addColumn('#__events', 'images')->text()->notNull()->after('time_zone');
            $schema->addColumn('#__events', 'reccurtype')
                ->tinyInteger(1)
                ->notNull()
                ->default(0)
                ->after('images')
                ->execute();
            $schema->addColumn('#__events', 'reccurday')
                ->string(4)
                ->notNull()
                ->default('')
                ->after('reccurtype')
                ->execute();
            $schema->addColumn('#__events', 'reccurweekdays')
                ->string(20)
                ->notNull()
                ->default('')
                ->after('reccurday')
                ->execute();
            $schema->addColumn('#__events', 'reccurweeks')
                ->string(10)
                ->notNull()
                ->default('')
                ->after('reccurweekdays')
                ->execute();
            $schema->addColumn('#__events', 'announcement')->tinyInteger(1)->notNull()->default(0)->after('approved');
            $schema->addColumn('#__events', 'ordering')->integer(11)->notNull()->default(0)->after('announcement');
            $schema->addColumn('#__events', 'archived')->tinyInteger(1)->notNull()->default(0)->after('ordering');
            $schema->addColumn('#__events', 'access')
                ->integer(11)
                ->unsigned()
                ->notNull()
                ->default(0)
                ->after('archived');
            $schema->addColumn('#__events', 'hits')->integer(11)->notNull()->default(0)->after('access');
        }

        // remove new repeating rule
        if ($schema->hasColumn('#__events', 'repeating_rule')) {
            $schema->dropColumn('#__events', 'repeating_rule');
        }
    }
}
