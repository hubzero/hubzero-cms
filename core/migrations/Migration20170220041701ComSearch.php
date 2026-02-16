<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for creating the facet table for Solr
  *
**/
class Migration20170220041701ComSearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__solr_search_facets')) {
            $schema->createTable('#__solr_search_facets')
                ->id()
                ->string('name', 255)
                ->longText('facet')->nullable()
                ->tinyInteger('state')->default(0)
                ->tinyInteger('protected')->default(0)
                ->string('ordering', 45)->default('0')
                ->integer('parent_id')->nullable()
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__solr_search_facets')
                ->columns(['id', 'name', 'facet', 'state', 'protected', 'ordering', 'parent_id'])
                ->values([
                    [1, 'Content', 'hubtype:content', 1, 1, '0', 0],
                    [2, 'Resources', 'hubtype:resource', 1, 1, '0', 0],
                    [3, 'Collections', 'hubtype:collection', 1, 1, '0', 0],
                    [4, 'Members', 'hubtype:member', 1, 1, '0', 0],
                    [5, 'Projects', 'hubtype:project', 1, 1, '0', 0],
                    [6, 'Groups', 'hubtype:group', 1, 1, '0', 0],
                    [7, 'Courses', 'hubtype:course', 1, 1, '0', 0],
                    [8, 'Wiki', 'hubtype:wiki', 1, 1, '0', 0],
                    [9, 'Events', 'hubtype:event', 1, 1, '0', 0],
                    [10, 'Knowledge Base Article', 'hubtype:kb-article', 1, 1, '0', 0],
                    [11, 'Blog Posts', 'hubtype:blog-entry', 1, 1, '0', 0],
                    [12, 'Wishes', 'hubtype:wishlist', 1, 1, '0', 0],
                    [13, 'Publications', 'hubtype:publication', 1, 1, '0', 0],
                    [14, 'Questions', 'hubtype:question', 1, 1, '0', 0],
                    [15, 'Citations', 'hubtype:citation', 1, 1, '0', 0]
                ])
                ->execute();
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__solr_search_facets')) {
            $schema->dropTable('#__solr_search_facets');
        }
    }
}
