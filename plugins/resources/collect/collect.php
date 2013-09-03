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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

/**
 * Resources Plugin class for favoriting a resource
 */
class plgResourcesCollect extends JPlugin
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
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function onResourcesAreas($model)
	{
		return array();
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 * 
	 * @param      object  $resource Current resource
	 * @param      string  $option    Name of the component
	 * @param      array   $areas     Active area(s)
	 * @param      string  $rtrn      Data to be returned
	 * @return     array
	 */
	public function onResources($model, $option, $areas, $rtrn='all')
	{
		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas)) 
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model)))) 
			{
				$rtrn = 'metadata';
			}
		}

		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Build the HTML meant for the "about" tab's metadata overview
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) 
		{
			if ($rtrn == 'all' || $rtrn == 'metadata') 
			{
				// Push some scripts to the template
				ximport('Hubzero_Document');
				Hubzero_Document::addPluginScript('resources', $this->_name);
				Hubzero_Document::addPluginStylesheet('resources', $this->_name);

				ximport('Hubzero_Plugin_View');
				$view = new Hubzero_Plugin_View(
					array(
						'folder'  => 'resources',
						'element' => $this->_name,
						'name'    => 'metadata'
					)
				);
				$view->option = $option;
				if (is_a($model, 'ResourcesResource'))
				{
					$view->resource = $model;
				}
				else
				{
					$view->resource = $model->resource;
				}
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
	public function onResourcesFavorite($option)
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'collections.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'item.php');
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'tables' . DS . 'post.php');

		$this->option = $option;
		$this->juser = JFactory::getUser();
		$this->database = JFactory::getDBO();

		$rid = JRequest::getInt('rid', 0);

		$this->resource = new ResourcesResource($this->database);
		$this->resource->load($rid);

		$arr = array('html' => '');
		if ($rid) 
		{
			$arr['html'] = $this->fav();
		}
		return $arr;
	}

	/**
	 * Un/favorite an item
	 * 
	 * @param      integer $oid Resource to un/favorite
	 * @return     void
	 */
	public function fav()
	{
		// Incoming
		$item_id       = JRequest::getInt('item', 0);
		$collection_id = JRequest::getInt('collection', 0);
		$collection_title = JRequest::getVar('collection_title', '');
		$no_html       = JRequest::getInt('no_html', 0);

		$model = new CollectionsModel('member', $this->juser->get('id'));
		//if (!$item_id && $collection_id)
		//{
			$b = new CollectionsTableItem($this->database);
			$b->loadType($this->resource->id, 'resource');
			if (!$b->id)
			{
				$row = new CollectionsTableCollection($this->database);
				$row->load($collection_id);

				$b->type        = 'resource';
				$b->object_id   = $this->resource->id;
				$b->title       = $this->resource->title;
				$b->description = $this->resource->introtext;
				$b->url         = JRoute::_('index.php?option=com_resources&id=' . $this->resource->id);
				if (!$b->check()) 
				{
					$this->setError($b->getError());
				}
				// Store new content
				if (!$b->store()) 
				{
					$this->setError($b->getError());
				}
				$collection_id = 0;
			}
			$item_id = $b->id;
		//}

		// No board ID selected so present repost form
		if (!$collection_id && !$collection_title)
		{
			ximport('Hubzero_Plugin_View');
			$view = new Hubzero_Plugin_View(
				array(
					'folder'  => 'resources',
					'element' => $this->_name,
					'name'    => 'metadata',
					'layout'  => 'collect'
				)
			);

			if (!$model->collections(array('count' => true)))
			{
				$collection = $model->collection();
				$collection->setup($this->juser->get('id'), 'member');
			}

			$view->myboards      = $model->mine();
			$view->groupboards   = $model->mine('groups');

			$view->name        = $this->_name;
			$view->option      = $this->option;
			$view->resource    = $this->resource;
			$view->no_html     = $no_html;
			$view->item_id = $item_id;

			if ($no_html)
			{
				$view->display();
				exit;
			}
			else 
			{
				return $view->loadTemplate();
			}
		}

		if (!$collection_id)
		{
			$collection = new CollectionsModelCollection();
			$collection->set('title', $collection_title);
			$collection->set('object_id', $this->juser->get('id'));
			$collection->set('object_type', 'member');
			if (!$collection->store())
			{
				$this->setError($collection->getError());
			}
			$collection_id = $collection->get('id');
		}

		if (!$this->getError())
		{
			// Try loading the current board/bulletin to see
			// if this has already been posted to the board (i.e., no duplicates)
			$stick = new CollectionsTablePost($this->database);
			$stick->loadByBoard($collection_id, $item_id);
			if (!$stick->id)
			{
				// No record found -- we're OK to add one
				$stick->item_id       = $item_id;
				$stick->collection_id = $collection_id;
				$stick->description   = JRequest::getVar('description', '');
				if ($stick->check()) 
				{
					// Store new content
					if (!$stick->store()) 
					{
						$this->setError($stick->getError());
					}
				}
			}
		}

		$response = new stdClass();
		$response->code = 0;
		if ($this->getError())
		{
			$response->code = 1;
			$response->message = $this->getError();
		}
		else
		{
			$response->message = 'Resource collected! ' . $item_id;
		}
		ob_clean();
		header('Content-type: text/plain');
		echo json_encode($response);
		exit();

		// Display updated bulletin stats if called via AJAX
		/*if ($no_html)
		{
			echo JText::sprintf('%s reposts', $stick->getCount(array('item_id' => $stick->item_id, 'original' => 0)));
			exit;
		}

		// Display the main listing
		return $this->_browse();*/
	}
}
