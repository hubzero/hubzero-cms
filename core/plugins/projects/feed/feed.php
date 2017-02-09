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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Projects Feed plugin
 */
class plgProjectsFeed extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Component name
	 *
	 * @var  string
	 */
	protected $_option = 'com_projects';

	/**
	 * Store internal message
	 *
	 * @var	 array
	 */
	protected $_msg = null;

	/**
	 * Repository path
	 *
	 * @var	 array
	 */
	protected $_path = null;

	/**
	 * Event call to determine if this plugin should return data
	 *
	 * @param   string  $alias
	 * @return  array   Plugin name and title
	 */
	public function &onProjectAreas($alias = null)
	{
		$area = array(
			'name'    => 'feed',
			'alias'   => null,
			'title'   => Lang::txt('PLG_PROJECTS_UPDATES'),
			'submenu' => null,
			'show'    => true,
			'icon'    => 'f053'
		);
		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param   object  $model  Project
	 * @return  array   integer
	 */
	public function &onProjectCount($model)
	{
		// New activity count
		$counts['feed'] = $model->newCount();

		return $counts;
	}

	/**
	 * Event call to return data for a specific project
	 *
	 * @param   object  $model   Project model
	 * @param   string  $action  Plugin task
	 * @param   string  $areas   Plugins to return data
	 * @return  array   Return array of html
	 */
	public function onProject($model, $action = '', $areas = null)
	{
		$returnhtml = true;

		$arr = array(
			'html'     =>'',
			'metadata' =>''
		);

		// Get this area details
		$this->_area = $this->onProjectAreas();

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (empty($this->_area) || !in_array($this->_area['name'], $areas))
			{
				return;
			}
		}
		// Check that project exists
		if (!$model->exists())
		{
			return $arr;
		}

		// Check authorization
		if (!$model->access('member'))
		{
			return $arr;
		}

		// Model
		$this->model = $model;

		// Are we returning HTML?
		if ($returnhtml)
		{
			$ajax = Request::getInt('ajax', 0);

			// Record page visit
			if (!$ajax)
			{
				// First-time visit, record join activity
				$model->recordFirstJoinActivity();

				// Record page visit
				$model->recordVisit();
			}

			// Hide welcome screen?
			$c = Request::getInt('c', 0);
			if ($c)
			{
				$model->member()->saveParam(
					$model->get('id'),
					User::get('id'),
					$param = 'hide_welcome',
					1
				);
				App::redirect(Route::url($model->link()));
				return;
			}

			// Set vars
			$this->_config   = $model->config();
			$this->_task     = Request::getVar('action', '');
			$this->_database = App::get('db');
			$this->_uid      = User::get('id');

			switch ($this->_task)
			{
				case 'delete':
					$arr['html'] = $this->_delete();
					break;
				case 'save':
					$arr['html'] = $this->_save();
					break;
				case 'savecomment':
					$arr['html'] = $this->_saveComment();
					break;
				case 'deletecomment':
					$arr['html'] = $this->_deleteComment();
					break;
				case 'update':
					$arr['html'] = $this->updateFeed();
					break;
				case 'page':
				default:
					$arr['html'] = $this->page();
					break;
			}
		}

		// Return data
		return $arr;
	}

	/**
	 * Event call to get side content
	 *
	 * @param   object  $model
	 * @param   string  $area
	 * @return  mixed
	 */
	public function onProjectExtras($model, $area)
	{
		// Check if our area is the one we want to return results for
		if ($area != 'feed')
		{
			return;
		}

		// No suggestions for read-only users
		if (!$model->access('content'))
		{
			return false;
		}

		// Allow to place custom modules on project pages
		$html = \Hubzero\Module\Helper::renderModules('projectpage');

		// Side blocks from other plugins?
		$sections = Event::trigger('projects.onProjectMiniList', array($model));

		if (!empty($sections))
		{
			// Show subscription to feed (new)
			$subscribe = Event::trigger('projects.onProjectMember', array($model));

			$html .= !empty($subscribe[0]) ? $subscribe[0] : null;
			foreach ($sections as $section)
			{
				$html .= !empty($section) ? $section : null;
			}
		}

		return $html;
	}

	/**
	 * Event call to get plugin notification
	 *
	 * @param   object  $model
	 * @param   string  $area
	 * @return  mixed
	 */
	public function onProjectNotification($model, $area)
	{
		// Check if our area is the one we want to return results for
		if ($area != 'feed')
		{
			return;
		}

		$html = '';

		// Acting member
		$member = $model->member();
		if (!is_object($member->params))
		{
			$member->params = new \Hubzero\Config\Registry($member->params);
		}

		// Show welcome screen?
		$showWelcome = $member && is_object($member->params) && $member->params->get('hide_welcome') == 0  ? 1 : 0;

		// Show welcome banner with suggestions
		if ($showWelcome)
		{
			// Get suggestions
			$suggestions = \Components\Projects\Helpers\Html::getSuggestions($model);

			// Display welcome message
			$view = $this->view('_welcome', 'modules')
				->set('option', $this->_option)
				->set('suggestions', $suggestions)
				->set('model', $model);

			$html .= $view->loadTemplate();
		}

		return $html;
	}

	/**
	 * View of project updates
	 *
	 * @return  string
	 */
	public function page()
	{
		$limit = intval($this->params->get('limit', 25));

		$filters = array(
			'role'    => $this->model->member()->role,
			'limit'   => Request::getVar('limit', $limit, 'request')
		);

		$objAC = $this->model->table('Activity');

		// Get count
		$total = $objAC->getActivities(
			$this->model->get('id'),
			$filters,
			1,
			$this->_uid
		);

		// Get activities
		$activities = $objAC->getActivities(
			$this->model->get('id'),
			$filters,
			0,
			$this->_uid
		);
		$activities = $this->_prepActivities(
			$activities,
			$filters,
			$limit
		);

		// Output html
		$view = $this->view('default', 'view')
			->set('params', $this->model->params)
			->set('option', $this->_option)
			->set('database', $this->_database)
			->set('model', $this->model)
			->set('uid', $this->_uid)
			->set('filters', $filters)
			->set('limit', $limit)
			->set('total', $activities)
			->set('activities', $activities)
			->set('title', $this->_area['title']);

		return $view->loadTemplate();
	}

	/**
	 * Save new blog entry
	 *
	 * @return  void  redirect
	 */
	protected function _save()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$managers  = Request::getInt('managers_only', 0);
		$entry     = trim(Request::getVar('blogentry', ''));
		$eid       = Request::getInt('eid', 0);
		$posted    = Date::toSql();
		$posted_by = $this->_uid;
		$isNew     = true;

		// Text clean-up
		$entry = \Hubzero\Utility\Sanitize::stripScripts($entry);
		$entry = \Hubzero\Utility\Sanitize::stripImages($entry);

		// Instantiate project microblog entry
		$objM = new \Components\Projects\Tables\Blog($this->_database);

		if ($eid)
		{
			$objM->load($eid);

			$managers  = $objM->managers_only;
			$posted    = $objM->posted;
			$posted_by = $objM->posted_by;
			$isNew     = false;
		}

		if ($entry)
		{
			$objM->projectid     = $this->model->get('id');
			$objM->blogentry     = $entry;
			$objM->managers_only = $managers;
			$objM->posted        = $posted;
			$objM->posted_by     = $posted_by;

			// Save new blog entry
			if (!$objM->store())
			{
				$this->setError($objM->getError());
			}
			else
			{
				$this->_msg = ($isNew ? Lang::txt('PLG_PROJECTS_BLOG_NEW_BLOG_ENTRY_SAVED') : Lang::txt('PLG_PROJECTS_BLOG_BLOG_ENTRY_SAVED'));
			}

			// Get new entry ID
			if (!$objM->id)
			{
				$objM->checkin();
			}

			// Record activity
			if ($objM->id && $isNew)
			{
				$aid = $this->model->recordActivity(
					Lang::txt('COM_PROJECTS_SAID'),
					$objM->id,
					'', '', 'blog', 1
				);

				// Store activity ID
				if ($aid)
				{
					$objM->activityid = $aid;
					$objM->store();
				}
			}
		}

		// Pass error or success message
		if ($this->getError())
		{
			Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($this->model->link()));
	}

	/**
	 * Delete blog entry
	 *
	 * @return  void  redirect
	 */
	protected function _delete()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			throw new Exception(Lang::txt('ALERTNOTAUTH'), 403);
		}

		// Incoming
		$tbl = trim(Request::getVar('tbl', 'activity'));
		$eid = Request::getInt('eid', 0);

		// Are we deleting a blog entry?
		if ($tbl == 'blog')
		{
			$objM = new \Components\Projects\Tables\Blog($this->_database);

			if ($eid && $objM->load($eid))
			{
				// Get associated commenting activities
				$objC = new \Components\Projects\Tables\Comment($this->_database);
				$activities = $objC->collectActivities($eid, $tbl);
				$activities[] = $objM->activityid;

				// Delete blog entry
				if ($objM->deletePost())
				{
					$this->_msg = Lang::txt('PLG_PROJECTS_BLOG_ENTRY_DELETED');

					// Delete all associated comments
					$comments = $objC->deleteComments($eid, $tbl);

					// Delete all associated activities
					foreach ($activities as $a)
					{
						$objAA = $this->model->table('Activity');
						$objAA->loadActivity($a, $this->model->get('id'));
						$objAA->deleteActivity();
					}
				}
			}
		}

		// Are we deleting activity?
		if ($tbl == 'activity')
		{
			$objAA = $this->model->table('Activity');
			$objAA->loadActivity($eid, $this->model->get('id'));

			if ($this->model->access('content') || $objAA->userid == $this->_uid)
			{
				// Get associated commenting activities
				$objC = new \Components\Projects\Tables\Comment($this->_database);
				$activities = $objC->collectActivities($eid, $tbl);

				if ($objAA->deleteActivity())
				{
					$this->_msg = Lang::txt('PLG_PROJECTS_BLOG_ENTRY_DELETED');

					// Delete all associated comments
					$comments = $objC->deleteComments($eid, $tbl);

					// Delete all associated activities
					foreach ($activities as $a)
					{
						$objAA = $this->model->table('Activity');
						$objAA->loadActivity($a, $this->model->get('id'));
						$objAA->deleteActivity();
					}
				}
			}
			else
			{
				// Unauthorized
				$this->setError(Lang::txt('COM_PROJECTS_ERROR_ACTION_NOT_AUTHORIZED'));
			}
		}

		// Pass error or success message
		if ($this->getError())
		{
			Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($this->model->link('feed')));
	}

	/**
	 * Update activity feed (load more entries)
	 *
	 * @return  string
	 */
	public function updateFeed()
	{
		$objAC = $this->model->table('Activity');

		$filters = array();

		$total = $objAC->getActivities(
			$this->model->get('id'),
			$filters,
			1,
			$this->_uid
		);
		$limit = intval($this->params->get('limit', 25));
		$filters['limit'] = Request::getVar('limit', $limit);

		if ($start = Request::getVar('recorded'))
		{
			$filters['recorded'] = $start;
			$filters['sortby']   = 'recorded';
			$filters['sortdir']  = 'ASC';
		}

		$activities = $objAC->getActivities(
			$this->model->get('id'),
			$filters,
			0,
			$this->_uid
		);

		// In this case, we're expecting JSON output
		// @TODO: Move to API
		if (isset($filters['recorded']))
		{
			$data = new stdClass();
			$data->activities = array();

			if (count($activities))
			{
				$objM  = new \Components\Projects\Tables\Blog($this->_database);
				$objC  = new \Components\Projects\Tables\Comment($this->_database);
				$objTD = new \Components\Projects\Tables\Todo($this->_database);

				$shown = array();

				// Loop through activities
				foreach ($activities as $a)
				{
					if (in_array($a->id, $shown))
					{
						continue;
					}

					$shown[] = $a->id;

					// Is this a comment?
					$class = $a->class ? $a->class : 'activity';

					// Display hyperlink
					if ($a->highlighted && $a->url)
					{
						$a->activity = str_replace($a->highlighted, '<a href="' . $a->url . '">' . $a->highlighted . '</a>', $a->activity);
					}

					// Set vars
					$ebody     = '';
					$eid       = $a->id;
					$etbl      = 'activity';
					$deletable = 0;
					$parent    = 0;
					$comments  = null;
					$content   = '';

					// Get blog entry
					if ($class == 'blog')
					{
						$blog = $objM->getEntries(
							$a->projectid,
							$bfilters = array('activityid' => $a->id),
							$a->referenceid
						);
						if (!$blog)
						{
							continue;
						}

						$content   = $blog[0]->blogentry;
						$ebody     = $this->drawBodyText($blog[0]->blogentry);
						$eid       = $a->referenceid;
						$etbl      = 'blog';
						$deletable = 1;
					}
					elseif ($class == 'todo')
					{
						$todo = $objTD->getTodos(
							$a->projectid,
							$tfilters = array('activityid' => $a->id),
							$a->referenceid
						);
						if (!$todo)
						{
							continue;
						}

						$content = $todo[0]->details ? $todo[0]->details : $todo[0]->content;
						$ebody   = $this->drawBodyText($content);
						$eid     = $a->referenceid;
						$etbl    = 'todo';
					}
					else if ($a->class == 'quote')
					{
						$comment = $objC->getComments(null, 'blog', $a->id);
						if (!$comment)
						{
							continue;
						}

						$objM->load($comment->itemid);

						$content = $comment->comment;
						$ebody   = $this->drawBodyText($content);
						$eid     = $a->referenceid;
						$etbl    = 'quote';
						$parent  = $objM->activityid;
					}

					// Get/parse & save item preview if available
					$preview = empty($this->miniView) ? $this->getItemPreview($class, $a) : '';

					// Is user allowed to delete item?
					$deletable = empty($this->miniView)
						&& $deletable
						&& $this->model->access('content')
						&& ($a->userid == $this->_uid or $this->model->access('manager'))
						? 1 : 0;

					$deletable = $this->model->access('manager') ? 1 :$deletable;

					$prep = array(
						'activity'   => $a,
						'eid'        => $eid,
						'etbl'       => $etbl,
						'body'       => $ebody,
						'raw'        => $content,
						'deletable'  => $deletable,
						'comments'   => $comments,
						'class'      => $class,
						'preview'    => $preview,
						'parent'     => $parent
					);

					if ($a->class == 'quote')
					{
						$prep['body'] = $this->view('_comment', 'activity')
							->set('option', $this->_option)
							->set('model', $this->model)
							->set('activity', $prep)
							->set('uid', $this->_uid)
							->set('comment', $comment)
							->set('edit', true)
							->loadTemplate();
					}
					else
					{
						$prep['body'] = $this->view('_activity', 'activity')
							->set('option', $this->_option)
							->set('model', $this->model)
							->set('activity', $prep)
							->set('uid', $this->_uid)
							->loadTemplate();
					}

					$data->activities[] = $prep;
				}
			}

			ob_clean();
			header('Content-type: text/plain');
			echo json_encode($data);
			exit();
		}

		$activities = $this->_prepActivities(
			$activities,
			$filters,
			$limit
		);

		$view = $this->view('default', 'activity')
			->set('option', $this->_option)
			->set('model', $this->model)
			->set('filters', $filters)
			->set('limit', $limit)
			->set('total', $activities)
			->set('activities', $activities)
			->set('uid', $this->_uid)
			->set('database', $this->_database)
			->set('title', $this->_area['title']);

		return $view->loadTemplate();
	}

	/**
	 * Activity data in multiple projects (members/groups plugins)
	 *
	 * @param   string   $area
	 * @param   object   $model
	 * @param   array    $projects
	 * @param   integer  $uid
	 * @param   array    $filters  Query filters
	 * @return  string
	 */
	public function onShared($area, $model, $projects, $uid, $filters)
	{
		// Check if our area is the one we want to return results for
		if ($area != 'feed')
		{
			return '';
		}

		$this->model     = $model;
		$this->_database = App::get('db');
		$this->_uid      = $uid;
		$this->miniView  = true;

		$limit = (isset($filters['limit']) ? $filters['limit'] : 0);

		// Get and sort activities
		$objAC = $this->model->table('Activity');

		$activities = $objAC->getActivities(0, $filters, 0, $uid, $projects);
		$activities = $this->_prepActivities(
			$activities,
			$filters,
			$limit
		);

		// Get total
		$total = $objAC->getActivities(0, array(), 1, $uid, $projects);

		// Output HTML
		$view = $this->view('shared', 'activity')
			->set('limit', $limit)
			->set('filters', $filters)
			->set('activities', $activities)
			->set('total', $total)
			->set('uid', $this->_uid)
			->set('model', $model);

		return $view->loadTemplate();
	}

	/**
	 * Collect activity data
	 *
	 * @param   array    $activities
	 * @param   array    $filters     Query filters
	 * @param   integer  $limit       Number of entries
	 * @return  array
	 */
	protected function _prepActivities($activities, $filters, $limit)
	{
		$objAC = $this->model->table('Activity');

		// Instantiate some classes
		$objM  = new \Components\Projects\Tables\Blog($this->_database);
		$objC  = new \Components\Projects\Tables\Comment($this->_database);
		$objTD = new \Components\Projects\Tables\Todo($this->_database);

		// Collectors
		$shown   = array();
		$newc    = array();
		$skipped = array();
		$prep    = array();

		// Loop through activities
		if (is_array($activities) && count($activities) > 0)
		{
			foreach ($activities as $a)
			{
				// Is this a comment?
				if ($a->class == 'quote')
				{
					// Get comment
					$c = $objC->getComments(null, null, $a->id);
					if (!$c)
					{
						continue;
					}

					// Bring up commented item
					$needle = array('id' => $c->parent_activity);
					$key = \Components\Projects\Helpers\Html::myArraySearch($needle, $activities);
					$shown[] = $a->id;
					if (!$key)
					{
						// get and add parent activity
						$filters['id'] = $c->parent_activity;
						$pa = $objAC->getActivities($a->projectid, $filters, 0, $this->_uid);
						if ($pa && count($pa) > 0)
						{
							$a = $pa[0];
						}
					}
					else
					{
						$a = $activities[$key];
					}
					$a->new = isset($c->newcount) ? $c->newcount : 0;
				}

				if (!in_array($a->id, $shown))
				{
					$shown[] = $a->id;
					$class = $a->class ? $a->class : 'activity';

					// Display hyperlink
					if ($a->highlighted && $a->url)
					{
						$a->activity = str_replace($a->highlighted, '<a href="' . $a->url . '">' . $a->highlighted . '</a>', $a->activity);
					}

					// Set vars
					$ebody     = '';
					$eid       = $a->id;
					$etbl      = 'activity';
					$deletable = 0;
					$content   = '';

					// Get blog entry
					if ($class == 'blog')
					{
						$blog = $objM->getEntries(
							$a->projectid,
							$bfilters = array('activityid' => $a->id),
							$a->referenceid
						);
						if (!$blog)
						{
							continue;
						}

						$content   = $blog[0]->blogentry;
						$ebody     = $this->drawBodyText($blog[0]->blogentry);
						$eid       = $a->referenceid;
						$etbl      = 'blog';
						$deletable = 1;
					}
					elseif ($class == 'todo')
					{
						$todo = $objTD->getTodos(
							$a->projectid,
							$tfilters = array('activityid' => $a->id),
							$a->referenceid
						);
						if (!$todo)
						{
							continue;
						}

						$content = $todo[0]->details ? $todo[0]->details : $todo[0]->content;
						$ebody   = $this->drawBodyText($content);
						$eid     = $a->referenceid;
						$etbl    = 'todo';
					}

					// Get/parse & save item preview if available
					$preview = empty($this->miniView) ? $this->getItemPreview($class, $a) : '';

					// Get comments
					if ($a->commentable)
					{
						$comments = $objC->getComments($eid, $etbl);
					}
					else
					{
						$comments = null;
					}

					// Is user allowed to delete item?
					$deletable = empty($this->miniView)
						&& $deletable
						&& $this->model->access('content')
						&& ($a->userid == $this->_uid or $this->model->access('manager'))
						? 1 : 0;

					$deletable = $this->model->access('manager') ? 1 :$deletable;

					$prep[] = array(
						'activity'  => $a,
						'eid'       => $eid,
						'etbl'      => $etbl,
						'body'      => $ebody,
						'raw'       => $content,
						'deletable' => $deletable,
						'comments'  => $comments,
						'class'     => $class,
						'preview'   => $preview
					);
				}
			}
		}

		return $prep;
	}

	/**
	 * Display 'more' link if text is too long
	 *
	 * @param   string  $body  Text body to shorten
	 * @return  mixed
	 */
	public function drawBodyText($body = null)
	{
		if (!$body)
		{
			return false;
		}

		$isHtml = false;
		if (preg_match('/^(<([a-z]+)[^>]*>.+<\/([a-z]+)[^>]*>|<(\?|%|([a-z]+)[^>]*).*(\?|%|)>)/is', $body))
		{
			$isHtml = true;
		}

		$shorten = ($body && strlen(strip_tags($body)) > 250) ? 1 : 0;
		$shortBody = $shorten ? \Hubzero\Utility\String::truncate($body, 250, array('html' => true)) : $body;

		// Embed links
		$body      = \Components\Projects\Helpers\Html::replaceUrls($body, 'external');
		$shortBody = \Components\Projects\Helpers\Html::replaceUrls($shortBody, 'external');

		// Emotions (new)
		$body      = \Components\Projects\Helpers\Html::replaceEmoIcons($body);
		$shortBody = \Components\Projects\Helpers\Html::replaceEmoIcons($shortBody);

		// Style body text
		if (!$isHtml)
		{
			$shortBody = preg_replace("/\n/", '<br />', trim($shortBody));
		}
		$ebody  = '<div class="body';
		$ebody .= strlen($shortBody) > 50 || $isHtml ? ' newline' : ' sameline';
		$ebody .= '">' . $shortBody;
		if ($shorten)
		{
			$ebody .= ' <a href="#" class="more-content">' . Lang::txt('COM_PROJECTS_MORE') . '</a>';
		}
		$ebody .= '</div>';

		if ($shorten)
		{
			if (!$isHtml)
			{
				$body = preg_replace("/\n/", '<br />', trim($body));
			}
			$ebody .= '<div class="fullbody hidden">' . $body . '</div>';
		}

		return $ebody;
	}

	/**
	 * Get preview
	 *
	 * @param   string  $type      Item type (files, notes etc.)
	 * @param   object  $activity  Individual activity
	 * @param   string  $body
	 * @param   bool    $reload
	 * @return  string
	 */
	public function getItemPreview($type = null, $activity = null, $body = null, $reload = false)
	{
		$ref = $activity->referenceid;

		// Do we have a saved preview?
		if ($activity->preview && !$reload)
		{
			return $activity->preview;
		}

		if ($body)
		{
			return $this->drawBodyText($body);
		}

		if (!$ref || !$type)
		{
			return false;
		}

		$previewBody = null;

		switch ($type)
		{
			case 'files':
				$previewBody = $this->_getFilesPreview($ref);
				break;

			case 'notes':
				$previewBody = $this->_getNotesPreview($ref);
				break;
		}

		// Save preview
		if ($previewBody)
		{
			$objA = $this->model->table('Activity');
			$objA->saveActivityPreview($activity->id, $previewBody);
		}

		return $previewBody;
	}

	/**
	 * Get Note Previews
	 *
	 * @param   string  $ref  Reference to note
	 * @return  bool
	 */
	protected function _getNotesPreview($ref = '')
	{
		// TBD
		return false;
	}

	/**
	 * Get File Previews
	 *
	 * @param   string  $ref  Reference to files
	 * @return  mixed
	 */
	protected function _getFilesPreview($ref = '')
	{
		if (!$ref)
		{
			return false;
		}

		if (!$this->_path)
		{
			// Get project file path
			$this->_path = \Components\Projects\Helpers\Html::getProjectRepoPath($this->model->get('alias'));
		}

		// We do need project file path
		if (!$this->_path || !is_dir($this->_path))
		{
			return false;
		}

		$files     = explode(',', $ref);
		$selected  = array();
		$maxHeight = 0;
		$minHeight = 0;
		$minWidth  = 0;
		$maxWidth  = 0;

		$imagepath = trim($this->_config->get('imagepath', '/site/projects'), DS);
		$to_path = DS . $imagepath . DS . strtolower($this->model->get('alias')) . DS . 'preview';

		foreach ($files as $item)
		{
			$parts = explode(':', $item);
			$file  = count($parts) > 1 ? $parts[1] : $parts[0];
			$hash  = count($parts) > 1 ? $parts[0] : null;

			if ($hash)
			{
				// Only preview mid-size images from now on
				$hashed = md5(basename($file) . '-' . $hash) . '.png';

				if (is_file(PATH_APP. $to_path . DS . $hashed))
				{
					$preview['image'] = $hashed;
					$preview['url']   = null;
					$preview['title'] = basename($file);

					// Get image properties
					list($width, $height, $type, $attr) = getimagesize(PATH_APP. $to_path . DS . $hashed);

					$preview['width'] = $width;
					$preview['height'] = $height;
					$preview['orientation'] = $width > $height ? 'horizontal' : 'vertical';

					// Record min and max width and height to build image grid
					if ($height >= $maxHeight)
					{
						$maxHeight = $height;
					}
					if ($height && $height <= $minHeight)
					{
						$minHeight = $height;
					}
					else
					{
						$minHeight = $height;
					}
					if ($width > $maxWidth)
					{
						$maxWidth = $width;
					}

					$selected[] = $preview;
				}
			}
		}

		// No files for preview
		if (empty($selected))
		{
			return false;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  => 'projects',
				'element' => $this->_name,
				'name'    => 'preview',
				'layout'  => 'files'
			)
		);
		$view->maxHeight = $maxHeight;
		$view->maxWidth  = $maxWidth;
		$view->minHeight = ($minHeight > 400) ? 400 : $minHeight;
		$view->selected  = $selected;
		$view->option    = $this->_option;
		$view->model     = $this->model;
		return $view->loadTemplate();
	}

	/**
	 * Save comment
	 *
	 * @return  void
	 */
	protected function _saveComment()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}

		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		// Incoming
		$itemid          = Request::getInt('itemid', 0, 'post');
		$tbl             = trim(Request::getVar('tbl', 'activity', 'post'));
		$comment         = trim(Request::getVar('comment', '', 'post'));
		$parent_activity = Request::getInt('parent_activity', 0, 'post');
		$cid             = Request::getInt('cid', 0, 'post');
		$created         = Date::toSql();
		$created_by      = $this->_uid;
		$isNew           = true;

		// Clean-up
		$comment = \Hubzero\Utility\Sanitize::stripScripts($comment);
		$comment = \Hubzero\Utility\Sanitize::stripImages($comment);

		// Instantiate comment
		$objC = new \Components\Projects\Tables\Comment($this->_database);

		if ($cid)
		{
			$objC->load($cid);

			$itemid     = $objC->itemid;
			$tbl        = $objC->tbl;
			$created    = $objC->created;
			$created_by = $objC->created_by;
			$parent_activity = $objC->parent_activity;
			$isNew      = false;
		}

		if ($comment)
		{
			$objC->itemid          = $itemid;
			$objC->tbl             = $tbl;
			$objC->parent_activity = $parent_activity;
			$objC->comment         = $comment;
			$objC->created         = $created;
			$objC->created_by      = $created_by;

			if (!$objC->store())
			{
				$this->setError($objC->getError());
			}
			else
			{
				$this->_msg = ($isNew ? Lang::txt('PLG_PROJECTS_BLOG_COMMENT_POSTED') : Lang::txt('PLG_PROJECTS_BLOG_COMMENT_UPDATED'));
			}

			// Get new entry ID
			if (!$objC->id)
			{
				$objC->checkin();
			}

			// Record activity
			if ($isNew)
			{
				$what = $tbl == 'blog' ? Lang::txt('COM_PROJECTS_BLOG_POST') : Lang::txt('COM_PROJECTS_AN_ACTIVITY');
				$what = $tbl == 'todo' ? Lang::txt('COM_PROJECTS_TODO_ITEM') : $what;
				$url  = $tbl == 'todo' ? Route::url($this->model->link('todo') . '&action=view&todoid=' . $itemid) : Route::url($this->model->link('feed')) . '#tr_' . $parent_activity; // same-page link

				$aid  = $this->model->recordActivity(
					Lang::txt('COM_PROJECTS_COMMENTED') . ' ' . Lang::txt('COM_PROJECTS_ON') . ' ' . $what,
					$objC->id,
					$what,
					$url,
					'quote',
					0
				);

				// Store activity ID
				if ($aid)
				{
					$objC->activityid = $aid;
					$objC->store();
				}
			}
		}

		// Pass error or success message
		if ($this->getError())
		{
			Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($this->model->link()));
	}

	/**
	 * Delete comment
	 *
	 * @return  void
	 */
	protected function _deleteComment()
	{
		// Check permission
		if (!$this->model->access('content'))
		{
			App::abort(403, Lang::txt('ALERTNOTAUTH'));
		}

		// Incoming
		$cid = Request::getInt('cid', 0);

		// Instantiate comment
		$objC = new \Components\Projects\Tables\Comment($this->_database);

		if ($objC->load($cid))
		{
			$activityid = $objC->activityid;

			// delete comment
			if ($objC->deleteComment())
			{
				$this->_msg = Lang::txt('PLG_PROJECTS_BLOG_COMMENT_DELETED');
			}

			// delete associated activity
			$objAA = $this->model->table('Activity');
			if ($activityid && $objAA->load($activityid))
			{
				$objAA->deleteActivity();
			}
		}

		// Pass error or success message
		if ($this->getError())
		{
			Notify::message($this->getError(), 'error', 'projects');
		}
		elseif (!empty($this->_msg))
		{
			Notify::message($this->_msg, 'success', 'projects');
		}

		// Redirect
		App::redirect(Route::url($this->model->link()));
	}
}
