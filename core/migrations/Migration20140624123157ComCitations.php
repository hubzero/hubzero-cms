<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing uid from username to int
 *
*/
class Migration20140624123157ComCitations extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $columns = $this->db->schema()->getTableColumns('#__citations');
        $uidField = $columns['uid'] ?? '';

        // if we have an INT already, were good to go
        if (stripos($uidField, 'int') !== false) {
            return;
        }

        // load all citations
        $citations = $this->db->getQuery(true)
            ->select(['id', 'uid'])
            ->from('#__citations')
            ->loadObjectList();
        foreach ($citations as $citation) {
            if (!is_numeric($citation->uid)) {
                $newId = 62;
                $profile = \Hubzero\User\User::oneOrNew($citation->uid);
                if ($profile->get('id')) {
                    $newId = $profile->get('id');
                }

                $this->db->getQuery(true)
                    ->update('#__citations')
                    ->set(['uid' => $newId])
                    ->where('id', '=', $citation->id)
                    ->execute();
            }
        }

        // change column type
        $this->db->schema()->modifyColumn('#__citations', 'uid')->integer(11);
    }
}
