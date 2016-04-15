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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * @param   object  $resource  Current resource
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
		$this->link     = 'index.php?option=com_resources&' . ($this->resource->alias ? 'alias=' . $this->resource->alias : 'id=' . $this->resource->id);

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

		$url = Route::url('index.php?option=com_resources&' . ($this->resource->alias ? 'alias=' . $this->resource->alias : 'id=' . $this->resource->id));

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