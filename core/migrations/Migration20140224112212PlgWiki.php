<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for getting rid of duplicate section date entries
 *
*/
class Migration20140224112212PlgWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        /*
        $this->deletePluginEntry('hubzero', 'wikiparser');
        $this->deletePluginEntry('hubzero', 'wikieditortoolbar');
        $this->deletePluginEntry('hubzero', 'wikieditorwykiwyg');

        $this->addPluginEntry('wiki', 'parserdefault');
        $this->addPluginEntry('wiki', 'editortoolbar');
        $this->addPluginEntry('wiki', 'editorwykiwyg');
        */

        /* We do an update instead of the above remove/add so as to preserve state and params */
        $this->db->getQuery(true)
            ->update('#__extensions')
            ->set([
                'folder'  => 'wiki',
                'element' => 'parserdefault',
                'name'    => 'plg_wiki_parserdefault'
            ])
            ->where('folder', '=', 'hubzero')
            ->where('type', '=', 'plugin')
            ->where('element', '=', 'wikiparser')
            ->execute();

        $this->db->getQuery(true)
            ->update('#__extensions')
            ->set([
                'folder'  => 'wiki',
                'element' => 'editortoolbar',
                'name'    => 'plg_wiki_editortoolbar'
            ])
            ->where('folder', '=', 'hubzero')
            ->where('type', '=', 'plugin')
            ->where('element', '=', 'wikieditortoolbar')
            ->execute();

        $this->db->getQuery(true)
            ->update('#__extensions')
            ->set([
                'folder'  => 'wiki',
                'element' => 'editorwykiwyg',
                'name'    => 'plg_wiki_editorwykiwyg'
            ])
            ->where('folder', '=', 'hubzero')
            ->where('type', '=', 'plugin')
            ->where('element', '=', 'wikieditorwykiwyg')
            ->execute();
    }

    /**
     * Up
     **/
    public function down()
    {
        /*
        $this->addPluginEntry('hubzero', 'wikiparser');
        $this->addPluginEntry('hubzero', 'wikieditortoolbar');
        $this->addPluginEntry('hubzero', 'wikieditorwykiwyg');

        $this->deletePluginEntry('wiki', 'parserdefault');
        $this->deletePluginEntry('wiki', 'editortoolbar');
        $this->deletePluginEntry('wiki', 'editorwykiwyg');
        */

        /* We do an update instead of the above remove/add so as to preserve state and params */
        $this->db->getQuery(true)
            ->update('#__extensions')
            ->set([
                'folder'  => 'hubzero',
                'element' => 'wikiparser',
                'name'    => 'plg_hubzero_wikiparser'
            ])
            ->where('folder', '=', 'wiki')
            ->where('type', '=', 'plugin')
            ->where('element', '=', 'parserdefault')
            ->execute();

        $this->db->getQuery(true)
            ->update('#__extensions')
            ->set([
                'folder'  => 'hubzero',
                'element' => 'wikieditortoolbar',
                'name'    => 'plg_hubzero_wikieditortoolbar'
            ])
            ->where('folder', '=', 'wiki')
            ->where('type', '=', 'plugin')
            ->where('element', '=', 'editortoolbar')
            ->execute();

        $this->db->getQuery(true)
            ->update('#__extensions')
            ->set([
                'folder'  => 'hubzero',
                'element' => 'wikieditorwykiwyg',
                'name'    => 'plg_hubzero_wikieditorwykiwyg'
            ])
            ->where('folder', '=', 'wiki')
            ->where('type', '=', 'plugin')
            ->where('element', '=', 'editorwykiwyg')
            ->execute();
    }
}
