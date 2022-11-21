<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Helper class for reviews
 */
class PlgResourcesReviewsHelper extends \Hubzero\Base\Obj
{
	/**
	 * Execute an action
	 *
	 * @return  void
	 */
	public function execute()
	{
		// Incoming action
		$action = Request::getString('action', '');

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
			case 'addreview':
				$this->editreview();
				break;
			case 'editreview':
				$this->editreview();
				break;
			case 'savereview':
				$this->savereview();
				break;
			case 'deletereview':
				$this->deletereview();
				break;
			case 'savereply':
				$this->savereply();
				break;
			case 'deletereply':
				$this->deletereply();
				break;
			case 'rateitem':
				$this->rateitem();
				break;
			default:
				break;
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
		$comment = Request::getArray('comment', array(), 'post');

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

		// Incoming
		$replyid = Request::getInt('comment', 0);

		// Do we have a review ID?
		if (!$replyid)
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_COMMENT_ERROR_NO_REFERENCE_ID'));
			return;
		}

		// Do we have a resource ID?
		if (!$this->resource->id)
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
			Route::url($this->resource->link() . '&active=reviews', false)
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
		$cat  = Request::getString('category', 'review');
		$vote = Request::getString('vote', '');
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
			Route::url($this->resource->link() . '&active=reviews', false)
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

		// Do we have an ID?
		if (!$this->resource->id)
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
			$review = \Components\Resources\Reviews\Models\Review::oneByUser($this->resource->id, User::get('id'));
		}

		if (!$review->get('id'))
		{
			// New review, get the user's ID
			$review->set('user_id', User::get('id'));
			$review->set('resource_id', $this->resource->id);
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

		// Is the user an author ont his resource?
		if ($this->isAuthor)
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_AUTHOR_NOTICE'));
			return;
		}

		// Check for request forgeries
		Request::checkToken();

		// Incoming
		$data = Request::getArray('review', array(), 'post');

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
		$calculated = \Components\Resources\Reviews\Models\Review::averageByResource($this->resource->get('id'));

		// Recalculate the average rating for the parent resource
		$this->resource->set('rating', $calculated['rating']);
		$this->resource->set('times_rated', $calculated['times_rated']);
		$this->resource->save();

		// Instantiate a helper object and get all the contributor IDs
		$users = $this->resource->authors()
			->rows()
			->fieldsByKey('authorid');

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
		$eview->resource = $this->resource;
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

		// Incoming
		$reviewid = Request::getInt('comment', 0);

		// Do we have a review ID?
		if (!$reviewid)
		{
			$this->setError(Lang::txt('PLG_RESOURCES_REVIEWS_NO_ID'));
			return;
		}

		// Do we have a resource ID?
		if (!$this->resource->id)
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

		$ratings = \Components\Resources\Reviews\Models\Review::all()
			->whereEquals('resource_id', $this->resource->get('id'))
			->rows()
			->fieldsByKey('rating');

		$totalcount = count($ratings);
		$totalvalue = 0;

		// Add the ratings up
		foreach ($ratings as $rating)
		{
			$totalvalue = $totalvalue + $rating;
		}

		// Find the average of all ratings
		$newrating = ($totalcount > 0) ? $totalvalue / $totalcount : 0;

		// Round to the nearest half
		$newrating = ($newrating > 0) ? round($newrating*2)/2 : 0;

		// Recalculate the average rating for the parent resource
		$this->resource->set('rating', $newrating);
		$this->resource->set('times_rated', $totalcount);
		$this->resource->save();

		App::redirect(
			Route::url($this->resource->link() . '&active=reviews', false)
		);
	}
}
