<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for allowing registered users to create questions by default.
 **/
class Migration20150715123430ComAnswers extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if ($schema->tableExists('#__assets')) {
            $rules = '{"core.admin":{"7":1},"core.manage":{"6":1},"core.create":{"2":1},'
                . '"core.delete":[],"core.edit":{"2":1},"core.edit.state":[],"core.edit.own":[]}';

            $query = $this->db->getQuery(true)
                ->select('id')
                ->from('#__assets')
                ->where('name', '=', 'com_answers');
            $id = $query->value('id');

            if (!$id) {
                $parent = \Hubzero\Access\Asset::oneOrNew(\Hubzero\Access\Asset::getRootId());

                $tbl = \Hubzero\Access\Asset::blank();
                $tbl->set('level', 1);
                $tbl->set('parent', 1);
                $tbl->set('name', 'com_answers');
                $tbl->set('title', 'com_answers');
                $tbl->set('rules', $rules);
                $tbl->saveAsLastChildOf($parent);
            } else {
                // Set the first zone as default
                $this->db->getQuery(true)
                    ->update('#__assets')
                    ->set(['rules' => $rules])
                    ->where('id', '=', $id)
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

        if ($schema->tableExists('#__assets')) {
            $rules = '{"core.admin":{"7":1},"core.manage":{"6":1},"core.view":[],"core.create":[],'
                . '"core.delete":[],"core.edit":[],"core.edit.state":[],"core.edit.own":[]}';

            $query = $this->db->getQuery(true)
                ->select('id')
                ->from('#__assets')
                ->where('name', '=', 'com_answers');
            $id = $query->value('id');

            if (!$id) {
                $parent = \Hubzero\Access\Asset::oneOrNew(\Hubzero\Access\Asset::getRootId());

                $tbl = \Hubzero\Access\Asset::blank();
                $tbl->set('level', 1);
                $tbl->set('parent', 1);
                $tbl->set('name', 'com_answers');
                $tbl->set('title', 'com_answers');
                $tbl->set('rules', $rules);
                $tbl->saveAsLastChildOf($parent);
            } else {
                // Set the first zone as default
                $this->db->getQuery(true)
                    ->update('#__assets')
                    ->set(['rules' => $rules])
                    ->where('id', '=', $id)
                    ->execute();
            }
        }
    }
}
