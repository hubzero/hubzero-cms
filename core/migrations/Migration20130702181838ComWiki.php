<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing nanoHUB reference in wiki formatting page
 *
*/
class Migration20130702181838ComWiki extends Base
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
                ->where('wp.pagename', '=', 'Help:WikiFormatting')
                ->order('version', 'DESC')
                ->first();

            if ($result) {
                $pagetext = preg_replace('/(nanoHUB)/', 'This site', $result->pagetext);
                $pagehtml = preg_replace('/(nanoHUB)/', 'This site', $result->pagehtml);

                $this->db->getQuery(true)
                    ->update('#__wiki_version')
                    ->set(['pagetext' => $pagetext, 'pagehtml' => $pagehtml])
                    ->where('id', '=', $result->id)
                    ->execute();
            }
        }
    }
}
