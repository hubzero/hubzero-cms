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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Resources Plugin class for questions and answers
 */
class plgResourcesQuestions extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var    boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param      object $resource Current resource
	 * @return     array
	 */
	public function &onResourcesAreas($model)
	{
		$areas = array();

		if (isset($model->resource->toolpublished) || isset($model->resource->revision))
		{
			if (isset($model->resource->thistool)
			 && $model->resource->thistool
			 && ($model->resource->revision=='dev' or !$model->resource->toolpublished))
			{
				$model->type->params->set('plg_questions', 0);
			}
		}
		if ($model->type->params->get('plg_questions')
			&& $model->access('view-all'))
		{
			$areas['questions'] = Lang::txt('PLG_RESOURCES_QUESTIONS');
		}

		return $areas;
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
		$arr = array(
			'area'     => $this->_name,
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onResourcesAreas($model))
			 && !array_intersect($areas, array_keys($this->onResourcesAreas($model))))
			{
				$rtrn = 'metadata';
			}
		}
		if (!$model->type->params->get('plg_questions'))
		{
			return $arr;
		}

		$this->database = App::get('db');
		$this->model    = $model;
		$this->option   = $option;

		// Get a needed library
		require_once(PATH_CORE . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'question.php');

		// Get all the questions for this tool
		$this->filters = array(
			'limit'    => Request::getInt('limit', 0),
			'start'    => Request::getInt('limitstart', 0),
			'tag'      => ($this->model->isTool() ? 'tool:' . $this->model->resource->alias : 'resource:' . $this->model->resource->id),
			'search'   => Request::getVar('q', ''),
			'filterby' => Request::getVar('filterby', ''),
			'sortby'   => Request::getVar('sortby', 'withinplugin'),
			'sort_Dir' => 'desc'
		);

		$this->count = $this->_find()->count();

		// Load component language file
		Lang::load('com_answers') ||
		Lang::load('com_answers', PATH_CORE . DS . 'components' . DS . 'com_answers' . DS . 'site');

		// Are we returning HTML?
		if ($rtrn == 'all' || $rtrn == 'html')
		{
			switch (strtolower(Request::getWord('action', 'browse')))
			{
				case 'save':
					$arr['html'] = $this->_save();
				break;

				case 'new':
					$arr['html'] = $this->_new();
				break;

				case 'browse':
				default:
					$arr['html'] = $this->_browse();
				break;
			}
		}

		// Are we returning metadata?
		if ($rtrn == 'all' || $rtrn == 'metadata')
		{
			$view = $this->view('default', 'metadata');
			$view->resource = $this->model->resource;
			$view->count    = $this->count;

			$arr['metadata'] = $view->loadTemplate();
		}

		// Return output
		return $arr;
	}

	/**
	 * Parse a list of filters to return data for
	 *
	 * @return  object
	 */
	private function _find()
	{
		$records = \Components\Answers\Models\Question::all()
			->including(['responses', function ($response)
			{
				$response
					->select('id')
					->select('question_id')
					->where('state', '!=', 2);
			}]);

		if ($this->filters['tag'])
		{
			$cloud = new \Components\Answers\Models\Tags();
			$tags = $cloud->parse($this->filters['tag']);

			$records
				->select('#__answers_questions.*')
				->join('#__tags_object', '#__tags_object.objectid', '#__answers_questions.id')
				->join('#__tags', '#__tags.id', '#__tags_object.tagid')
				->whereEquals('#__tags_object.tbl', 'answers')
				->whereIn('#__tags.tag', $tags);
		}

		if ($this->filters['search'])
		{
			$this->filters['search'] = strtolower((string)$this->filters['search']);

			$records->whereLike('subject', $this->filters['search'], 1)
					->orWhereLike('question', $this->filters['search'], 1)
					->resetDepth();
		}

		if ($this->filters['filterby'] == 'open')
		{
			$records->whereEquals('state', 0);
		}
		if ($this->filters['filterby'] == 'closed')
		{
			$records->whereEquals('state', 1);
		}
		if (!$this->filters['filterby'] || $this->filters['filterby'] == 'both')
		{
			$records->where('state', '<', 2);
		}

		return $records;
	}

	/**
	 * Show a list of questions attached to this resource
	 *
	 * @return  string
	 */
	private function _browse()
	{
		switch ($this->filters['sortby'])
		{
			case 'rewards': $order = 'points'; break;
			case 'votes':   $order = 'helpful'; break;
			case 'date':
			default:        $order = 'created'; break;
		}

		// Get results
		$results = $this->_find()
			->limit($this->params->get('display_limit', 10))
			->order($order, $this->filters['sort_Dir'])
			->paginated()
			->rows();

		$view = $this->view('default', 'browse')
			->setError($this->getErrors())
			->set('option', $this->option)
			->set('resource', $this->model->resource)
			->set('banking', Component::params('com_members')->get('bankAccounts'))
			->set('infolink', Component::params('com_answers')->get('infolink', '/kb/points/'))
			->set('rows', $results)
			->set('count', $this->count);

		return $view->loadTemplate();
	}

	/**
	 * Display a form for adding a question
	 *
	 * @param   object  $row
	 * @return  string
	 */
	private function _new($row=null)
	{
		// Login required
		if (User::isGuest())
		{
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->option . '&id=' . $this->model->resource->id . '&active=' . $this->_name, false, true), 'server');

			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				Lang::txt('PLG_RESOURCES_QUESTIONS_LOGIN_TO_ASK_QUESTION'),
				'warning'
			);
			return;
		}

		$view = $this->view('new', 'question');
		$view->option   = $this->option;
		$view->resource = $this->model->resource;
		if (!is_object($row))
		{
			$row  = new \Components\Answers\Models\Question();
		}
		$view->row  = $row;
		$view->tag      = $this->filters['tag'];

		// Are we banking?
		$upconfig = Component::params('com_members');
		$view->banking = $upconfig->get('bankAccounts');

		$view->funds = 0;
		if ($view->banking)
		{
			$BTL = new \Hubzero\Bank\Teller($this->database, User::get('id'));
			$funds = $BTL->summary() - $BTL->credit_summary();
			$view->funds = ($funds > 0) ? $funds : 0;
		}

		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Save a question and redirect to the main listing when done
	 *
	 * @return  void
	 */
	private function _save()
	{
		// Login required
		if (User::isGuest())
		{
			return $this->_browse();
		}

		// Check for request forgeries
		Request::checkToken();

		Lang::load('com_answers');

		// Incoming
		$tags   = Request::getVar('tags', '');
		$funds  = Request::getInt('funds', 0);
		$reward = Request::getInt('reward', 0);

		// If offering a reward, do some checks
		if ($reward)
		{
			// Is it an actual number?
			if (!is_numeric($reward))
			{
				App::abort(500, Lang::txt('COM_ANSWERS_REWARD_MUST_BE_NUMERIC'));
				return;
			}
			// Are they offering more than they can afford?
			if ($reward > $funds)
			{
				App::abort(500, Lang::txt('COM_ANSWERS_INSUFFICIENT_FUNDS'));
				return;
			}
		}

		// Initiate class and bind posted items to database fields
		$fields = Request::getVar('question', array(), 'post', 'none', 2);

		$row = \Components\Answers\Models\Question::oneOrNew($fields['id'])->set($fields);

		if ($reward && $this->banking)
		{
			$row->set('reward', 1);
		}

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->_new($row);
		}

		// Hold the reward for this question if we're banking
		if ($reward && $this->banking)
		{
			$BTL = new \Hubzero\Bank\Teller($this->database, User::get('id'));
			$BTL->hold($reward, Lang::txt('COM_ANSWERS_HOLD_REWARD_FOR_BEST_ANSWER'), 'answers', $row->get('id'));
		}

		// Add the tags
		$row->tag($tags);

		// Add the tag to link to the resource
		$tag = ($this->model->isTool() ? 'tool:' . $this->model->resource->alias : 'resource:' . $this->model->resource->id);
		$row->addTag($tag, User::get('id'), ($this->model->isTool() ? 0 : 1));

		// Get users who need to be notified on every question
		$config = Component::params('com_answers');
		$apu = $config->get('notify_users', '');
		$apu = explode(',', $apu);
		$apu = array_map('trim', $apu);

		$receivers = array();

		// Get tool contributors if question is about a tool
		if ($tags)
		{
			$tags = explode(',', $tags);
			if (count($tags) > 0)
			{
				require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'author.php');
				require_once(PATH_CORE . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');

				$TA = new \Components\Tools\Tables\Author($this->database);
				$objV = new \Components\Tools\Tables\Version($this->database);

				if ($this->model->isTool())
				{
					$toolname = $this->model->resource->alias;

					$rev = $objV->getCurrentVersionProperty($toolname, 'revision');
					$authors = $TA->getToolAuthors('', 0, $toolname, $rev);
					if (count($authors) > 0)
					{
						foreach ($authors as $author)
						{
							$receivers[] = $author->uidNumber;
						}
					}
				}
			}
		}

		if (!empty($apu))
		{
			foreach ($apu as $u)
			{
				$user = User::getInstance($u);
				if ($user)
				{
					$receivers[] = $user->get('id');
				}
			}
		}
		$receivers = array_unique($receivers);

		// Send the message
		if (!empty($receivers))
		{
			// Send a message about the new question to authorized users (specified admins or related content authors)
			$from = array(
				'email'     => Config::get('mailfrom'),
				'name'      => Config::get('sitename') . ' ' . Lang::txt('COM_ANSWERS_ANSWERS'),
				'multipart' => md5(date('U'))
			);

			// Build the message subject
			$subject = Lang::txt('COM_ANSWERS_ANSWERS') . ', ' . Lang::txt('new question about content you author or manage');

			// Build the message
			$eview = new \Hubzero\Mail\View(array(
				'base_path' => PATH_CORE . DS . 'components' . DS . 'com_answers' . DS . 'site',
				'name'      => 'emails',
				'layout'    => 'question_plaintext'
			));
			$eview->option   = 'com_answers';
			$eview->sitename = Config::get('sitename');
			$eview->question = $row;
			$eview->id       = $row->get('id', 0);
			$eview->boundary = $from['multipart'];

			$message['plaintext'] = $eview->loadTemplate(false);
			$message['plaintext'] = str_replace("\n", "\r\n", $message['plaintext']);

			// HTML message
			$eview->setLayout('question_html');

			$message['multipart'] = $eview->loadTemplate();
			$message['multipart'] = str_replace("\n", "\r\n", $message['multipart']);

			if (!Event::trigger('xmessage.onSendMessage', array('new_question_admin', $subject, $message, $from, $receivers, 'com_answers')))
			{
				$this->setError(Lang::txt('COM_ANSWERS_MESSAGE_FAILED'));
			}
		}

		// Redirect to the question
		App::redirect(
			Route::url('index.php?option=' . $this->option . '&id=' . $this->model->resource->id . '&active=' . $this->_name)
		);
	}
}

