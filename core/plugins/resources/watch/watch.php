<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Display Watch feature on resources page
 */
class plgResourcesWatch extends \Hubzero\Plugin\Plugin
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
	 * @param   object  $publication  Current publication
	 * @return  array
	 */
	public function &onResourcesSubAreas($publication)
	{
		$areas = array(
			'watch' => Lang::txt('PLG_RESOURCES_WATCH')
		);

		return $areas;
	}

	/**
	 * Return data on a resource sub view (this will be some form of HTML)
	 *
	 * @param   object   $resource  Current resource
	 * @param   string   $option    Name of the component
	 * @param   integer  $miniview  View style
	 * @return  array
	 */
	public function onResourcesSub($resource, $option, $miniview=0)
	{
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Only show for logged-in users
		if (User::isGuest())
		{
			return;
		}

		$this->resource = $resource;
		$this->action   = strtolower(Request::getWord('action', ''));
		$this->link     = $resource->link();

		switch ($this->action)
		{
			case 'subscribe':
			case 'unsubscribe':
				$arr['html'] = $this->subscribeAction();
			break;

			default:
				$arr['html'] = $this->statusAction();
			break;
		}

		return $arr;
	}

	/**
	 * Show subscription status
	 *
	 * @return  string  HTML
	 */
	protected function statusAction()
	{
		// Instantiate a view
		$view = $this->view('default', 'index')
			->set('resource', $this->resource)
			->set('link', $this->link)
			->set('watched', \Hubzero\Activity\Subscription::oneByScope(
				$this->resource->id,
				'resource',
				User::get('id')
			));

		// Return the output
		return $view->loadTemplate();
	}

	/**
	 * Subscribe
	 *
	 * @return  string  HTML
	 */
	protected function subscribeAction()
	{
		// Login required
		if (User::isGuest() || !$this->resource->id)
		{
			App::redirect(
				Route::url($this->link)
			);
		}

		// Load a subscription, if it exists
		$watch = \Hubzero\Activity\Subscription::oneByScope(
			$this->resource->id,
			'resource',
			User::get('id')
		);

		// Unsubscribing
		if ($this->action == 'unsubscribe')
		{
			$msg = Lang::txt('PLG_RESOURCES_WATCH_SUCCESS_UNSUBSCRIBED');

			if ($watch->get('id'))
			{
				if (!$watch->destroy())
				{
					$this->setError($watch->getError());
				}
			}
		}

		// Subscribing
		if ($this->action == 'subscribe')
		{
			$msg = Lang::txt('PLG_RESOURCES_WATCH_SUCCESS_SUBSCRIBED');

			if (!$watch->get('id'))
			{
				$watch->set('scope_id', $this->resource->id);
				$watch->set('scope', 'resource');
				$watch->set('user_id', User::get('id'));

				if (!$watch->save())
				{
					$this->setError($watch->getError());
				}
			}
		}

		$url = Route::url($this->resource->link());

		// Log the activity
		Event::trigger('system.logActivity', [
			'activity' => [
				'action'      => $this->action . 'd',
				'scope'       => 'resource',
				'scope_id'    => $this->resource->id,
				'description' => Lang::txt('PLG_RESOURCES_WATCH_' . strtoupper($this->action) . 'D', '<a href="' . $url . '">' . $this->resource->title . '</a>'),
				'details'     => array(
					'title' => $this->resource->title,
					'url'   => $url
				)
			],
			'recipients' => [
				User::get('id')
			]
		]);

		// Redirect
		App::redirect(
			Route::url($this->link),
			($this->getError() ? $this->getError() : $msg),
			($this->getError() ? 'error' : null)
		);
	}
}
