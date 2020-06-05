<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin\Controllers;

use Components\Newsletter\Models\Mailing;
use Components\Newsletter\Models\Mailing\Recipient;
use Components\Newsletter\Models\Mailing\Recipient\Action;
use Components\Newsletter\Models\Newsletter;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Notify;
use Route;
use Lang;
use App;

/**
 * Newsletter Mailings Controller
 */
class Mailings extends AdminController
{
	/**
	 * Display Newsletter Mailings
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		$filters = array(
			'search' => Request::getState(
				$this->_option . '.' . $this->_controller . '.search',
				'search',
				''
			),
			'sort' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sort',
				'filter_order',
				'subject'
			),
			'sort_Dir' => Request::getState(
				$this->_option . '.' . $this->_controller . '.sortdir',
				'filter_order_Dir',
				'ASC'
			)
		);

		$mailings = Mailing::all()
			->including(['newsletter', function ($newsletter) {
				$newsletter->select('*');
			}])
			->whereEquals('deleted', 0);

		if ($filters['search'])
		{
			$filters['search'] = strtolower((string)$filters['search']);
			$mailings->whereLike('subject', $filters['search']);
		}

		$rows = $mailings
			->order($filters['sort'], $filters['sort_Dir'])
			->paginated('limitstart', 'limit')
			->rows();

		// Add the number sent
		foreach ($rows as $mailing)
		{
			$emails_sent = $mailing
				->recipients()
				->whereEquals('status', 'sent')
				->total();

			$emails_total = $mailing
				->recipients()
				->total();

			$mailing->set('emails_sent', $emails_sent);
			$mailing->set('emails_total', $emails_total);
		}

		// Output the HTML
		$this->view
			->setLayout('display')
			->set('mailings', $rows)
			->set('filters', $filters)
			->display();
	}

	/**
	 * View Tracking Information task
	 *
	 * @return  void
	 */
	public function trackingTask()
	{
		// Get request vars
		$ids = Request::getArray('id', array());
		$id = (isset($ids)) ? $ids[0] : 0;

		// Instantiate newsletter mailing object
		$mailing = Mailing::oneOrFail($id);

		// Instantiate newsletter object
		$newsletter = $mailing->newsletter;

		// Make sure we are supposed to be tracking
		if (!$newsletter->tracking)
		{
			Notify::warning(Lang::txt('COM_NEWSLETTER_MAILING_NOT_TRACKING'));
			return $this->cancelTask();
		}

		// Get mailing recipients
		$recipients = $mailing->recipients()->total();

		// Get bounces
		$db = App::get('db');
		$sql = "SELECT COUNT(*) FROM `#__email_bounces`
				WHERE component='com_newsletter'
				AND object=" . $db->quote('Campaign Mailing') . "
				AND object_id=" . $db->quote($id);
		$db->setQuery($sql);
		$bounces = $db->loadResult();

		// Get opens, clicks, forwards, and prints
		$opens = Action::all()
			->whereEquals('mailingid', $id)
			->whereEquals('action', 'open')
			->total();

		$forwards = Action::all()
			->whereEquals('mailingid', $id)
			->whereEquals('action', 'forward')
			->total();

		$prints = Action::all()
			->whereEquals('mailingid', $id)
			->whereEquals('action', 'print')
			->total();

		// Get opens geo
		$opensGeo = $this->getOpensGeoTask($id);

		// Get clicks and process
		$clcks = Action::all()
			->whereEquals('mailingid', $id)
			->whereEquals('action', 'click')
			->rows();

		$clicks = array();
		foreach ($clcks as $click)
		{
			// Get click action
			$clickAction = json_decode($click->action_vars);
			$clicks[$clickAction->url] = (isset($clicks[$clickAction->url])) ? $clicks[$clickAction->url] + 1 : 1;
		}

		// Output the HTML
		$this->view
			->setLayout('tracking')
			->set('mailing', $mailing)
			->set('recipients', $recipients)
			->set('bounces', $bounces)
			->set('opens', $opens)
			->set('forwards', $forwards)
			->set('prints', $prints)
			->set('opensGeo', $opensGeo)
			->set('clicks', $clicks)
			->display();
	}

