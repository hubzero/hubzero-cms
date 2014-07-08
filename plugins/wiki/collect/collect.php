<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Wiki Plugin class for favoriting a wiki page
 */
class plgWikiCollect extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * After display content method
	 * Method is called by the view and the results are imploded and displayed in a placeholder
	 *
	 * @param      object $page     Wiki page
	 * @param      object $revision Wiki revision
	 * @param      object $config   Wiki config
	 * @return     string
	 */
	public function onAfterDisplayContent($page, $revision, $config)
	{
		$this->page = $page;
		$this->revision = $revision;

		// Incoming action
		$action = JRequest::getVar('action', '');
		if ($action && $action == 'collect')
		{
			// Check the user's logged-in status
			return $this->fav();
		}

		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Build the HTML meant for the "about" tab's metadata overview
		$juser = JFactory::getUser();
		if (!$juser->get('guest'))
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
					'element' => $this->_name,
					'name'    => 'metadata'
				)
			);
			$view->option = JRequest::getCmd('option', 'com_wiki');
			$view->page = $page;

			return $view->loadTemplate();
		}

		return '';
	}

	/**
	 * Un/favorite an item
	 *
	 * @param      integer $oid Resource to un/favorite
	 * @return     void
	 */
	public function fav()
	{
		include_once(JPATH_ROOT . DS . 'components' . DS . 'com_collections' . DS . 'models' . DS . 'collections.php');

		$this->option   = JRequest::getCmd('option', 'com_wiki');
		$this->juser    = JFactory::getUser();
		$this->database = JFactory::getDBO();

		// Incoming
		$item_id          = JRequest::getInt('item', 0);
		$collection_id    = JRequest::getInt('collection', 0);
		$collection_title = JRequest::getVar('collection_title', '');
		$no_html          = JRequest::getInt('no_html', 0);

		$model = new CollectionsModel('member', $this->juser->get('id'));

		$b = new CollectionsTableItem($this->database);
		$b->loadType($this->page->get('id'), 'wiki');
		if (!$b->id)
		{
			$row = new CollectionsTableCollection($this->database);
			$row->load($collection_id);

			$b->url         = JRoute::_($this->page->link());
			$b->type        = 'wiki';
			$b->object_id   = $this->page->get('id');
			$b->title       = $this->page->get('title');
			$b->description = \Hubzero\Utility\String::truncate($this->revision->content('clean'), 300);
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

		// No board ID selected so present repost form
		if (!$collection_id && !$collection_title)
		{
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  => $this->_type,
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

			$view->myboards    = $model->mine();
			if ($view->myboards)
			{
				foreach ($view->myboards as $board)
				{
					$ids[] = $board->id;
				}
			}

			$view->groupboards = $model->mine('groups');
			if ($view->groupboards)
			{
				foreach ($view->groupboards as $optgroup => $boards)
				{
					if (count($boards) <= 0) continue;

					foreach ($boards as $board)
					{
						$ids[] = $board->id;
					}
				}
			}

			$posts = $model->posts(array(
				'collection_id' => $ids,
				'item_id'       => $item_id,
				'limit'         => 25,
				'start'         => 0
			));
			$view->collections = array();
			if ($posts)
			{
				foreach ($posts as $post)
				{
					$found = false;
					foreach ($view->myboards as $board)
					{
						if ($board->id == $post->collection_id)
						{
							$view->collections[] = new CollectionsModelCollection($board);
							$found = true;
						}
					}
					if (!$found)
					{
						foreach ($view->groupboards as $optgroup => $boards)
						{
							if (count($boards) <= 0) continue;

							foreach ($boards as $board)
							{
								if ($board->id == $post->collection_id)
								{
									$view->collections[] = new CollectionsModelCollection($board);
									$found = true;
								}
							}
						}
					}
				}
			}

			$view->name     = $this->_name;
			$view->option   = $this->option;
			$view->page     = $this->page;
			$view->revision = $this->revision;
			$view->no_html  = $no_html;
			$view->item_id  = $item_id;

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

		// Check for request forgeries
		JRequest::checkToken('get') or JRequest::checkToken() or jexit('Invalid Token');

		if ($collection_title)
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
				$stick->description = JRequest::getVar('description', '', 'none', 2);
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

		// Display updated bulletin stats if called via AJAX
		if ($no_html)
		{
			$response = new stdClass();
			$response->success = true;
			if ($this->getError())
			{
				$response->success = false;
				$response->message = $this->getError();
			}
			else
			{
				$response->message = JText::sprintf('PLG_WIKI_COLLECT_PAGE_COLLECTED', $item_id);
			}
			ob_clean();
			header('Content-type: text/plain');
			echo json_encode($response);
			exit;
		}
	}
}
