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
		$ourLastHour    = Sender::sentLastHour();
		$systemLastHour = MailLog::sentLastHour();
		$effectiveSent  = ($systemLastHour >= 0) ? max($systemLastHour, $ourLastHour) : $ourLastHour;

		$hourly    = (int) $params->get('hourly_limit', 300);
		$watermark = (int) $params->get('queue_watermark', 150);
		$perRun    = (int) $params->get('per_run_cap', 50);

		$hourlyRemaining = max(0, $hourly - $effectiveSent);
		$nextRunBudget   = Rate::budget($hourly, $effectiveSent, $watermark, $queueSize, $perRun);

		// --- Totals -----------------------------------------------------------
		$totalMailings  = Mailing::all()->total();
		$activeMailings = Mailing::all()->whereIn('status', array(
			Mailing::STATUS_PREPARING, Mailing::STATUS_SENDING,
			Mailing::STATUS_PAUSED, Mailing::STATUS_PENDING_APPROVAL
		))->total();

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

		// --- Mailings by status ----------------------------------------------
		$db->setQuery("SELECT status, COUNT(*) c FROM `#__massmail_mailings` GROUP BY status");
		$byStatus = array();
		foreach ($db->loadObjectList() as $r)
		{
			$byStatus[$r->status] = (int) $r->c;
		}

		// --- Sends per hour, last 24h (bucketed by UTC hour of send) ---------
		$db->setQuery(
			"SELECT DATE_FORMAT(sent_on, '%Y-%m-%d %H') AS hk, COUNT(*) c
			 FROM `#__massmail_recipients`
			 WHERE status = " . Recipient::SENT . " AND sent_on IS NOT NULL
			 AND sent_on >= (UTC_TIMESTAMP() - INTERVAL 24 HOUR)
			 GROUP BY hk"
		);
		$map = array();
		foreach ($db->loadObjectList() as $r)
		{
			$map[$r->hk] = (int) $r->c;
		}
		$hourlySeries = array();
		for ($i = 23; $i >= 0; $i--)
		{
			$t = time() - ($i * 3600);
			$key = gmdate('Y-m-d H', $t);
			$hourlySeries[] = array(
				'label' => date('H', $t),
				'value' => isset($map[$key]) ? $map[$key] : 0
			);
		}

		// --- Queue depth over time (last 24h of cron snapshots) --------------
		$db->setQuery(
			"SELECT recorded, depth FROM `#__massmail_queue_samples`
			 WHERE recorded >= (UTC_TIMESTAMP() - INTERVAL 48 HOUR)
			 ORDER BY recorded ASC"
		);
		$queueRecent = $db->loadObjectList();

		$db->setQuery("SELECT COALESCE(MAX(depth), 0) FROM `#__massmail_queue_samples` WHERE recorded >= (UTC_TIMESTAMP() - INTERVAL 24 HOUR)");
		$queuePeak24h = (int) $db->loadResult();

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

		$db->setQuery("SELECT sent FROM `#__massmail_postfix_daily` WHERE `day` = CURDATE()");
		$sentToday = (int) $db->loadResult();
		$db->setQuery("SELECT COALESCE(SUM(sent),0) FROM `#__massmail_postfix_daily` WHERE `day` >= (CURDATE() - INTERVAL 6 DAY)");
		$sent7d = (int) $db->loadResult();

		$db->setQuery("SELECT last_run FROM `#__massmail_postfix_state` WHERE id = 1");
		$collectorRun = $db->loadResult();

		// --- Cron ------------------------------------------------------------
		$db->setQuery("SELECT state, last_run FROM `#__cron_jobs` WHERE plugin = 'massmail' AND event = 'processMassmailQueue' LIMIT 1");
		$cron = $db->loadObject();

		// --- Recent mailings -------------------------------------------------
		$recent = Mailing::all()->order('created', 'desc')->limit(5)->rows();

		$this->view
			->set('params', $params)
			->set('snapshot', StatusModel::instance())
			->set('queue', $queue)
			->set('queueSize', $queueSize)
			->set('ourLastHour', $ourLastHour)
			->set('systemLastHour', $systemLastHour)
			->set('effectiveSent', $effectiveSent)
			->set('hourly', $hourly)
			->set('watermark', $watermark)
			->set('hourlyRemaining', $hourlyRemaining)
			->set('nextRunBudget', $nextRunBudget)
			->set('totalMailings', $totalMailings)
			->set('activeMailings', $activeMailings)
			->set('messagesSent', $messagesSent)
			->set('messagesFailed', $messagesFailed)
			->set('messagesPending', $messagesPending)
			->set('byStatus', $byStatus)
			->set('hourlySeries', $hourlySeries)
			->set('queueRecent', $queueRecent)
			->set('queuePeak24h', $queuePeak24h)
			->set('dailyVolume', $dailyVolume)
			->set('hourlyVolume', $hourlyVolume)
			->set('lastHourVolume', $lastHourVolume)
			->set('sentToday', $sentToday)
			->set('sent7d', $sent7d)
			->set('collectorRun', $collectorRun)
			->set('recent', $recent)
			->set('cron', $cron)
			->display();
	}
}