	/**
	 * Get Opens and Return as JSON
	 * 
	 * @param   integer  $mailingId
	 * @return  string
	 */
	public function getOpensGeoTask($mailingId = null)
	{
		// Are we getting through ajax
		$no_html = Request::getInt('no_html', 0);

		// Get the mailing id
		if (is_null($mailingId))
		{
			$mailingId = Request::getInt('mailingid', 0);
		}

		$states = array(
			"alabama" => 'al',
			"alaska" => 'ak',
			"arizona" => 'az',
			"arkansas" => 'ar',
			"california" => 'ca',
			"colorado" => 'co',
			"connecticut" => 'ct',
			"delaware" => 'de',
			"florida" => 'fl',
			"georgia" => 'ga',
			"hawaii" => 'hi',
			"idaho" => 'id',
			"illinois" => 'il',
			"indiana" => 'in',
			"iowa" => 'ia',
			"kansas" => 'ks',
			"kentucky" => 'ky',
			"louisiana" => 'la',
			"maine" => 'me',
			"maryland" => 'md',
			"massachusetts" =>' ma',
			"michigan" => 'mi',
			"minnesota" => 'mn',
			"mississippi" => 'ms',
			"missouri" => 'mo',
			"montana" => 'mt',
			"nebraska" => 'ne',
			"nevada" => 'nv',
			"new hampshire" => 'nh',
			"new jersey" => 'nj',
			"new mexico" => 'nm',
			"new york" => 'ny',
			"north carolina" => 'nc',
			"north dakota" => 'nd',
			"ohio" =>  'oh',
			"oklahoma" => 'ok',
			"oregon" => 'or',
			"pennsylvania" => 'pa',
			"rhode island" => 'ri',
			"south carolina" => 'sc',
			"south dakota" => 'sd',
			"tennessee" => 'tn',
			"texas" => 'tx',
			"utah" => 'ut',
			"vermont" => 'vt',
			"virginia" => 'va',
			"washington" => 'wa',
			"west virginia" => 'wv',
			"wisconsin" => 'wi',
			"wyoming" => 'wy'
		);

		// Get opens
		$opens = Action::all()
			->whereEquals('mailingid', $mailingId)
			->whereEquals('action', 'open')
			->rows();

		// Get country and state data
		$countryGeo = array();
		$statesGeo  = array();
		foreach ($opens as $open)
		{
			$country = ($open->countrySHORT) ? strtolower($open->countrySHORT) : 'undetermined';
			$state = ($open->ipREGION) && isset($states[strtolower($open->ipREGION)]) ? 'us-' . strtolower($states[strtolower($open->ipREGION)]) : 'undetermined';

			$countryGeo[$country] = (isset($countryGeo[$country])) ? $countryGeo[$country] + 1 : 1;
			$statesGeo[$state] = (isset($statesGeo[$state])) ? $statesGeo[$state] + 1 : 1;
		}

		// Build return data
		$geo = array(
			'country' => $countryGeo,
			'state'   => $statesGeo
		);

		if ($no_html)
		{
			echo json_encode($geo);
			exit();
		}
		else
		{
			return $geo;
		}
	}

	/**
	 * Stop sending campaign or deleted scheduled
	 *
	 * @return  void
	 */
	public function stopTask()
	{
		// Get request vars
		$ids = Request::getArray('id', array());
		$id = (isset($ids)) ? $ids[0] : null;

		// Instantiate newsletter mailing object
		$mailing = Mailing::oneOrFail($id);

		// Mark as deleted
		$mailing->set('deleted', 1);

		// Save
		if (!$mailing->save())
		{
			Notify::error($mailing->getError());
			return $this->cancelTask();
		}

		Notify::success(Lang::txt('COM_NEWSLETTER_MAILING_STOPPED'));

		$this->cancelTask();
	}
}
