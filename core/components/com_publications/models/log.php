<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Model;
use Components\Publications\Helpers\Html;
use Components\Publications\Tables;
use Hubzero\Facades\Component;

/**
 * Publications log model class
 */
class Log extends Model
{
    /**
     * Table class name
     *
     * @var  string
     */
    protected $tableClass = '\\Components\\Publications\\Tables\\Log';

    /**
     * Registry
     *
     * @var  object|null
     */
    private $config = null;

    /**
     * Table class object
     *
     * @var  object
     */
    public $pubLog = null;

    /**
     * Parse log file
     *
     * @param   object  $pubLog
     * @param   string  $logFile
     * @param   string  $type     primary or view
     * @param   string  $category
     * @return  int|null
     */
    public function parseLog($pubLog = null, $logFile = null, $type = 'view', $category = 'unique')
    {
        $ips = [];
        $filtered = [];
        $unique = 0;
        $all = 0;

        if (!is_file($logFile)) {
            return null;
        }
        if (!$pubLog) {
            $pubLog = new $this->tableClass($this->db);
        }

        $file_handle = fopen($logFile, "r");
        while (!feof($file_handle)) {
            $line = fgets($file_handle);
            $parts = explode("\t", $line);

            if ((count($parts) == 4) && trim($parts[3]) == $type) {
                // One form per address, so 2001:DB8::1 and 2001:db8::1 are
                // one visitor; unparseable text is kept as it was
                $ip = \Hubzero\Utility\Ip::normalize(trim($parts[1])) ?: trim($parts[1]);

                // Check if bot
                if ($pubLog->checkBotIp($ip)) {
                    // Skip bots
                    continue;
                }

                // Keyed by address: in_array() over the list was quadratic
                if (!isset($ips[$ip])) {
                    // Add to list of unique IPs
                    $ips[$ip] = true;
                    $unique++;
                }
                // Add to non-unique list
                $all++;
            }
        }
        fclose($file_handle);
        return $category == 'filtered' ? $all : $unique;
    }

    /**
     * Get user data from log file
     *
     * @param   integer  $pid
     * @param   string   $type
     * @param   integer  $numMonths
     * @param   boolean  $includeCurrent
     * @return  mixed
     */
    public function digestLogs($pid = null, $type = 'view', $numMonths = 1, $includeCurrent = false)
    {
        if (!$pid) {
            return false;
        }

        $stats = ['unique' => [], 'filtered' => []];
        $types = ($type == 'all') ? ['view', 'primary'] : [$type];

        // Get all public versions
        $row  = new Tables\Version($this->db);
        $versions = $row->getVersions($pid, $filters = ['public' => 1]);

        if (!$versions) {
            return $stats;
        }

        // Collect data for each version
        foreach ($versions as $version) {
            $logPath = $this->getLogPath($pid, $version->id);
            if (!$logPath) {
                continue;
            }

            $n = ($includeCurrent) ? 0 : 1;

            for ($a = $numMonths; $a >= $n; $a--) {
                $yearNum  = intval(date('y', strtotime("-" . $a . " month")));
                $monthNum = intval(date('m', strtotime("-" . $a . " month")));
                $date     = date('Y-m', strtotime("-" . $a . " month"));
                $logFile  = 'pub-' . $pid . '-v-' . $version->id . '.' . $date . '.log';

                $fpath = $logPath . DS . $logFile;
                $mo = date('M', strtotime("-" . $a . " month"));

                $pubLog = new $this->tableClass($this->db);

                foreach ($types as $type) {
                    if (!trim($type)) {
                        continue;
                    }
                    if (!isset($stats['unique'][$type])) {
                        $stats['unique'][$type] = [];
                    }
                    if (!isset($stats['filtered'][$type])) {
                        $stats['filtered'][$type] = [];
                    }
                    if (!isset($stats['unique'][$type][$mo])) {
                        $stats['unique'][$type][$mo] = 0;
                    }
                    if (!isset($stats['filtered'][$type][$mo])) {
                        $stats['filtered'][$type][$mo] = 0;
                    }
                    if (is_file($fpath)) {
                        // Get count of unique views/accesses
                        $count = $this->parseLog($pubLog, $fpath, $type);
                        $stats['unique'][$type][$mo] = $stats['unique'][$type][$mo] + $count;

                        // Log unique
                        $pubLog->logParsed(
                            $pid,
                            $version->id,
                            date('y', strtotime($date)),
                            date('m', strtotime($date)),
                            $count,
                            $type,
                            'unique',
                        );

                        // Get filtered count
                        $count = $this->parseLog($pubLog, $fpath, $type, 'filtered');
                        $stats['filtered'][$type][$mo] = $stats['filtered'][$type][$mo] + $count;

                        // Log filtered
                        $pubLog->logParsed(
                            $pid,
                            $version->id,
                            date('y', strtotime($date)),
                            date('m', strtotime($date)),
                            $count,
                            $type,
                            'filtered',
                        );
                    }
                }
            }
        }

        return $stats;
    }

    /**
     * Get path to log file
     *
     * @param   integer  $pid
     * @param   integer  $vid
     * @return  mixed
     */
    public function getLogPath($pid = 0, $vid = 0)
    {
        if (!isset($this->config)) {
            $this->config = Component::params('com_publications');
        }
        if (!$pid || !$vid) {
            return false;
        }

        // Build log path (access logs)
        $logPath = Html::buildPubPath($pid, $vid, $this->config->get('webpath'), 'logs', 1);

        if (!is_dir($logPath)) {
            return false;
        }
        return $logPath;
    }
}
