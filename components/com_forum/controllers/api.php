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

JLoader::import('Hubzero.Api.Controller');

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'models' . DS . 'forum.php');

/**
 * API controller class for forum posts
 */
class ForumControllerApi extends Hubzero_Api_Controller
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

		$this->config = JComponentHelper::getParams('com_forum');
		$this->database = JFactory::getDBO();

		switch ($this->segments[0]) 
		{
			case 'threads':    $this->threads();   break;
			case 'sections':   $this->sections();   break;
			case 'thread':     $this->thread();     break;
			case 'categories': $this->categories(); break;
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
	 * Displays ticket stats
	 *
	 * @return    void
	 */
	private function sections()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$model = new ForumModel('site', 0);

		$response = new stdClass;
		$response->sections = array();

		$response->total = $model->sections('count', array('state' => 1));

		if ($response->total)
		{
			$juri =& JURI::getInstance();
			$base = str_replace('/api', '', rtrim($juri->base(), DS));

			foreach ($model->sections('list', array('state' => 1)) as $section)
			{
				$obj = new stdClass;
				$obj->id         = $section->get('id');
				$obj->title      = $section->get('title');
				$obj->alias      = $section->get('alias');
				$obj->created    = $section->get('created');
				$obj->scope      = $section->get('scope');
				$obj->scope_id   = $section->get('scope_id');

				$obj->categories = $section->count('categories');
				$obj->threads    = $section->count('threads');
				$obj->posts      = $section->count('posts');

				$obj->url        = $base . DS . ltrim(JRoute::_('index.php?option=com_forum&section=' . $section->get('alias')), DS);

				$response->sections[] = $obj;
			}
		}

		$response->success = true;

		$this->setMessage($response);
	}

	/**
	 * Displays ticket stats
	 *
	 * @return    void
	 */
	private function categories()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$filters = array(
			'authorized' => 1,
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'section'    => JRequest::getVar('section', ''),
			'search'     => JRequest::getVar('search', ''),
			'scope'      => JRequest::getWord('scope', 'site'),
			'scope_id'   => JRequest::getInt('scope_id', 0),
			'state'      => 1,
			'parent'     => 0
		);

		$model = new ForumModel($filters['scope'], $filters['scope_id']);

		$section = $model->section($filters['section'], $model->get('scope'), $model->get('scope_id'));
		if (!$section->exists())
		{
			$this->errorMessage(
				500, 
				JText::_('Section not found.'), 
				JRequest::getWord('format', 'json')
			);
			return;
		}

		$response = new stdClass;

		$response->section = new stdClass;
		$response->section->id         = $section->get('id');
		$response->section->title      = $section->get('title');
		$response->section->alias      = $section->get('alias');
		$response->section->created    = $section->get('created');
		$response->section->scope      = $section->get('scope');
		$response->section->scope_id   = $section->get('scope_id');

		$response->categories = array();
		$response->total = $section->categories('count', array('state' => 1));

		if ($response->total)
		{
			$juri =& JURI::getInstance();
			$base = str_replace('/api', '', rtrim($juri->base(), DS));

			foreach ($section->categories('list', array('state' => 1)) as $category)
			{
				$obj = new stdClass;
				$obj->id          = $category->get('id');
				$obj->title       = $category->get('title');
				$obj->alias       = $category->get('alias');
				$obj->description = $category->get('description');
				$obj->created     = $category->get('created');
				$obj->scope       = $category->get('scope');
				$obj->scope_id    = $category->get('scope_id');

				$obj->threads     = $category->count('threads');
				$obj->posts       = $category->count('posts');

				$obj->url         = $base . DS . ltrim(JRoute::_('index.php?option=com_forum&section=' . $section->get('alias') . '&category=' . $category->get('alias')), DS);

				$response->categories[] = $obj;
			}
		}

		$response->success = true;

		$this->setMessage($response);
	}

	/**
	 * Displays ticket stats
	 *
	 * @return    void
	 */
	private function threads()
	{
		$this->setMessageType(JRequest::getWord('format', 'json'));

		$filters = array(
			'authorized' => 1,
			'limit'      => JRequest::getInt('limit', 25),
			'start'      => JRequest::getInt('limitstart', 0),
			'section'    => JRequest::getVar('section', ''),
			'category'   => JRequest::getVar('category', ''),
			'search'     => JRequest::getVar('search', ''),
			'scope'      => JRequest::getWord('scope', 'site'),
			'scope_id'   => JRequest::getInt('scope_id', 0),
			'state'      => 1,
			'parent'     => 0
		);

		$model = new ForumModel($filters['scope'], $filters['scope_id']);

		$section = $model->section($filters['section'], $model->get('scope'), $model->get('scope_id'));
		if (!$section->exists())
		{
			$this->errorMessage(
				500, 
				JText::_('Section not found.'), 
				JRequest::getWord('format', 'json')
			);
			return;
		}

		$category = $section->category($filters['category']);
		if (!$category->exists())
		{
			$this->errorMessage(
				500, 
				JText::_('Category not found.'), 
				JRequest::getWord('format', 'json')
			);
			return;
		}

		$response = new stdClass;

		$response->section = new stdClass;
		$response->section->id         = $section->get('id');
		$response->section->title      = $section->get('title');
		$response->section->alias      = $section->get('alias');
		$response->section->created    = $section->get('created');
		$response->section->scope      = $section->get('scope');
		$response->section->scope_id   = $section->get('scope_id');

		$response->category = new stdClass;
		$response->category->id          = $category->get('id');
		$response->category->title       = $category->get('title');
		$response->category->alias       = $category->get('alias');
		$response->category->description = $category->get('description');
		$response->category->created     = $category->get('created');
		$response->category->scope       = $category->get('scope');
		$response->category->scope_id    = $category->get('scope_id');

		$response->threads = array();
		$response->total = $category->threads('count', array('state' => 1));

		if ($response->total)
		{
			$juri =& JURI::getInstance();
			$base = str_replace('/api', '', rtrim($juri->base(), DS));

			foreach ($category->threads('list', array('state' => 1)) as $thread)
			{
				$obj = new stdClass;
				$obj->id          = $thread->get('id');
				$obj->title       = $thread->get('title');
				//$obj->description = $category->get('description');
				$obj->created     = $thread->get('created');
				$obj->modified    = $thread->get('modified');
				$obj->anonymous   = ($thread->get('anonymous') ? true : false);
				$obj->closed      = ($thread->get('closed') ? true : false);
				$obj->scope       = $thread->get('scope');
				$obj->scope_id    = $thread->get('scope_id');

				$obj->creator = new stdClass;
				$obj->creator->id = $thread->get('created_by');
				$obj->creator->name = $thread->creator('name');

				$obj->posts       = $thread->posts('count');

				$obj->url         = $base . DS . ltrim(JRoute::_('index.php?option=com_forum&section=' . $section->get('alias') . '&category=' . $category->get('alias') . '&thread=' . $thread->get('id')), DS);

				$response->threads[] = $obj;
			}
		}

		$response->success = true;

		$this->setMessage($response);
	}

	/**
	 * Displays ticket stats
	 *
	 * @return    void
	 */
	private function thread()
	{
		//get request vars
		$format = JRequest::getVar('format', 'json');
		$find   = strtolower(JRequest::getWord('find', 'results'));

		$jconfig = JFactory::getConfig();

		$filters = array();
		$filters['limit']    = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));
		$filters['start']    = JRequest::getInt('limitstart', 0);

		$filters['section']  = JRequest::getCmd('section', '');
		$filters['category'] = JRequest::getCmd('category', '');
		if ($thread = JRequest::getInt('thread', 0))
		{
			$filters['thread'] = $thread;
		}

		$filters['state']     = JRequest::getInt('state', 1);
		$filters['scope']     = JRequest::getWord('scope', '');
		$filters['scope_id']  = JRequest::getInt('scope_id', 0);
		$filters['scope_sub_id']  = JRequest::getInt('scope_sub_id', 0);
		$filters['object_id'] = JRequest::getInt('object_id', 0);
		$filters['sticky']    = false;

		$filters['start_id'] = JRequest::getInt('start_id', 0);
		$filters['start_at'] = JRequest::getVar('start_at', '');

		$sort = JRequest::getVar('sort', 'newest');
		switch ($sort)
		{
			case 'oldest':
				$filters['sort_Dir'] = 'ASC';
			break;

			case 'newest':
			default:
				$filters['sort_Dir'] = 'DESC';
			break;
		}
		$filters['sort'] = 'c.created';

		if ($filters['start_id'])
		{
			$filters['limit'] = 0;
			$filters['start'] = 0;
		}

		$post = new ForumPost($this->database);

		$data = new stdClass();
		$data->code = 0;

		if ($find == 'count')
		{
			$data->count = 0;
			$data->threads = 0;

			if (isset($filters['thread']))
			{
				$data->count = $post->countTree($filters['thread'], $filters);
			}
			/*else
			{
				$data->count = $post->count($filters);
			}*/
			$post->loadByObject($filters['object_id'], $filters['scope_id'], $filters['scope']);
			if ($post->id)
			{
				$filters['start_at'] = JRequest::getVar('threads_start', '');
				$filters['parent'] = 0; //$post->id;
			}
			$data->threads = $post->count($filters);
		}
		else
		{
			$rows = $post->find($filters);
			if ($rows)
			{
				if ($filters['start_id'])
				{
					$filters['limit'] = JRequest::getInt('limit', $jconfig->getValue('config.list_limit'));

					$children = array(
						0 => array()
					);

					$levellimit = ($filters['limit'] == 0) ? 500 : $filters['limit'];

					foreach ($rows as $v)
					{
						$pt      = $v->parent;
						$list    = @$children[$pt] ? $children[$pt] : array();
						array_push($list, $v);
						$children[$pt] = $list;
					}

					$list = $this->_treeRecurse($view->post->get('id'), '', array(), $children, max(0, $levellimit-1));

					$inc = false;
					$newlist = array();
					foreach ($list as $l)
					{
						if ($l->id == $filters['start_id']) 
						{
							$inc = true;
						}
						else
						{
							if ($inc)
							{
								$newlist[] = $l;
							}
						}
					}

					$rows = array_slice($newlist, $filters['start'], $filters['limit']);
				}
			}
			$data->response = $rows;
		}

		/*if ($this->getError())
		{
			$data->code   = 1;
			$data->errors = $this->getErrors();
		}*/
