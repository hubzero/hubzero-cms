<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Massmail\Helpers;

use Components\Massmail\Models\Mailing;
use Components\Massmail\Models\Recipient;
use Components\Massmail\Models\Status;

require_once __DIR__ . DS . 'rate.php';
require_once __DIR__ . DS . 'queue.php';
require_once __DIR__ . DS . 'log.php';
require_once __DIR__ . DS . 'token.php';
require_once __DIR__ . DS . 'formatter.php';
require_once __DIR__ . DS . 'recipients.php';

/**
 * Drives one cron tick of mass-mail delivery: expand any freshly-launched
 * mailings, compute the rate budget, feed that many messages to Postfix, and
 * record a status snapshot. All delivery is serialized by an advisory lock so
 * overlapping cron runs can never double-send.
 */
class Sender
{
	const LOCK = 'com_massmail.send';

	/**
	 * How many times we'll try to hand a single recipient's message to the local
	 * mail system before marking it failed. This counts handoff attempts to
	 * Postfix, not downstream delivery -- once Postfix accepts a message, its own
	 * retry/bounce handling takes over. Fixed internally; not worth exposing.
	 */
	const MAX_ATTEMPTS = 3;

	/**
	 * Run one tick.
	 *
	 * @param   boolean  $dryRun  mark recipients sent WITHOUT emitting mail (testing)
	 * @return  array
	 */
	public static function run($dryRun = false)
	{
		$params = \Component::params('com_massmail');
		$db     = \App::get('db');

		$stats = array(
			'skipped_run' => false,
			'prepared'    => 0,
			'budget'      => 0,
			'sent'        => 0,
			'failed'      => 0,
			'queue'       => -1,
			'system_sent_last_hour' => -1
		);

		// One sender at a time.
		$db->setQuery("SELECT GET_LOCK(" . $db->quote(self::LOCK) . ", 1)");
		if (!$db->loadResult())
		{
			$stats['skipped_run'] = true;
			return $stats;
		}

		try
		{
			// 1. Expand any mailings that were just launched.
			$stats['prepared'] = self::prepare();

			// 2. Work out this run's budget. The rolling-hour ceiling is measured
			//    against the TRUE system-wide send rate -- every sender sharing
			//    this host's egress (newsletters, mailman, system mail), not just
			//    our own -- read from the Postfix log, with the current queue
			//    counted as committed egress. Pace is the hourly limit spread over
			//    the send job's cron cadence.
			$hourlyLimit    = (int) $params->get('hourly_limit', 300);
			$ourLastHour    = self::sentLastHour();
			$systemLastHour = MailLog::sentLastHour();
			$queue          = Queue::size();

			if ($systemLastHour >= 0)
			{
				// Log readable: the delivered count includes our own sends and is
				// disjoint from the still-queued set, so both may be subtracted
				// without double counting.
				$deliveredLastHour = $systemLastHour;
				$queueForBudget    = $queue;
			}
			else
			{
				// Log unreadable: fall back to our own injected count, which
				// already includes anything of ours still sitting in the queue --
				// so don't subtract the queue again. Pace on $perTick alone.
				$deliveredLastHour = $ourLastHour;
				$queueForBudget    = -1;
			}

			$perTick = Rate::perTick($hourlyLimit, self::ticksPerHour());
			$budget  = Rate::budget($hourlyLimit, $deliveredLastHour, $queueForBudget, $perTick);

			$stats['queue']  = $queue;
			$stats['budget'] = $budget;
			$stats['system_sent_last_hour'] = $systemLastHour;

			// 3. Feed that many messages from the oldest active mailing.
			if ($budget > 0)
			{
				$mailing = self::nextActiveMailing();
				if ($mailing)
				{
					$res = self::sendBatch($mailing, $budget, $dryRun);
					$stats['sent']   = $res['sent'];
					$stats['failed'] = $res['failed'];
				}
			}

			// 4. Record snapshots: the single-row status + the time series.
			$status = Status::instance();
			$status->set('queue_size', max(0, $queue));
			$status->set('sent_last_hour', $ourLastHour + $stats['sent']);
			$status->set('last_checked', \Date::toSql());
			$status->set('last_run', \Date::toSql());
			$status->save();

			self::recordHistory($queue, $ourLastHour + $stats['sent'], $systemLastHour, $stats['sent']);
		}
		finally
		{
			$db->setQuery("SELECT RELEASE_LOCK(" . $db->quote(self::LOCK) . ")");
			$db->loadResult();
		}

		return $stats;
	}

