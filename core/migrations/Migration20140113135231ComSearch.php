<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for deleting com_search
 *
*/
class Migration20140113135231ComSearch extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $id = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', 'search')
            ->where('protected', '=', 1)
            ->value('extension_id');

        if ($id) {
            $this->deleteComponentEntry('search');

            $this->deletePluginEntry('search');

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set([
                    'element' => 'com_search',
                    'name'    => 'Search'
                ])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_ysearch')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__menu')
                ->set([
                    'title' => 'com_search',
                    'alias' => 'search',
                    'path'  => 'search',
                    'link'  => 'index.php?option=com_search&task=configure'
                ])
                ->where('title', '=', 'com_ysearch')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['folder' => 'search'])
                ->where('folder', '=', 'ysearch')
                ->where('type', '=', 'plugin')
                ->execute();

            $results = $this->db->getQuery(true)
                ->select(['extension_id', 'name', 'element', 'folder'])
                ->from('#__extensions')
                ->where('type', '=', 'plugin')
                ->where('folder', '=', 'search')
                ->loadObjectList();

            if ($results) {
                foreach ($results as $result) {
                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['name' => 'plg_' . $result->folder . '_' . $result->element])
                        ->where('extension_id', '=', (int)$result->extension_id)
                        ->execute();
                }
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
        $id = $this->db->getQuery(true)
            ->select('extension_id')
            ->from('#__extensions')
            ->where('type', '=', 'component')
            ->where('element', '=', 'com_search')
            ->where('protected', '=', 0)
            ->value('extension_id');

        if ($id) {
            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set([
                    'element' => 'com_ysearch',
                    'name'    => 'YSearch'
                ])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_search')
                ->where('protected', '=', 0)
                ->execute();

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['folder' => 'ysearch'])
                ->where('folder', '=', 'search')
                ->where('type', '=', 'plugin')
                ->execute();

            $this->db->getQuery(true)
                ->update('#__menu')
                ->set([
                    'title' => 'com_ysearch',
                    'alias' => 'ysearch',
                    'path'  => 'ysearch',
                    'link'  => 'index.php?option=com_ysearch&task=configure'
                ])
                ->where('title', '=', 'com_search')
                ->execute();

            $this->addComponentEntry('search');

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['protected' => 1])
                ->where('type', '=', 'component')
                ->where('element', '=', 'com_search')
                ->execute();

            $results = $this->db->getQuery(true)
                ->select(['extension_id', 'name', 'element', 'folder'])
                ->from('#__extensions')
                ->where('type', '=', 'plugin')
                ->where('folder', '=', 'ysearch')
                ->loadObjectList();

            if ($results) {
                foreach ($results as $result) {
                    $this->db->getQuery(true)
                        ->update('#__extensions')
                        ->set(['name' => 'plg_' . $result->folder . '_' . $result->element])
                        ->where('extension_id', '=', (int)$result->extension_id)
                        ->execute();
                }
            }
        }
    }
}
