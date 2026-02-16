<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;
use Hubzero\Database\Expression;

/**
 * Migration script for adding objects and substitutes columns to tags table
 *
 */
class Migration20160212200400ComTags extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__tags')) {
            if (!$schema->hasColumn('#__tags', 'objects')) {
                $schema->addColumn('#__tags', 'objects')->integer()->notNull()->default(0)->execute();
                $schema->addIndex('#__tags', 'idx_objects', 'objects');

                $subquery = $this->db->getQuery(true)
                    ->select(Expression::count())
                    ->from('#__tags_object', 'o')
                    ->where('o.tagid', '=', 't.id', false);

                $this->db->getQuery(true)
                    ->update('#__tags', 't')
                    ->set(['t.objects' => (string)$subquery])
                    ->execute();
            }

            if (!$schema->hasColumn('#__tags', 'substitutes')) {
                $schema->addColumn('#__tags', 'substitutes')->integer()->notNull()->default(0)->execute();
                $schema->addIndex('#__tags', 'idx_substitutes', 'substitutes');

                $subquery = $this->db->getQuery(true)
                    ->select(Expression::count())
                    ->from('#__tags_substitute', 'o')
                    ->where('o.tag_id', '=', 't.id', false);

                $this->db->getQuery(true)
                    ->update('#__tags', 't')
                    ->set(['t.substitutes' => (string)$subquery])
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__tags')) {
            if ($schema->hasColumn('#__tags', 'objects')) {
                $schema->dropColumn('#__tags', 'objects');
            }

            if ($schema->hasColumn('#__tags', 'substitutes')) {
                $schema->dropColumn('#__tags', 'substitutes');
            }
        }
    }
}
