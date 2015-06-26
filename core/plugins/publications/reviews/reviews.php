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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

require_once( PATH_CORE . DS . 'components'.DS
	.'com_publications' . DS . 'tables' . DS . 'review.php');

/**
 * Publications Plugin class for reviews
 */
class plgPublicationsReviews extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->infolink = '/kb/points/';
		$this->banking = Component::params('com_members')->get('bankAccounts');
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $publication 	Current publication
	 * @param      string $version 		Version name
	 * @param      boolean $extended 	Whether or not to show panel
	 * @return     array
	 */
	public function &onPublicationAreas( $model, $version = 'default', $extended = true )
	{
		$areas = array();
		if ($model->_category->_params->get('plg_reviews') && $extended && $model->access('view-all'))
		{
			$areas = array(
				'reviews' => Lang::txt('PLG_PUBLICATIONS_REVIEWS')
			);
		}

		return $areas;
	}

	/**
	 * Rate item (AJAX)
	 *
	 * @param      string $option
	 * @return     array
	 */
	public function onPublicationRateItem( $option )
	{
		$arr = array(
			'html'    =>'',
			'metadata'=>''
		);

		$h = new PlgPublicationsReviewsHelper();
		$h->option   = $option;
		$h->_option  = $option;
		$h->execute();

		return $arr;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  	$publication 	Current publication
	 * @param      string  	$option    		Name of the component
	 * @param      array   	$areas     		Active area(s)
	 * @param      string  	$rtrn      		Data to be returned
	 * @param      string 	$version 		Version name
	 * @param      boolean 	$extended 		Whether or not to show panel
	 * @return     array
	 */
	public function onPublication( $model, $option, $areas, $rtrn='all', $version = 'default', $extended = true )
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas ))
		{
			if (!array_intersect( $areas, $this->onPublicationAreas( $model ) )
			&& !array_intersect( $areas, array_keys( $this->onPublicationAreas( $model ) ) ))
			{
				$rtrn = 'metadata';
			}
		}
		if (!$model->category()->_params->get('plg_reviews') || !$extended)
		{
			return $arr;
		}

		include_once(__DIR__ . DS . 'models' . DS . 'review.php');

		// Instantiate a helper object and perform any needed actions
		$h = new PlgPublicationsReviewsHelper();
		$h->publication = $model;
		$h->option      = $option;
		$h->_option     = $option;
		$h->execute();

		// Get reviews for this publication
		$database = App::get('db');
		$r = new \Components\Publications\Tables\Review( $database );
		$reviews = $r->getRatings($model->get('id'));
		if (!$reviews)
		{
			$reviews = array();
		}

		$arr['count'] = count($reviews);
		$arr['name']  = 'reviews';
		// Are we returning any HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			// Did they perform an action?
			// If so, they need to be logged in first.
			if (!$h->loggedin)
			{
				$rtrn = Request::getVar('REQUEST_URI', Route::url($model->link($this->_name)), 'server');

				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
					Lang::txt('PLG_PUBLICATIONS_REVIEWS_LOGIN_NOTICE'),
					'warning'
				);
				return;
			}
			else
			{
				// Instantiate a view
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'  =>'publications',
						'element' =>'reviews',
						'name'    =>'browse'
					)
				);
			}

			// Pass the view some info
			$view->option 		= $option;
			$view->publication 	= $model;
			$view->reviews 		= $reviews;
			$view->voting 		= $this->params->get('voting', 1);
			$view->h 			= $h;
			$view->banking 		= $this->banking;
			$view->infolink 	= $this->infolink;
			$view->config   	= $this->params;
			if ($h->getError())
			{
				$view->setError( $h->getError() );
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder' =>'publications',
					'element'=>'reviews',
					'name'   =>'metadata'
				)
			);

			$view->url     = Route::url($model->link($this->_name));
			$view->url2    = Route::url($model->link($this->_name) . '&action=addreview#reviewform');
			$view->reviews = $reviews;

			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Get all replies for an item
	 *
	 * @param      object  $item     Item to look for reports on
	 * @param      string  $category Item type
	 * @param      integer $level    Depth
	 * @param      boolean $abuse    Abuse flag
	 * @return     array
	 */
	public function getComments($id, $item, $category, $level, $abuse=false)
	{
		$database = App::get('db');

		$level++;

		$hc = new \Hubzero\Item\Comment($database);
		$comments = $hc->find(array(
			'parent'    => ($level == 1 ? 0 : $item->id),
			'item_id'   => $id,
			'item_type' => $category
		));

		if ($comments)
		{
			foreach ($comments as $comment)
			{
				$comment->replies = self::getComments($id, $comment, 'pubreview', $level, $abuse);
				if ($abuse)
				{
					$comment->abuse_reports = self::getAbuseReports($comment->id, 'pubreview');
				}
			}
		}
		return $comments;
	}

	/**
	 * Get abuse reports for a comment
	 *
	 * @param      integer $item     Item to look for reports on
	 * @param      string  $category Item type
	 * @return     integer
	 */
	public function getAbuseReports($item, $category)
	{
		$database = App::get('db');

		$ra = new \Components\Support\Tables\ReportAbuse( $database );
		return $ra->getCount( array('id' => $item, 'category' => $category) );
	}
}

