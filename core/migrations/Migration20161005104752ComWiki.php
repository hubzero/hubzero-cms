<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for updating old 'new page' links in group wikis
 *
*/
class Migration20161005104752ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wiki_versions') && $schema->tableExists('#__wiki_pages')) {
            $versions = $this->db->getQuery(true)
                ->select('v.id, v.pagetext, v.pagehtml')
                ->from('#__wiki_versions', 'v')
                ->innerJoin('#__wiki_pages AS p', 'p.id', 'v.page_id')
                ->where('p.scope', '=', 'group')
                ->whereLike('v.pagetext', '[?task=new Create a new article]')
                ->loadObjectList();

            foreach ($versions as $version) {
                $version->pagetext = str_replace(
                    '[?task=new Create a new article]',
                    '[?action=new Create a new article]',
                    $version->pagetext
                );
                $version->pagehtml = str_replace(
                    '/wiki/tasknew">Create a new article</a>',
                    '/wiki/?action=new">Create a new article</a>',
                    $version->pagehtml
                );

                $this->db->getQuery(true)
                    ->update('#__wiki_versions')
                    ->set(['pagetext' => $version->pagetext])
                    ->where('id', '=', $version->id)
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

        if ($schema->tableExists('#__wiki_versions') && $schema->tableExists('#__wiki_pages')) {
            $versions = $this->db->getQuery(true)
                ->select('v.id, v.pagetext, v.pagehtml')
                ->from('#__wiki_versions', 'v')
                ->innerJoin('#__wiki_pages AS p', 'p.id', 'v.page_id')
                ->where('p.scope', '=', 'group')
                ->whereLike('v.pagetext', '[?action=new Create a new article]')
                ->loadObjectList();

            foreach ($versions as $version) {
                $version->pagetext = str_replace(
                    '[?action=new Create a new article]',
                    '[?task=new Create a new article]',
                    $version->pagetext
                );
                $version->pagehtml = str_replace(
                    '/wiki/?action=new">Create a new article</a>',
                    '/wiki/tasknew">Create a new article</a>',
                    $version->pagehtml
                );

                $this->db->getQuery(true)
                    ->update('#__wiki_versions')
                    ->set(['pagetext' => $version->pagetext])
                    ->where('id', '=', $version->id)
                    ->execute();
            }
        }
    }
}
