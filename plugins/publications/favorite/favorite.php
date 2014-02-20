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

jimport('joomla.plugin.plugin');

/**
 * Publication Plugin class for favoriting a publication
 */
class plgPublicationsFavorite extends JPlugin
{
	/**
	 * Constructor
	 * 
	 * @param      object &$subject Event observer
	 * @param      array  $config   Optional config values
	 * @return     void
	 */
	public function __construct(&$subject, $config)
	{
		parent::__construct($subject, $config);

		$this->loadLanguage();
	}

	/**
	 * Return the alias and name for this category of content
	 * 
	 * @param      object $publication 	Current publication
	 * @param      string $version 		Version name
	 * @param      boolean $extended 	Whether or not to show panel
	 * @return     array
	 */	
	public function &onPublicationAreas( $publication, $version = 'default', $extended = true)
	{
		$areas = array();
		return $areas;
	}
	
	/**
	 * Return data on a resource view (this will be some form of HTML)
	 * 
	 * @param      object  $publication 	Current publication
	 * @param      string  $option    		Name of the component
	 * @param      array   $areas     		Active area(s)
	 * @param      string  $rtrn      		Data to be returned
	 * @param      string  $version 		Version name
	 * @param      boolean $extended 		Whether or not to show panel
	 * @return     array
	 */	
	public function onPublication( $publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true  )
	{
		$arr = array(
			'html'=>'',
			'metadata'=>''
		);
				
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) 
		{
			if (!array_intersect( $areas, $this->onPublicationAreas( $publication ) ) 
			&& !array_intersect( $areas, array_keys( $this->onPublicationAreas( $publication ) ) )) 
			{
				if ($publication->_category->_params->get('plg_favorite')) 
				{
					$rtrn == 'metadata';
				}
				else
				{
					return $arr;
				}
			}
		}
				
		// Only applicable to latest published version
		if (!$extended) 
		{
			return $arr;
		}
		
		// Incoming action
		$action = JRequest::getVar('action', '');
		if ($action && $action == 'favorite') 
		{
			// Check the user's logged-in status
			$this->fav($publication->id);
		}

		// Build the HTML meant for the "about" tab's metadata overview
		$juser = JFactory::getUser();
		if (!$juser->get('guest')) 
		{
			if ($rtrn == 'all' || $rtrn == 'metadata') 
			{
				// Push some scripts to the template
				\Hubzero\Document\Assets::addPluginScript('publications', 'favorite');

				$database = JFactory::getDBO();

				$fav = new \Hubzero\Item\Favorite($database);
				$fav->loadFavorite($juser->get('id'), $publication->id, 'publications');
				if (!$fav->id) 
				{
					$txt = JText::_('PLG_PUBLICATION_FAVORITES_FAVORITE_THIS');
					$cls = '';
				} 
				else 
				{
					$txt = JText::_('PLG_PUBLICATION_FAVORITES_UNFAVORITE_THIS');
					$cls = 'faved';
				}

				$view = new \Hubzero\Plugin\View(
					array(
						'folder'  => 'publications',
						'element' => 'favorite',
						'name'    => 'metadata'
					)
				);
				$view->cls = $cls;
				$view->txt = $txt;
				$view->option = $option;
				$view->publication = $publication;
				$arr['metadata'] = $view->loadTemplate();
			}
		}

		return $arr;
	}

	/**
	 * Set an item's favorite status
	 * 
	 * @param      string $option Component name
	 * @return     void
	 */
	public function onPublicationsFavorite($option)
	{
		$rid = JRequest::getInt('rid', 0);

		$arr = array('html' => '');
		if ($rid) 
		{
			$arr['html'] = $this->fav($rid);
		}
		return $arr;
	}

	/**
	 * Un/favorite an item
	 * 
	 * @param      integer $oid Publication id to un/favorite
	 * @return     void
	 */
	public function fav($oid)
	{
		$juser = JFactory::getUser();
		if (!$juser->get('guest')) 
		{
			$database = JFactory::getDBO();

			$fav = new \Hubzero\Item\Favorite($database);
			$fav->loadFavorite($juser->get('id'), $oid, 'publications');

			if (!$fav->id) 
			{
				$fav->uid = $juser->get('id');
				$fav->oid = $oid;
				$fav->tbl = 'publications';
				$fav->faved = JFactory::getDate()->toSql();
				$fav->check();
				$fav->store();

				return JText::_('PLG_PUBLICATION_FAVORITES_UNFAVORITE_THIS');
			} 
			else 
			{
				$fav->delete();

				return JText::_('PLG_PUBLICATION_FAVORITES_FAVORITE_THIS');
			}
		}
	}
}
