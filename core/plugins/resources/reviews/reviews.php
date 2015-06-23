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
 * Resources Plugin class for review
 */
class plgResourcesReviews extends \Hubzero\Plugin\Plugin
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
		$this->banking  = Component::params('com_members')->get('bankAccounts');

		include_once(__DIR__ . DS . 'helper.php');
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesAreas($model)
	{
		$areas = array();

		if ($model->type->params->get('plg_reviews') && $model->access('view-all'))
		{
			$areas['reviews'] = Lang::txt('PLG_RESOURCES_REVIEWS');
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
		$id = Request::getInt('rid', 0);

		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		$database = JFactory::getDBO();
		$resource = new \Components\Resources\Tables\Resource($database);
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
			'area'     => $this->_name,
			'html'     => '',
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

		include_once(__DIR__ . DS . 'models' . DS . 'review.php');

		// Instantiate a helper object and perform any needed actions
		$h = new PlgResourcesReviewsHelper();
		$h->resource = $model->resource;
		$h->option   = $option;
		$h->_option  = $option;
		$h->execute();

		// Get reviews for this resource
		$database = JFactory::getDBO();
		$r = new \Components\Resources\Tables\Review($database);
		$reviews = $r->getRatings($model->resource->id);
		if (!$reviews)
		{
			$reviews = array();
		}

		// Are we returning any HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			// Did they perform an action?
			// If so, they need to be logged in first.
			if (!$h->loggedin)
			{
				// Instantiate a view
				$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $option . '&id=' . $model->resource->id . '&active=' . $this->_name, false, true), 'server');
				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
				);
				return;
			}

			// Instantiate a view
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
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
			$view = $this->view('default', 'metadata');

			$url  = 'index.php?option=' . $option . '&' . ($model->resource->alias ? 'alias=' . $model->resource->alias : 'id=' . $model->resource->id) . '&active=' . $this->_name;

			$view->reviews = $reviews;
			$view->url     = Route::url($url);
			$view->url2    = Route::url($url . '&action=addreview#reviewform');

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

		$ra = new \Components\Support\Tables\ReportAbuse($database);
		return $ra->getCount(array('id' => $item, 'category' => $category));
	}
}