/**
 * Helper class for reviews
 */
class PlgPublicationsReviewsHelper extends \Hubzero\Base\Object
{
	/**
	 * Execute an action
	 *
	 * @return     void
	 */
	public function execute()
	{
		// Incoming action
		$action = Request::getVar( 'action', '' );

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
		switch ( $action )
		{
			case 'addreview':    $this->editreview();   break;
			case 'editreview':   $this->editreview();   break;
			case 'savereview':   $this->savereview();   break;
			case 'deletereview': $this->deletereview(); break;
			case 'savereply': 	 $this->savereply(); 	break;
			case 'deletereply':  $this->deletereply();  break;
			case 'rateitem':   	 $this->rateitem();  	break;
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
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_LOGIN_NOTICE') );
			return;
		}

		$publication =& $this->publication;

		// Trim and addslashes all posted items
		$comment = Request::getVar('comment', array(), 'post', 'none', 2);

		if (!$publication->exists())
		{
			// Cannot proceed
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_COMMENT_ERROR_NO_REFERENCE_ID') );
			return;
		}

		$database = App::get('db');

		$row = new \Hubzero\Item\Comment($database);
		if (!$row->bind($comment))
		{
			$this->setError($row->getError());
			return;
		}

		$message = $row->id ? Lang::txt('PLG_PUBLICATIONS_REVIEWS_EDITS_SAVED') : Lang::txt('PLG_PUBLICATIONS_REVIEWS_COMMENT_POSTED');

		// Perform some text cleaning, etc.
		$row->content    = \Hubzero\Utility\Sanitize::clean($row->content);
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

		// Redirect
		App::redirect(Route::url($publication->link('reviews')), $message);
	}

	/**
	 * Delete a reply
	 *
	 * @return     void
	 */
	public function deletereply()
	{
		$database = App::get('db');
		$publication =& $this->publication;

		// Incoming
		$replyid = Request::getInt( 'comment', 0 );

		// Do we have a review ID?
		if (!$replyid)
		{
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_COMMENT_ERROR_NO_REFERENCE_ID') );
			return;
		}

		// Do we have a publication ID?
		if (!$publication->exists())
		{
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_NO_RESOURCE_ID') );
			return;
		}

		// Delete the review
		$reply = new \Hubzero\Item\Comment($database);
		$reply->load($replyid);

		// Permissions check
		if ($reply->created_by != User::get('id'))
		{
			return;
		}
		$reply->setState($replyid, 2);

		// Redirect
		App::redirect(
			Route::url($publication->link('reviews')),
			Lang::txt('PLG_PUBLICATIONS_REVIEWS_COMMENT_DELETED')
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
		$publication =& $this->publication;

		$id   = Request::getInt( 'refid', 0 );
		$ajax = Request::getInt( 'no_html', 0 );
		$cat  = Request::getVar( 'category', 'pubreview' );
		$vote = Request::getVar( 'vote', '' );
		$ip   = Request::ip();

		if (!$id || !$publication->exists())
		{
			// Cannot proceed
			return;
		}

		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_LOGIN_NOTICE') );
			return;
		}

		// Load answer
		$rev = new \Components\Publications\Tables\Review($database);
		$rev->load($id);
		$voted = $rev->getVote($id, $cat, User::get('id'), 'v.id');

		if ($vote)
		{
			require_once( PATH_CORE . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'vote.php' );
			$v = new \Components\Answers\Tables\Vote( $database );
			if ($voted)
			{
				$v->load($voted);
			}
			$v->referenceid = $id;
			$v->category    = $cat;
			$v->voter       = User::get('id');
			$v->ip          = $ip;
			$v->voted       = Date::toSql();
			$v->helpful     = $vote;

			if (!$v->check())
			{
				$this->setError( $v->getError() );
				return;
			}
			if (!$v->store())
			{
				$this->setError( $v->getError() );
				return;
			}
		}

		// update display
		if ($ajax)
		{
			$response = $rev->getRating( $publication->get('id'), User::get('id'));
			$view = new \Hubzero\Plugin\View(
				array(
					'folder' =>'publications',
					'element'=>'reviews',
					'name'   =>'browse',
					'layout' =>'_rateitem'
				)
			);
			$view->option = $this->_option;
			$view->item   = new PublicationsModelReview($response[0]);
			$view->rid    = $publication->get('id');

			$view->display();
			exit();
		}

		App::redirect(Route::url($publication->get('reviews')));
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
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_LOGIN_NOTICE') );
			return;
		}

		$publication =& $this->publication;

		// Do we have an ID?
		if (!$publication->exists())
		{
			// No - fail! Can't do anything else without an ID
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_NO_RESOURCE_ID') );
			return;
		}

		// Incoming
		$myr = Request::getInt( 'myrating', 0 );

		$database = App::get('db');

		$review = new \Components\Publications\Tables\Review( $database );
		$review->loadUserReview( $publication->get('id'), User::get('id'), $publication->get('version_id') );

