<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Massmail\Models\Mailing;
use Components\Massmail\Models\Recipient;
use Components\Massmail\Models\Status as StatusModel;
use Components\Massmail\Helpers\Queue;
use Components\Massmail\Helpers\MailLog;
use Components\Massmail\Helpers\Rate;
use Components\Massmail\Helpers\Sender;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'queue.php';
require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'log.php';
require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'rate.php';
require_once dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'sender.php';

/**
 * Admin controller: mass-mail status dashboard.
 */
class Status extends AdminController
{
	/**
	 * Show the dashboard
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$db     = App::get('db');
		$params = \Component::params('com_massmail');

		// --- Live rate signals ------------------------------------------------
		$queue          = Queue::status();
		$queueSize      = $queue['available'] ? $queue['size'] : -1;
		$pfRunning      = Queue::isRunning();
		$pfConfig       = Queue::config();
		$ourLastHour    = Sender::sentLastHour();
		$systemLastHour = MailLog::sentLastHour();

		$hourly = (int) $params->get('hourly_limit', 300);

		// Cron cadence -> per-run pace (mirrors Sender::run()).
		$db->setQuery("SELECT state, last_run, recurrence FROM `#__cron_jobs` WHERE plugin = 'massmail' AND event = 'processMassmailQueue' LIMIT 1");
		$cron         = $db->loadObject();
		$ticksPerHour = $cron ? Rate::firingsPerHour($cron->recurrence) : 0;
		if ($ticksPerHour < 1) { $ticksPerHour = 12; }
		$perRun = Rate::perTick($hourly, $ticksPerHour);

		// Rolling-hour headroom, same accounting the sender uses: the queue is
		// committed egress and counts against the limit.
		if ($systemLastHour >= 0)
		{
			$deliveredLastHour = $systemLastHour;
			$queueForBudget    = $queueSize;
			$effectiveSent     = $systemLastHour;
		}
		else
		{
			$deliveredLastHour = $ourLastHour;
			$queueForBudget    = -1;
			$effectiveSent     = $ourLastHour;
		}
		$queuedNow       = ($queueForBudget < 0) ? 0 : $queueForBudget;
		$hourlyRemaining = max(0, $hourly - $effectiveSent - $queuedNow);
		$nextRunBudget   = Rate::budget($hourly, $deliveredLastHour, $queueForBudget, $perRun);

		// --- Message counts ---------------------------------------------------
		$db->setQuery("SELECT COUNT(*) FROM `#__massmail_recipients` WHERE status = " . Recipient::SENT);
		$messagesSent = (int) $db->loadResult();

		$db->setQuery("SELECT COUNT(*) FROM `#__massmail_recipients` WHERE status = " . Recipient::FAILED);
		$messagesFailed = (int) $db->loadResult();

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__massmail_recipients` r
			 JOIN `#__massmail_mailings` m ON m.id = r.mailing_id
			 WHERE r.status = " . Recipient::PENDING . "
			 AND m.status IN (" . $db->quote(Mailing::STATUS_SENDING) . ", " . $db->quote(Mailing::STATUS_PREPARING) . ", " . $db->quote(Mailing::STATUS_PAUSED) . ")"
		);
		$messagesPending = (int) $db->loadResult();

		// --- Mass mail actually sent (from our own recipient records) --------
		$mmLastHour = $this->series($this->sentMap('%Y-%m-%d %H:%i', '1 HOUR'), 60, 60, 'Y-m-d H:i', 'H:i');
		$mmHourly   = $this->series($this->sentMap('%Y-%m-%d %H', '48 HOUR'), 48, 3600, 'Y-m-d H', 'H');
		$mmDaily    = $this->series($this->sentMap('%Y-%m-%d', '29 DAY'), 30, 86400, 'Y-m-d', 'n/j');

		// --- Queue depth over time (last 24h of cron snapshots) --------------
		// Peak queue depth over each postfix chart's window (for the pill).
		$db->setQuery("SELECT COALESCE(MAX(depth), 0) FROM `#__massmail_queue_samples` WHERE recorded >= (UTC_TIMESTAMP() - INTERVAL 1 HOUR)");
		$qPeakLastHour = (int) $db->loadResult();
		$db->setQuery("SELECT COALESCE(MAX(depth), 0) FROM `#__massmail_queue_samples` WHERE recorded >= (UTC_TIMESTAMP() - INTERVAL 48 HOUR)");
		$qPeak48h = (int) $db->loadResult();

		// Queue-depth lines aligned to the postfix chart buckets (for overlay).
		$qLastHour = $this->queueLine(60, 60, '%Y-%m-%d %H:%i', 'Y-m-d H:i');
		$qHourly   = $this->queueLine(48, 3600, '%Y-%m-%d %H', 'Y-m-d H');

		// Daily queue peak (30d) for the 30-day chart overlay + pill.
		$db->setQuery("SELECT `day`, `max` FROM `#__massmail_queue_daily` WHERE `day` >= (CURDATE() - INTERVAL 29 DAY)");
		$qdmap = array();
		foreach ($db->loadObjectList() as $r) { $qdmap[$r->day] = (int) $r->max; }
		$q30 = array();
		for ($i = 29; $i >= 0; $i--)
		{
			$d = date('Y-m-d', strtotime("-$i day"));
			$q30[] = isset($qdmap[$d]) ? $qdmap[$d] : 0;
		}
		$qPeak30d = $q30 ? max($q30) : 0;

		// --- Postfix outbound volume (all senders, from the log collector) ---
		$db->setQuery("SELECT `day`, sent FROM `#__massmail_postfix_daily` WHERE `day` >= (CURDATE() - INTERVAL 29 DAY) ORDER BY `day`");
		$dmap = array();
		foreach ($db->loadObjectList() as $r) { $dmap[$r->day] = (int) $r->sent; }
		$dailyVolume = array();
		for ($i = 29; $i >= 0; $i--)
		{
			$d = date('Y-m-d', strtotime("-$i day"));
			$dailyVolume[] = array('label' => date('n/j', strtotime($d)), 'value' => isset($dmap[$d]) ? $dmap[$d] : 0);
		}

		$db->setQuery("SELECT `hour`, sent FROM `#__massmail_postfix_hourly` WHERE `hour` >= (NOW() - INTERVAL 48 HOUR) ORDER BY `hour`");
		$hmap = array();
		foreach ($db->loadObjectList() as $r) { $hmap[$r->hour] = (int) $r->sent; }
		$hourlyVolume = array();
		for ($i = 47; $i >= 0; $i--)
		{
			$t = time() - ($i * 3600);
			$k = date('Y-m-d H:00:00', $t);
			$hourlyVolume[] = array('label' => date('H', $t), 'value' => isset($hmap[$k]) ? $hmap[$k] : 0);
		}

		// Last 60 minutes, by minute -- computed live from the log tail.
		$lastHourVolume = MailLog::sentLastHourByMinute();

		$db->setQuery("SELECT last_run, last_sent FROM `#__massmail_postfix_state` WHERE id = 1");
		$pfState = $db->loadObject();
		$collectorRun = $pfState ? $pfState->last_run : null;
		$lastSent     = $pfState ? $pfState->last_sent : null;

		$this->view
			->set('params', $params)
			->set('snapshot', StatusModel::instance())
			->set('queue', $queue)
			->set('queueSize', $queueSize)
			->set('pfRunning', $pfRunning)
			->set('pfConfig', $pfConfig)
			->set('lastSent', $lastSent)
			->set('ourLastHour', $ourLastHour)
			->set('systemLastHour', $systemLastHour)
			->set('effectiveSent', $effectiveSent)
			->set('hourly', $hourly)
			->set('perRun', $perRun)
			->set('ticksPerHour', $ticksPerHour)
			->set('hourlyRemaining', $hourlyRemaining)
			->set('nextRunBudget', $nextRunBudget)
			->set('messagesSent', $messagesSent)
			->set('messagesFailed', $messagesFailed)
			->set('messagesPending', $messagesPending)
			->set('mmLastHour', $mmLastHour)
			->set('mmHourly', $mmHourly)
			->set('mmDaily', $mmDaily)
			->set('qPeakLastHour', $qPeakLastHour)
			->set('qPeak48h', $qPeak48h)
			->set('dailyVolume', $dailyVolume)
			->set('hourlyVolume', $hourlyVolume)
			->set('lastHourVolume', $lastHourVolume)
			->set('qLastHour', $qLastHour)
			->set('qHourly', $qHourly)
			->set('q30', $q30)
			->set('qPeak30d', $qPeak30d)
			->set('collectorRun', $collectorRun)
			->set('cron', $cron)
			->display();
	}

	/**
	 * Map of "sent" recipient counts keyed by a DATE_FORMAT of sent_on (UTC),
	 * over a trailing interval.
	 *
	 * @param   string  $fmt       MySQL DATE_FORMAT string
	 * @param   string  $interval  e.g. '48 HOUR', '29 DAY'
	 * @return  array
	 */
	protected function sentMap($fmt, $interval)
	{
		$db = App::get('db');
		$db->setQuery(
			"SELECT DATE_FORMAT(sent_on, '" . $fmt . "') AS k, COUNT(*) c
			 FROM `#__massmail_recipients`
			 WHERE status = " . Recipient::SENT . " AND sent_on IS NOT NULL
			 AND sent_on >= (UTC_TIMESTAMP() - INTERVAL " . $interval . ")
			 GROUP BY k"
		);
		$map = array();
		foreach ($db->loadObjectList() as $r)
		{
			$map[$r->k] = (int) $r->c;
		}
		return $map;
	}

