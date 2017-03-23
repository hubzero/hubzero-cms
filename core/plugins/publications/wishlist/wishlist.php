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
			if ($publication->title && $publication->state == 1)
			{
				$rtitle = isset($publication->alias) && $publication->alias
					? Lang::txt('COM_WISHLIST_NAME_RESOURCE') . ' ' . $publication->alias
					: Lang::txt('COM_WISHLIST_NAME_PUB_ID') . ' ' . $publication->id;

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
			$html = '<p class="error">' . Lang::txt('COM_WISHLIST_ERROR_LIST_NOT_FOUND') . '</p>';
		}
		else
		{
			// Get the component parameters
			$this->config = Component::params('com_wishlist');

			// Get list owners
			$owners = $wishlist->getOwners();

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
					->set('rows', $rows)
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
