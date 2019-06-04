<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

require_once Component::path('com_publications') . DS . 'tables' . DS . 'review.php';

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
	 * Return the alias and name for this category of content
	 *
	 * @param   object   $model     Current publication
	 * @param   string   $version   Version name
	 * @param   boolean  $extended  Whether or not to show panel
	 * @return  array
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
	 * @param   object   $model     Current publication
	 * @param   string   $option    Name of the component
	 * @param   array    $areas     Active area(s)
	 * @param   string   $rtrn      Data to be returned
	 * @param   string   $version   Version name
	 * @param   boolean  $extended  Whether or not to show panel
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
				$rtrn = Request::getString('REQUEST_URI', Route::url($model->link($this->_name)), 'server');

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

			$infolink = '/kb/points/';
			$banking = Component::params('com_members')->get('bankAccounts');

			// Instantiate a view
			$view = $this->view('default', 'browse')
				->set('option', $option)
				->set('publication', $model)
				->set('reviews', $reviews)
				->set('voting', $this->params->get('voting', 1))
				->set('h', $h)
				->set('banking', $banking)
				->set('infolink', $infolink)
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
	 * @param   integer  $id        Item id
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
					$comment->set('reports', self::getAbuseReports($comment->get('id'), 'pubreview'));
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
		include_once Component::path('com_support') . DS . 'models' . DS . 'report.php';

		return \Components\Support\Models\Report::all()
			->whereEquals('referenceid', $item)
			->whereEquals('category', $category)
			->total();
	}
}
