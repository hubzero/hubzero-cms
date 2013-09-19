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
 * /administrator/components/com_support/controllers/tickets.php
 * 
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

JLoader::import('Hubzero.Api.Controller');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'collections.php');

/**
 * API controller class for support tickets
 */
class CollectionsControllerApi extends Hubzero_Api_Controller
{
	/**
	 * Execute a request
	 *
	 * @return    void
	 */
	public function execute()
	{
		JLoader::import('joomla.environment.request');
		JLoader::import('joomla.application.component.helper');

		$this->config   = JComponentHelper::getParams('com_blog');
		$this->database = JFactory::getDBO();

		switch ($this->segments[0]) 
		{
			case 'collections':  $this->collectionsTask();  break;
			case 'collection':  $this->collectionTask();  break;

			case 'posts': $this->postsTask();  break;
			case 'post': $this->postTask();  break;

			default:
				$this->errorMessage(
					500, 
					JText::_('Invalid task.'), 
					JRequest::getWord('format', 'json')
				);
			break;
		}
	}

	/**
	 * Method to report errors. creates error node for response body as well
	 *
	 * @param	$code		Error Code
	 * @param	$message	Error Message
	 * @param	$format		Error Response Format
	 *
	 * @return     void
	 */
	private function errorMessage($code, $message, $format = 'json')
	{
		//build error code and message
		$object = new stdClass();
		$object->error->code    = $code;
		$object->error->message = $message;

		//set http status code and reason
		$this->getResponse()
		     ->setErrorMessage($object->error->code, $object->error->message);

		//add error to message body
		$this->setMessageType(JRequest::getWord('format', $format));
		$this->setMessage($object);
	}

	/**
	 * Displays a list of tags
	 *
	 * @return    void
	 */
	private function postsTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$model = new CollectionsModelCollection();

		$filters = array(
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'search'     => JRequest::getVar('search', ''),
			'sort'       => 'p.created',
			'state'      => 1,
			'sort_Dir'   => strtoupper(JRequest::getWord('sortDir', 'DESC')),
			'is_default' => 0
		);

		if ($collection = JRequest::getInt('collection', 0))
		{
			$filters['collection_id'] = $collection;
		}

		$response = new stdClass;
		$response->posts = array();

		$filters['count'] = true;

		$response->total = $model->posts($filters);

		if ($response->total)
		{
			$juri =& JURI::getInstance();

			$href = 'index.php?option=com_collections&controller=media&post=';
			$base = str_replace('/api', '', rtrim($juri->base(), DS));

			$filters['count'] = false;

			foreach ($model->posts($filters) as $i => $entry)
			{
				$item = $entry->item();

				$obj = new stdClass;
				$obj->id        = $entry->get('id');
				$obj->title     = $entry->get('title', $item->get('title'));
				$obj->type      = $item->get('type');;
				$obj->posted    = $entry->get('created');
				$obj->author    = $entry->creator()->get('name');
				$obj->url       = $base . DS . ltrim(JRoute::_($entry->link()), DS);

				$obj->tags      = $item->tags('string');
				$obj->comments  = $item->get('comments', 0);
				$obj->likes     = $item->get('positive', 0);
				$obj->reposts   = $item->get('reposts', 0);
				$obj->assets    = array();

				$assets = $item->assets();
				if ($assets->total() > 0)
				{
					foreach ($assets as $asset)
					{
						$a = new stdClass;
						$a->title       = ltrim($asset->get('filename'), '/');
						$a->description = $asset->get('description');
						$a->url         = ($asset->get('type') == 'link' ? $asset->get('filename') : $base . DS . ltrim(JRoute::_($href . $entry->get('id') . '&task=download&file=' . $a->title), DS));

						$obj->assets[] = $a;
					}
				}

				$response->posts[] = $obj;
			}
		}

		$response->success = true;

		$this->setMessage($response);
	}

	/**
	 * Displays a list of tags
	 *
	 * @return    void
	 */
	private function collectionsTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$model = new CollectionsModel();

		$filters = array(
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'search'     => JRequest::getVar('search', ''),
			'state'      => 1,
			'sort_Dir'   => strtoupper(JRequest::getWord('sortDir', 'DESC')),
			'is_default' => 0,
			'access'     => 0
		);

		$response = new stdClass;
		$response->collections = array();

		$filters['count'] = true;

		$response->total = $model->collections($filters);

		if ($response->total)
		{
			$juri =& JURI::getInstance();
			$base = str_replace('/api', '', rtrim($juri->base(), DS));

			$filters['count'] = false;

			foreach ($model->collections($filters) as $i => $entry)
			{
				$collection = CollectionsModelCollection::getInstance($entry->item()->get('object_id'));

				$obj = new stdClass;
				$obj->id          = $entry->get('id');
				$obj->title       = $entry->get('title', $collection->get('title'));
				$obj->description = $entry->get('description', $collection->get('description'));
				$obj->type        = 'collection';
				$obj->posted      = $entry->get('created');
				$obj->author      = $entry->creator()->get('name');

				switch ($collection->get('object_type'))
				{
					case 'member':
						$url = 'index.php?option=com_members&id=' . $collection->get('object_id') . '&active=collections&task=' . $collection->get('alias');
					break;

					case 'group':
						ximport('Hubzero_Group');
						$group = new Hubzero_Group();
						$group->read($collection->get('object_id'));
						$url = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=collections&scope=' . $collection->get('alias');
					break;
					
					default:
						$url = 'index.php?option=com_collections&task=all&id=' . $collection->get('id');
					break;
				}
				$obj->url         = $base . DS . ltrim(JRoute::_($url), DS);

				$obj->files       = $collection->count('file');
				$obj->links       = $collection->count('link');
				$obj->collections = $collection->count('collection');

				$response->collections[] = $obj;
			}
		}

		$response->success = true;

		$this->setMessage($response);
	}

	/**
	 * Displays a list of tags
	 *
	 * @return    void
	 */
	private function collectionTask()
	{
		$this->postsTask();
	}

	/**
	 * Displays a list of tags
	 *
	 * @return    void
	 */
	private function postTask()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$response = new stdClass;
		$response->success = true;

		$this->setMessage($response);
	}
}
