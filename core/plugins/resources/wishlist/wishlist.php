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
		require_once(PATH_CORE . DS . 'components' . DS . $option . DS . 'models' . DS . 'wishlist.php');
		require_once(PATH_CORE . DS . 'components' . DS . $option . DS . 'site' . DS . 'controllers' . DS . 'wishlists.php');

		// Configure controller
		$controller = new \Components\Wishlist\Site\Controllers\Wishlists();

		// Get filters
		$filters = $controller->getFilters(0);
		$filters['limit'] = $this->params->get('limit');

		// Load some objects
		$obj      = new \Components\Wishlist\Tables\Wishlist($database);
		$objWish  = new \Components\Wishlist\Tables\Wish($database);
		$objOwner = new \Components\Wishlist\Tables\Owner($database);

		// Get wishlist id
		$id = $obj->get_wishlistID($refid, $cat);

		// Create a new list if necessary
		if (!$id)
		{
			if ($model->resource->title
			 && $model->resource->standalone == 1
			 && $model->resource->published == 1)
			{
				$rtitle = ($model->istool()) ? Lang::txt('COM_WISHLIST_NAME_RESOURCE_TOOL') . ' ' . $model->resource->alias : Lang::txt('COM_WISHLIST_NAME_RESOURCE_ID') . ' ' . $model->resource->id;
				$id = $obj->createlist($cat, $refid, 1, $rtitle, $model->resource->title);
			}
		}

		// get wishlist data
		$wishlist = $obj->get_wishlist($id, $refid, $cat);

		if (!$wishlist)
		{
			$html = '<p class="error">' . Lang::txt('ERROR_WISHLIST_NOT_FOUND') . '</p>';
		}
		else
		{
			// Get list owners
			$owners = $objOwner->get_owners($id, $this->config->get('group'), $wishlist);

			// Authorize admins & list owners
			if (!User::isGuest())
			{
				if (User::authorize($option, 'manage'))
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

			$items = $objWish->get_count($id, $filters, $admin);

			if ($rtrn != 'metadata')
			{
				// Get wishes
				$wishlist->items = $objWish->get_wishes($wishlist->id, $filters, $admin, User::getInstance());

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
