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

require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'category.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'section.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'attachment.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_forum' . DS . 'tables' . DS . 'post.php');

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
			case 'sections':   $this->sections();   break;
			case 'thread':     $this->thread();     break;
			case 'categories': $this->categories(); break;
			default:           $this->error();      break;
		}
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
				$filters['parent'] = $post->id;
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

		//encode results and return response
		$this->setMessageType($format);
		$this->setMessage($data);
	}

	public function error()
	{
		$format = JRequest::getVar('format', 'json');

		$data = new stdClass();
		$data->code = 1;
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
	public function _treeRecurse($id, $indent, $list, $children, $maxlevel=9999, $level=0, $type=1)
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
