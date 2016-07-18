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

use Components\Feedaggregator\Models\Feed;
use Components\Feedaggregator\Models\Post;
use Hubzero\Component\SiteController;
use Hubzero\Utility\Sanitize;
use Guzzle\Http\Client;
use Exception;
use Document;
use Request;
use Notify;
use Route;
use User;
use Lang;
use App;

require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'feed.php');
require_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'post.php');

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
		if (User::isGuest()) // have person login
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->_option . '&task=' . $this->_task), 'server');
			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				Lang::txt('COM_FEEDAGGREGATOR_LOGIN_NOTICE'),
				'warning'
			);
		}

		$userId = User::get('id');
		$authlevel = User::getAuthorisedViewLevels();
		$access_level = 3; //author_level

		if (!in_array($access_level, $authlevel) && User::get('id'))
		{
			$this->view
				->set('title', Lang::txt('COM_FEEDAGGREGATOR'))
				->setLayout('feedurl')
				->display();

			return;
		}

		// Incoming
		$filters = array(
			'limit'    => Request::getInt('limit', 25),
			'start'    => Request::getInt('limitstart', 0),
			'time'     => Request::getString('timesort', ''),
			'filterby' => Request::getString('filterby', 'all')
		);

		if (isset($posts))
		{
			$fromfeed = TRUE;
		}
		else
		{
			$fromfeed = FALSE;

			// Don't have a 0, because then it won't return anything. Doing mysql-workbench default
			if ($filters['limit'] == 0)
			{
				$filters['limit'] = 1000;
			}

			$model = Post::all();

			switch ($filters['filterby'])
			{
				case 'new':
					$posts = $model
						->whereEquals('status', 0);
				break;
				case 'approved':
					$posts = $model
						->whereEquals('status', 2);
				break;
				case 'review':
					$posts = $model
						->whereEquals('status', 1);
				break;
				case 'removed':
					$posts = $model
						->whereEquals('status', 3);
				break;
				case 'all':
				default:

				break;
			}

			$posts = $model
				->ordered()
				->limit($filters['limit'])
				->start($filters['start'])
				->rows();

			$total = intval($posts->count());
		}

		// Truncates the title to save screen real-estate. Full version shown in FancyBox
		foreach ($posts as $post)
		{
			if (strlen($post->title) >= 60)
			{
				$string = substr($post->title, 0, 60);
				$string = substr($string, 0, strrpos($string, ' ')) . '...';
				$post->set('shortTitle', $string);
			}
			else
			{
				$post->set('shortTitle', $post->title);
			}

			// output = 2012-08-15 00:00:00
			$post->created = Date::of($post->created)->toLocal();

			$post->description = wordwrap($post->description, 100, "<br />\n");
			$post->title = wordwrap($post->title, 65, "<br />\n");

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

		$messages = Notify::messages($this->_option);

		if (!isset($total))
		{
			$total = 0;
		}

		$this->view
			->set('messages', $messages)
			->set('posts', $posts)
			->set('filters', $filters)
			->set('fromfeed', $fromfeed)
			->set('total', $total)
			->set('title', Lang::txt('COM_FEEDAGGREGATOR'))
			->setLayout('display')
			->display();
	}

	/**
	 * Updates a post's status
	 *
	 * @return  string
	 */
	public function updateStatusTask()
	{
		$id     = Request::getVar('id', '');
		$action = Request::getVar('action', '');

		switch ($action)
		{
			case 'new':
				$action_id = 0;
				break;
			case 'mark':
				$action_id = 1;
				break;
			case 'approve':
				$action_id = 2;
				break;
			case 'remove':
				$action_id = 3;
		} //end switch

		$model = Post::oneOrFail($id);
		$model->set('status', $action_id);
		$model->save();

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
		$posts = Post::all()
			->whereEquals('feed_id', Request::getInt('id', 0))
			->ordered()
			->rows();

		$this->displayTask($posts);
	}

	/**
	 * Saves posts from enabled Source Feeds
	 *
	 * @return  void
	 */
	public function RetrieveNewPostsTask()
	{
		$feeds = Feed::all()
			->rows();

		$savedURLS = array();
		$urls = Post::all()
			->select('url')
			->rows();

		foreach ($urls as $url)
		{
			$savedURLS[] = $url->url;
		}

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
								$post = new Post; //create post object
								$post->set('title', html_entity_decode(strip_tags($item->title)));
								$post->set('feed_id', (integer) $feed->id);
								$post->set('status', 0);  //force new status

								//ATOM original content link
								$post->set('url', (string) $link);

								if (isset($item->published) == TRUE)
								{
									$post->set('created', Date::of($item->published)->toSql());
								}
								else
								{
									$post->set('created', Date::of($item->updated)->toSql());
								}

								$post->set('description', (string) html_entity_decode(strip_tags($item->content, '<img>')));
								$post->save(); //save the post
							} // end check for prior existance
						}
						else if ($feedType == 'RSS')
						{
							if (in_array($item->link, $savedURLS) == FALSE) //checks to see if we have this item
							{
								$post = new Post; //create post object
								$post->set('title',  (string) html_entity_decode(strip_tags($item->title)));
								$post->set('feed_id', (integer) $feed->id);
								$post->set('status', 0);  //force new status
								$post->set('created', Date::of($item->pubDate)->toSql());
								$post->set('description', (string) html_entity_decode(strip_tags($item->description, '<img>')));
								$post->set('url', (string) $item->link);

								$post->save(); //save the post
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
		$posts = Post::all()
			->whereEquals('status', 2)
			->ordered()
			->limit(1000)
			->rows();

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
				$item->title = preg_replace("/&#?[a-z1-9]{2,8};/i","",$post->title);
				$item->title = (string) strip_tags($item->title);
				$item->title = html_entity_decode($item->title);
				$item->title = Sanitize::clean($item->title);

				// encapsulate link in unparseable
				$item->link = '<![CDATA[' . $post->link . ']]>';
				$item->date = date($post->created);

				// sanitize ouput
				$item->description = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $post->description);
				$item->description = preg_replace('/[^A-Za-z0-9 ]/', '', $item->description);
				$item->description = preg_replace("/&#?[a-z1-9]{2,8};/i","",$post->description);
				$item->description = html_entity_decode($post->description);
				$item->description = Sanitize::html($post->description);

				$doc->addItem($item);
			}
		}
		// Output the feed
		echo $doc->render();
	}
}
