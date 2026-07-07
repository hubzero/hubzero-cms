<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Helpers;

/**
 * Collects Postfix outbound volume (messages that left the host) from the mail
 * log into hourly and daily rollups for the dashboard's volume chart.
 *
 * First run scans every readable maillog* file (including rotated .gz) to
 * backfill history; later runs read only the bytes appended since last time,
 * tracked by inode + byte offset, with rotation handled by reading the tail of
 * the previous file plus all of the new one. Buckets are self-consistent in the
 * log's own wall-clock (parse and format use the same timezone).
 */
class PostfixCollector
{
	protected static $months = array(
		'Jan' => 1, 'Feb' => 2, 'Mar' => 3, 'Apr' => 4, 'May' => 5, 'Jun' => 6,
		'Jul' => 7, 'Aug' => 8, 'Sep' => 9, 'Oct' => 10, 'Nov' => 11, 'Dec' => 12
	);

	/**
	 * @return  string
	 */
	public static function logPath()
	{
		return (string) \Component::params('com_massmail')->get('maillog_path', '/var/log/maillog');
	}

	/**
	 * An external delivery worth counting (excludes local/virtual aliasing).
	 *
	 * @param   string   $line
	 * @return  boolean
	 */
	protected static function isExternalSent($line)
	{
		if (strpos($line, 'status=sent') === false)
		{
			return false;
		}
		if (strpos($line, 'relay=local') !== false || strpos($line, 'relay=virtual') !== false)
		{
			return false;
		}
		return true;
	}

	/**
	 * Parse a syslog "Mon dd HH:MM:SS" prefix to a unix timestamp, inferring the
	 * (year-less) year from a reference time so December lines in a January file
	 * resolve correctly.
	 *
	 * @param   string   $line
	 * @param   integer  $refTime
	 * @return  integer|false
	 */
	protected static function parseTs($line, $refTime)
	{
		$mon = substr($line, 0, 3);
		if (!isset(self::$months[$mon]))
		{
			return false;
		}
		$month = self::$months[$mon];
		$day   = (int) substr($line, 4, 2);
		$h     = (int) substr($line, 7, 2);
		$mi    = (int) substr($line, 10, 2);
		$s     = (int) substr($line, 13, 2);

		if ($day < 1 || $day > 31)
		{
			return false;
		}

		$year = (int) date('Y', $refTime);
		$ts   = mktime($h, $mi, $s, $month, $day, $year);
		if ($ts === false)
		{
			return false;
		}
		// More than a day in the "future" vs the reference => previous year.
		if ($ts - $refTime > 86400)
		{
			$ts = mktime($h, $mi, $s, $month, $day, $year - 1);
		}
		return $ts;
	}

	/**
	 * Count external sends in an open stream, adding to hour/day buckets.
	 *
	 * @return  integer  messages counted
	 */
	protected static function scanStream($fh, $refTime, &$hours, &$days, $gz = false)
	{
		$n = 0;
		while (($line = ($gz ? gzgets($fh) : fgets($fh))) !== false)
		{
			if (!self::isExternalSent($line))
			{
				continue;
			}
			$ts = self::parseTs($line, $refTime);
			if ($ts === false)
			{
				continue;
			}
			$hk = date('Y-m-d H:00:00', $ts);
			$dk = date('Y-m-d', $ts);
			$hours[$hk] = isset($hours[$hk]) ? $hours[$hk] + 1 : 1;
			$days[$dk]  = isset($days[$dk])  ? $days[$dk] + 1  : 1;
			$n++;
		}
		return $n;
	}