	/**
	 * Expand recipients for every mailing sitting in "preparing" and move it to
	 * "sending".
	 *
	 * @return  integer  number of mailings prepared
	 */
	public static function prepare()
	{
		$n = 0;
		$rows = Mailing::all()
			->whereEquals('status', Mailing::STATUS_PREPARING)
			->rows();

		foreach ($rows as $mailing)
		{
			$total = Recipients::expand($mailing);
			$mailing->set('total_recipients', $total);
			$mailing->set('status', Mailing::STATUS_SENDING);
			if (!$mailing->get('started_on'))
			{
				$mailing->set('started_on', \Date::toSql());
			}
			$mailing->save();
			$n++;
		}
		return $n;
	}

	/**
	 * Messages successfully sent in the last rolling hour (all mailings).
	 *
	 * @return  integer
	 */
	public static function sentLastHour()
	{
		$db = \App::get('db');
		$db->setQuery(
			"SELECT COUNT(*) FROM `#__massmail_recipients`
			 WHERE status = " . Recipient::SENT . "
			 AND sent_on IS NOT NULL
			 AND sent_on >= (UTC_TIMESTAMP() - INTERVAL 1 HOUR)"
		);
		return (int) $db->loadResult();
	}

	/**
	 * How many times the send job runs per hour, from its own cron schedule, so
	 * the per-run pace tracks the configured cadence. Defaults to 12 (every five
	 * minutes -- the seeded schedule) if the recurrence can't be read or parsed.
	 *
	 * @return  integer
	 */
	public static function ticksPerHour()
	{
		$db = \App::get('db');
		$db->setQuery(
			"SELECT recurrence FROM `#__cron_jobs`
			 WHERE plugin = 'massmail' AND event = 'processMassmailQueue' LIMIT 1"
		);
		$n = Rate::firingsPerHour((string) $db->loadResult());
		return $n > 0 ? $n : 12;
	}

