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
 * @author    Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedaggregator\Site\Controllers;

use Components\Feedaggregator\Models;
use Hubzero\Component\SiteController;
use Guzzle\Http\Client;
use Exception;
use Document;
use Request;
use Notify;
use Route;
use User;
use Lang;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'feeds.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'posts.php');

/**
 *  Feed Aggregator controller class
 */
class Posts extends SiteController
{
	/**
	 * Default component view
	 *
	 * @param   $posts mixed post objects
	 * @return  void
	 */
	public function displayTask($posts = NULL)
	{
		$userId = User::get('id');
		$authlevel = User::getAuthorisedViewLevels();
		$access_level = 3; //author_level

		if (in_array($access_level, $authlevel) && User::get('id'))
		{
			if (isset($posts))
			{
				$this->view->filters = array(
					'limit'    => Request::getInt('limit', 25),
					'start'    => Request::getInt('limitstart', 0),
					'time'     => Request::getString('timesort', ''),
					'filterby' => Request::getString('filterby', 'all')
				);

				$this->setView('posts','display');
				$this->view->fromfeed = TRUE;
			}
			else
			{
				$this->view->fromfeed = FALSE;
				$this->view->setLayout('display');
				// Incoming
				$this->view->filters = array(
					'limit'    => Request::getInt('limit', 25),
					'start'    => Request::getInt('limitstart', 0),
					'time'     => Request::getString('timesort', ''),
					'filterby' => Request::getString('filterby', 'all')
				);

				// Don't have a 0, because then it won't return anything. Doing mysql-workbench default
				if ($this->view->filters['limit'] == 0)
				{
					$this->view->filters['limit'] = 1000;
				}

				$feeds = array(); //page on websites
				$posts = array();

				$model = new Models\Posts;

				switch ($this->view->filters['filterby'])
				{
					case 'all':
						$posts = $model->loadAllPosts($this->view->filters['limit'], $this->view->filters['start']);
						$this->view->total = intval($model->loadRowCount());
					break;
					case 'new':
						$posts = $model->getPostsByStatus($this->view->filters['limit'], $this->view->filters['start'],0);
						$this->view->total = intval($model->loadRowCount(0));
					break;
					case 'approved':
						$posts = $model->getPostsByStatus($this->view->filters['limit'], $this->view->filters['start'],2);
						$this->view->total = intval($model->loadRowCount(2));
					break;
					case 'review':
						$posts = $model->getPostsByStatus($this->view->filters['limit'], $this->view->filters['start'],1);
						$this->view->total = intval($model->loadRowCount(1));
					break;
					case 'removed':
						$posts = $model->getPostsByStatus($this->view->filters['limit'], $this->view->filters['start'],3);
						$this->view->total = intval($model->loadRowCount(3));
					break;
					default:
						//load stored posts
						$model = new Models\Posts;
						$posts = $model->loadAllPosts($this->view->filters['limit'], $this->view->filters['start']);
						$this->view->total = intval($model->loadRowCount());
					break;
				}
			}

			// Truncates the title to save screen real-estate. Full version shown in FancyBox
			foreach ($posts as $post)
			{
				if (strlen($post->title) >= 60)
				{
					$string = substr($post->title, 0, 60);
					$string = substr($string, 0, strrpos($string, ' ')) . '...';
					$post->shortTitle = $string;
				}
				else
				{
					$post->shortTitle = $post->title;
				}

				$epoch = $post->created;
				// convert UNIX timestamp to PHP DateTime
				$dt = new \DateTime("@$epoch");
				// output = 2012-08-15 00:00:00
				$post->created =  $dt->format('m-d-y h:i A');

				$post->description = wordwrap($post->description,100,"<br>\n");
				$post->title = wordwrap($post->title, 65, "<br>\n");

				switch ($post->status)
				{
					case 0:
						$post->status = 'new';
					break;
					case 1:
						$post->status = 'under review';
					break;
					case 2:
						$post->status = 'approved';
					break;
					case 3:
						$post->status = 'removed';
					break;
				} //end switch
			} //end foreach
			$this->view->messages = Notify::messages($this->_option);
			$this->view->posts = $posts;
			$this->view->title = Lang::txt('COM_FEEDAGGREGATOR');
			$this->view->display();
		}
		else if (User::get('id'))
		{
			$this->view
				->set('title', Lang::txt('COM_FEEDAGGREGATOR'))
				->setLayout('feedurl')
				->display();
		}
		else if (User::isGuest()) // have person login
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				Lang::txt('COM_FEEDAGGREGATOR_LOGIN_NOTICE'),
				'warning'
			);
		}
	}

	/**
	 * Updates a post's status
	 *
	 * @return     string $action_id."-".$action
	 */
	public function updateStatusTask()
	{
		$id = Request::getVar('id', '');
		$action = Request::getVar('action', '');
		$model = new Models\Posts;

		switch ($action)
		{
			case "new":
				$action_id = 0;
				break;
			case "mark":
				$action_id = 1;
				break;
			case "approve":
				$action_id = 2;
				break;
			case "remove":
				$action_id = 3;
		} //end switch


		$model->updateStatus($id, $action_id);
		echo $action_id . '-' . $action;
		exit();
	}

	/**
	 * Displays posts within a category
	 *
	 * @return  void
	 */
	public function PostsByIdTask()
	{
		$model = new Models\Posts;
		$posts = $model->loadPostsByFeedId(Request::getVar('id', ''));

		$this->displayTask($posts);
	}

	/**
	 * Saves posts from enabled Source Feeds
	 *
	 * @return  void
	 */
	public function RetrieveNewPostsTask()
	{
		$model = new Models\Feeds;
		$feeds = $model->loadAll();

		$model = new Models\Posts;
		$savedURLS = $model->loadURLs();

		foreach ($feeds as $feed)
		{
			if ($feed->enabled == 1 && filter_var($feed->url, FILTER_VALIDATE_URL) == TRUE)
			{
				try
				{
					$ch = curl_init();

					// set URL and other appropriate options
					curl_setopt($ch, CURLOPT_URL, $feed->url);
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

					$data = curl_exec($ch);
					curl_close($ch);

					if (!$data)
					{
						$this->setError(Lang::txt('COM_FEEDAGGREGATOR_ERROR_READING_FEED', $feed->url));
						continue;
					}

					$page = simplexml_load_string(utf8_encode($data));

					if (isset($page->entry) == TRUE)
					{
						$items = $page->entry;
						$feedType = 'ATOM';
					}
					else
					{
						$items = $page->channel->item; //gets the items of the channel
						$feedType = 'RSS';
					}

					foreach ($items as $item)
					{
						if ($feedType == 'ATOM')
						{
							// get the href attribute of the orignal content link
							foreach ($item->link->attributes() as $link)
							{
								$link = $link;
							}

							if (in_array($link, $savedURLS) == FALSE) //checks to see if we have this item
							{
								$post = new Models\Posts; //create post object
								$post->set('title', html_entity_decode(strip_tags($item->title)));
								$post->set('feed_id', (integer) $feed->id);
								$post->set('status', 0);  //force new status

								//ATOM original content link
								$post->set('url', (string) $link);

								if (isset($item->published) == TRUE)
								{
									$post->set('created', strtotime($item->published));
								}
								else
								{
									$post->set('created', strtotime($item->updated));
								}

								$post->set('description', (string) html_entity_decode(strip_tags($item->content, '<img>')));
								$post->store(); //save the post
							} // end check for prior existance
						}
						else if ($feedType == 'RSS')
						{
							if (in_array($item->link, $savedURLS) == FALSE) //checks to see if we have this item
							{
								$post = new Models\Posts; //create post object
								$post->set('title',  (string) html_entity_decode(strip_tags($item->title)));
								$post->set('feed_id', (integer) $feed->id);
								$post->set('status', 0);  //force new status
								$post->set('created', strtotime($item->pubDate));
								$post->set('description', (string) html_entity_decode(strip_tags($item->description, '<img>')));
								$post->set('url', (string) $item->link);

								$post->store(); //save the post
							}
						}
					} //end foreach
				} //end try
				catch (Exception $e)
				{
					$this->setError(Lang::txt('COM_FEEDAGGREGATOR_ERROR_READING_FEED', $feed->url));
					continue;
				}
			}//end if
		}

		if ($this->getError())
		{
			Notify::warning(implode('<br />', $this->getErrors()), $this->_option);
		}

		// Output messsage and redirect
		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=posts&filterby=all', false),
			Lang::txt('COM_FEEDAGGREGATOR_GOT_NEW_POSTS')
		);
	}

	/**
	 * Generates RSS feed when called by URL
	 *
	 * @return  void
	 */
	public function generateFeedTask()
	{
		// Get the approved posts
		$model = new Models\Posts;
		$posts = $model->getPostsByStatus(1000,0,2);

		// Set the mime encoding for the document
		Document::setType('feed');

		// Start a new feed object
		$doc = Document::instance();

		$doc->title       = Config::get('sitename') . ' ' . Lang::txt('COM_FEEDAGGREGATOR_AGGREGATED_FEED');
		$doc->description = Lang::txt(Config::get('sitename') . ' ' . Lang::txt('COM_FEEDAGGREGATOR_AGGREGATED_FEED_SELECTED_READING'));
		$doc->copyright   = Lang::txt(date("Y"), Config::get('sitename'));
		$doc->category    = Lang::txt('COM_FEEDAGGREGATOR_EXTERNAL_CONTENT');

		// Start outputing results if any found
		if (count($posts) > 0)
		{
			foreach ($posts as $post)
			{
				// Load individual item creator class
				$item = new \Hubzero\Document\Type\Feed\Item();

				// sanitize ouput
				$item->title = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $post->title);
				$item->title = (string) html_entity_decode(strip_tags($item->title));

				// encapsulate link in unparseable
				$item->link = '<![CDATA[' . $post->link . ']]>';
				$item->date = date($post->created);

				// sanitize ouput
				$item->description = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $post->description);
				$item->description = (string) html_entity_decode(strip_tags($item->description, '<img>'));

				$doc->addItem($item);
			}
		}
		// Output the feed
		echo $doc->render();
	}
}
