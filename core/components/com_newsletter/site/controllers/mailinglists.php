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

namespace Components\Newsletter\Site\Controllers;

use Components\Newsletter\Helpers\Helper;
use Components\Newsletter\Models\Mailinglist\Email;
use Components\Newsletter\Models\Mailinglist;
use Components\Newsletter\Models\Mailing;
use Hubzero\Component\SiteController;
use stdClass;
use Pathway;
use Route;
use User;
use Lang;
use Date;
use App;

/**
 * Newsletter Mailing List Controller
 */
class Mailinglists extends SiteController
{
	/**
	 * Override parent build title method
	 *
	 * @param   object  $newsletter  Newsletter object for adding campaign name pathway
	 * @return  void
	 */
	public function _buildTitle($newsletter = null)
	{
		//default if no campaign
		$this->_title = Lang::txt(strtoupper($this->_option));

		//add campaign name to title
		if (is_object($newsletter) && $newsletter->id)
		{
			$this->_title = Lang::txt('COM_NEWSLETTER_NEWSLETTER') . ': ' . $newsletter->name;
		}

		//if we are unsubscribing
		if ($this->_task == 'unsubscribe')
		{
			$this->_title = Lang::txt('COM_NEWSLETTER_NEWSLETTER') . ': ' . Lang::txt('COM_NEWSLETTER_UNSUBSCRIBE');
		}

		//if we are subscribing
		if ($this->_task == 'subscribe')
		{
			$this->_title = Lang::txt('COM_NEWSLETTER_NEWSLETTER') . ': ' . Lang::txt('COM_NEWSLETTER_SUBSCRIBE');
		}

		//set title of browser window
		App::get('document')->setTitle($this->_title);
	}

	/**
	 * Override parent build pathway method
	 *
	 * @param   object  $newsletter  Newsletter object for adding campaign name pathway
	 * @return  void
	 */
	public function _buildPathway($newsletter = null)
	{
		//add 'newlsetters' item to pathway
		if (Pathway::count() <= 0)
		{
			Pathway::append(Lang::txt(strtoupper($this->_option)), 'index.php?option=' . $this->_option);
		}

		//add campaign
		if (is_object($newsletter) && $newsletter->id)
		{
			Pathway::append(Lang::txt($newsletter->name), 'index.php?option=' . $this->_option . '&id=' . $newsletter->id);
		}

		//if we are unsubscribing
		if ($this->_task == 'unsubscribe')
		{
			Pathway::append(Lang::txt('COM_NEWSLETTER_SUBSCRIBE'), 'index.php?option=' . $this->_option . '&task=unsubscribe');
		}

		//if we are subscribing
		if ($this->_task == 'subscribe')
		{
			Pathway::append(Lang::txt('COM_NEWSLETTER_SUBSCRIBE'), 'index.php?option=' . $this->_option . '&task=subscribe');
		}
	}

	/**
	 * Subscribe to Mailing Lists View
	 *
	 * @return 	void
	 */
	public function subscribeTask()
	{
		//must be logged in
		if (User::isGuest())
		{
			//build return url and redirect url
			$return   = Route::url('index.php?option=com_newsletter&task=subscribe');
			$redirect = Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return));

