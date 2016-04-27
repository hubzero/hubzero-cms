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
 * Publications Plugin class for wishes
 */
class plgPublicationsWishlist extends \Hubzero\Plugin\Plugin
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
	 * @param   object   $publication  Current publication
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function &onPublicationAreas($publication, $version = 'default', $extended = true)
	{
		$areas = array();

		if ($publication->_category->_params->get('plg_wishlist') && $extended)
		{
			$areas['wishlist'] = Lang::txt('Wishlist');
		}

		return $areas;
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
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onPublicationAreas($publication))
			 && !array_intersect($areas, array_keys($this->onPublicationAreas($publication))))
			{
				$rtrn = 'metadata';
			}
		}
		if (!$publication->_category->_params->get('plg_wishlist') || !$extended)
		{
			return $arr;
		}

		$database = App::get('db');

		// Load component language file
		Lang::load('com_wishlist') || Lang::load('com_wishlist', PATH_CORE . DS . 'components' . DS . 'com_wishlist' . DS . 'site');

		$option = 'com_wishlist';
		$cat    = 'publication';
		$refid  = $publication->id;
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
		$obj = new \Components\Wishlist\Tables\Wishlist($database);
		$objWish = new \Components\Wishlist\Tables\Wish($database);
		$objOwner = new \Components\Wishlist\Tables\Owner($database);

		// Get wishlist id
		$id = $obj->get_wishlistID($refid, $cat);

		// Create a new list if necessary
		if (!$id)
		{
			if ($publication->title && $publication->state == 1)
			{
				$rtitle = isset($publication->alias) && $publication->alias
					? Lang::txt('COM_WISHLIST_NAME_RESOURCE') . ' ' . $publication->alias
					: Lang::txt('COM_WISHLIST_NAME_PUB_ID') . ' ' . $publication->id;
				$id = $obj->createlist($cat, $refid, 1, $rtitle, $publication->title);
			}
		}

		// get wishlist data
		$wishlist = $obj->get_wishlist($id, $refid, $cat);

		if (!$wishlist)
		{
			$html = '<p class="error">' . Lang::txt('COM_WISHLIST_ERROR_LIST_NOT_FOUND') . '</p>';
		}
		else
		{
			// Get the component parameters
			$this->config = Component::params('com_wishlist');

			// Get list owners
			$owners = $objOwner->get_owners($id, $this->config->get('group') , $wishlist);

			// Authorize admins & list owners
			if (!User::isGuest())
			{
				if (User::authorise($option, 'manage'))
				{
					$admin = 1;
				}
				if (in_array(User::get('id'), $owners['individuals']))
				{
					$admin = 2;
				}
				elseif (in_array(User::get('id'), $owners['advisory']))
				{
					$admin = 3;
				}
			}
			elseif (!$wishlist->public && $rtrn != 'metadata')
			{
				// not authorized
				App::abort(403, Lang::txt('COM_WISHLIST_ERROR_ALERTNOTAUTH'));
			}

			$items = $objWish->get_count($id, $filters, $admin);

			if ($rtrn != 'metadata')
			{
				// Get wishes
				$wishlist->items = $objWish->get_wishes($wishlist->id, $filters, $admin, User::getInstance());

				$title = ($admin) ?  Lang::txt('COM_WISHLIST_TITLE_PRIORITIZED') : Lang::txt('COM_WISHLIST_TITLE_RECENT_WISHES');
				if (count($wishlist->items) > 0 && $items > $filters['limit'])
				{
					$title.= ' (<a href="' . Route::url('index.php?option=' . $option . '&task=wishlist&category=' . $wishlist->category . '&rid=' . $wishlist->referenceid) . '">' . Lang::txt('view all') . ' ' . $items . '</a>)';
				}
				else
				{
					$title .= ' (' . $items . ')';
				}

				// HTML output
				// Instantiate a view
				$view = $this->view('default', 'browse')
					->set('option', $option)
					->set('publication', $publication)
					->set('title', $title)
					->set('wishlist', $wishlist)
					->set('filters', $filters)
					->set('admin', $admin)
					->set('config', $this->config);

				if ($this->getError())
				{
					$view->setError($this->getError());
				}

				// Return the output
				$html = $view->loadTemplate();
			}
		}

		// Build the HTML meant for the "about" tab's metadata overview
		$metadata = '';

		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$view = $this->view('default', 'metadata')
				->set('publication', $publication)
				->set('items', $items)
				->set('wishlistid', $id);

			$metadata = $view->loadTemplate();
		}

		$arr = array(
			'html'     => $html,
			'metadata' => $metadata
		);

		if ($publication->state == 1)
		{
			$arr['count'] = $items;
			$arr['name']  = 'wishlist';
		}

		return $arr;
	}
}