	/**
	 * Run one collection pass.
	 *
	 * @return  array  summary
	 */
	public static function collect()
	{
		$db     = \App::get('db');
		$path   = self::logPath();
		$result = array('mode' => '', 'counted' => 0, 'hours' => 0, 'days' => 0, 'readable' => false);

		if (!$path || !is_readable($path))
		{
			$result['error'] = 'log unreadable';
			return $result;
		}
		$result['readable'] = true;

		$db->setQuery("SELECT last_inode, last_offset FROM `#__massmail_postfix_state` WHERE id = 1");
		$state = $db->loadObject();
		$lastInode  = $state ? (int) $state->last_inode : 0;
		$lastOffset = $state ? (int) $state->last_offset : 0;

		clearstatcache();
		$st = @stat($path);
		if ($st === false)
		{
			$result['error'] = 'stat failed';
			return $result;
		}
		$curInode = $st['ino'];
		$curSize  = $st['size'];
		$now      = time();

		$hours = array();
		$days  = array();

		if (!$state || $lastInode === 0)
		{
			// First run: scan all readable maillog* files (including .gz).
			$result['mode'] = 'first';
			$files = glob(dirname($path) . '/' . basename($path) . '*');
			if (!$files)
			{
				$files = array($path);
			}
			foreach ($files as $f)
			{
				if (!is_file($f) || !is_readable($f))
				{
					continue;
				}
				$ref = @filemtime($f) ?: $now;
				if (substr($f, -3) === '.gz')
				{
					if ($fh = @gzopen($f, 'rb'))
					{
						$result['counted'] += self::scanStream($fh, $ref, $hours, $days, true);
						gzclose($fh);
					}
				}
				elseif ($fh = @fopen($f, 'rb'))
				{
					$result['counted'] += self::scanStream($fh, $ref, $hours, $days, false);
					fclose($fh);
				}
			}
		}
		elseif ($curInode == $lastInode)
		{
			// Same file: read only the bytes appended since last time.
			$result['mode'] = 'incremental';
			$start = ($curSize < $lastOffset) ? 0 : $lastOffset;
			if ($fh = @fopen($path, 'rb'))
			{
				if ($start > 0)
				{
					fseek($fh, $start);
				}
				$result['counted'] += self::scanStream($fh, $now, $hours, $days, false);
				fclose($fh);
			}
		}
		else
		{
			// Rotation: read the tail of the previous file (matched by inode),
			// then all of the new current file.
			$result['mode'] = 'rotated';
			foreach (glob(dirname($path) . '/' . basename($path) . '*') as $f)
			{
				if (!is_file($f) || !is_readable($f) || substr($f, -3) === '.gz')
				{
					continue;
				}
				$fst = @stat($f);
				if ($fst && $fst['ino'] == $lastInode && $fst['size'] >= $lastOffset)
				{
					$ref = @filemtime($f) ?: $now;
					if ($fh = @fopen($f, 'rb'))
					{
						fseek($fh, $lastOffset);
						$result['counted'] += self::scanStream($fh, $ref, $hours, $days, false);
						fclose($fh);
					}
					break;
				}
			}
			if ($fh = @fopen($path, 'rb'))
			{
				$result['counted'] += self::scanStream($fh, $now, $hours, $days, false);
				fclose($fh);
			}
		}

		// Upsert buckets (add, since we only ever read each byte once).
		foreach ($hours as $hk => $c)
		{
			$db->setQuery(
				"INSERT INTO `#__massmail_postfix_hourly` (`hour`, `sent`) VALUES (" . $db->quote($hk) . ", " . (int) $c . ")
				 ON DUPLICATE KEY UPDATE `sent` = `sent` + " . (int) $c
			);
			$db->execute();
		}
		foreach ($days as $dk => $c)
		{
			$db->setQuery(
				"INSERT INTO `#__massmail_postfix_daily` (`day`, `sent`) VALUES (" . $db->quote($dk) . ", " . (int) $c . ")
				 ON DUPLICATE KEY UPDATE `sent` = `sent` + " . (int) $c
			);
			$db->execute();
		}
		$result['hours'] = count($hours);
		$result['days']  = count($days);

		// Advance the read position.
		$db->setQuery(
			"UPDATE `#__massmail_postfix_state`
			 SET last_inode = " . (int) $curInode . ", last_offset = " . (int) $curSize . ", last_run = " . $db->quote(\Date::toSql()) . "
			 WHERE id = 1"
		);
		$db->execute();

		// Retention: keep fine-grained data only while useful; keep daily long.
		$db->setQuery("DELETE FROM `#__massmail_postfix_hourly` WHERE `hour` < (NOW() - INTERVAL 45 DAY)");
		$db->execute();
		$db->setQuery("DELETE FROM `#__massmail_postfix_daily` WHERE `day` < (CURDATE() - INTERVAL 730 DAY)");
		$db->execute();

		return $result;
	}
}
