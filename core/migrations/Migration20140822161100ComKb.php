<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for adding indexes to com_kb tables
 *
*/
class Migration20140822161100ComKb extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__faq')) {
            $schema->dropIndex('#__faq', 'jos_faq_title_introtext_fulltext_ftidx');

            if (
                $schema->hasColumn('#__faq', 'title')
                && $schema->hasColumn('#__faq', 'fulltxt')
            ) {
                $schema->addFulltextIndex('#__faq', 'ftidx_title_fulltxt', ['title', 'fulltxt']);
            }

            $schema->addIndex('#__faq', 'idx_section', 'section');

            $schema->addIndex('#__faq', 'idx_category', 'category');

            $schema->addIndex('#__faq', 'idx_alias', 'alias');
        }

        if ($schema->tableExists('#__faq_categories')) {
            $schema->addIndex('#__faq_categories', 'idx_alias', 'alias');

            $schema->addIndex('#__faq_categories', 'idx_section', 'section');

            $schema->addIndex('#__faq_categories', 'idx_state', 'state');
        }

        if ($schema->tableExists('#__faq_comments')) {
            $schema->addIndex('#__faq_comments', 'idx_entry_id', 'entry_id');

            $schema->addIndex('#__faq_comments', 'idx_state', 'state');
        }

        if ($schema->tableExists('#__faq_helpful_log')) {
            if (
                $schema->hasColumn('#__faq_helpful_log', 'type')
                && $schema->hasColumn('#__faq_helpful_log', 'object_id')
            ) {
                $schema->addIndex('#__faq_helpful_log', 'idx_type_object_id', ['type', 'object_id']);
            }

            $schema->addIndex('#__faq_helpful_log', 'idx_user_id', 'user_id');
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__faq')) {
            $schema->dropIndex('#__faq', 'ftidx_title_fulltxt');

            if (
                $schema->hasColumn('#__faq', 'title')
                && $schema->hasColumn('#__faq', 'params')
                && $schema->hasColumn('#__faq', 'fulltxt')
            ) {
                $schema->addFulltextIndex('#__faq', 'jos_faq_title_introtext_fulltext_ftidx', [
                    'title',
                    'params',
                    'fulltxt',
                ]);
            }

            $schema->dropIndex('#__faq', 'idx_section');

            $schema->dropIndex('#__faq', 'idx_category');

            $schema->dropIndex('#__faq', 'idx_alias');
        }

        if ($schema->tableExists('#__faq_categories')) {
            $schema->dropIndex('#__faq_categories', 'idx_alias');

            $schema->dropIndex('#__faq_categories', 'idx_section');

            $schema->dropIndex('#__faq_categories', 'idx_state');
        }

        if ($schema->tableExists('#__faq_comments')) {
            $schema->dropIndex('#__faq_comments', 'idx_entry_id');

            $schema->dropIndex('#__faq_comments', 'idx_state');
        }

        if ($schema->tableExists('#__faq_helpful_log')) {
            $schema->dropIndex('#__faq_helpful_log', 'idx_type_object_id');

            $schema->dropIndex('#__faq_helpful_log', 'idx_user_id');
        }
    }
}
