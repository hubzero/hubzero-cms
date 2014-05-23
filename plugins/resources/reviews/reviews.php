<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Resources Plugin class for review
 */
class plgResourcesReviews extends JPlugin
{
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

		$this->loadLanguage();

		$this->infolink = '/kb/points/';
		$upconfig = JComponentHelper::getParams('com_members');
		$this->banking = $upconfig->get('bankAccounts');
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesAreas($model)
	{
		if ($model->type->params->get('plg_reviews')) 
		{
			$areas = array(
				'reviews' => JText::_('PLG_RESOURCES_REVIEWS')
			);
		} 
		else 
		{
			$areas = array();
		}
		return $areas;
	}

	/**
	 * Rate a resource
	 * 
	 * @param      string $option Name of the component
	 * @return     array
	 */
	public function onResourcesRateItem($option)
	{
		$id = JRequest::getInt('rid', 0);

		$arr = array(
			'area' => 'reviews',
			'html' => '',
			'metadata' => ''
		);

		$database = JFactory::getDBO();
		$resource = new ResourcesResource($database);
		$resource->load($id);

		$h = new PlgResourcesReviewsHelper();
		$h->resource = $resource;
		$h->option   = $option;
		$h->_option  = $option;
		$h->execute();

		return $arr;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 * 
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		$arr = array(
			'area' => 'reviews',
			'html' => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model)))) 
			{
				$rtrn = 'metadata';
			}
		}
		if (!$model->type->params->get('plg_reviews')) 
		{
			return $arr;
		}

		$ar = $this->onResourcesAreas($model);
		if (empty($ar)) 
		{
			$rtrn = '';
		}

		include_once(__DIR__ . '/models/review.php');

		// Instantiate a helper object and perform any needed actions
		$h = new PlgResourcesReviewsHelper();
		$h->resource = $model->resource;
		$h->option   = $option;
		$h->_option  = $option;
		$h->execute();

		// Get reviews for this resource
		$database = JFactory::getDBO();
		$r = new ResourcesReview($database);
		$reviews = $r->getRatings($model->resource->id);
		if (!$reviews)
		{
			$reviews = array();
		}

		// Are we returning any HTML?
		if ($rtrn == 'all' || $rtrn == 'html') 
		{
			\Hubzero\Document\Assets::addPluginStylesheet('resources', 'reviews');
			\Hubzero\Document\Assets::addPluginScript('resources', 'reviews');

			// Did they perform an action?
			// If so, they need to be logged in first.
			if (!$h->loggedin) 
			{
				// Instantiate a view
				$rtrn = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $option . '&id=' . $model->resource->id . '&active=reviews', false, true), 'server');
				//$this->_redirect = JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn));
				JFactory::getApplication()->redirect(JRoute::_('index.php?option=com_login&return=' . base64_encode($rtrn)));
				return;
			} 

			// Instantiate a view
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'resources',
					'element' => 'reviews',
					'name'    => 'browse'
				)
			);

			// Thumbs voting CSS & JS
			$view->voting = $this->params->get('voting', 1);

			// Pass the view some info
			$view->option   = $option;
			$view->resource = $model->resource;
			$view->reviews  = $reviews;
			//$view->voting = $voting;
			$view->h = $h;
			$view->banking  = $this->banking;
			$view->infolink = $this->infolink;
			$view->config   = $this->params;
			if ($h->getError()) 
			{
				foreach ($h->getErrors() as $error)
				{
					$view->setError($error);
				}
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata') 
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'=>'resources',
					'element'=>'reviews',
					'name'=>'metadata'
				)
			);
			if ($model->resource->alias) 
			{
				$url = JRoute::_('index.php?option=' . $option . '&alias=' . $model->resource->alias . '&active=reviews');
				$url2 = JRoute::_('index.php?option=' . $option . '&alias=' . $model->resource->alias . '&active=reviews&action=addreview#reviewform');
			} 
			else 
			{
				$url = JRoute::_('index.php?option=' . $option . '&id=' . $model->resource->id . '&active=reviews');
				$url2 = JRoute::_('index.php?option=' . $option . '&id=' . $model->resource->id . '&active=reviews&action=addreview#reviewform');
			}

			$view->reviews = $reviews;
			$view->url = $url;
			$view->url2 = $url2;

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
	public static function getComments($id, $item, $category, $level, $abuse=false)
	{
		$database = JFactory::getDBO();

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
				$comment->replies = self::getComments($id, $comment, 'review', $level, $abuse);
				if ($abuse) 
				{
					$comment->abuse_reports = self::getAbuseReports($comment->id, 'review');
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
	public static function getAbuseReports($item, $category)
	{
		$database = JFactory::getDBO();

		$ra = new ReportAbuse($database);
		return $ra->getCount(array('id' => $item, 'category' => $category));
	}
}

/**
 * Helper class for reviews
 */
class PlgResourcesReviewsHelper extends JObject
{
	/**
	 * Container for data
	 * 
	 * @var array
	 */
	private $_data  = array();

	/**
	 * Set a property
	 * 
	 * @param      string $property Property name
	 * @param      mixed  $value    Property value
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Get a property
	 * 
	 * @param      unknown $property Property to set
	 * @return     mixed
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) 
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Redirect page
	 * 
	 * @return     void
	 */
	public function redirect()
	{
		if ($this->_redirect != NULL) 
		{
			$app = JFactory::getApplication();
			$app->redirect($this->_redirect, $this->_message, $this->_messageType);
		}
	}

	/**
	 * Execute an action
	 * 
	 * @return     void
	 */
	public function execute()
	{
		// Incoming action
		$action = JRequest::getVar('action', '');

		$this->loggedin = true;

		if ($action) 
		{
			// Check the user's logged-in status
			$juser = JFactory::getUser();
			if ($juser->get('guest')) 
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
		JRequest::checkToken() or jexit('Invalid Token');

		$juser = JFactory::getUser();

		// Is the user logged in?
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_LOGIN_NOTICE'));
			return;
		}

		// Incoming
		$id = JRequest::getInt('id', 0);

		// Trim and addslashes all posted items
		$comment = JRequest::getVar('comment', array(), 'post', 'none', 2);

		if (!$id) 
		{
			// Cannot proceed
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_COMMENT_ERROR_NO_REFERENCE_ID'));
			return;
		}

		$database = JFactory::getDBO();

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
		$row->created    = ($row->id ? $row->created : JFactory::getDate()->toSql());
		$row->state      = ($row->id ? $row->state : 0);
		$row->created_by = ($row->id ? $row->created_by : $juser->get('id'));

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
		$database = JFactory::getDBO();
		$resource =& $this->resource;

		// Incoming
		$replyid = JRequest::getInt('refid', 0);

		// Do we have a review ID?
		if (!$replyid) 
		{
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_COMMENT_ERROR_NO_REFERENCE_ID'));
			return;
		}

		// Do we have a resource ID?
		if (!$resource->id) 
		{
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID'));
			return;
		}

		// Delete the review
		$reply = new \Hubzero\Item\Comment($database);

		$comments = $reply->find(array('parent'=>$replyid, 'item_type'=>'review', 'item_id' => $resource->id));
		if (count($comments) > 0) 
		{
			foreach ($comments as $comment)
			{
				$reply->delete($comment->id);
			}
		}
		$reply->delete($replyid);
	}

	/**
	 * Rate an item
	 * 
	 * @return     void
	 */
	public function rateitem()
	{
		$database = JFactory::getDBO();
		$juser = JFactory::getUser();

		$id   = JRequest::getInt('refid', 0);
		$ajax = JRequest::getInt('no_html', 0);
		$cat  = JRequest::getVar('category', 'review');
		$vote = JRequest::getVar('vote', '');
		$ip   = JRequest::ip();
		$rid  = JRequest::getInt('id', 0);

		if (!$id) 
		{
			// Cannot proceed
			return;
		}

		// Is the user logged in?
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_PLEASE_LOGIN_TO_VOTE'));
			return;
		}

		// Load answer
		$rev = new ResourcesReview($database);
		$rev->load($id);
		$voted = $rev->getVote($id, $cat, $juser->get('id'));

		if (!$voted && $vote) // && $rev->user_id != $juser->get('id')) 
		{
			require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_answers' . DS . 'tables' . DS . 'vote.php');
			$v = new Vote($database);
			$v->referenceid = $id;
			$v->category    = $cat;
			$v->voter       = $juser->get('id');
			$v->ip          = $ip;
			$v->voted       = JFactory::getDate()->toSql();
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
			$response = $rev->getRating($id, $juser->get('id'));

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
		else 
		{
			$this->_redirect = JRoute::_('index.php?option=' . $this->_option . '&id=' . $rid . '&active=reviews');
		}
	}

	/**
	 * Edit a review
	 * 
	 * @return     void
	 */
	public function editreview()
	{
		// Is the user logged in?
		$juser = JFactory::getUser();
		if ($juser->get('guest')) 
		{
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_LOGIN_NOTICE'));
			return;
		}

		$resource =& $this->resource;

		// Do we have an ID?
		if (!$resource->id) 
		{
			// No - fail! Can't do anything else without an ID
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID'));
			return;
		}

		// Incoming
		$myr = JRequest::getInt('myrating', 0);

		$database = JFactory::getDBO();

		$review = new ResourcesReview($database);
		$review->loadUserReview($resource->id, $juser->get('id'));
		if (!$review->id) {
			// New review, get the user's ID
			$review->user_id = $juser->get('id');
			$review->resource_id = $resource->id;
			$review->tags = '';
		} 
		else 
		{
			// Editing a review, do some prep work
			$review->comment = str_replace('<br />', '', $review->comment);

			$RE = new ResourcesHelper($resource->id, $database);
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
		$resource_id = JRequest::getInt('resource_id', 0);

		// Do we have a resource ID?
		if (!$resource_id) 
		{
			// No ID - fail! Can't do anything else without an ID
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID'));
			return;
		}

		$database = JFactory::getDBO();

		// Bind the form data to our object
		$row = new ResourcesReview($database);
		if (!$row->bind($_POST)) 
		{
			$this->setError($row->getError());
			return;
		}

		// Perform some text cleaning, etc.
		$row->id        = JRequest::getInt('reviewid', 0);
		$row->comment   = \Hubzero\Utility\Sanitize::clean($row->comment);
		//$row->comment   = nl2br($row->comment);
		$row->anonymous = ($row->anonymous == 1 || $row->anonymous == '1') ? $row->anonymous : 0;
		$row->created   = ($row->created) ? $row->created : JFactory::getDate()->toSql();

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
		$tags = trim(JRequest::getVar('review_tags', ''));
		if ($tags) 
		{
			$rt = new ResourcesTags($database);
			$rt->tag_object($row->user_id, $resource_id, $tags, 1, 0);
		}

		// Instantiate a helper object and get all the contributor IDs
		$helper = new ResourcesHelper($resource->id, $database);
		$helper->getContributorIDs();
		$users = $helper->contributorIDs;

		// Get the HUB configuration
		$jconfig = JFactory::getConfig();

		// Build the subject
		$subject = $jconfig->getValue('config.sitename') . ' ' . JText::_('PLG_RESOURCES_REVIEWS_CONTRIBUTIONS');

		// Message
		$juser = JFactory::getUser();
		$eview = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'resources',
				'element' => 'reviews',
				'name'    => 'emails'
			)
		);
		$eview->option   = $this->_option;
		$eview->juser    = $juser;
		$eview->resource = $resource;
		$eview->review   = $row;
		$message = $eview->loadTemplate();

		// Build the "from" data for the e-mail
		$from = array();
		$from['name']  = $jconfig->getValue('config.sitename') . ' ' . JText::_('PLG_RESOURCES_REVIEWS_CONTRIBUTIONS');
		$from['email'] = $jconfig->getValue('config.mailfrom');

		// Send message
		JPluginHelper::importPlugin('xmessage');
		$dispatcher = JDispatcher::getInstance();
		if (!$dispatcher->trigger('onSendMessage', array('resources_new_comment', $subject, $message, $from, $users, $this->_option))) 
		{
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_FAILED_TO_MESSAGE'));
		}
	}

	/**
	 * Delete a review
	 * 
	 * @return     void
	 */
	public function deletereview()
	{
		$database = JFactory::getDBO();
		$resource =& $this->resource;

		// Incoming
		$reviewid = JRequest::getInt('reviewid', 0);

		// Do we have a review ID?
		if (!$reviewid) 
		{
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_NO_ID'));
			return;
		}

		// Do we have a resource ID?
		if (!$resource->id) 
		{
			$this->setError(JText::_('PLG_RESOURCES_REVIEWS_NO_RESOURCE_ID'));
			return;
		}

		$review = new ResourcesReview($database);

		// Delete the review's comments
		$reply = new \Hubzero\Item\Comment($database);

		$comments1 = $reply->find(array('parent'=>$reviewid, 'item_type'=>'review', 'item_id' => $resource->id));
		if (count($comments1) > 0) 
		{
			foreach ($comments1 as $comment1)
			{
				$comments2 = $reply->find(array('parent'=>$comment1->id, 'item_type'=>'review', 'item_id' => $resource->id));
				if (count($comments2) > 0) 
				{
					foreach ($comments2 as $comment2)
					{
						$comments3 = $reply->find(array('parent'=>$comment2->id, 'item_type'=>'review', 'item_id' => $resource->id));
						if (count($comments3) > 0) 
						{
							foreach ($comments3 as $comment3)
							{
								$reply->delete($comment3->id);
							}
						}
						$reply->delete($comment2->id);
					}
				}
				$reply->delete($comment1->id);
			}
		}

		// Delete the review
		$review->delete($reviewid);

		// Recalculate the average rating for the parent resource
		$resource->calculateRating();
		$resource->updateRating();
	}
}

