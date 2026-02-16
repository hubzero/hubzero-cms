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
 * Migration script for adding params field to asset groups
 *
*/
class Migration20130916080500ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__wiki_page')
            && !$schema->hasColumn('#__wiki_page', 'created')
        ) {
            $schema->addColumn('#__wiki_page', 'created')
                ->datetime()
                ->notNull()
                ->default('0000-00-00 00:00:00')
                ->execute();

            $query = $this->db->getQuery(true)
                ->select('id')
                ->from('#__wiki_page');

            $pages = $query->loadObjectList();

            if ($pages) {
                foreach ($pages as $page) {
                    $created = $this->db->getQuery(true)
                         ->select('created')
                         ->from('#__wiki_version')
                         ->where('pageid', '=', $page->id)
                         ->order('version', 'ASC')
                         ->value('created');

                    if ($created) {
                        $this->db->getQuery(true)
                            ->update('#__wiki_page')
                            ->set(['created' => $created])
                            ->where('id', '=', $page->id)
                            ->execute();
                    }
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->hasColumn('#__wiki_page', 'created')) {
            $schema->dropColumn('#__wiki_page', 'created');
        }
    }
}
