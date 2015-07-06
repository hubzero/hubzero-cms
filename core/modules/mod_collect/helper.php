<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

namespace Modules\Collect;

use Hubzero\Module\Module;
use Components\Collections\Models\Archive;
use Components\Collections\Models\Collection;
use Components\Collections\Tables\Post;
use Request;
use User;
use Lang;
use stdClass;

/**
 * Module class for displaying a list of activity logs
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 * 
	 * @return  void
	 */
	public function display()
	{
		if (User::isGuest())
		{
			return;
		}

		include_once(\Component::path('com_collections') . DS . 'models' . DS . 'archive.php');

		$this->model = new Archive('member', User::get('id'));

		$this->item = $this->model->collectible(Request::getCmd('option'));
		if (!$this->item->canCollect())
		{
			return;
		}

		if (Request::getWord('tryto', '') == 'collect')
		{
			return $this->collect();
		}

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}

	/**
	 * Collect an item
	 *
	 * @return  void
	 */
	public function collect()
	{
		$collectible = Request::getVar('collectible', array(), 'post', 'none', 2);

		if (!$this->item->make())
		{
			$this->setError($this->item->getError());
		}

		// No collection ID selected so show form
		if (empty($collectible))
		{
			if (!$this->model->collections(array('count' => true)))
			{
				$collection = $this->model->collection();
				$collection->setup(User::get('id'), 'member');
			}

			$this->myboards = $this->model->mine();
			if ($this->myboards)
			{
				foreach ($this->myboards as $board)
				{
					$ids[] = $board->id;
				}
			}

			$this->groupboards = $this->model->mine('groups');
			if ($this->groupboards)
			{
				foreach ($this->groupboards as $optgroup => $boards)
				{
					if (count($boards) <= 0) continue;

					foreach ($boards as $board)
					{
						$ids[] = $board->id;
					}
				}
			}

			$this->collections = array();
			if ($this->item->get('id'))
			{
				$posts = $this->model->posts(array(
					'collection_id' => $ids,
					'item_id'       => $this->item->get('id'),
					'limit'         => 25,
					'start'         => 0,
					'access'        => array(0,1,4)
				));

				if ($posts)
				{
					$found = array();
					foreach ($posts as $post)
					{
						foreach ($this->myboards as $board)
						{
							if (!in_array($board->id, $found) && $board->id == $post->collection_id)
							{
								$this->collections[] = new Collection($board);
								$found[] = $board->id;
							}
						}
						if (!in_array($post->collection_id, $found))
						{
							foreach ($this->groupboards as $optgroup => $boards)
							{
								if (count($boards) <= 0) continue;

								foreach ($boards as $board)
								{
									if (!in_array($board->id, $found) && $board->id == $post->collection_id)
									{
										$this->collections[] = new Collection($board);
										$found[] = $board->id;
									}
								}
							}
						}
					}
				}
			}

			ob_clean();
			require($this->getLayoutPath('collect'));
			exit;
		}

		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Was a collection title submitted?
		// If so, we'll create a new collection with that title.
		if (isset($collectible['title']) && $collectible['title'])
		{
			$collection = with(new Collection())
				->set('title', $collectible['title'])
				->set('access', 0)
				->set('object_id', User::get('id'))
				->set('object_type', 'member');
			if (!$collection->store())
			{
				$this->setError($collection->getError());
			}
			$collectible['collection_id'] = $collection->get('id');
		}

		if (!$this->getError())
		{
			// Try loading the current post to see if this has
			// already been posted to this collection (i.e., no duplicates)
			$database = \App::get('db');

			$post = new Post($database);
			$post->loadByBoard($collectible['collection_id'], $this->item->get('id'));
			if (!$post->id)
			{
				// No record found -- we're OK to add one
				$post = new Post($database);
				$post->item_id       = $this->item->get('id');
				$post->collection_id = $collectible['collection_id'];
				$post->description   = $collectible['description'];
				if ($post->check())
				{
					// Store new content
					if (!$post->store())
					{
						$this->setError($post->getError());
					}
				}
				else
				{
					$this->setError($post->getError());
				}
			}
		}

		// Display success message
		$response = new stdClass();
		$response->success = true;
		if ($this->getError())
		{
			$response->success = false;
			$response->message = $this->getError();
		}
		else
		{
			$response->message = Lang::txt('MOD_COLLECT_PAGE_COLLECTED');
		}
		ob_clean();
		header('Content-type: text/plain');
		echo json_encode($response);
		exit;
	}
}

