<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for languages table addition
**/
class Migration20130924000008Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__languages')) {
            $schema->createTable('#__languages')
                ->unsignedInteger('lang_id', ['autoIncrement' => true])
                ->char('lang_code', 7)
                ->string('title', 50)
                ->string('title_native', 50)
                ->string('sef', 50)
                ->string('image', 50)
                ->string('description', 512)
                ->text('metakey')
                ->text('metadesc')
                ->string('sitename', 1024)->default('')
                ->integer('published')->default(0)
                ->unsignedInteger('access')->default(0)
                ->integer('ordering')->default(0)
                ->primaryKey('lang_id')
                ->uniqueIndex('idx_sef', 'sef')
                ->uniqueIndex('idx_image', 'image')
                ->uniqueIndex('idx_langcode', 'lang_code')
                ->index('idx_access', 'access')
                ->index('idx_ordering', 'ordering')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__languages')
                ->columns([
                    'lang_id',
                    'lang_code',
                    'title',
                    'title_native',
                    'sef',
                    'image',
                    'description',
                    'metakey',
                    'metadesc',
                    'published',
                    'access',
                    'ordering',
                ])
                ->values("1, 'en-GB', 'English (UK)', 'English (UK)', 'en', 'en', '', '', '', 1, 1, 1")
                ->execute();
        }
    }
}