		if (!$review->id)
		{
			// New review, get the user's ID
			$review->created_by             = User::get('id');
			$review->publication_id         = $publication->get('id');
			$review->publication_version_id = $publication->get('version_id');
			$review->tags                   = '';
			$review->rating                 = 3;
		}
		else
		{
			// Editing a review, do some prep work
			$review->comment = str_replace('<br />', '', $review->comment);

			$this->publication->getTagsForEditing($review->created_by);
			$review->tags = ($this->publication->_tagsForEditing) ? $this->publication->_tagsForEditing : '';
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
		// Is the user logged in?
		if (User::isGuest())
		{
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_LOGIN_NOTICE') );
			return;
		}

		$publication =& $this->publication;

		// Do we have a publication ID?
		if (!$publication->exists())
		{
			// No ID - fail! Can't do anything else without an ID
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_NO_RESOURCE_ID') );
			return;
		}

		$database = App::get('db');

		// Bind the form data to our object
		$row = new \Components\Publications\Tables\Review( $database );
		if (!$row->bind( $_POST ))
		{
			$this->setError( $row->getError() );
			return;
		}

		// Perform some text cleaning, etc.
		$row->id         = Request::getInt( 'reviewid', 0 );
		$row->state      = 1;
		$row->comment    = \Hubzero\Utility\Sanitize::stripAll($row->comment);
		$row->anonymous  = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
		$row->created    = ($row->created) ? $row->created : Date::toSql();
		$row->created_by = User::get('id');

		$message = $row->id ? Lang::txt('PLG_PUBLICATIONS_REVIEWS_EDITS_SAVED') : Lang::txt('PLG_PUBLICATIONS_REVIEWS_REVIEW_POSTED');

		// Check for missing (required) fields
		if (!$row->check())
		{
			$this->setError( $row->getError() );
			return;
		}
		// Save the data
		if (!$row->store())
		{
			$this->setError( $row->getError() );
			return;
		}

		// Calculate the new average rating for the parent publication
		$publication->table()->calculateRating();
		$publication->table()->updateRating();

		// Process tags
		$tags = trim(Request::getVar( 'review_tags', '' ));
		if ($tags)
		{
			$rt = new \Components\Publications\Helpers\Tags( $database );
			$rt->tag_object($row->created_by, $publication->get('id'), $tags, 1, 0);
		}

		// Get version authors
		$users = $publication->table('Author')->getAuthors($publication->get('version_id'), 1, 1, true );

		// Build the subject
		$subject = Config::get('sitename') . ' ' . Lang::txt('PLG_PUBLICATIONS_REVIEWS_CONTRIBUTIONS');

		// Message
		$eview = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'publications',
				'element' =>'reviews',
				'name'    =>'emails'
			)
		);
		$eview->option      = $this->_option;
		$eview->juser       = User::getRoot();
		$eview->publication = $publication;
		$message            = $eview->loadTemplate();
		$message            = str_replace("\n", "\r\n", $message);

		// Build the "from" data for the e-mail
		$from = array();
		$from['name']  = Config::get('sitename').' '.Lang::txt('PLG_PUBLICATIONS_REVIEWS_CONTRIBUTIONS');
		$from['email'] = Config::get('mailfrom');

		// Send message
		if (!Event::trigger( 'xmessage.onSendMessage', array(
				'publications_new_comment',
				$subject,
				$message,
				$from,
				$users,
				$this->_option
			)
		))
		{
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_FAILED_TO_MESSAGE') );
		}

		App::redirect(Route::url($publication->link('reviews')), $message);
		return;
	}

	/**
	 * Delete a review
	 *
	 * @return     void
	 */
	public function deletereview()
	{
		$database = App::get('db');
		$publication =& $this->publication;

		// Incoming
		$reviewid = Request::getInt( 'comment', 0 );

		// Do we have a review ID?
		if (!$reviewid)
		{
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_NO_ID') );
			return;
		}

		// Do we have a publication ID?
		if (!$publication->exists())
		{
			$this->setError( Lang::txt('PLG_PUBLICATIONS_REVIEWS_NO_RESOURCE_ID') );
			return;
		}

		$review = new \Components\Publications\Tables\Review( $database );
		$review->load($reviewid);

		// Permissions check
		if ($review->created_by != User::get('id'))
		{
			return;
		}

		$review->state = 2;
		$review->store();

		// Delete the review's comments
		$reply = new \Hubzero\Item\Comment($database);

		$comments1 = $reply->find(array(
			'parent'    => $reviewid,
			'item_type' => 'pubreview',
			'item_id'   => $publication->get('id')
		));
		if (count($comments1) > 0)
		{
			foreach ($comments1 as $comment1)
			{
				$reply->setState($comment1->id, 2);
			}
		}

		// Recalculate the average rating for the parent publication
		$publication->table()->calculateRating();
		$publication->table()->updateRating();

		App::redirect(
			Route::url($publication->link('reviews')),
			Lang::txt('PLG_PUBLICATIONS_REVIEWS_REVIEW_DELETED')
		);
		return;
	}
}
