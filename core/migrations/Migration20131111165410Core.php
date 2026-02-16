<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Migrations;

use Hubzero\Content\Migration\Base;

/**
 * Migration script for stats setup
  *
**/
class Migration20131111165410Core extends Base
{
    /**
     * Up
     **/
    public function up()
    {
        $schema = $this->db->schema();

        $init_params = array(
            "period"     => "14",
            "chart_path" => "/site/stats/chart_resources/",
            "map_path"   => "/site/stats/resource_maps/"
        );

        if ($schema->tableExists('#__extensions')) {
            $query = $this->db->getQuery(true)
                ->select('params')
                ->from('#__extensions')
                ->where('folder', '=', 'resources')
                ->where('element', '=', 'usage');
            $result = $query->value('params');

            $params = (array) json_decode($result);
        } else {
            $query = $this->db->getQuery(true)
                ->select('params')
                ->from('#__plugins')
                ->where('folder', '=', 'resources')
                ->where('element', '=', 'usage');
            $result = $query->value('params');

            $params = array();

            if (!empty($result)) {
                $ar = explode("\n", $result);

                foreach ($ar as $a) {
                    $a = trim($a);
                    if (empty($a)) {
                        continue;
                    }

                    $ar2     = explode("=", $a, 2);
                    $params[$ar2[0]] = (isset($ar2[1])) ? $ar2[1] : '';
                }
            }
        }

        $found = array();

        if (!empty($params) && count($params) > 0) {
            foreach ($params as $k => $v) {
                if ($k == 'period' && $v == '15') {
                    $found[] = 'period';
                    $params[$k] = '14';
                } elseif ($k == 'chart_path' && $v == '/site/usage/chart_resources/') {
                    $found[] = 'chart_path';
                    $params[$k] = '/site/stats/chart_resources/';
                } elseif ($k == 'map_path' && $v == '/site/usage/resource_maps/') {
                    $found[] = 'map_path';
                    $params[$k] = '/site/stats/resource_maps/';
                }
            }

            if (!in_array('period', $found)) {
                $params['period'] = '14';
            }
            if (!in_array('chart_path', $found)) {
                $params['chart_path'] = '/site/stats/chart_resources/';
            }
            if (!in_array('map_path', $found)) {
                $params['map_path'] = '/site/stats/resource_maps/';
            }
        } else {
            $params = $init_params;
        }

        if ($schema->tableExists('#__extensions')) {
            $params = json_encode($params);

            $this->db->getQuery(true)
                ->update('#__extensions')
                ->set(['params' => $params])
                ->where('folder', '=', 'resources')
                ->where('element', '=', 'usage')
                ->execute();
        } else {
            $p = '';
            foreach ($params as $k => $v) {
                $p .= "{$k}={$v}\n";
            }

            $params = $p;

            $this->db->getQuery(true)
                ->update('#__plugins')
                ->set(['params' => $params])
                ->where('folder', '=', 'resources')
                ->where('element', '=', 'usage')
                ->execute();
        }

        if (!$schema->tableExists('#__resource_stats_tools_tops')) {
            $schema->createTable('#__resource_stats_tools_tops')
                ->tinyInteger('top')->default(0)
                ->string('name', 128)->default('')
                ->tinyInteger('valfmt')->default(0)
                ->tinyInteger('size')->default(0)
                ->primaryKey('top')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__resource_stats_tools_tops')
                ->columns(['top', 'name', 'valfmt', 'size'])
                ->values([1, 'Users By Country Of Residence', 1, 5])
                ->values([2, 'Top Domains By User Count', 1, 5])
                ->values([3, 'Users By Organization Type', 1, 5])
                ->execute();
        }

        if (!$schema->tableExists('#__stats_tops')) {
            $schema->createTable('#__stats_tops')
                ->tinyInteger('id')->default(0)
                ->string('name', 128)->default('')
                ->tinyInteger('valfmt')->default(0)
                ->tinyInteger('size')->default(0)
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();

            $this->db->getQuery(true)
                ->insert('#__stats_tops')
                ->columns(['id', 'name', 'valfmt', 'size'])
                ->values([1, 'Top Tools by Ranking', 1, 5])
                ->values([2, 'Top Tools by Simulation Users', 1, 5])
                ->values([3, 'Top Tools by Interactive Sessions', 1, 5])
                ->values([4, 'Top Tools by Simulation Sessions', 1, 5])
                ->values([5, 'Top Tools by Simulation Runs', 1, 5])
                ->values([6, 'Top Tools by Simulation Wall Time', 2, 5])
                ->values([7, 'Top Tools by Simulation CPU Time', 2, 5])
                ->values([8, 'Top Tools by Simulation Interaction Time', 2, 5])
                ->values([9, 'Top Tools by Citations', 1, 5])
                ->execute();
        }

        if (!$schema->tableExists('#__citations_secondary')) {
            $schema->createTable('#__citations_secondary')
                ->integer('id', ['autoIncrement' => true])
                ->integer('cid')
                ->integer('sec_cits_cnt')->nullable()
                ->tinyText('search_string')->nullable()
                ->primaryKey('id')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__session_geo')) {
            $schema->createTable('#__session_geo')
                ->string('session_id', 200)->default('0')
                ->string('username', 150)->default('')
                ->string('time', 14)->default('')
                ->tinyInteger('guest')->default(1)
                ->integer('userid')->default(0)
                ->string('ip', 15)->nullable()
                ->string('host', 128)->nullable()
                ->string('domain', 128)->nullable()
                ->tinyInteger('signed')->default(0)
                ->char('countrySHORT', 2)->nullable()
                ->string('countryLONG', 64)->nullable()
                ->string('ipREGION', 128)->nullable()
                ->string('ipCITY', 128)->nullable()
                ->double('ipLATITUDE')->nullable()
                ->double('ipLONGITUDE')->nullable()
                ->tinyInteger('bot')->default(0)
                ->primaryKey('session_id')
                ->index('userid', 'userid')
                ->index('time', 'time')
                ->index('ip', 'ip')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }

        if (!$schema->tableExists('#__metrics_ipgeo_cache')) {
            $schema->createTable('#__metrics_ipgeo_cache')
                ->integer('ip')->default(0)
                ->char('countrySHORT', 2)->default('')
                ->string('countryLONG', 64)->default('')
                ->string('ipREGION', 128)->default('')
                ->string('ipCITY', 128)->default('')
                ->double('ipLATITUDE')->nullable()
                ->double('ipLONGITUDE')->nullable()
                ->timestamp('lookup_datetime')
                ->primaryKey('ip')
                ->index('lookup_datetime', 'lookup_datetime')
                ->engine('MyISAM')
                ->charset('utf8')
                ->execute();
        }
    }
}
