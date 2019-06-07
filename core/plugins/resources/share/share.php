<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for showing social sharing options
 */
class plgResourcesShare extends \Hubzero\Plugin\Plugin
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
	 * @param   object  $model  Current model
	 * @return  array
	 */
	public function &onResourcesAreas($model)
	{
		static $area = array();

		if (!$model->type->params->get('plg_share'))
		{
			return $area;
		}

		return $area;
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
		if (!$model->type->params->get('plg_share'))
		{
			return;
		}

		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		$sef = Route::url($model->link());
		$url = Request::base() . ltrim($sef, '/');

		// Incoming action
		$sharewith = Request::getString('sharewith', '');

		if ($sharewith)
		{
			// Log the activity
			if (!User::isGuest())
			{
				Event::trigger('system.logActivity', [
					'activity' => [
						'action'      => 'shared',
						'scope'       => 'resource',
						'scope_id'    => $model->id,
						'description' => Lang::txt('PLG_RESOURCES_SHARE_ENTRY_SHARED', '<a href="' . $sef . '">' . $model->title . '</a>', $sharewith),
						'details'     => array(
							'with'  => $sharewith,
							'title' => $model->title,
							'url'   => $sef
						)
					],
					'recipients' => [
						['resource', $model->id], // The resource itself
						['user', $model->created_by], // The creator
						['user', User::get('id')] // The sharer
					]
				]);
			}

			// Email form
			if ($sharewith == 'email')
			{
				// Instantiate a view
				$view = $this->view('email', 'options')
					->set('option', $option)
					->set('resource', $model)
					->set('_params', $this->params)
					->set('url', $url)
					->setErrors($this->getErrors());

				// Return the output
				$view->display();
				exit();
			}

			return $this->share($sharewith, $url, $model);
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			// Instantiate a view
			$view = $this->view('default', 'options')
				->set('option', $option)
				->set('resource', $model)
				->set('_params', $this->params)
				->set('url', $url)
				->setErrors($this->getErrors());

			// Return the output
			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
	}

	/**
	 * Redirect to social sharer
	 *
	 * @param   string  $with      Social site to share with
	 * @param   string  $url       The URL to share
	 * @param   object  $resource  Resource to share
	 * @return  void
	 */
	public function share($with, $url, $resource)
	{
		$link = '';
		$description = \Hubzero\Utility\Str::truncate(stripslashes($resource->introtext), 250);
		$description = urlencode($description);
		$title = stripslashes($resource->title);
		$title = urlencode($title);

		switch ($with)
		{
			case 'facebook':
				$link = 'https://www.facebook.com/sharer/sharer.php?u=' . $url . '&t=' . $title;
				break;

			case 'twitter':
				$link = 'http://twitter.com/home?status=' . urlencode(Lang::txt('PLG_RESOURCES_SHARE_VIEWING', Config::get('sitename'), stripslashes($resource->title)) . ' ' . $url);
				break;

			case 'google':
				$link = 'https://plus.google.com/share?url=' . $url;
				break;

			case 'delicious':
				$link = 'http://del.icio.us/post?url=' . $url . '&title=' . $title;
				break;

			case 'reddit':
				$link = 'http://reddit.com/submit?url=' . $url . '&title=' . $title;
				break;

			case 'linkedin':
				$link = 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . $title . '&summary=' . $description;
				break;
		}

		if ($link)
		{
			App::redirect($link, '', '');
		}
	}
}
