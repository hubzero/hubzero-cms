<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

		include_once __DIR__ . DS . 'helper.php';
	}

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object  $model  Current model
	 * @return  array
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
	 * @param   string  $option  Name of the component
	 * @return  array
	 */
	public function onResourcesRateItem($option)
	{
		$id = Request::getInt('rid', 0);

		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		$resource = \Components\Resources\Models\Entry::oneOrFail($id);

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
	 * @param   object  $model   Current model
	 * @param   string  $option  Name of the component
	 * @param   array   $areas   Active area(s)
	 * @param   string  $rtrn    Data to be returned
	 * @return  array
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

		include_once __DIR__ . DS . 'models' . DS . 'review.php';

		$authors = array();
		foreach ($model->contributors() as $con)
		{
			$authors[] = $con->authorid;
		}
		$isAuthor = (in_array(User::get('id'), $authors));

		// Instantiate a helper object and perform any needed actions
		$h = new PlgResourcesReviewsHelper();
		$h->resource = $model;
		$h->isAuthor = $isAuthor;
		$h->option   = $option;
		$h->_option  = $option;
		$h->execute();

		// Get reviews for this resource
		$reviews = \Components\Resources\Reviews\Models\Review::all()
			->whereEquals('resource_id', $model->get('id'))
			->whereIn('state', array(
				\Components\Resources\Reviews\Models\Review::STATE_PUBLISHED,
				\Components\Resources\Reviews\Models\Review::STATE_FLAGGED
			))
			->ordered()
			->rows();

		// Are we returning any HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			// Did they perform an action?
			// If so, they need to be logged in first.
			if (!$h->loggedin)
			{
				// Instantiate a view
				$rtrn = Request::getString('REQUEST_URI', Route::url($model->link(), false, true), 'server');

				App::redirect(
					Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn))
				);
			}

			$this->infolink = '/kb/points/';
			$this->banking  = Component::params('com_members')->get('bankAccounts');

			// Instantiate a view
			$view = $this->view('default', 'browse')
				->set('voting', $this->params->get('voting', 1))
				->set('option', $option)
				->set('resource', $model)
				->set('reviews', $reviews)
				->set('banking', $this->banking)
				->set('infolink', $this->infolink)
				->set('config', $this->params)
				->set('h', $h)
				->set('isAuthor', $isAuthor)
				->setErrors($h->getErrors());

			// Return the output
			$arr['html'] = $view->loadTemplate();
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$url  = $model->link() . '&active=' . $this->_name;

			$view = $this->view('default', 'metadata')
				->set('reviews', $reviews)
				->set('isAuthor', $isAuthor)
				->set('url', Route::url($url))
				->set('url2', Route::url($url . '&action=addreview#reviewform'));

			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
	}
}
