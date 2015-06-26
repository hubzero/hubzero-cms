<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	 * @return     void
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
		}
	}

	/**
	 * Save a reply
	 *
	 * @return     void
	 */
	private function savereply()
	{
		// Check for request forgeries
		Request::checkToken();

		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_LOGIN_NOTICE'));
			return;
		}

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

		$database = App::get('db');

		$row = new \Hubzero\Item\Comment($database);
		if (!$row->bind($comment))
		{
			$this->setError($row->getError());
			return;
		}

		// Perform some text cleaning, etc.
		$row->content    = \Hubzero\Utility\Sanitize::clean($row->content);
		//$row->content    = nl2br($row->content);
		$row->anonymous  = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
		$row->created    = ($row->id ? $row->created : Date::toSql());
		$row->state      = ($row->id ? $row->state : 0);
		$row->created_by = ($row->id ? $row->created_by : User::get('id'));

		// Check for missing (required) fields
		if (!$row->check())
		{
			$this->setError($row->getError());
			return;
		}

		// Save the data
		if (!$row->store())
		{
			$this->setError($row->getError());
			return;
		}
	}

	/**
	 * Delete a reply
	 *
	 * @return     void
	 */
	public function deletereply()
	{
		$database = App::get('db');
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
		$reply = new \Hubzero\Item\Comment($database);
		$reply->load($replyid);

		// Permissions check
		if ($reply->created_by != User::get('id') && !User::authorise('core.admin'))
		{
			return;
		}

		$reply->setState($replyid, 2);

		App::Redirect(
			Route::url('index.php?option=' . $this->_option . '&id=' . $resource->id . '&active=reviews', false)
		);
	}

	/**
	 * Rate an item
	 *
	 * @return     void
	 */
	public function rateitem()
	{
		$database = App::get('db');

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

		// Load answer
		$rev = new \Components\Resources\Tables\Review($database);
		$rev->load($id);
		$voted = $rev->getVote($id, $cat, User::get('id'));

		if (!$voted && $vote) // && $rev->user_id != User::get('id'))
		{
			require_once(PATH_CORE . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'vote.php');
			$v = new \Components\Answers\Tables\Vote($database);
			$v->referenceid = $id;
			$v->category    = $cat;
			$v->voter       = User::get('id');
			$v->ip          = $ip;
			$v->voted       = Date::toSql();
			$v->helpful     = $vote;
			if (!$v->check())
			{
				$this->setError($v->getError());
				return;
			}
			if (!$v->store())
			{
				$this->setError($v->getError());
				return;
			}
		}

		// update display
		if ($ajax)
		{
			$response = $rev->getRating($id, User::get('id'));

			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'resources',
					'element' => 'reviews',
					'name'    => 'browse',
					'layout'  => '_rateitem'
				)
			);
			$view->option = $this->_option;
			$view->item = new ResourcesModelReview($response[0]);
			$view->rid = $rid;

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
	 * @return     void
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

		$database = App::get('db');

		$review = new \Components\Resources\Tables\Review($database);
		$review->loadUserReview($resource->id, User::get('id'));
		if (!$review->id)
		{
			// New review, get the user's ID
			$review->user_id = User::get('id');
			$review->resource_id = $resource->id;
			$review->tags = '';
		}
		else
		{
			// Editing a review, do some prep work
			$review->comment = str_replace('<br />', '', $review->comment);

			$RE = new \Components\Resources\Helpers\Helper($resource->id, $database);
			$RE->getTagsForEditing($review->user_id);
			$review->tags = ($RE->tagsForEditing) ? $RE->tagsForEditing : '';
		}
		$review->rating = ($myr) ? $myr : $review->rating;

		// Store the object in our registry
		$this->myreview = $review;
		return;
	}

	/**
	 * Save a review
	 *
	 * @return     void
	 */
	public function savereview()
	{
		// Incoming
		$resource_id = Request::getInt('resource_id', 0);

		// Do we have a resource ID?
		if (!$resource_id)
		{
			// No ID - fail! Can't do anything else without an ID
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID'));
			return;
		}

		$database = App::get('db');

		// Bind the form data to our object
		$row = new \Components\Resources\Tables\Review($database);
		if (!$row->bind($_POST))
		{
			$this->setError($row->getError());
			return;
		}

		// Perform some text cleaning, etc.
		$row->id        = Request::getInt('reviewid', 0);
		if (!$row->id)
		{
			$row->state = 1;
		}
		$row->comment   = \Hubzero\Utility\Sanitize::clean($row->comment);
		$row->anonymous = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
		$row->created   = ($row->created && $row->created != '0000-00-00 00:00:00') ? $row->created : Date::toSql();

		// Check for missing (required) fields
		if (!$row->check())
		{
			$this->setError($row->getError());
			return;
		}
		// Save the data
		if (!$row->store())
		{
			$this->setError($row->getError());
			return;
		}

		// Calculate the new average rating for the parent resource
		$resource =& $this->resource;
		$resource->calculateRating();
		$resource->updateRating();

		// Process tags
		$tags = trim(Request::getVar('review_tags', ''));
		if ($tags)
		{
			$rt = new \Components\Resources\Helpers\Tags($resource_id);
			$rt->setTags($tags, $row->user_id);
		}

		// Instantiate a helper object and get all the contributor IDs
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
		$eview->user     = User::getRoot();
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
	 * @return     void
	 */
	public function deletereview()
	{
		$database = App::get('db');
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

		$review = new \Components\Resources\Tables\Review($database);
		$review->load($reviewid);

		// Permissions check
		if ($review->user_id != User::get('id') && !User::authorise('core.admin'))
		{
			return;
		}

		$review->state = 2;
		$review->store();

		// Delete the review's comments
		$reply = new \Hubzero\Item\Comment($database);

		$comments1 = $reply->find(array(
			'parent'    => $reviewid,
			'item_type' => 'review',
			'item_id'   => $resource->id
		));
		if (count($comments1) > 0)
		{
			foreach ($comments1 as $comment1)
			{
				$reply->setState($comment1->id, 2);
			}
		}

		// Recalculate the average rating for the parent resource
		$resource->calculateRating();
		$resource->updateRating();

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&id=' . $resource->id . '&active=reviews', false)
		);
	}
}

