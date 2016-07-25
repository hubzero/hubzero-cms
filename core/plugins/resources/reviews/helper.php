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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Helper class for reviews
 */
class PlgResourcesReviewsHelper extends \Hubzero\Base\Object
{
	/**
	 * Execute an action
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Incoming action
		$action = Request::getVar('action', '');

		$this->loggedin = true;

		if ($action)
		{
			// Check the user's logged-in status
			if (User::isGuest())
			{
				$this->loggedin = false;
				return;
			}
		}

		// Perform an action
		switch ($action)
		{
			case 'addreview':    $this->editreview();   break;
			case 'editreview':   $this->editreview();   break;
			case 'savereview':   $this->savereview();   break;
			case 'deletereview': $this->deletereview(); break;
			case 'savereply':    $this->savereply();    break;
			case 'deletereply':  $this->deletereply();  break;
			case 'rateitem':     $this->rateitem();     break;
			default: break;
		}
	}

	/**
	 * Save a reply
	 *
	 * @return  void
	 */
	private function savereply()
	{
		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_LOGIN_NOTICE'));
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$id = Request::getInt('id', 0);

		// Trim and addslashes all posted items
		$comment = Request::getVar('comment', array(), 'post', 'none', 2);

		if (!$id)
		{
			// Cannot proceed
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_COMMENT_ERROR_NO_REFERENCE_ID'));
			return;
		}

		$row = \Hubzero\Item\Comment::oneOrNew($comment['id'])->set($comment);

		// Perform some text cleaning, etc.
		$row->set('content', \Hubzero\Utility\Sanitize::stripImages(\Hubzero\Utility\Sanitize::clean($row->get('content'))));
		$row->set('anonymous', ($row->get('anonymous') == 1 || $row->get('anonymous') == '1') ? $row->get('anonymous') : 0);
		$row->set('state', $row->isNew() ? 1 : $row->get('state'));

		// Save the data
		if (!$row->save())
		{
			$this->setError($row->getError());
			return;
		}
	}

	/**
	 * Delete a reply
	 *
	 * @return  void
	 */
	public function deletereply()
	{
		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_LOGIN_NOTICE'));
			return;
		}

		$resource =& $this->resource;

		// Incoming
		$replyid = Request::getInt('comment', 0);

