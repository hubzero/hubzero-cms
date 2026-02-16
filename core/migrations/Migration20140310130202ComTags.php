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
 * Migration script for merging duplicate tags
  *
**/
class Migration20140310130202ComTags extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__tags')) {
            // We need to clean out duplicates first
            $results = $this->db->getQuery(true)
                ->select(['*', Expression::count('id')->as('cnt')])
                ->from('#__tags')
                ->group('tag')
                ->having('cnt', '>', 1)
                ->loadObjectList();

            if ($results) {
                if (file_exists(PATH_CORE . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php')) {
                    require_once PATH_CORE . DS . 'components' . DS . 'com_tags' . DS . 'models' . DS . 'cloud.php';

                    $cls = '\\Components\\Tags\\Models\\Tag';
                    // [!] - Backwards compatibility
                    if (class_exists('TagsModelTag')) {
                        $cls = 'TagsModelTag';
                    }

                    foreach ($results as $result) {
                        // Get all duplicate tags
                        $tags = $this->db->getQuery(true)
                            ->select('*')
                            ->from('#__tags')
                            ->where('tag', '=', $result->tag)
                            ->loadObjectList();

                        if ($tags) {
                            foreach ($tags as $tag) {
                                if ($tag->id == $result->id) {
                                    continue;
                                }

                                $oldtag = new $cls($tag->id);
                                if ($oldtag instanceof \Hubzero\Database\Relational) {
                                    $oldtag = \Components\Tags\Models\Tag::oneOrNew($tag->id);
                                }
                                if (!$oldtag->mergeWith($result->id)) {
                                    continue;
                                }
                            }
                        }
                    }
                }
            }

            $schema->dropIndex('#__tags', 'idx_tag');

            $schema->addUniqueIndex('#__tags', 'idx_tag', 'tag');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__tags')) {
            $schema->dropIndex('#__tags', 'idx_tag');

            $schema->addIndex('#__tags', 'idx_tag', 'tag');
        }
    }
}