			//redirect
			App::redirect($redirect, Lang::txt('COM_NEWSLETTER_LOGIN_TO_SUBSCRIBE'), 'warning');
			return;
		}

		//get mailing lists user belongs to
		$e = Email::blank()->getTableName();
		$m = Mailinglist::blank()->getTableName();

		$mylists = Mailinglist::all()
			->select($m . '.*')
			->select($e . '.status')
			->select($e . '.confirmed')
			->select($e . '.id', 'emailid')
			->join($e, $e . '.mid', $m . '.id', 'inner')
			->whereEquals($e . '.email', User::get('email'))
			->whereEquals('deleted', 0)
			->rows();

		//get all lists
		$alllists = Mailinglist::all()
			->whereEquals('private', 0)
			->rows();

		//build title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		//output
		$this->view
			->set('title', $this->_title)
			->set('mylists', $mylists)
			->set('alllists', $alllists)
			->setLayout('subscribe')
			->display();
	}

	/**
	 * Subscribe to *Single* Mailing List (Newsletter Module)
	 *
	 * @return 	void
	 */
	public function doSingleSubscribeTask()
	{
		//check to make sure we have a valid token
		Request::checkToken();

		//get request vars
		$list   = Request::getInt('list_' . \Session::getFormToken(), '', 'post');
		$email  = Request::getVar('email_' . \Session::getFormToken(), User::get('email'), 'post');
		$sid    = Request::getInt('subscriptionid', 0);
		$hp1    = Request::getVar('hp1', '', 'post');
		$return = base64_decode(Request::getVar('return', '/', 'post'));

		//check to make sure our honey pot is good
		if ($hp1 != '')
		{
			die(Lang::txt('COM_NEWSLETTER_HP_ERROR'));
		}

		//validate email
		if (!isset($email) || $email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			//inform user and redirect
			App::redirect(
				Route::url($return),
				Lang::txt('COM_NEWSLETTER_SUBSCRIBE_BADEMAIL'),
				'error'
			);
			return;
		}

		//validate list
		if (!isset($list) || !is_numeric($list))
		{
			//inform user and redirect
			App::redirect(
				Route::url($return),
				Lang::txt('COM_NEWSLETTER_SUBSCRIBE_BADLIST'),
				'error'
			);
			return;
		}

		//load mailing list object
		$mailinglist = Mailinglist::oneOrFail($list);

		//make sure its not private or already deleted
		if (!$mailinglist->private && !$mailinglist->deleted)
		{
			$subscription = Email::blank()
				->set(array(
					'id'         => $sid,
					'mid'        => $list,
					'email'      => $email,
					'status'     => 'inactive',
					'date_added' => \Date::toSql()
				));

			//mail confirmation email and save subscription
			if (Helper::sendMailinglistConfirmationEmail($email, $mailinglist, false))
			{
				$subscription->save();
			}
		}

		//inform user and redirect
		App::redirect(
			Route::url($return),
			Lang::txt('COM_NEWSLETTER_SUBSCRIBE_SUCCESS', $mailinglist->name)
		);
	}

	/**
	 * Subscribe/Unsubscribe from *Multiple* Mailing Lists
	 *
	 * @return 	void
	 */
	public function doMultiSubscribeTask()
	{
		//get request vars
		$lists = Request::getVar('lists', array(), 'post');
		$email = User::get('email');

		//get mailing lists user belongs to
		$e = Email::blank()->getTableName();
		$m = Mailinglist::blank()->getTableName();

		$mylists = Mailinglist::all()
			->select($m . '.*')
			->select($e . '.status')
			->select($e . '.confirmed')
			->select($e . '.id', 'emailid')
			->join($e, $e . '.mid', $m . '.id', 'inner')
			->whereEquals($e . '.email', $email)
			->whereEquals('deleted', 0)
			->rows();

		$keys = array();
		foreach ($mylists as $mylist)
		{
			$keys[] = $mylist->id;
		}

		// subscribe user to checked lists
		foreach ($lists as $list)
		{
			//only subscribe if not previously
			if (!in_array($list, $keys))
			{
				//load mailing list object
				$mailinglist = Mailinglist::oneOrFail($list);

				//make sure its not private or already deleted
				if (!$mailinglist->private && !$mailinglist->deleted)
				{
					$subscription = Email::blank()
						->set(array(
							'mid'        => $list,
							'email'      => $email,
							'status'     => 'inactive',
							'date_added' => Date::toSql()
						));

					//mail confirmation email and save subscription
					if (Helper::sendMailinglistConfirmationEmail($email, $mailinglist, false))
					{
						$subscription->save();
					}
				}
			}
		}

		//check to make sure we dont need to unsubscribe from lists
		foreach ($mylists as $mylist)
		{
			//instantiate newsletter mailing email
			$memail = Email::oneOrFail($mylist->emailid);

			//do we want to mark as active or mark as unsubscribed
			if (!in_array($mylist->id, $lists))
			{
				//set as unsubscribed
				$memail->set('status', 'unsubscribed');
				$memail->set('confirmed', 0);
				$memail->set('date_confirmed', null);
			}
			else if ($mylist->status != 'active')
			{
				//set as active
				$memail->set('status', 'inactive');

				//load mailing list object
				$mailinglist = Mailinglist::oneOrFail($mylist->id);

				//send a new confirmation
				Helper::sendMailinglistConfirmationEmail($email, $mailinglist, false);

				//delete all unsubscribes
				$sql = "DELETE FROM `#__newsletter_mailinglist_unsubscribes`
						WHERE mid=" . $this->database->quote($mylist->id) . "
						AND email=" . $this->database->quote($email);
				$this->database->setQuery($sql);
				$this->database->query();
			}

			//save
			$memail->save();
		}

		//inform user and redirect
		App::redirect(
			Route::url('index.php?option=com_newsletter&task=subscribe'),
			Lang::txt('COM_NEWSLETTER_MAILINGLISTS_SAVE_SUCCESS')
		);
	}

	/**
	 * Unsubscribe From Mailing Lists
	 *
	 * @return 	void
	 */
	public function unsubscribeTask()
	{
		//get request vars
		$email = urldecode(Request::getVar('e', ''));
		$token = Request::getVar('t', '');

		//parse token
		$recipient = Helper::parseMailingToken($token);

		//make sure mailing recipient email matches email param
		if ($email != $recipient->email)
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter&task=subscribe'),
				Lang::txt('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_LINK_ISSUE'),
				'error'
			);
			return;
		}

		//get newsletter mailing to get mailing list id mailing was sent to
		$mailing = Mailing::oneOrFail($recipient->mid);

		//make sure we have a mailing object
		if (!is_object($mailing))
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter&task=subscribe'),
				Lang::txt('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_NO_MAILING'),
				'error'
			);
			return;
		}

		//is the mailing list to the default hub mailing list?
		if ($mailing->lid == '-1')
		{
			$mailinglist = Mailinglist::blank()
				->set(array(
					'id' => -1,
					'name' => 'HUB Members',
					'description' => Lang::txt('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_DEFAULTLIST')
				));
		}
		else
		{
			//load mailing list
			$mailinglist = Mailinglist::oneOrFail($mailing->lid);
		}

		//check to make sure were not already unsubscribed
		$unsubscribedAlready = false;
		if ($mailing->lid == '-1')
		{
			$sql = "SELECT *
					FROM `#__users` AS u
					WHERE u.email=" . $this->database->quote($recipient->email) . "
					AND u.sendEmail > " . $this->database->quote(0);
			$this->database->setQuery($sql);
			$profile = $this->database->loadObject();

			if (!is_object($profile) || $profile->id == '')
			{
				$unsubscribedAlready = true;
			}
		}
		else
		{
			//check to make sure email is on list
			$sql = "SELECT *
					FROM `#__newsletter_mailinglist_emails` AS mle
					WHERE mle.mid=" . $this->database->quote($mailing->lid) . "
					AND mle.email=" . $this->database->quote($recipient->email) . "
					AND mle.status=" . $this->database->quote('active');
			$this->database->setQuery($sql);
			$list = $this->database->loadObject();

			if (!is_object($list) || $list->id == '')
			{
				$unsubscribedAlready = true;
			}
		}

		//are we unsubscribed already
		if ($unsubscribedAlready)
		{
			Notify::error(Lang::txt('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_ALREADY_UNSUBSCRIBED', $mailinglist->name));

			if (User::isGuest())
			{
				App::redirect(
					Route::url('index.php?option=com_newsletter')
				);
				return;
			}

			App::redirect(
				Route::url('index.php?option=com_newsletter&task=subscribe')
			);
			return;
		}

		//build title
		$this->_buildTitle();

		//build pathway
		$this->_buildPathway();

		//output
		$this->view
			->set('title', $this->_title)
			->set('mailinglist', $mailinglist)
			->setLayout('unsubscribe')
			->display();
	}

	/**
	 * Unsubscribe User Mailing Lists
	 *
	 * @return 	void
	 */
	public function doUnsubscribeTask()
	{
		//get request vars
		$email      = urldecode(Request::getVar('e', ''));
		$token      = Request::getVar('t', '');
		$reason     = Request::getVar('reason', '');
		$reason_alt = Request::getVar('reason-alt', '');

		//grab the reason explaination if user selected other
		if ($reason == 'Other')
		{
			$reason = $reason_alt;
		}

		//parse mailing token
		$recipient = Helper::parseMailingToken($token);

		//make sure the token is valid
		if (!is_object($recipient) || $email != $recipient->email)
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter&task=subscribe'),
				Lang::txt('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_LINK_ISSUE'),
				'error'
			);
			return;
		}

		//get newsletter mailing to get mailing list id mailing was sent to
		$mailing = Mailing::oneOrNew($recipient->mid);

		//make sure we have a mailing object
		if (!$mailing->get('id'))
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter&task=subscribe'),
				Lang::txt('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_NO_MAILING'),
				'error'
			);
			return;
		}

		//are we unsubscribing from default list?
		$sql = '';
		if ($mailing->lid == '-1')
		{
			if (!User::isGuest())
			{
				$sql = "UPDATE `#__users` SET `sendEmail`=0 WHERE `id`=" . $this->database->quote(User::get('id'));
			}
			else
			{
				//build return url and redirect url
				$return = Route::url('index.php?option=com_newsletter&task=unsubscribe&e=' . $email . '&t=' . $token);

				//inform user and redirect
				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($return)),
					Lang::txt('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_MUST_LOGIN'),
					'warning'
				);
				return;
			}
		}
		else
		{
			//update the emails status on the mailing list
			$sql = "UPDATE `#__newsletter_mailinglist_emails`
					SET status=" . $this->database->quote('unsubscribed') . "
					WHERE mid=" . $this->database->quote($mailing->lid) . "
					AND email=" . $this->database->quote($recipient->email);
		}

		//set query and execute
		$this->database->setQuery($sql);
		if (!$this->database->query())
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter&task=unsubscribe&e=' . $email . '&t=' . $token),
				Lang::txt('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_ERROR'),
				'error'
			);
			return;
		}

		//insert unsubscribe reason
		$sql = "INSERT INTO `#__newsletter_mailinglist_unsubscribes` (mid,email,reason)
				VALUES (" . $this->database->quote($mailing->lid) . "," . $this->database->quote($recipient->email) . "," . $this->database->quote($reason) . ")";
		$this->database->setQuery($sql);
		$this->database->query();

		//inform user of successful unsubscribe
		Notify::success(Lang::txt('COM_NEWSLETTER_MAILINGLIST_UNSUBSCRIBE_SUCCESS'));

		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter')
			);
			return;
		}

		App::redirect(
			Route::url('index.php?option=com_newsletter&task=subscribe')
		);
	}

	/**
	 * Confirm Subscription to Mailing list
	 *
	 * @return 	void
	 */
	public function confirmTask()
	{
		//get request vars
		$email = urldecode(Request::getVar('e', ''));
		$token = Request::getVar('t', '');

		//make sure we have an email
		$mailinglistEmail = Helper::parseConfirmationToken($token);

		//make sure the token is valid
		if (!is_object($mailinglistEmail) || $email != $mailinglistEmail->email)
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter'),
				Lang::txt('COM_NEWSLETTER_MAILINGLIST_CONFIRMATION_LINK_ISSUE'),
				'error'
			);
			return;
		}

		//instantiate mailing list email object and load based on id
		$model = Email::oneOrFail($mailinglistEmail->id);

		//set that we are now confirmed
		$model->set('status', 'active');
		$model->set('confirmed', 1);
		$model->set('date_confirmed', Date::toSql());

		//save
		$model->save();

		//inform user
		Notify::success(Lang::txt('COM_NEWSLETTER_MAILINGLIST_CONFIRM_SUCCESS'));

		//if were not logged in go back to newsletter page
		if (User::isGuest())
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter')
			);
			return;
		}

		App::redirect(
			Route::url('index.php?option=com_newsletter&task=subscribe')
		);
	}

	/**
	 * Remove From Mailing list
	 *
	 * @return 	void
	 */
	public function removeTask()
	{
		//get request vars
		$email = urldecode(Request::getVar('e', ''));
		$token = Request::getVar('t', '');

		//make sure we have an email
		$mailinglistEmail = Helper::parseConfirmationToken($token);

		//make sure the token is valid
		if (!is_object($mailinglistEmail) || $email != $mailinglistEmail->email)
		{
			App::redirect(
				Route::url('index.php?option=com_newsletter'),
				Lang::txt('COM_NEWSLETTER_MAILINGLIST_CONFIRMATION_LINK_ISSUE'),
				'error'
			);
			return;
		}

		//instantiate mailing list email object and load based on id
		$model = Email::oneOrFail($mailinglistEmail->id);

		//unsubscribe & unconfirm email
		$model->set('status', 'unsubscribed');
		$model->set('confirmed', 0);

		//save
		$model->save();

		//inform user
		App::redirect(
			Route::url('index.php?option=com_newsletter'),
			Lang::txt('COM_NEWSLETTER_MAILINGLIST_REMOVED_SUCCESS'),
			'success'
		);
	}

	/**
	 * Resend Newsletter Confirmation
	 * 
	 * @return  void
	 */
	public function resendConfirmationTask()
	{
		//get request vars
		$mid = Request::getInt('mid', 0);

		//instantiate mailing list object
		$mailinglist = Mailinglist::oneOrFail($mid);

		//send confirmation email
		Helper::sendMailinglistConfirmationEmail(User::get('email'), $mailinglist, false);

		//inform user and redirect
		App::redirect(
			Route::url('index.php?option=com_newsletter&task=subscribe'),
			Lang::txt('COM_NEWSLETTER_MAILINGLISTS_CONFIRM_SENT', User::get('email'))
		);
	}
}