	/**
	 * Append a time-series snapshot for the dashboard charts and prune old rows.
	 *
	 * @param   integer  $queue        current Postfix queue size (-1 if unknown)
	 * @param   integer  $ourSent      our messages sent in the last hour
	 * @param   integer  $systemSent   all senders' messages last hour (-1 if unknown)
	 * @param   integer  $sentThisRun  messages sent on this tick
	 * @return  void
	 */
	protected static function recordHistory($queue, $ourSent, $systemSent, $sentThisRun)
	{
		$db = \App::get('db');

		$db->setQuery("SELECT COUNT(*) FROM `#__massmail_recipients` WHERE status = " . Recipient::SENT);
		$cumulative = (int) $db->loadResult();

		$db->setQuery(
			"SELECT COUNT(*) FROM `#__massmail_recipients` r
			 JOIN `#__massmail_mailings` m ON m.id = r.mailing_id
			 WHERE r.status = " . Recipient::PENDING . "
			 AND m.status IN (" . $db->quote(Mailing::STATUS_SENDING) . ", " . $db->quote(Mailing::STATUS_PREPARING) . ", " . $db->quote(Mailing::STATUS_PAUSED) . ")"
		);
		$pending = (int) $db->loadResult();

		$db->setQuery(
			"INSERT INTO `#__massmail_stats`
				(recorded, queue_size, sent_last_hour, system_sent_last_hour, sent_this_run, pending, cumulative_sent)
			 VALUES ("
				. $db->quote(\Date::toSql()) . ", "
				. (int) max(0, $queue) . ", "
				. (int) $ourSent . ", "
				. (int) $systemSent . ", "
				. (int) $sentThisRun . ", "
				. (int) $pending . ", "
				. $cumulative . ")"
		);
		$db->execute();

		// 5-minute snapshots are only used for the recent (24-48h) charts, so
		// purge them quickly; hourly/daily volume is rolled up separately.
		$db->setQuery("DELETE FROM `#__massmail_stats` WHERE recorded < (UTC_TIMESTAMP() - INTERVAL 3 DAY)");
		$db->execute();
	}

	/**
	 * The oldest mailing still in "sending" that has pending recipients.
	 *
	 * @return  Mailing|null
	 */
	public static function nextActiveMailing()
	{
		$db = \App::get('db');
		$db->setQuery(
			"SELECT m.id FROM `#__massmail_mailings` m
			 WHERE m.status = " . $db->quote(Mailing::STATUS_SENDING) . "
			 AND EXISTS (
				SELECT 1 FROM `#__massmail_recipients` r
				WHERE r.mailing_id = m.id AND r.status = " . Recipient::PENDING . "
			 )
			 ORDER BY m.started_on ASC, m.id ASC
			 LIMIT 1"
		);
		$id = (int) $db->loadResult();
		return $id ? Mailing::oneOrFail($id) : null;
	}

	/**
	 * Send up to $limit pending recipients of a mailing.
	 *
	 * @param   Mailing  $mailing
	 * @param   integer  $limit
	 * @param   boolean  $dryRun
	 * @return  array    ['sent' => int, 'failed' => int]
	 */
	public static function sendBatch(Mailing $mailing, $limit, $dryRun = false)
	{
		$params      = \Component::params('com_massmail');
		$maxAttempts = self::MAX_ATTEMPTS;

		$subject   = $mailing->get('subject');
		$bodyText  = (string) $mailing->get('body_text');
		$bodyHtml  = (string) $mailing->get('body_html');
		$fromName  = $mailing->get('from_name')  ?: ($params->get('from_name')  ?: \Config::get('fromname'));
		$fromEmail = $mailing->get('from_email') ?: ($params->get('from_email') ?: \Config::get('mailfrom'));
		$replyTo   = $mailing->get('reply_to');

		// Resolve attachment paths once.
		$attachments = array();
		foreach ($mailing->attachments()->rows() as $att)
		{
			$path = $att->get('stored_path');
			if ($path && is_file($path))
			{
				$attachments[] = array('path' => $path, 'name' => $att->get('filename'));
			}
		}

		$recipients = Recipient::all()
			->whereEquals('mailing_id', $mailing->get('id'))
			->whereEquals('status', Recipient::PENDING)
			->order('id', 'asc')
			->limit((int) $limit)
			->rows();

		$sent = 0;
		$failed = 0;

		foreach ($recipients as $r)
		{
			$ok  = false;
			$err = '';

			try
			{
				if ($dryRun)
				{
					$ok = true;
				}
				else
				{
					$unsub = Token::unsubscribeUrl($mailing->get('id'), $r->get('email'));
					$text  = str_replace(Formatter::UNSUBSCRIBE_TOKEN, $unsub, $bodyText);
					$html  = str_replace(Formatter::UNSUBSCRIBE_TOKEN, $unsub, $bodyHtml);

					$message = new \Hubzero\Mail\Message();
					$message->setSubject($subject);
					$message->addFrom($fromEmail, $fromName);
					if ($replyTo)
					{
						$message->setReplyTo($replyTo);
					}
					$message->addPart($text, 'text/plain');
					$message->addPart($html, 'text/html');
					foreach ($attachments as $a)
					{
						$message->addAttachment($a['path'], $a['name']);
					}
					$message->setTo(array($r->get('email')));

					$ok = (bool) $message->send();
				}
			}
			catch (\Exception $e)
			{
				$ok  = false;
				$err = $e->getMessage();
			}

			if ($ok)
			{
				$r->set('status', Recipient::SENT);
				$r->set('sent_on', \Date::toSql());
				$r->set('error', '');
				$r->save();
				$sent++;
			}
			else
			{
				$r->set('attempts', (int) $r->get('attempts') + 1);
				$r->set('error', substr($err, 0, 255));
				if ((int) $r->get('attempts') >= $maxAttempts)
				{
					$r->set('status', Recipient::FAILED);
					$failed++;
				}
				$r->save();
			}
		}

		self::refreshCounts($mailing);

		// Mark complete once nothing is left pending.
		$pending = Recipient::all()
			->whereEquals('mailing_id', $mailing->get('id'))
			->whereEquals('status', Recipient::PENDING)
			->total();
		if ($pending == 0)
		{
			$mailing->set('status', Mailing::STATUS_SENT);
			$mailing->set('completed_on', \Date::toSql());
			$mailing->save();
		}

		return array('sent' => $sent, 'failed' => $failed);
	}

	/**
	 * Recompute a mailing's sent/failed/skipped counters from its recipients.
	 *
	 * @param   Mailing  $mailing
	 * @return  void
	 */
	public static function refreshCounts(Mailing $mailing)
	{
		$db  = \App::get('db');
		$mid = (int) $mailing->get('id');
		$db->setQuery(
			"SELECT
				SUM(status = " . Recipient::SENT . ")    AS s,
				SUM(status = " . Recipient::FAILED . ")  AS f,
				SUM(status = " . Recipient::SKIPPED . ") AS k
			 FROM `#__massmail_recipients` WHERE mailing_id = $mid"
		);
		$row = $db->loadObject();
		$mailing->set('sent_count', (int) $row->s);
		$mailing->set('failed_count', (int) $row->f);
		$mailing->set('skipped_count', (int) $row->k);
		$mailing->save();
	}
}