//print_r($this->database); die();
		//encode results and return response
		$this->setMessageType($format);
		$this->setMessage($data);
	}

	/**
	 * Recursive function to build tree
	 * 
	 * @param      integer $id       Parent ID
	 * @param      string  $indent   Indent text
	 * @param      array   $list     List of records
	 * @param      array   $children Container for parent/children mapping
	 * @param      integer $maxlevel Maximum levels to descend
	 * @param      integer $level    Indention level
	 * @param      integer $type     Indention type
	 * @return     void
	 */
	private function _treeRecurse($id, $indent, $list, $children, $maxlevel=9999, $level=0, $type=1)
	{
		if (@$children[$id] && $level <= $maxlevel)
		{
			foreach ($children[$id] as $v)
			{
				$id = $v->id;

				//if ($type) 
				//{
					$pre    = ' treenode';
					$spacer = ' indent' . $level;
				/*} 
				else 
				{
					$pre    = '- ';
					$spacer = '&nbsp;&nbsp;';
				}*/

				if ($v->parent == 0) 
				{
					$txt = '';
				} 
				else 
				{
					$txt = $pre;
				}
				$pt = $v->parent;

				$list[$id] = $v;
				$list[$id]->treename = "$indent$txt";
				$list[$id]->children = count(@$children[$id]);

				$list = $this->_treeRecurse($id, $indent . $spacer, $list, $children, $maxlevel, $level+1, $type);
			}
		}
		return $list;
	}
}
