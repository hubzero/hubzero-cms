<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for content
 *
*/
class Migration20130924000001ComContent extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();
        $schema->setTableEngine('#__content_frontpage', 'MYISAM');
        $schema->setTableEngine('#__content_rating', 'MYISAM');

        // Add new columns with position (MySQL-specific)
        if (!$schema->hasColumn('#__content', 'asset_id') && $schema->hasColumn('#__content', 'id')) {
            $schema->addColumn('#__content', 'asset_id')
                ->integer()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->after('id')
                ->execute();
        }
        if (!$schema->hasColumn('#__content', 'featured') && $schema->hasColumn('#__content', 'metadata')) {
            $schema->addColumn('#__content', 'featured')
                ->tinyInteger()
                ->unsigned()
                ->notNull()
                ->default(0)
                ->after('metadata')
                ->execute();
        }
        if ($schema->hasColumn('#__content', 'featured') && $schema->hasColumn('#__content', 'catid')) {
            $schema->addIndex('#__content', 'idx_featured_catid', ['featured', 'catid']);
        }
        if (!$schema->hasColumn('#__content', 'language') && $schema->hasColumn('#__content', 'featured')) {
            $schema->addColumn('#__content', 'language')
                ->char(7)
                ->notNull()
                ->after('featured')
                ->execute();
        }
        if (!$schema->hasColumn('#__content', 'xreference') && $schema->hasColumn('#__content', 'language')) {
            $schema->addColumn('#__content', 'xreference')
                ->string(50)
                ->notNull()
                ->after('language')
                ->execute();
        }

        // Batch index and column modifications
        $schema->table('#__content')->alter()
            ->addIndex('idx_language', 'language')
            ->addIndex('idx_xreference', 'xreference')
            ->dropIndex('idx_section')
            ->modifyColumn('attribs', function ($column) {
                $column->string(5120)->notNull();
            })
            ->modifyColumn('alias', function ($column) {
                $column->string(255)->notNull()->default('');
            })
            ->modifyColumn('title_alias', function ($column) {
                $column->string(255)->notNull()->default('');
            })
            ->modifyColumn('sectionid', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('mask', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('catid', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('created_by', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('modified_by', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('checked_out', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('version', function ($column) {
                $column->integer()->unsigned()->notNull()->default(1);
            })
            ->modifyColumn('parentid', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('access', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('hits', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->execute();

        // modifyColumn handles AUTO_INCREMENT appropriately for each database
        $schema->modifyColumn('#__content', 'id')
            ->integer()
            ->unsigned()
            ->notNull()
            ->autoIncrement()
            ->execute();

        // Batch content_rating modifications
        $schema->table('#__content_rating')->alter()
            ->modifyColumn('rating_sum', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->modifyColumn('rating_count', function ($column) {
                $column->integer()->unsigned()->notNull()->default(0);
            })
            ->execute();

        // Update "uncategorised" cat_id from 0
        // Update "uncategorised" cat_id from 0
        $id = $this->db->getQuery(true)
            ->select('id')
            ->from('#__categories')
            ->where('extension', '=', 'com_content')
            ->where('alias', '=', 'uncategorised')
            ->value('id');

        if (is_numeric($id)) {
            $this->db->getQuery(true)
                ->update('#__content')
                ->set(['catid' => $id])
                ->where('catid', '=', 0)
                ->execute();
        }

        // Convert params to json
        // Convert params to json
        // Convert params to json
        $results = $this->db->getQuery(true)
            ->select(['id', 'attribs'])
            ->from('#__content')
            ->beginOrGroup()
                ->whereIsNotNull('attribs')
                ->orWhere('attribs', '!=', '')
            ->endAndGroup()
            ->loadObjectList();

        if (count($results) > 0) {
            foreach ($results as $r) {
                $attribs = trim($r->attribs);
                if (empty($attribs) || $attribs[0] == '{') {
                    continue;
                }

                $array = array();
                $ar    = explode("\n", $attribs);

                foreach ($ar as $a) {
                    $a = trim($a);
                    if (empty($a)) {
                        continue;
                    }

                    $ar2     = explode("=", $a, 2);
                    $array[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
                }

                $this->db->getQuery(true)
                    ->update('#__content')
                    ->set(['attribs' => json_encode($array)])
                    ->where('id', '=', $r->id)
                    ->execute();
            }
        }
    }
}
