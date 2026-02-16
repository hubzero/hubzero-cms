<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing improperly stored URLs from the plg_content_collect plugin
**/
class Migration20141121144051ComCollections extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__collections_items')) {
            $query = $this->db->getQuery(true)
                ->select(['id', 'url'])
                ->from('#__collections_items')
                ->where('type', '=', 'article')
                ->where('url', 'LIKE', '%&', false); // false to prevent auto-escaping of %

            if ($articles = $query->loadObjectList()) {
                foreach ($articles as $article) {
                    $article->url = rtrim($article->url, '&');
                    $this->db->getQuery(true)
                        ->update('#__collections_items')
                        ->set(['url' => $this->db->quote($article->url)])
                        ->where('id', '=', $article->id)
                        ->execute();
                }
            }
        }
    }
}
