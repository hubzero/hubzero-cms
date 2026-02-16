<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for changing time to track start and end times of entries
 *
*/
class Migration20141120161609ComTime extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        if (!$schema->tableExists('#__time_records') || !$schema->hasColumn('#__time_records', 'date')) {
            return;
        }

        $cols = $schema->getTableColumns('#__time_records', false);

        if ($cols['date']->Type == 'date') {
            $schema->modifyColumn('#__time_records', 'date')
                ->datetime()
                ->notNull()
                ->execute();

            $results = $this->db->getQuery(true)
                ->select(['id', 'date'])
                ->from('#__time_records')
                ->loadObjectList();

            if ($results && count($results) > 0) {
                $total = count($results);
                $i     = 0;
                $this->callback('progress', 'init', array('Updating existing start dates to include time:'));

                foreach ($results as $result) {
                    $date = with(
                        new \Hubzero\Utility\Date($result->date, \App::get('config')->get('offset'))
                    )->toSql();

                    $this->db->getQuery(true)
                        ->update('#__time_records')
                        ->set(['date' => $date])
                        ->where('id', '=', (int)$result->id)
                        ->execute();

                    $i++;
                    $progress = round($i / $total * 100);
                    $this->callback('progress', 'setProgress', array($progress));
                }

                $this->callback('progress', 'done');
            }
        }

        if ($schema->hasColumn('#__time_records', 'date') && !$schema->hasColumn('#__time_records', 'end')) {
            $schema->addColumn('#__time_records', 'end')
                ->datetime()
                ->notNull()
                ->after('date')
                ->execute();

            $results = $this->db->getQuery(true)
                ->select(['id', 'date', 'time'])
                ->from('#__time_records')
                ->loadObjectList();

            if ($results && count($results) > 0) {
                $total = count($results);
                $i     = 0;
                $this->callback('progress', 'init', array('Adding end dates based on record duration:'));

                foreach ($results as $result) {
                    $date = date('Y-m-d H:i:s', strtotime($result->date) + $result->time * 3600);

                    $this->db->getQuery(true)
                        ->update('#__time_records')
                        ->set(['end' => $date])
                        ->where('id', '=', (int)$result->id)
                        ->execute();

                    $i++;
                    $progress = round($i / $total * 100);
                    $this->callback('progress', 'setProgress', array($progress));
                }

                $this->callback('progress', 'done');
            }
        }
    }
}