	/**
	 * Build a gap-filled chart series ending now.
	 *
	 * @param   array    $map       counts keyed by gmdate($keyFmt)
	 * @param   integer  $count     number of buckets
	 * @param   integer  $step      seconds per bucket
	 * @param   string   $keyFmt    gmdate() format matching the map keys (UTC)
	 * @param   string   $labelFmt  date() format for the (local) label
	 * @return  array
	 */
	protected function series($map, $count, $step, $keyFmt, $labelFmt)
	{
		$out = array();
		for ($i = $count - 1; $i >= 0; $i--)
		{
			$t = time() - ($i * $step);
			$k = gmdate($keyFmt, $t);
			$out[] = array(
				'label' => date($labelFmt, $t),
				'value' => isset($map[$k]) ? $map[$k] : 0
			);
		}
		return $out;
	}

	/**
	 * Forward-filled queue-depth values aligned to a chart's buckets, for the
	 * line overlaid on the outbound charts. Returns a flat array of depths.
	 *
	 * @param   integer  $count   number of buckets
	 * @param   integer  $step    seconds per bucket
	 * @param   string   $sqlFmt  MySQL DATE_FORMAT for bucketing recorded
	 * @param   string   $keyFmt  matching gmdate() format
	 * @return  array
	 */
	protected function queueLine($count, $step, $sqlFmt, $keyFmt)
	{
		$db   = App::get('db');
		$secs = $count * $step;
		$db->setQuery(
			"SELECT DATE_FORMAT(recorded, '" . $sqlFmt . "') k, AVG(depth) d
			 FROM `#__massmail_queue_samples`
			 WHERE recorded >= (UTC_TIMESTAMP() - INTERVAL " . (int) $secs . " SECOND)
			 GROUP BY k"
		);
		$map = array();
		foreach ($db->loadObjectList() as $r)
		{
			$map[$r->k] = (float) $r->d;
		}

		$out  = array();
		$last = 0;
		$have = false;
		for ($i = $count - 1; $i >= 0; $i--)
		{
			$k = gmdate($keyFmt, time() - ($i * $step));
			if (isset($map[$k]))
			{
				$last = $map[$k];
				$have = true;
			}
			$out[] = $have ? $last : 0;
		}
		return $out;
	}
}
