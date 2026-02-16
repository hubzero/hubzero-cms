<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for fixing project notes that got assigned as group wiki pages
  *
**/
class Migration20160824181200ComWiki extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__wiki_pages')) {
            // Convert group pages
            $rows = $this->db->getQuery(true)
                ->select('w.*')
                ->select('g.cn')
                ->from('#__wiki_pages', 'w')
                ->leftJoin('#__xgroups AS g', 'w.scope_id', 'g.gidNumber')
                ->whereLike('w.path', '%/notes%')
                ->where('w.scope', '=', 'group')
                ->loadObjectList();

            foreach ($rows as $row) {
                if (substr($row->cn, 0, strlen('pr-')) != 'pr-') {
                    continue;
                }

                $path = array();
                $start = false;
                $p = explode('/', $row->path);
                foreach ($p as $s) {
                    if ($s == 'notes') {
                        $start = true;
                        continue;
                    }
                    if ($start) {
                        $path[] = $s;
                    }
                }
                $row->path  = implode('/', $path);
                $row->scope = 'project';

                $project = substr($row->cn, strlen('pr-'));

                $row->pidNumber = $this->db->getQuery(true)
                    ->select('id')
                    ->from('#__projects')
                    ->where('alias', '=', $project)
                    ->value('id');

                $this->db->getQuery(true)
                    ->update('#__wiki_pages')
                    ->set([
                        'scope'    => 'project',
                        'scope_id' => $row->pidNumber,
                        'path'     => $row->path
                    ])
                    ->where('id', '=', $row->id)
                    ->execute();
            }
        }
    }

    /**
     * Down
     **/
    public function down()
    {
    }
}
