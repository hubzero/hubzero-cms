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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once(PATH_CORE . DS . 'components'.DS .'com_publications' . DS . 'tables' . DS . 'review.php');

/**
 * Publications Plugin class for reviews
 */
class plgPublicationsReviews extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Constructor
	 *
	 * @param   object  &$subject  Event observer
	 * @param   array   $config    Optional config values
	 * @return  void
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
	public function &onPublicationAreas($model, $version = 'default', $extended = true)
	{
		$areas = array();

		if ($model->_category->_params->get('plg_reviews') && $extended && $model->access('view-all'))
		{
			$areas['reviews'] = Lang::txt('PLG_PUBLICATIONS_REVIEWS');
		}

		return $areas;
	}

	/**
	 * Rate item (AJAX)
	 *
	 * @param   string  $option
	 * @return  array
	 */
	public function onPublicationRateItem($option)
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
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   array    $areas        Active area(s)
	 * @param   string   $rtrn         Data to be returned
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function onPublication($model, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onPublicationAreas($model))
			 && !array_intersect($areas, array_keys($this->onPublicationAreas($model))))
			{
				$rtrn = 'metadata';
			}
		}
		if (!$model->category()->_params->get('plg_reviews') || !$extended)
		{
			return $arr;
		}

		include_once __DIR__ . DS . 'models' . DS . 'review.php';
		include_once __DIR__ . DS . 'helper.php';

		// Instantiate a helper object and perform any needed actions
		$h = new PlgPublicationsReviewsHelper();
		$h->publication = $model;
		$h->option      = $option;
		$h->_option     = $option;
		$h->execute();

		// Get reviews for this publication
		$database = App::get('db');
		$r = new \Components\Publications\Tables\Review($database);

		$arr['count'] = $r->countRatings($model->get('id'));
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

			$reviews = $r->getRatings($model->get('id'));
			if (!$reviews)
			{
				$reviews = array();
			}

			// Instantiate a view
			$view = $this->view('default', 'browse')
				->set('option', $option)
				->set('publication', $model)
				->set('reviews', $reviews)
				->set('voting', $this->params->get('voting', 1))
				->set('h', $h)
				->set('banking', $this->banking)
				->set('infolink', $this->infolink)
				->set('config', $this->params);

			if ($h->getError())
			{
				$view->setError($h->getError());
			}

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$view = $this->view('default', 'metadata')
				->set('url', Route::url($model->link($this->_name)))
				->set('url2', Route::url($model->link($this->_name) . '&action=addreview#reviewform'))
				->set('reviews', $arr['count']);

			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Get all replies for an item
	 *
	 * @param   object   $item      Item to look for reports on
	 * @param   string   $category  Item type
	 * @param   integer  $level     Depth
	 * @param   boolean  $abuse     Abuse flag
	 * @return  array
	 */
	public function getComments($id, $item, $category, $level, $abuse=false)
	{
		$level++;

		$comments = \Hubzero\Item\Comment::all()
			->whereEquals('parent', ($level == 1 ? 0 : $item->id))
			->whereEquals('item_id', $id)
			->whereEquals('item_type', $category)
			->ordered()
			->rows();

		if ($comments)
		{
			foreach ($comments as $comment)
			{
				//$comment->replies = self::getComments($id, $comment, 'pubreview', $level, $abuse);

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
	 * @param   integer  $item      Item to look for reports on
	 * @param   string   $category  Item type
	 * @return  integer
	 */
	public function getAbuseReports($item, $category)
	{
		$database = App::get('db');

		$ra = new \Components\Support\Tables\ReportAbuse($database);
		return $ra->getCount(array('id' => $item, 'category' => $category));
	}
}
