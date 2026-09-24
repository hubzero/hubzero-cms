<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

		include_once \Component::path('com_collections') . DS . 'models' . DS . 'archive.php';

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
		Lang::load('com_collections', \Component::path('com_collections') . DS . 'site');

		$collectible = Request::getArray('collectible', array(), 'post');

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
					if (count($boards) <= 0)
					{
						continue;
					}

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
								if (count($boards) <= 0)
								{
									continue;
								}

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
			require $this->getLayoutPath('collect');
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

			// collectible[collection_id] comes straight from the module's form and
			// nothing downstream re-checks it -- Tables\Post::check() only wants a
			// non-zero value and stamps created_by from the session -- so the
			// board has to be one this caller may post to.
			//
			$__board = new \Components\Collections\Models\Collection((int) $collectible['collection_id']);

			// The form's JavaScript expects the JSON reply built below, so a
			// refusal is reported as an error rather than by returning
			if (!$__board->canBePostedToBy())
			{
				$this->setError(Lang::txt('COM_COLLECTIONS_NOT_AUTH'));
			}

			// Whether the ITEM needs gating depends on where it came from, and
			// that turns on the option this module is rendering under.
			//
			// Archive::collectible() returns a per-option adapter for every
			// option BUT com_collections, and each of those adapters creates the
			// item row for the thing being collected (models/item/resources.php
			// and its eight siblings). A brand-new item sits on no board yet, so
			// asking "which boards already carry it" would refuse every
			// first-ever collect of a resource, wiki page, blog entry and so on.
			// What the caller may see there is the page they are already on.
			//
			// For com_collections it returns the BASE Models\Item, whose make()
			// does Tables\Post::load(Request::getInt('post')) -- an unscoped
			// primary-key load, so the item is whatever post id the request
			// names. That is the path canCollect() exists to serve, and it is
			// exactly the "mint a post carrying any item on the hub" shape the
			// rest of this campaign closed. It still has to be gated.
			if (!$this->getError() && Request::getCmd('option') == 'com_collections')
			{
				$__item = new \Components\Collections\Models\Item($this->item->get('id'));

				if (!$__item->isCollectableBy())
				{
					$this->setError(Lang::txt('COM_COLLECTIONS_NOT_AUTH'));
				}
			}

			$post = new Post($database);
			if (!$this->getError())
			{
				$post->loadByBoard($collectible['collection_id'], $this->item->get('id'));
			}
			if (!$this->getError() && !$post->id)
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
