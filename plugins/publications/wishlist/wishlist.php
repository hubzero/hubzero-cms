<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Publications Plugin class for wishes
 */
class plgPublicationsWishlist extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $publication 	Current publication
	 * @param      string $version 		Version name
	 * @param      boolean $extended 	Whether or not to show panel
	 * @return     array
	 */
	public function &onPublicationAreas($publication, $version = 'default', $extended = true)
	{
		$areas = array();

		if ($publication->_category->_params->get('plg_wishlist') && $extended)
		{
			$areas['wishlist'] = JText::_('Wishlist');
		}

		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param      object  	$publication 	Current publication
	 * @param      string  	$option    		Name of the component
	 * @param      array   	$areas     		Active area(s)
	 * @param      string  	$rtrn      		Data to be returned
	 * @param      string 	$version 		Version name
	 * @param      boolean 	$extended 		Whether or not to show panel
	 * @return     array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect( $areas, $this->onPublicationAreas( $publication ) )
			&& !array_intersect( $areas, array_keys( $this->onPublicationAreas( $publication ) ) ))
			{
				$rtrn = 'metadata';
			}
		}
		if (!$publication->_category->_params->get('plg_wishlist') || !$extended)
		{
			return $arr;
		}

		$database = JFactory::getDBO();
		$juser    = JFactory::getUser();

		$option = 'com_wishlist';
		$cat    = 'publication';
		$refid  = $publication->id;
		$items  = 0;
		$admin  = 0;
		$html   = '';

		// Include some classes & scripts
		require_once(JPATH_ROOT . DS . 'components' . DS . $option . DS . 'models' . DS . 'wishlist.php');
		require_once(JPATH_ROOT . DS . 'components' . DS . $option . DS . 'controllers' . DS . 'wishlist.php');

		$lang = JFactory::getLanguage();
		$lang->load('com_wishlist');

		// Configure controller
		$controller = new WishlistControllerWishlist();

		// Get filters
		$filters = $controller->getFilters(0);
		$filters['limit'] = $this->params->get('limit');

		// Load some objects
		$obj = new Wishlist($database);
		$objWish = new Wish($database);
		$objOwner = new WishlistOwner($database);

		// Get wishlist id
		$id = $obj->get_wishlistID($refid, $cat);

		// Create a new list if necessary
		if (!$id)
		{
			if ($publication->title && $publication->state == 1)
			{
				$rtitle = isset($publication->alias) && $publication->alias
				? JText::_('COM_WISHLIST_NAME_RESOURCE') . ' ' . $publication->alias
				: JText::_('COM_WISHLIST_NAME_PUB_ID') . ' ' . $publication->id;
				$id = $obj->createlist($cat, $refid, 1, $rtitle, $publication->title);
			}
		}

		// get wishlist data
		$wishlist = $obj->get_wishlist($id, $refid, $cat);

		if (!$wishlist)
		{
			$html = '<p class="error">' . JText::_('COM_WISHLIST_ERROR_LIST_NOT_FOUND') . '</p>';
		}
		else
		{
			// Get the component parameters
			$this->config = JComponentHelper::getParams('com_wishlist');

			// Get list owners
			$owners = $objOwner->get_owners($id, $this->config->get('group') , $wishlist);

			// Authorize admins & list owners
			if (!$juser->get('guest'))
			{
				if ($juser->authorize($option, 'manage'))
				{
					$admin = 1;
				}
				if (in_array($juser->get('id'), $owners['individuals']))
				{
					$admin = 2;
				}
				elseif (in_array($juser->get('id'), $owners['advisory']))
				{
					$admin = 3;
				}
			}
			elseif (!$wishlist->public && $rtrn != 'metadata')
			{
				// not authorized
				JError::raiseError(403, JText::_('COM_WISHLIST_ERROR_ALERTNOTAUTH'));
				return;
			}

			$items = $objWish->get_count($id, $filters, $admin);

			if ($rtrn != 'metadata')
			{
				// Get wishes
				$wishlist->items = $objWish->get_wishes($wishlist->id, $filters, $admin, $juser);

				$title = ($admin) ?  JText::_('COM_WISHLIST_TITLE_PRIORITIZED') : JText::_('COM_WISHLIST_TITLE_RECENT_WISHES');
				if (count($wishlist->items) > 0 && $items > $filters['limit'])
				{
					$title.= ' (<a href="' . JRoute::_('index.php?option=' . $option . '&task=wishlist&category=' . $wishlist->category.'&rid='.$wishlist->referenceid) . '">' . JText::_('view all') . ' ' . $items . '</a>)';
				}
				else
				{
					$title .= ' (' . $items . ')';
				}

				// HTML output
				// Instantiate a view
				$view = new \Hubzero\Plugin\View(
					array(
						'folder'  => 'publications',
						'element' => 'wishlist',
						'name'    => 'browse'
					)
				);

				// Pass the view some info
				$view->option      = $option;
				$view->publication = $publication;
				$view->title       = $title;
				$view->wishlist    = $wishlist;
				$view->filters     = $filters;
				$view->admin       = $admin;
				$view->config      = $this->config;
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
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => 'publications',
					'element' => 'wishlist',
					'name'    => 'metadata'
				)
			);
			$view->publication = $publication;
			$view->items       = $items;
			$view->wishlistid  = $id;

			$metadata = $view->loadTemplate();
		}

		$arr = array(
			'html'=>$html,
			'metadata'=>$metadata
		);

		if ($publication->state == 1)
		{
			$arr['count'] = $items;
			$arr['name']  = 'wishlist';
		}

		return $arr;
	}
}