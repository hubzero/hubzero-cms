<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Site\Controllers;

use Components\Newsletter\Helpers\Helper;
use Components\Newsletter\Models\Mailing\Recipient\Action;
use Hubzero\Component\SiteController;
use stdClass;
use Request;
use Date;
use App;

/**
 * Newsletter Mailing Controller
 */
class Mailings extends SiteController
{
	/**
	 * General Tracking Task - Routes to open and click tracking based on type
	 *
	 * @return 	void
	 */
	public function trackTask()
	{
		$type = Request::getWord('type');

		switch ($type)
		{
			case 'open':
				$this->openTrackingTask();
				break;
			case 'click':
				$this->clickTrackingTask();
				break;
			case 'print':
				$this->printTrackingTask();
				break;
			case 'forward':
				$this->forwardTrackingTask();
				break;
		}
	}

	/**
	 * Open Tracker
	 *
	 * @return 	void
	 */
	private function openTrackingTask()
	{
		//get reqest vars
		$token = Request::getString('t', '');

		//parse mailing token
		$recipient = Helper::parseMailingToken($token);

		//if we found an object lets track it
		if (is_object($recipient) && $recipient->id)
		{
			$action = Action::oneForMailingAndEmail($recipient->mid, $recipient->email, 'open');

			//check to see if we already opened
			if (!$action->get('id'))
			{
				//create object holding our vars to store action
				$action = Action::blank()
					->set(array(
						'mailingid'   => $recipient->mid,
						'action'      => 'open',
						'email'       => $recipient->email,
						'ip'          => Request::ip(),
						'user_agent'  => $_SERVER['HTTP_USER_AGENT'],
						'date'        => Date::toSql()
					));

				//save action
				$action->save();
			}
		}

		//create image to ouput
		Helper::mailingOpenTrackerGif();
		exit();
	}

	/**
	 * Click Tracker
	 *
	 * @return 	void
	 */
	private function clickTrackingTask()
	{
		//get reqest vars
		$token = Request::getString('t', '');
		$link  = Request::getString('l', '', 'get');

		//parse mailing token
		$recipient = Helper::parseMailingToken($token);

		//url decode and replace zero width spaces
		$link = urldecode($link);
		$link = str_replace('&#8203;', '', $link);

		//if we found an object lets track it
		if (is_object($recipient) && $recipient->id)
		{
			//array of action vars, json encoded for saving to db
			$actionVars = json_encode(array('url' => $link));

			//create object holding our vars to store action
			$action = Action::blank()
				->set(array(
					'mailingid'   => $recipient->mid,
					'action'      => 'click',
					'action_vars' => $actionVars,
					'email'       => $recipient->email,
					'ip'          => Request::ip(),
					'user_agent'  => $_SERVER['HTTP_USER_AGENT'],
					'date'        => Date::toSql()
				));

			//save action
			$action->save();
		}

		//make sure we have a valid link
		//if we do redirect
		if (filter_var($link, FILTER_VALIDATE_URL))
		{
			App::redirect($link);
		}
	}

	/**
	 * Print Tracker
	 *
	 * @return 	void
	 */
	private function printTrackingTask()
	{
		//get reqest vars
		$token = Request::getString('t', '');

		//parse mailing token
		$recipient = Helper::parseMailingToken($token);

		//if we found an object lets track it
		if (is_object($recipient) && $recipient->id)
		{
			//create object holding our vars to store action
			$action = Action::blank()
				->set(array(
					'mailingid'   => $recipient->mid,
					'action'      => 'print',
					'email'       => $recipient->email,
					'ip'          => Request::ip(),
					'user_agent'  => $_SERVER['HTTP_USER_AGENT'],
					'date'        => Date::toSql()
				));

			//save action
			$action->save();
		}
	}

	/**
	 * Forward Tracker
	 *
	 * @return 	void
	 */
	private function forwardTrackingTask()
	{
		//get reqest vars
		$token = Request::getString('t', '');

		//parse mailing token
		$recipient = Helper::parseMailingToken($token);

		//if we found an object lets track it
		if (is_object($recipient) && $recipient->id)
		{
			// create object holding our vars to store action
			$action = Action::blank()
				->set(array(
					'mailingid'   => $recipient->mid,
					'action'      => 'forward',
					'email'       => $recipient->email,
					'ip'          => Request::ip(),
					'user_agent'  => $_SERVER['HTTP_USER_AGENT'],
					'date'        => Date::toSql()
				));

			// save action
			$action->save();
		}
	}
}
