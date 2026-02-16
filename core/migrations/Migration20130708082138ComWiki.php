<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing 'xsearch' reference in wiki formatting page
 *
*/
class Migration20130708082138ComWiki extends Base
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
                ->select('wv.*')
                ->from('#__wiki_page', 'wp')
                ->join('#__wiki_version AS wv', 'wp.id', 'wv.pageid')
                ->where('wp.pagename', '=', 'MainPage')
                ->beginOrGroup()
                    ->where('wp.group_cn', '=', '')
                    ->orWhereIsNull('wp.group_cn')
                ->endAndGroup()
                ->order('wv.version', 'DESC')
                ->first();

            if ($result) {
                $pagetext = preg_replace('/(xsearch)/', 'search', $result->pagetext);
                $pagehtml = preg_replace('/(xsearch)/', 'search', $result->pagehtml);

                $this->db->getQuery(true)
                    ->update('#__wiki_version')
                    ->set(['pagetext' => $pagetext, 'pagehtml' => $pagehtml])
                    ->where('id', '=', $result->id)
                    ->execute();
            }
        }
    }
}