		// Do we have a review ID?
		if (!$replyid)
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_COMMENT_ERROR_NO_REFERENCE_ID'));
			return;
		}

		// Do we have a resource ID?
		if (!$resource->id)
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID'));
			return;
		}

		// Delete the review
		$reply = \Hubzero\Item\Comment::oneOrFail($replyid);

		// Permissions check
		if ($reply->get('created_by') != User::get('id') && !User::authorise('core.admin'))
		{
			return;
		}

		$reply->set('state', \Hubzero\Item\Comment::STATE_DELETED);
		$reply->save();

		App::Redirect(
			Route::url('index.php?option=' . $this->_option . '&id=' . $resource->id . '&active=reviews', false)
		);
	}

	/**
	 * Rate an item
	 *
	 * @return  void
	 */
	public function rateitem()
	{
		$id   = Request::getInt('refid', 0);
		$ajax = Request::getInt('no_html', 0);
		$cat  = Request::getVar('category', 'review');
		$vote = Request::getVar('vote', '');
		$ip   = Request::ip();
		$rid  = Request::getInt('id', 0);

		if (!$id)
		{
			// Cannot proceed
			return;
		}

		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_PLEASE_LOGIN_TO_VOTE'));
			return;
		}

		// Load entry
		$rev = \Components\Resources\Reviews\Models\Review::oneOrNew($id);

		if (!$rev->vote($vote, User::get('id'), $ip))
		{
			$this->setError($rev->getError());
			return;
		}

		// update display
		if ($ajax)
		{
			$rev->set('vote', $vote);
			$rev->set('helpful', $rev->votes()->whereEquals('vote', 1)->total());
			$rev->set('nothelpful', $rev->votes()->whereEquals('vote', -1)->total());

			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'resources',
					'element' => 'reviews',
					'name'    => 'browse',
					'layout'  => '_rateitem'
				)
			);
			$view->option = $this->_option;
			$view->item = $rev;

			$view->display();
			exit();
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&id=' . $rid . '&active=reviews', false)
		);
	}

	/**
	 * Edit a review
	 *
	 * @return  void
	 */
	public function editreview()
	{
		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_LOGIN_NOTICE'));
			return;
		}

		$resource =& $this->resource;

		// Do we have an ID?
		if (!$resource->id)
		{
			// No - fail! Can't do anything else without an ID
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID'));
			return;
		}

		// Incoming
		$myr = Request::getInt('comment', 0);

		if ($myr)
		{
			$review = \Components\Resources\Reviews\Models\Review::oneOrNew($myr);
		}
		else
		{
			$review = \Components\Resources\Reviews\Models\Review::oneByUser($resource->id, User::get('id'));
		}

		if (!$review->get('id'))
		{
			// New review, get the user's ID
			$review->set('user_id', User::get('id'));
			$review->set('resource_id', $resource->id);
		}
		else
		{
			// Editing a review, do some prep work
			$review->set('comment', str_replace('<br />', '', $review->get('comment')));
		}
		$review->set('rating', ($myr ? $myr : $review->get('rating')));
		$review->set('state', \Components\Resources\Reviews\Models\Review::STATE_PUBLISHED);

		// Store the object in our registry
		$this->myreview = $review;
		return;
	}

	/**
	 * Save a review
	 *
	 * @return  void
	 */
	public function savereview()
	{
		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_LOGIN_NOTICE'));
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$data = Request::getVar('review', array(), 'post', 'none', 2);

		// Bind the form data to our object
		$row = \Components\Resources\Reviews\Models\Review::oneOrNew($data['id'])->set($data);

		// Perform some text cleaning, etc.
		if ($row->isNew())
		{
			$row->set('state', \Components\Resources\Reviews\Models\Review::STATE_PUBLISHED);
		}
		$row->set('comment', \Hubzero\Utility\Sanitize::stripImages(\Hubzero\Utility\Sanitize::clean($row->get('comment'))));
		$row->set('anonymous', ($row->get('anonymous') ? 1 : 0));

		// Save the data
		if (!$row->save())
		{
			$this->setError($row->getError());
			return;
		}

		// Calculate the new average rating for the parent resource
		$resource =& $this->resource;
		$resource->calculateRating();
		$resource->updateRating();

		// Instantiate a helper object and get all the contributor IDs
		$database = App::get('db');

		$helper = new \Components\Resources\Helpers\Helper($resource->id, $database);
		$helper->getContributorIDs();
		$users = $helper->contributorIDs;

		// Build the subject
		$subject = Config::get('sitename') . ' ' . Lang::txt('PLG_RESOURCES_REVIEWS_CONTRIBUTIONS');

		// Message
		$eview = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'resources',
				'element' => 'reviews',
				'name'    => 'emails'
			)
		);
		$eview->option   = $this->_option;
		$eview->user     = User::getInstance();
		$eview->resource = $resource;
		$eview->review   = $row;
		$message = $eview->loadTemplate();

		// Build the "from" data for the e-mail
		$from = array(
			'name'  => Config::get('sitename') . ' ' . Lang::txt('PLG_RESOURCES_REVIEWS_CONTRIBUTIONS'),
			'email' => Config::get('mailfrom')
		);

		// Send message
		if (!Event::trigger('xmessage.onSendMessage', array('resources_new_comment', $subject, $message, $from, $users, $this->_option)))
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_FAILED_TO_MESSAGE'));
		}
	}

	/**
	 * Delete a review
	 *
	 * @return  void
	 */
	public function deletereview()
	{
		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_LOGIN_NOTICE'));
			return;
		}

		$resource =& $this->resource;

		// Incoming
		$reviewid = Request::getInt('comment', 0);

		// Do we have a review ID?
		if (!$reviewid)
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_NO_ID'));
			return;
		}

		// Do we have a resource ID?
		if (!$resource->id)
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID'));
			return;
		}

		$review = \Components\Resources\Reviews\Models\Review::oneOrFail($reviewid);

		// Permissions check
		if ($review->get('user_id') != User::get('id') && !User::authorise('core.admin'))
		{
			return;
		}

		$review->set('state', \Components\Resources\Reviews\Models\Review::STATE_DELETED);
		$review->save();

		// Recalculate the average rating for the parent resource
		$resource->calculateRating();
		$resource->updateRating();

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&id=' . $resource->id . '&active=reviews', false)
		);
	}
}
