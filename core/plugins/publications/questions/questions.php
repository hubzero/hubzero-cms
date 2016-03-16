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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Publications Plugin class for questions
 */
class plgPublicationsQuestions extends \Hubzero\Plugin\Plugin
{
	/**
	 * Affects constructor behavior. If true, language files will be loaded automatically.
	 *
	 * @var  boolean
	 */
	protected $_autoloadLanguage = true;

	/**
	 * Return the alias and name for this category of content
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function &onPublicationAreas($publication, $version = 'default', $extended = true)
	{
		$areas = array();

		if ($publication->_category->_params->get('plg_questions') && $extended)
		{
			$areas['questions'] = Lang::txt('PLG_PUBLICATION_QUESTIONS');
		}

		return $areas;
	}

	/**
	 * Return data on a resource view (this will be some form of HTML)
	 *
	 * @param   object   $publication  Current publication
	 * @param   string   $option       Name of the component
	 * @param   array    $areas        Active area(s)
	 * @param   string   $rtrn         Data to be returned
	 * @param   string   $version      Version name
	 * @param   boolean  $extended     Whether or not to show panel
	 * @return  array
	 */
	public function onPublication($publication, $option, $areas, $rtrn='all', $version = 'default', $extended = true)
	{
		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onPublicationAreas($publication))
			 && !array_intersect($areas, array_keys($this->onPublicationAreas($publication))))
			{
				$rtrn = 'metadata';
			}
		}

		if (!$publication->_category->_params->get('plg_questions') || !$extended)
		{
			return $arr;
		}

		$this->publication = $publication;
		$this->option      = $option;

		// Get a needed library
		require_once(PATH_CORE . DS . 'components' . DS . 'com_answers' . DS . 'models' . DS . 'question.php');

		// Get all the questions for this publication
		$this->filters = array(
			'sort_Dir' => 'desc',
			'limit'    => Request::getInt('limit', 0),
			'start'    => Request::getInt('limitstart', 0),
			'search'   => Request::getVar('q', ''),
			'filterby' => Request::getVar('filterby', ''),
			'sortby'   => Request::getVar('sortby', 'withinplugin')
		);

		$identifier = $this->publication->identifier();
		$this->filters['tag']    = $this->publication->isTool() ? 'tool:' . $identifier : 'publication:' . $identifier;
		$this->filters['rawtag'] = $this->publication->isTool() ? 'tool:' . $identifier : 'publication:' . $identifier;

		$this->count = $this->_find()->count();

		$arr['count'] = $this->count;
		$arr['name']  = 'questions';

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
			$view = $this->view('default', 'metadata')
				->set('publication', $this->publication)
				->set('count', $this->count);

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
	 * Show a list of questions attached to this publication
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
			->set('publication', $this->publication)
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
			$rtrn = Request::getVar('REQUEST_URI', Route::url('index.php?option=' . $this->option . '&id=' . $this->publication->id . '&active=' . $this->_name, false, true), 'server');

			App::redirect(
				Route::url('index.php?option=com_users&view=login&return=' . base64_encode($rtrn)),
				Lang::txt('PLG_PUBLICATIONS_QUESTIONS_LOGIN_TO_ASK_QUESTION'),
				'warning'
			);
			return;
		}

		if (!is_object($row))
		{
			$row  = new \Components\Answers\Models\Question();
		}

		// Are we banking?
		$banking = Component::params('com_members')->get('bankAccounts');

		$funds = 0;

		if ($banking)
		{
			$BTL = new \Hubzero\Bank\Teller(User::get('id'));
			$funds = $BTL->summary() - $BTL->credit_summary();
			$funds = ($funds > 0) ? $funds : 0;
		}

		$view = $this->view('new', 'question')
			->set('option', $this->option)
			->set('publication', $this->publication)
			->set('row', $row)
			->set('tag', $this->filters['tag'])
			->set('banking', $banking)
			->set('funds', $funds);

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

		// Incoming
		$tags   = Request::getVar('tags', '');
		$funds  = Request::getInt('funds', 0);
		$reward = Request::getInt('reward', 0);

		// Initiate class and bind posted items to database fields
		$fields = Request::getVar('question', array(), 'post', 'none', 2);

		$row = \Components\Answers\Models\Question::oneOrNew($fields['id'])->set($fields);

		$banking = Component::params('com_members')->get('bankAccounts');

		if ($reward && $banking)
		{
			$row->set('reward', 1);
		}

		// If offering a reward, do some checks
		if ($reward)
		{
			// Is it an actual number?
			if (!is_numeric($reward))
			{
				$this->setError(Lang::txt('COM_ANSWERS_REWARD_MUST_BE_NUMERIC'));
				return $this->_new($row);
			}

			// Are they offering more than they can afford?
			if ($reward > $funds)
			{
				$this->setError(Lang::txt('COM_ANSWERS_INSUFFICIENT_FUNDS'));
				return $this->_new($row);
			}
		}

		// Store new content
		if (!$row->save())
		{
			$this->setError($row->getError());
			return $this->_new($row);
		}

		// Hold the reward for this question if we're banking
		if ($reward && $banking)
		{
			$BTL = new \Hubzero\Bank\Teller(User::get('id'));
			$BTL->hold($reward, Lang::txt('COM_ANSWERS_HOLD_REWARD_FOR_BEST_ANSWER'), 'answers', $row->get('id'));
		}

		// Add the tags
		$row->tag($tags);

		// Add the tag to link to the publication
		$identifier = $this->publication->get('alias') ? $this->publication->get('alias') : $this->publication->get('id');
		$tag = $this->publication->isTool() ?  'tool:' . $identifier : 'publication:' . $identifier;

		$row->addTag($tag, User::get('id'), ($this->publication->isTool() ? 0 : 1));

		// Redirect to the question
		App::redirect(
			Route::url($this->publication->link() . '&active=questions')
		);
	}
}
