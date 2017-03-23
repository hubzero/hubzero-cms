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

/**
 * Resources Plugin class for wishlist items
 */
class plgResourcesWishlist extends \Hubzero\Plugin\Plugin
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
	public function &onResourcesAreas($model)
	{
		$areas = array();

		if ($model->type->params->get('plg_' . $this->_name) && $model->access('view-all'))
		{
			$areas['wishlist'] = Lang::txt('PLG_RESOURCES_WISHLIST');
		}

		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object  $resource  Current resource
	 * @param   string  $option    Name of the component
	 * @param   array   $areas     Active area(s)
	 * @param   string  $rtrn      Data to be returned
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
		if (!$model->type->params->get('plg_' . $this->_name))
		{
			return $arr;
		}

		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model))))
			{
				$rtrn = 'metadata';
			}
		}

		$this->config = Component::params('com_wishlist');

		Lang::load('com_wishlist', PATH_APP . DS . 'bootstrap' . DS . 'site') ||
		Lang::load('com_wishlist', Component::path('com_wishlist') . DS . 'site');

		$database = App::get('db');

		$option = 'com_wishlist';
		$cat    = 'resource';
		$refid  = $model->resource->id;
		$items  = 0;
		$admin  = 0;
		$html   = '';

		// Include some classes & scripts
		require_once Component::path($option) . DS . 'models' . DS . 'wishlist.php';
		require_once Component::path($option) . DS . 'site' . DS . 'controllers' . DS . 'wishlists.php';

		// Configure controller
		$controller = new \Components\Wishlist\Site\Controllers\Wishlists();

		// Get filters
		$filters = $controller->getFilters(0);
		$filters['limit'] = $this->params->get('limit');

		// Load some objects
		$wishlist = \Components\Wishlist\Models\Wishlist::oneByReference($refid, $cat);

		// Get wishlist id
		$id = $wishlist->get('id');

		// Create a new list if necessary
		if (!$id)
		{
			if ($model->resource->title
			 && $model->resource->standalone == 1
			 && $model->resource->published == 1)
			{
				$rtitle = ($model->istool()) ? Lang::txt('COM_WISHLIST_NAME_RESOURCE_TOOL') . ' ' . $model->resource->alias : Lang::txt('COM_WISHLIST_NAME_RESOURCE_ID') . ' ' . $model->resource->id;

				$wishlist->set('category', $cat);
				$wishlist->set('referenceid', $refid);
				$wishlist->set('public', 1);
				$wishlist->set('title', $rtitle);
				$wishlist->stage();

				$id = $wishlist->get('id');
			}
		}

		if (!$wishlist->get('id'))
		{
			$html = '<p class="error">' . Lang::txt('ERROR_WISHLIST_NOT_FOUND') . '</p>';
		}
		else
		{
			// Get list owners
			$owners = $wishlist->getOwners();

			// Authorize admins & list owners
			if (!User::isGuest())
			{
				if (User::authorise($option, 'manage'))
				{
					$admin = 1;
				}
				if (isset($owners['individuals']) && in_array(User::get('id'), $owners['individuals']))
				{
					$admin = 2;
				}
				else if (isset($owners['advisory']) && in_array(User::get('id'), $owners['advisory']))
				{
					$admin = 3;
				}
			}
			else if (!$wishlist->public && $rtrn != 'metadata')
			{
				// not authorized
				App::abort(403, Lang::txt('ALERTNOTAUTH'));
				return;
			}

			$entries = \Components\Wishlist\Models\Wish::all()
				->whereEquals('wishlist', $wishlist->get('id'));

			$w = $entries->getTableName();

			if ($filters['search'])
			{
				$entries
					->whereLike('subject', strtolower((string)$filters['search']), 1)
					->orWhereLike('about', strtolower((string)$filters['search']), 1)
					->resetDepth();
			}

			if ($filters['filterby'])
			{
				// list  filtering
				switch ($filters['filterby'])
				{
					case 'granted':
						$entries->whereEquals('status', 1);
						break;
					case 'open':
						$entries->whereEquals('status', 0);
						break;
					case 'accepted':
						$entries
							->whereIn('status', array(0, 6))
							->whereEquals('accepted', 1);
						break;
					case 'pending':
						$entries
							->whereEquals('accepted', 0)
							->whereEquals('status', 0);
						break;
					case 'rejected':
						$entries->whereEquals('status', 3);
						break;
					case 'withdrawn':
						$entries->whereEquals('status', 4);
						break;
					case 'deleted':
						$entries->whereEquals('status', 2);
						break;
					case 'useraccepted':
						$entries
							->whereEquals('accepted', 3)
							->where('status', '!=', 2);
						break;
					case 'private':
						$entries
							->whereEquals('private', 1)
							->where('status', '!=', 2);
						break;
					case 'public':
						$entries
							->whereEquals('private', 0)
							->where('status', '!=', 2);
						break;
					case 'assigned':
						$entries
							->where('status', '!=', 2)
							->whereRaw('assigned NOT NULL');
						break;
					case 'mine':
						$entries
							->where('status', '!=', 2)
							->whereEquals('assigned', User::get('id'));
					break;
					case 'submitter':
						$entries
							->where('status', '!=', 2)
							->whereEquals('proposed_by', User::get('id'));
						break;
					case 'all':
					default:
						$entries->where('status', '!=', 2);
						break;
				}
			}

			if (!$admin)
			{
				$entries->whereEquals('private', 0);
			}

			// If filtering by tags...
			if (isset($filters['tag']) && $filters['tag'])
			{
				$tags = $filters['tag'];
				if (is_string($tags))
				{
					$tags = trim($tags);
					$tags = preg_split("/(,|;)/", $tags);
				}

				foreach ($tags as $k => $tag)
				{
					$tags[$k] = strtolower(preg_replace("/[^a-zA-Z0-9]/", '', $tag));
				}

				$to = '#__tags_object';
				$t  = '#__tags';
				$entries
					->join($to, $to . '.objectid', $w . '.id', 'left')
					->join($t, $to . '.tagid', $t . '.id', 'left')
					->whereEquals($to . '.tbl', 'wishlist')
					->whereIn($t . '.tag', $tags, 1)
					->group($w . '.id');
			}

			// Get a total
			$items = $entries->copy()->total();

			if ($rtrn != 'metadata')
			{
				// Get wishes
				// Select vote totals
				$vote = \Components\Wishlist\Models\Vote::blank();

				$entries
					->select($entries->getTableName() . '.*')
					->select("(SELECT COUNT(*) FROM `" . $vote->getTableName() . "` AS v WHERE v.helpful='yes' AND v.category='wish' AND v.referenceid=" . $entries->getTableName() . ".id)", 'positive')
					->select("(SELECT COUNT(*) FROM `" . $vote->getTableName() . "` AS v WHERE v.helpful='no' AND v.category='wish' AND v.referenceid=" . $entries->getTableName() . ".id)", 'negative');

				// list sorting
				if ($filters['sortby'])
				{
					switch ($filters['sortby'])
					{
						case 'date':
							$entries
								->order('status', 'asc')
								->order('proposed', 'desc');
							break;
						case 'submitter':
							$u = User::getTableName(); //'#__users';
							$entries
								->select($u . '.name', 'authorname')
								->join($u, $u . '.id', $entries->getTableName() . '.proposed_by', 'left')
								->order($u . '.name', 'asc');
							break;
						case 'feedback':
							$entries
								->order('positive', 'desc')
								->order('status', 'asc');
							break;
						case 'ranking':
							$entries
								->order('status', 'asc')
								->order('ranking', 'desc')
								->order('positive', 'desc')
								->order('proposed', 'desc');
							break;
						case 'bonus':
							$entries
								->select("(SELECT SUM(amount) FROM `#__users_transactions` WHERE category='wish' AND type='hold' AND referenceid=" . $entries->getTableName() . ".id)", 'bonus')
								->order('status', 'asc')
								->order('bonus', 'desc')
								->order('positive', 'desc')
								->order('proposed', 'desc');
							break;
						case 'all':
						default:
							$entries
								->order('accepted', 'desc')
								->order('status', 'asc')
								->order('proposed', 'desc');
							break;
					}
				}

				$rows = $entries
					->limit($filters['limit'])
					->start($filters['start'])
					->rows();

				$title = ($admin) ?  Lang::txt('COM_WISHLIST_TITLE_PRIORITIZED') : Lang::txt('COM_WISHLIST_TITLE_RECENT_WISHES');
				if (count($wishlist->items) > 0 && $items > $filters['limit'])
				{
					$title.= ' (<a href="' . Route::url('index.php?option=' . $option . '&task=wishlist&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid) . '">' . Lang::txt('PLG_RESOURCES_WISHLIST_VIEW_ALL') . '</a>)';
				}
				else
				{
					$title .= ' (' . $items . ')';
				}

				// HTML output
				// Instantiate a view
				$view = $this->view('default', 'browse')
					->set('option', $option)
					->set('resource', $model->resource)
					->set('title', $title)
					->set('wishlist', $wishlist)
					->set('filters', $filters)
					->set('admin', $admin)
					->set('rows', $rows)
					->set('config', $this->config);

				foreach ($this->getErrors() as $error)
				{
					$view->setError($error);
				}

				// Return the output
				$arr['html'] = $view->loadTemplate();
			}
		}

		// Build the HTML meant for the "about" tab's metadata overview
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$view = $this->view('default', 'metadata')
				->set('resource', $model->resource)
				->set('items', $items)
				->set('wishlistid', $id);

			$arr['metadata'] = $view->loadTemplate();
		}

		return $arr;
	}
}
