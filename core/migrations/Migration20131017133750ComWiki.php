<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing some dated references to topics, rather than wiki
 *
 */
class Migration20131017133750ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (
            $schema->tableExists('#__wiki_page')
            && $schema->tableExists('#__wiki_version')
            && $schema->hasColumn('#__wiki_version', 'pageid')
        ) {
            $result = $this->db->getQuery(true)
                ->select('*')
                ->from('#__wiki_page', 'wp')
                ->innerJoin('#__wiki_version AS wv', 'wp.id', 'wv.pageid')
                ->where('wp.pagename', '=', 'MainPage')
                ->beginOrGroup()
                    ->where('wp.group_cn', '=', '')
                    ->orWhereIsNull('wp.group_cn')
                ->endAndGroup()
                ->order('wv.version', 'DESC')
                ->first();

            if ($result) {
                $pagetext = preg_replace('/(Topic)/', 'Wiki', $result->pagetext);
                $pagehtml = preg_replace('/(Topic)/', 'Wiki', $result->pagehtml);
                $pagetext = preg_replace('/(topic)/', 'wiki', $pagetext);
                $pagehtml = preg_replace('/(topic)/', 'wiki', $pagehtml);

                $this->db->getQuery(true)
                    ->update('#__wiki_version')
                    ->set([
                        'pagetext' => $pagetext,
                        'pagehtml' => $pagehtml
                    ])
                    ->where('pageid', '=', $result->pageid)
                    ->where('version', '=', $result->version)
                    ->execute();

                $title = preg_replace('/(Topic)/', 'Wiki', $result->title);

                $this->db->getQuery(true)
                    ->update('#__wiki_page')
                    ->set(['title' => $title])
                    ->where('id', '=', $result->pageid)
                    ->execute();
            }
        }
    }
}
