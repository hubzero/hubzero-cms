<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Admin\Controllers;

use Components\Newsletter\Tables\Mailing as NewsletterMailing;
use Components\Newsletter\Tables\MailingRecipient;
use Components\Newsletter\Tables\MailingRecipientAction;
use Components\Newsletter\Tables\Newsletter;
use Hubzero\Component\AdminController;
use stdClass;
use Request;
use Route;
use Lang;
use App;

/**
 * Newsletter Mailings Controller
 */
class Mailing extends AdminController
{
	/**
	 * Display Newsletter Mailings
	 *
	 * @return 	void
	 */
	public function displayTask()
	{
		//instantiate newsletter mailing object
		$newsletterMailing = new NewsletterMailing($this->database);
		$this->view->mailings = $newsletterMailing->getMailingNewsletters();

		//add the number sent
		foreach ($this->view->mailings as $mailing)
		{
			$newsletterMailingRecipient = new MailingRecipient($this->database);
			$mailing->emails_sent = count($newsletterMailingRecipient->getRecipients($mailing->mailing_id, 'sent'));
			$mailing->emails_total = count($newsletterMailingRecipient->getRecipients($mailing->mailing_id));
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->setLayout('display')->display();
	}

	/**
	 * View Tracking Information task
	 *
	 * @return 	void
	 */
	public function trackingTask()
	{
		//get request vars
		$ids = Request::getVar('id', array());
		$id = (isset($ids)) ? $ids[0] : null;

		//instantiate newsletter mailing object
		$newsletterMailing = new NewsletterMailing($this->database);
		$mailing = $newsletterMailing->getMailings($id);

		//get mailing recipients
		$newsletterMailingRecipient = new MailingRecipient($this->database);
		$this->view->recipients = $newsletterMailingRecipient->countRecipients($id, 'sent');

		//instantiate newsletter object
		$newsletterNewsletter = new Newsletter($this->database);
		$newsletterNewsletter->load($mailing->nid);

		//make sure we are supposed to be tracking
		if (!$newsletterNewsletter->tracking)
		{
			$this->setError(Lang::txt('COM_NEWSLETTER_MAILING_NOT_TRACKING'));
			$this->displayTask();
			return;
		}

		//get bounces
		$sql = "SELECT COUNT(*) FROM `#__email_bounces`
				WHERE component='com_newsletter'
				AND object=" . $this->database->quote('Campaign Mailing') . "
				AND object_id=" . $this->database->quote($id);
		$this->database->setQuery($sql);
		$this->view->bounces = $this->database->loadResult();

		//new mailing recipient action object
		$newsletterMailingRecipientAction = new MailingRecipientAction($this->database);

		//get opens, clicks, forwards, and prints
		$this->view->opens    = $newsletterMailingRecipientAction->countMailingActions($id, 'open');
		$this->view->forwards = $newsletterMailingRecipientAction->countMailingActions($id, 'forward');
		$this->view->prints   = $newsletterMailingRecipientAction->countMailingActions($id, 'print');

		//get opens geo
		$this->view->opensGeo = $this->getOpensGeoTask($id);

		//get clicks and process
		$clicks = $newsletterMailingRecipientAction->getMailingActions($id, 'click');
		$this->view->clicks = array();
		foreach ($clicks as $click)
		{
			//get click action
			$clickAction = json_decode($click->action_vars);
			$this->view->clicks[$clickAction->url] = (isset($this->view->clicks[$clickAction->url])) ? $this->view->clicks[$clickAction->url] + 1 : 1;
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->setLayout('tracking')->display();
	}

	/**
	 * Get Opens and Return as JSON
	 * 
	 * @param  [type] $mailingId [description]
	 * @return [type]            [description]
	 */
	public function getOpensGeoTask($mailingId = null)
	{
		//are we getting through ajax
		$no_html = Request::getInt('no_html', 0);

		//get the mailing id
		if (is_null($mailingId))
		{
			$mailingId = Request::getVar('mailingid', 0);
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

		//new mailing recipient action object
		$newsletterMailingRecipientAction = new MailingRecipientAction($this->database);

		//get opens
		$opens = $newsletterMailingRecipientAction->getMailingActions($mailingId, 'open');

		//get country and state data
		$countryGeo = array();
		$statesGeo = array();
		foreach ($opens as $open)
		{
			$country = ($open->countrySHORT) ? strtolower($open->countrySHORT) : 'undetermined';
			$state = ($open->ipREGION) && isset($states[strtolower($open->ipREGION)]) ? 'us-' . strtolower($states[strtolower($open->ipREGION)]) : 'undetermined';

			$countryGeo[$country] = (isset($countryGeo[$country])) ? $countryGeo[$country] + 1 : 1;
			$statesGeo[$state] = (isset($statesGeo[$state])) ? $statesGeo[$state] + 1 : 1;
		}

		//build return object
		$geo = array(
			'country' => $countryGeo,
			'state' => $statesGeo
		);

		//return
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
	 * @return 	void
	 */
	public function stopTask()
	{
		//get request vars
		$ids = Request::getVar('id', array());
		$id = (isset($ids)) ? $ids[0] : null;

		//instantiate newsletter mailing object
		$newsletterMailing = new NewsletterMailing($this->database);
		$newsletterMailing->load($id);

		//mark as deleted
		$newsletterMailing->deleted = 1;

		//save
		if (!$newsletterMailing->save($newsletterMailing))
		{
			$this->setError($newsletterMailing->getError());
			$this->displayTask();
			return;
		}

		//inform and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false),
			Lang::txt('COM_NEWSLETTER_MAILING_STOPPED')
		);
	}
}
