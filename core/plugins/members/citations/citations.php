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
 * @author    Alissa Nedossekina <alisa@purdue.edu>, Kevin Wojkovich <kevinw@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Include needed libs
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'citation.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'author.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'secondary.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'sponsor.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'format.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'type.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'tag.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'tagobject.php');
require_once(PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'models' . DS . 'importer.php');

use Hubzero\Config\Registry;
use Components\Tags\Models\Tag;
use Components\Tags\Models\Cloud;
use Components\Citations\Models\Citation;
use Components\Citations\Models\Author;
use Components\Citations\Models\Type;
use Components\Citations\Models\Format;
use Components\Citations\Models\Importer;

/**
 * Groups plugin class for citations
 */
class plgMembersCitations extends \Hubzero\Plugin\Plugin
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
	 * @param   object  $user    Current user
	 * @param   object  $member  Current member page
	 * @return  array
	 */
	public function &onMembersAreas($user, $member)
	{
		$areas = array(
			'citations' => Lang::txt('PLG_MEMBERS_CITATIONS'),
			'icon'      => '275D'
		);
		return $areas;
	}

	/**
	 * Perform actions when viewing a member profile
	 *
	 * @param   object  $user    Current user
	 * @param   object  $member  Current member page
	 * @param   string  $option  Start of records to pull
	 * @param   array   $areas   Active area(s)
	 * @return  array
	 */
	public function onMembers($user, $member, $option, $areas)
	{
		$returnhtml = true;

		// Check if our area is in the array of areas we want to return results for
		if (is_array($areas))
		{
			if (!array_intersect($areas, $this->onMembersAreas($user, $member))
			 && !array_intersect($areas, array_keys($this->onMembersAreas($user, $member))))
			{
				$returnhtml = false;
			}
		}

		$arr = array(
			'html'     => '',
			'metadata' => ''
		);

		$this->database = App::get('db');

		// Instantiate citations object and get count
		$obj = new \Components\Citations\Tables\Citation($this->database);
		$this->grand_total = $obj->getCount(array(
			'scope'    => 'member',
			'scope_id' => $member->get('id')
		), true);

		$arr['metadata']['count'] = $this->grand_total;

		//if we want to return content
		if ($returnhtml)
		{
			$this->member   = $member;
			$this->option   = $option;

			if (User::get('id') == $this->member->get('id'))
			{
				$this->params->set('access-manage', true);
			}

			$this->action = Request::getCmd('action', 'browse');

			if (!$this->params->get('access-manage'))
			{
				$this->action = 'browse';
			}

			if (in_array($this->action, array('import', 'upload', 'review', 'process', 'saved')))
			{
				include_once(Component::path('com_citations') . DS . 'models' . DS . 'importer.php');

				$this->importer = new \Components\Citations\Models\Importer(
					App::get('db'),
					App::get('filesystem'),
					App::get('config')->get('tmp_path') . DS . 'citations',
					App::get('session')->getId()
				);
				$this->importer->set('scope', 'member');
				$this->importer->set('scope_id', User::get('id'));
				$this->importer->set('user', User::get('id'));
				$this->importer->set('published', 0); //let the user decide if they want to publish or not
			}

			// Run task based on action
			switch ($this->action)
			{
				case 'save':     $arr['html'] .= $this->saveAction();     break;
				case 'add':
				case 'edit':     $arr['html'] .= $this->editAction();     break;
				case 'delete':   $arr['html'] .= $this->deleteAction();   break;
				case 'publish':  $arr['html'] .= $this->publishAction();  break;
				case 'browse':   $arr['html'] .= $this->browseAction();   break;
				case 'settings': $arr['html'] .= $this->settingsAction(); break;

				case 'import':   $arr['html'] .= $this->importAction();   break;
				case 'upload':   $arr['html'] .= $this->uploadAction();   break;
				case 'review':   $arr['html'] .= $this->reviewAction();   break;
				case 'process':  $arr['html'] .= $this->processAction();  break;
				case 'saved':    $arr['html'] .= $this->savedAction();    break;

				default:         $arr['html'] .= $this->browseAction();   break;
			}
		}

		// Return the output
		return $arr;
	}

	/**
	 * Display a list of all citations, with filtering&search options.
	 *
	 * @return  string  HTML
	 */
	private function browseAction()
	{
		 // Instantiate a new citations object
		$obj = $this->_filterHandler(Request::getVar('filters', array()), $this->member->get('id'));

		$count = clone $obj['citations'];
		$count = $count->count();
		$isAdmin = $this->member->get('id') == User::get('id');
		$config =  $this->member->params;

		$total = \Components\Citations\Models\Citation::all()
			->where('scope', '=', 'member')
			->where('scope_id', '=', $this->member->get('id'))
			->where('published', '!=', \Components\Citations\Models\Citation::STATE_DELETED)
			->count();

		if ($total == 0 && $isAdmin)
		{
			$view = $this->view('intro');
			$view->member = $this->member;
			$view->isAdmin = User::get('id') == $this->member->get('id');
		}
		elseif ((int) $count == 0 && $isAdmin && isset($display) && $total <= 0)
		{
			$view = $this->view('intro', 'browse');
			$view->group = $this->member;
			$view->isManager = ($this->authorized == 'manager') ? true : false;
		}
		else
		{
			// initialize the view
			$view = $this->view('browse');

			// push objects to the view
			$view->option      = $this->option;
			$view->member      = $this->member;
			$view->task        = $this->_name;
			$view->database    = $this->database;
			$view->title       = Lang::txt(strtoupper($this->_name));
			$view->isAdmin     = $isAdmin;
			$view->config      = $config;
			$view->grand_total = $total;

		}

		// get applied filters
		$view->filters = $obj['filters'];

		// only display published citations to non-managers.
		if ($view->isAdmin)
		{
			// get filtered citations
			$view->citations = $obj['citations']->paginated()->rows();
		}
		else
		{
			$view->citations = $obj['citations']
				->where('published', '=', \Components\Citations\Models\Citation::STATE_PUBLISHED)
				->paginated()
				->rows();
		}

		// get the earliest year we have citations for
		$view->earliest_year = 2001;

		// Affiliation filter
		$view->filterlist = array(
			'all'    => Lang::txt('PLG_MEMBERS_CITATIONS_ALL'),
			'aff'    => Lang::txt('PLG_MEMBERS_CITATIONS_AFFILIATED'),
			'nonaff' => Lang::txt('PLG_MEMBERS_CITATIONS_NONAFFILIATED'),
			'member' => Lang::txt('PLG_MEMBERS_CITATIONS_MEMBERCONTRIB')
		);

		// set default values for required filters for this view.
		$view->filters['search'] = isset($view->filters['search']) ? $view->filters['search'] : "";
		$view->filters['type'] = isset($view->filters['type']) ? $view->filters['type'] : "";
		$view->filters['tag'] = isset($view->filters['tag']) ? $view->filters['tag'] : "";
		$view->filters['author'] = isset($view->filters['author']) ? $view->filters['author'] : "";
		$view->filters['publishedin'] = isset($view->filters['publishedin']) ? $view->filters['publishedin'] : "";
		$view->filters['year_start'] = isset($view->filters['year_start']) ? $view->filters['year_start'] : "";
		$view->filters['year_end'] = isset($view->filters['year_end']) ? $view->filters['year_end'] : "";
		$view->filters['startuploaddate'] = isset($view->filters['startuploaddate']) ? $view->filters['startuploaddate'] : "";
		$view->filters['enduploaddate'] = isset($view->filters['enduploaddate']) ? $view->filters['enduploaddate'] : "";
		$view->filters['sort'] = isset($view->filters['sort']) ? $view->filters['sort'] : "";
		$view->filters['filter'] = isset($view->filters['filter']) ? $view->filters['filter'] : "";

		// Sort Filter
		$view->sorts = array(
			//'sec_cnt DESC' => Lang::txt('PLG_MEMBERS_CITATIONS_CITEDBY'),
			'year DESC'    => Lang::txt('PLG_MEMBERS_CITATIONS_YEAR'),
			'created DESC' => Lang::txt('PLG_MEMBERS_CITATIONS_NEWEST'),
			'title ASC'    => Lang::txt('PLG_MEMBERS_CITATIONS_TITLE'),
			'author ASC'   => Lang::txt('PLG_MEMBERS_CITATIONS_AUTHOR'),
			'journal ASC'  => Lang::txt('PLG_MEMBERS_CITATIONS_JOURNAL')
		);

		// Handling ids of the the boxes checked for download
		$referer = (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';
		$session = App::get('session');

		// If it's new search remove all user citation checkmarks
		if (isset($_POST['filter']))
		{
			$view->filters['idlist'] = "";
			$session->set('idlist', $view->filters['idlist']);
		}
		else
		{
			$view->filters['idlist'] = Request::getVar('idlist', $session->get('idlist'));
			$session->set('idlist', $view->filters['idlist']);
		}

		// Reset the filter if the user came from a different section
		if (strpos($referer, "/citations/browse") == false)
		{
			$view->filters['idlist'] = "";
			$session->set('idlist', $view->filters['idlist']);
		}

		// get the preferred labeling scheme
		$view->label = "both";

		if ($view->label == "none")
		{
			$view->citations_label_class = "no-label";
		}
		elseif ($view->label == "number")
		{
			$view->citations_label_class = "number-label";
		}
		elseif ($view->label == "type")
		{
			$view->citations_label_class = "type-label";
		}
		elseif ($view->label == "both")
		{
			$view->citations_label_class = "both-label";
		}
		else
		{
			$view->citations_label_class = "both-label";
		}

		// enable coins support
		$view->coins = 1;

		// types
		$ct = \Components\Citations\Models\Type::all();
		$view->types = $ct;

		// OpenURL
		$openURL = $this->_handleOpenURL();
		$view->openurl['link'] = $openURL['link'];
		$view->openurl['text'] = $openURL['text'];
		$view->openurl['icon'] = $openURL['icon'];

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Display the form allowing to edit a citation
	 *
	 * @return  string  HTML
	 */
	private function editAction($row=null)
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginAction();
		}

		if (!$this->params->get('access-manage'))
		{
			throw new Exception(Lang::txt('PLG_MEMBERS_CITATIONS_NOT_AUTHORIZED'), 403);
		}

		// Create view object
		$view = $this->view('edit');

		$view->member   = $this->member;
		$view->option   = $this->option;
		$view->database = $this->database;
		$view->allow_tags = $this->member->getParam('citation_allow_tags', 'yes');
		$view->allow_badges = $this->member->getParam('citatoin_allow_badges', 'yes');

		// Get the citation types
		$citationsType = \Components\Citations\Models\Type::all();
		$view->types = $citationsType->rows();

		$fields = array();
		foreach ($view->types as $type)
		{
			if (isset($type->fields))
			{
				$f = $type->fields;
				if (strpos($f, ',') !== false)
				{
					$f = str_replace(',', "\n", $f);
				}

				$f = array_map('trim', explode("\n", $f));
				$f = array_values(array_filter($f));

				$fields[strtolower(str_replace(' ', '', $type->type_title))] = $f;
			}
		}

		// Incoming
		$id = Request::getInt('cid', 0);

		// Load the object
		if (is_object($row))
		{
			$view->row = $row;
		}
		else
		{
			$view->row = \Components\Citations\Models\Citation::oneOrNew($id);

			// check to see if this member created this citation
			if (!$view->row->isNew() && ($view->row->uid != User::get('id') || $view->row->scope != 'member'))
			{
				// redirect
				App::redirect(
					Route::url($this->member->link() . '&active=' . $this->_name),
					Lang::txt('PLG_MEMBERS_CITATIONS_OWNER_ONLY'),
					'warning'
				);
			}
		}

		//make sure title isnt too long
		$maxTitleLength = 30;
		$shortenedTitle = (strlen($view->row->title) > $maxTitleLength)
			? substr($view->row->title, 0, $maxTitleLength) . '&hellip;'
			: $view->row->title;

		// Set the pathway
		if ($id && $id != 0)
		{
			Pathway::append($shortenedTitle, 'index.php?option=com_citations&task=view&id=' . $view->row->id);
			Pathway::append(Lang::txt('PLG_MEMBERS_CITATIONS_EDIT'));
		}
		else
		{
			Pathway::append(Lang::txt('PLG_MEMBERS_CITATIONS_ADD'));
		}

		// Set the page title
		Document::setTitle( Lang::txt('PLG_MEMBERS_CITATIONS_CITATION') . $shortenedTitle );

		//push jquery to doc
		Document::addScriptDeclaration('var fields = ' . json_encode($fields) . ';');

		// Instantiate a new view
		$view->title  = Lang::txt('PLG_MEMBERS_CITATIONS') . ': ' . Lang::txt('PLG_MEMBERS_CITATIONS_' . strtoupper($this->action));

		// No ID, so we're creating a new entry
		// Set the ID of the creator
		if (!$id)
		{
			$view->row->uid = User::get('id');

			//tags & badges
			$view->tags   = array();
			$view->badges = array();

			$view->row->id = -time();
		}
		else
		{
			if ($view->row->relatedAuthors->count())
			{
				$view->authors = $view->row->relatedAuthors;
			}
			elseif ($view->row->relatedAuthors->count() == 0 && $view->row->author != '')
			{
				// formats the author for the multi-author plugin
				$authors = explode(';',$view->row->author);

				$authorString = '';
				$totalAuths = count($authors);
				$x = 0;


				foreach ($authors as &$author)
				{
					/***
					* Because the multi-select keys off of a comma,
					* imported entries may display incorrectly (Wojkovich, Kevin) breaks the multi-select
					* Convert this to Kevin Wojkovich and I'll @TODO add some logic in the formatter to
					* format it properly within the bibilographic format ({LASTNAME},{FIRSTNAME})
					***/
					$authorEntry = explode(',', $author);
					if (count($authorEntry == 2))
					{
						$author = $authorEntry[1] . ' ' . $authorEntry[0];
					}

					$authorString .= $author;

					if ($totalAuths > 1 && $x < $totalAuths - 1 )
					{
						$authorString .= ',';
					}

					$x = $x + 1;
				}

				$view->authorString = $authorString;
			}

			// tags & badges
			$view->tags   = \Components\Citations\Helpers\Format::citationTags($view->row, $this->database, false);
			$view->badges = \Components\Citations\Helpers\Format::citationBadges($view->row, $this->database, false);
		}

		// Output HTML
		foreach ($this->getErrors() as $error)
		{
			$view->setError($error);
		}

		return $view->loadTemplate();
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	private function saveAction()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginAction();
		}

		if (!$this->params->get('access-manage'))
		{
			throw new Exception(\Lang::txt('PLG_MEMBERS_CITATIONS_NOT_AUTHORIZED'), 403);
		}

		// set scope & scope id in save so no one can mess with hidden form inputs
		$scope    = 'member';
		$scopeID = $this->member->get('id');

		// get tags
		$tags = trim(Request::getVar('tags', ''));

		// get badges
		$badges = trim(Request::getVar('badges', ''));

		// check to see if new
		$cid = Request::getInt('cid');
		$isNew = ($cid < 0 ? true : false);

		// get the citation (single) or create a new one
		$citation = \Components\Citations\Models\Citation::oneOrNew($cid)
			->set(array(
				'type' => Request::getInt('type'),
				'cite' => Request::getVar('cite'),
				'ref_type' => Request::getVar('ref_type'),
				'date_submit' => Request::getVar('date_submit', '0000-00-00 00:00:00'),
				'date_accept' => Request::getVar('date_accept', '0000-00-00 00:00:00'),
				'date_publish' => Request::getVar('date_publish', '0000-00-00 00:00:00'),
				'year' => Request::getVar('year'),
				'month' => Request::getVar('month'),
				'author_address' => Request::getVar('author_address'),
				'editor' => Request::getVar('editor'),
				'title' => Request::getVar('title'),
				'booktitle' => Request::getVar('booktitle'),
				'short_title' => Request::getVar('short_title'),
				'journal' => Request::getVar('journal'),
				'volume' => Request::getVar('volume'),
				'number' => Request::getVar('number'),
				'pages' => Request::getVar('pages'),
				'isbn' => Request::getVar('isbn'),
				'doi' => Request::getVar('doi'),
				'call_number' => Request::getVar('call_number'),
				'accession_number' => Request::getVar('accession_number'),
				'series' => Request::getVar('series'),
				'edition' => Request::getVar('edition'),
				'school' => Request::getVar('school'),
				'publisher' => Request::getVar('publisher'),
				'institution' => Request::getVar('institution'),
				'address' => Request::getVar('address'),
				'location' => Request::getVar('location'),
				'howpublished' => Request::getVar('howpublished'),
				'url' => Request::getVar('uri'),
				'eprint' => Request::getVar('eprint'),
				'abstract' => Request::getVar('abstract'),
				'keywords' => Request::getVar('keywords'),
				'research_notes' => Request::getVar('research_notes'),
				'language' => Request::getVar('language'),
				'label' => Request::getVar('label'),
				'uid' => User::get('id'),
				'created' => Date::toSql(),
				'affiliated' => Request::getInt('affiliated', 0),
				'fundedby' => Request::getInt('fundedby', 0),
				'scope' => $scope,
				'scope_id' => $scopeID
			));

		// Store new content
		if (!$citation->save() && !$citation->validate())
		{
			$this->setError($citation->getError());
			$this->editAction($citation);
			return;
		}

		$authorCount = $citation->relatedAuthors()->count();

		// update authors entries for new citations
		if ($isNew)
		{
			$authors = \Components\Citations\Models\Author::all()
				->where('cid', '=', $cid);

			foreach ($authors as $author)
			{
				$author->set('cid', $citation->id);
				$author->save();
			}
		}
		elseif (!$isNew && ($authorCount == 0))
		{
			$authorField = explode(',',Request::getVar('author'));
			$totalAuths = count($authorField);

			if ($totalAuths == 0)
			{
				// redirect
			}

			foreach ($authorField as $key => $a)
			{
				// create a new row
				$authorObj = \Components\Citations\Models\Author::blank()->set(array(
					'cid' => $citation->id,
					'ordering' => $key,
					'author' => $a
				));

				$authorObj->save();
			}
			// turn the author string into author entries
		}

		// check if we are allowing tags
		$ct1 = new \Components\Tags\Models\Cloud($citation->id, 'citations');
		$ct1->setTags($tags, User::get('id'), 0, 1, '');

		// check if we are allowing badges
		$ct2 = new \Components\Tags\Models\Cloud($citation->id, 'citations');
		$ct2->setTags($badges, User::get('id'), 0, 1, 'badge');

		// resdirect after save
		App::redirect(
			Route::url($this->member->link() . '&active=' . $this->_name),
			($this->getError() ? $this->getError() : Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_SAVED')),
			($this->getError() ? 'error' : 'success')
		);
		return;
	}

	/**
	 * Save an entry
	 *
	 * @return  void
	 */
	private function deleteAction()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginAction();
		}

		if (!$this->params->get('access-manage'))
		{
			throw new Exception(\Lang::txt('PLG_MEMBERS_CITATIONS_NOT_AUTHORIZED'), 403);
		}

		// Incoming
		$id = Request::getInt('cid', 0);
		$citationIDs = Request::getVar('citationIDs', '');
		$bulk = Request::getVar('bulk', false);

		// for single citation operation
		if ($id != 0 && !$bulk)
		{
			$citation = \Components\Citations\Models\Citation::oneOrFail($id);
			$citation->set('published', $citation::STATE_DELETED);

			if ($citation->save() && $citation->scope == 'member'
					&& $citation->scope_id == $this->member->get('id'))
			{
				App::redirect(
					Route::url($this->member->link() . '&active=' . $this->_name),
					Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_DELETED'),
					'success'
				);
				return;
			}
			else
			{
				App::redirect(
					Route::url($this->member->link() . '&active=' . $this->_name),
					Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_NOT_FOUND'),
					'error'
				);
				return;
			}
		}
		// for bulk citations operation
		elseif ((bool) $bulk)
		{
			/**
			 * @TODO move to API, possible use of whereIn()?
			 **/

			// when no selection has been made
			if ($bulk == true && $citationIDs == '')
			{
				// redirect and warn
				App::redirect(
					Route::url($this->member->link() . '&active=' . $this->_name),
					Lang::txt('PLG_MEMBERS_CITATIONS_SELECTION_NOT_FOUND'),
					'warning'
				);
			}

			$deleted  = array();

			$citationIDs = explode(',',$citationIDs);

			foreach ($citationIDs as $id)
			{
				$citation = \Components\Citations\Models\Citation::oneOrFail($id);
				$citation->set('published', $citation::STATE_DELETED);

				// update the record
				if ($citation->save() && $citation->scope == 'member'
				&& $citation->scope_id == $this->member->get('id'))
				{
					array_push($deleted, $id);
				}
			}

			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name),
				Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_DELETED'),
				'success'
			);
			return;
		}
		else
		{
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name),
				Lang::txt('PLG_MEMBERS_CITATIONS_NO_SUCH_CITATION'),
				'error'
			);
			return;
		}
		return;
	}

	/**
	 * Settings for group citations
	 *
	 * @return  void
	 */
	private function settingsAction()
	{
		if ($_POST)
		{
			$display = Request::getVar('display', '');
			$format = Request::getVar('citation-format', '');

			// craft a clever name
			$name =  "custom-member-" . $this->member->get('id');

			// fetch or create new format
			$citationFormat = \Components\Citations\Models\Format::oneOrNew($format);

			// if the setting a custom member citation type
			if (($citationFormat->isNew()) || ($citationFormat->style == $name && !$citationFormat->isNew()))
			{
				$citationFormat->set(array(
					'format' => Request::getVar('template'),
					'style'  => $name
				));

				// save format
				$citationFormat->save();

				// update group
				$citationFormatID = $citationFormat->id;
			}
			else
			{
				// returned value from format select box
				$citationFormatID = $format;
			}

			$include_coins = \Hubzero\Utility\Sanitize::clean(Request::getVar('include_coins', ''));
			$coins_only = \Hubzero\Utility\Sanitize::clean(Request::getVar('coins_only', ''));
			$citation_show_tags = \Hubzero\Utility\Sanitize::clean(Request::getVar('citations_show_tags', ''));
			$citation_show_badges = \Hubzero\Utility\Sanitize::clean(Request::getVar('citations_show_badges', ''));

			// set member citation parameters
			$this->member->setParam('citationFormat', $citationFormatID);
			$this->member->setParam('include_coins' , $include_coins);
			$this->member->setParam('coins_only' , $coins_only);
			$this->member->setParam('citations_show_tags' , $citation_show_tags);
			$this->member->setParam('citations_show_badges' , $citation_show_badges);

			// save profile settings
			if (!$this->member->update())
			{
				// failed
				App::redirect(
					Route::url($this->member->link() . '&active=' . $this->_name),
					Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_NOT_SAVED'),
					'error'
				);
			}

			// redirect after save
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name),
				Lang::txt('PLG_MEMBERS_CITATIONS_SETTINGS_SAVED'),
				'success'
			);
			return;
		}
		else
		{
			// instansiate the view
			$view = $this->view('settings');

			// pass the group through
			$view->member = $this->member;

			// get group settings
			$params = json_decode($this->member->get('params'));

			$view->include_coins = (isset($params->include_coins) ? $params->include_coins : "false");
			$view->coins_only = (isset($params->coins_only) ? $params->coins_only : "false");
			$view->citations_show_tags = (isset($params->citations_show_tags) ? $params->citations_show_tags: "true");
			$view->citations_show_badges = (isset($params->citations_show_badges) ? $params->citations_show_badges: "true");
			$citationsFormat = (isset($params->citationFormat) ? $params->citationFormat : 1);

			// intended for the case that the group's custom
			// format is removed from the jos_citations_format
			try
			{
				$view->currentFormat = \Components\Citations\Models\Format::oneOrFail($citationsFormat);
			}
			catch (\Exception $e)
			{
				$view->currentFormat = \Components\Citations\Models\Format::all()->where('style', 'like', 'ieee');
			}

			// get the name of the current format (see if it's custom)
			// the name of the custom format
			$name = "custom-member-" . $this->member->get('id');

			$custom = \Components\Citations\Models\Format::all()->where('style', 'LIKE', $name)->count();

			if ($custom > 0)
			{
				// show the menu entry for the custom
				$view->customFormat = true;
			}
			else
			{
				// show menu item for new custom format
				$view->customFormat = false;
			}

			// get formats
			$view->formats = \Components\Citations\Models\Format::all()
					->where('style', 'NOT LIKE', '%custom-member-%')
					->where('style', 'NOT LIKE', '%custom-group-%')
					->orWhere('style', '=', $name)
					->rows()->toObject();

			$view->templateKeys = \Components\Citations\Models\Format::all()->getTemplateKeys();

			// Output HTML
			foreach ($this->getErrors() as $error)
			{
				$view->setError($error);
			}

			return $view->loadTemplate();
		}
	}

	/**
	 * Publish method for group citations
	 *
	 * @return  void
	 */
	private function publishAction()
	{
		$id = Request::getVar('cid', 0);
		$citationIDs = Request::getVar('citationIDs', array());
		$bulk = Request::getVar('bulk', false);

		if ($id != 0 && !$bulk)
		{
			$citation = \Components\Citations\Models\Citation::oneOrFail($id);

			if ($citation->uid != $this->member->get('id'))
			{
				// redirect
				App::redirect(
					Route::url($this->member->link() . '&active=' . $this->_name),
					Lang::txt('PLG_MEMBERS_CITATIONS_OWNER_ONLY'),
					'warning'
				);
			}

			// toggle the state
			if ($citation->published != $citation::STATE_PUBLISHED)
			{
				$citation->set('published',  $citation::STATE_PUBLISHED);
				$string = 'PLG_MEMBERS_CITATIONS_CITATION_PUBLISHED';
			}
			else
			{
				$citation->set('published', $citation::STATE_UNPUBLISHED);
				$string = 'PLG_MEMBERS_CITATIONS_CITATION_UNPUBLISHED';
			}

			// save the state
			if ($citation->save() && $citation->scope == 'member'
					&& $citation->scope_id == $this->member->get('id'))
			{
				App::redirect(
					Route::url($this->member->link() . '&active=' . $this->_name),
					Lang::txt($string),
					'success'
				);
				return;
			}
			else
			{
				App::redirect(
					Route::url($this->member->link() . '&active=' . $this->_name),
					Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_NOT_FOUND'),
					'error'
				);
				return;
			}
		}
		elseif ((bool)$bulk)
		{
			/***
			 * @TODO move to API, possible use of whereIn()?
			 ***/

			// when no selection has been made
			if ($bulk == true && $citationIDs == '')
			{
				// redirect and warn
				App::redirect(
					Route::url($this->member->link() . '&active=' . $this->_name),
					Lang::txt('PLG_MEMBERS_CITATIONS_SELECTION_NOT_FOUND'),
					'warning'
				);
			}

			$published = array();
			$citationIDs = explode(',',$citationIDs);
			$string = 'PLG_MEMBERS_CITATIONS_CITATION_PUBLISHED';

			// error, no such citation
			foreach ($citationIDs as $id)
			{
				$citation = \Components\Citations\Models\Citation::oneOrFail($id);

				// toggle the state
				if ($citation->published != $citation::STATE_PUBLISHED)
				{
					$citation->set('published',  $citation::STATE_PUBLISHED);
				}
				else
				{
					$citation->set('published', $citation::STATE_UNPUBLISHED);
				}

				// save the state
				if ($citation->save() && $citation->scope == 'member'
					&& $citation->scope_id == $this->member->get('id'))
				{
					array_push($published, $id);
				}
			}
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name),
				Lang::txt($string),
				'success'
			);
			return;
		}
		else
		{
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name),
				Lang::txt('PLG_MEMBERS_CITATIONS_CITATION_NOT_FOUND'),
				'error'
			);
			return;
		}
	}

	/**
	 * Redirect to login form
	 *
	 * @return  void
	 */
	private function loginAction()
	{
		App::redirect(
			Route::url('index.php?option=com_users&view=login&return=' . base64_encode(Route::url($this->member->link() . '&active=' . $this->_name . '&action=' . $this->action, false, true))),
			Lang::txt('PLG_MEMBERS_CITATIONS_NOT_LOGGEDIN'),
			'warning'
		);
		return;
	}

	/**
	 * Display a form for importing citations
	 *
	 * @return  void
	 */
	private function importAction()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginAction();
		}

		if (!$this->params->get('access-manage'))
		{
			throw new Exception(Lang::txt('PLG_MEMBERS_CITATIONS_NOT_AUTHORIZED'), 403);
		}

		//are we allowing importing

		$view = $this->view('display', 'import');

		// push objects to the view
		$view->member   = $this->member;
		$view->option   = $this->option;
		$view->database = $this->database;
		$view->isAdmin  = $this->params->get('access-manage');

		// citation temp file cleanup
		$this->importer->cleanup();

		$view->accepted_files = Event::trigger('citation.onImportAcceptedFiles' , array());

		$view->messages = Notify::messages('plg_members_citations');

		return $view->loadTemplate();
	}

	/**
	 * Upload a file
	 *
	 * @return  void
	 */
	private function uploadAction()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginAction();
		}

		if (!$this->params->get('access-manage'))
		{
			throw new Exception(Lang::txt('PLG_MEMBERS_CITATIONS_NOT_AUTHORIZED'), 403);
		}

		Request::checkToken();

		// get file
		$file = Request::file('citations_file');

		// make sure we have a file
		$filename = $file->getClientOriginalName();
		if ($filename == '')
		{
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name . '&action=import'),
				Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_MISSING_FILE'),
				'error'
			);
			return;
		}

		// make sure file is under 4MB
		if ($file->getSize() > 4000000)
		{
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name . '&action=import'),
				Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_FILE_TOO_BIG'),
				'error'
			);
			return;
		}

		// make sure we dont have any file errors
		if ($file->getError() > 0)
		{
			throw new Exception(Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD_FAILURE'), 500);
		}

		// call the plugins
		$citations = Event::trigger('citation.onImport' , array($file, 'member', User::get('id')));
		$citations = array_values(array_filter($citations));

		// did we get citations from the citation plugins
		if (!$citations)
		{
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name . '&action=import'),
				Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_PROCESS_FAILURE'),
				'error'
			);
			return;
		}

		if (!isset($citations[0]['attention']))
		{
			$citations[0]['attention'] = '';
		}
		if (!isset($citations[0]['no_attention']))
		{
			$citations[0]['no_attention'] = '';
		}

		if (!$this->importer->writeRequiresAttention($citations[0]['attention']))
		{
			Notify::error(Lang::txt('Unable to write temporary file.'), 'plg_members_citations');
		}

		if (!$this->importer->writeRequiresNoAttention($citations[0]['no_attention']))
		{
			Notify::error(Lang::txt('Unable to write temporary file.'), 'plg_members_citations');
		}

		App::redirect(
			Route::url($this->member->link() . '&active=' . $this->_name . '&action=review')
		);
	}

	/**
	 * Review import items
	 *
	 * @return  void
	 */
	private function reviewAction()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginAction();
		}

		if (!$this->params->get('access-manage'))
		{
			throw new Exception(Lang::txt('PLG_MEMBERS_CITATIONS_NOT_AUTHORIZED'), 403);
		}

		$citations_require_attention    = $this->importer->readRequiresAttention();
		$citations_require_no_attention = $this->importer->readRequiresNoAttention();

		// make sure we have some citations
		if (!$citations_require_attention && !$citations_require_no_attention)
		{
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name . '&action=import'),
				Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_MISSING_FILE_CONTINUE'),
				'error'
			);
			return;
		}

		$view = $this->view('review', 'import');
		$view->citations_require_attention    = $citations_require_attention;
		$view->citations_require_no_attention = $citations_require_no_attention;

		$view->member   = $this->member;
		$view->option   = $this->option;
		$view->database = $this->database;
		$view->isAdmin  = $this->params->get('access-manage');

		$view->messages = Notify::messages('plg_members_citations');

		return $view->loadTemplate();
	}

	/**
	 * Process import selections
	 *
	 * @return  void
	 */
	private function processAction()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginAction();
		}

		if (!$this->params->get('access-manage'))
		{
			throw new Exception(Lang::txt('PLG_MEMBERS_CITATIONS_NOT_AUTHORIZED'), 403);
		}

		Request::checkToken();

		$cites_require_attention    = $this->importer->readRequiresAttention();
		$cites_require_no_attention = $this->importer->readRequiresNoAttention();

		// action for citations needing attention
		$citations_action_attention    = Request::getVar('citation_action_attention', array());

		// action for citations needing no attention
		$citations_action_no_attention = Request::getVar('citation_action_no_attention', array());

		// check to make sure we have citations
		if (!$cites_require_attention && !$cites_require_no_attention)
		{
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name . '&action=import'),
				Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_MISSING_FILE_CONTINUE'),
				'error'
			);
			return;
		}

		// vars
		$allow_tags   = "yes";
		$allow_badges = "yes";

		$this->importer->set('user', User::get('id'));
		$this->importer->setTags($allow_tags == 'yes');
		$this->importer->setBadges($allow_badges == 'yes');
		$this->importer->set('scope_id', $this->member->get('id'));
		$this->importer->set('scope', 'member');

		// Process
		$results = $this->importer->process(
			$citations_action_attention,
			$citations_action_no_attention
		);

		// success message a redirect
		Notify::success(
			Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_RESULTS_SAVED', count($results['saved'])),
			'plg_citations'
		);

		// if we have citations not getting saved
		if (count($results['not_saved']) > 0)
		{
			Notify::warning(
				Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_RESULTS_NOT_SAVED', count($results['not_saved'])),
				'plg_citations'
			);
		}

		if (count($results['error']) > 0)
		{
			Notify::error(
				Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_RESULTS_SAVE_ERROR', count($results['error'])),
				'plg_citations'
			);
		}

		//get the session object
		$session = App::get('session');

		//ids of sessions saved and not saved
		$session->set('citations_saved', $results['saved']);
		$session->set('citations_not_saved', $results['not_saved']);
		$session->set('citations_error', $results['error']);

		//delete the temp files that hold citation data
		$this->importer->cleanup(true);

		//redirect
		App::redirect(
			Route::url($this->member->link() . '&active=' . $this->_name . '&action=saved')
		);
	}

	/**
	 * Show the results of the import
	 *
	 * @return  void
	 */
	private function savedAction()
	{
		// Check if they're logged in
		if (User::isGuest())
		{
			return $this->loginAction();
		}

		if (!$this->params->get('access-manage'))
		{
			throw new Exception(Lang::txt('PLG_MEMBERS_CITATIONS_NOT_AUTHORIZED'), 403);
		}

		// Get the session object
		$session = App::get('session');

		// Get the citations
		$citations_saved     = $session->get('citations_saved');
		$citations_not_saved = $session->get('citations_not_saved');
		$citations_error     = $session->get('citations_error');

		// Check to make sure we have citations
		if (!$citations_saved && !$citations_not_saved)
		{
			App::redirect(
				Route::url($this->member->link() . '&active=' . $this->_name . '&action=import'),
				Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_MISSING_FILE_CONTINUE'),
				'error'
			);
			return;
		}

		$view = $this->view('saved', 'import');
		$view->member   = $this->member;
		$view->option   = $this->option;
		$view->isAdmin  = $this->params->get('access-manage');
		$view->config   = Component::params('com_citations');
		$view->database = $this->database;
		$view->filters  = array(
			'start'  => 0,
			'search' => ''
		);
		$view->citations = array();

		foreach ($citations_saved as $cs)
		{
			$cc = new \Components\Citations\Tables\Citation($this->database);
			$cc->load($cs);
			$view->citations[] = $cc;
		}

		$view->openurl['link'] = '';
		$view->openurl['text'] = '';
		$view->openurl['icon'] = '';

		//take care fo type
		$ct = new \Components\Citations\Tables\Type($this->database);
		$view->types = $ct->getType();

		$view->messages = Notify::messages('plg_members_citations');

		return $view->loadTemplate();
	}

	/**
	* Applies filters to Citations model and returns applied filters
	* @param array  $filters array of POST values
	* @return	array sanitized and validated filter values
	*/
	private function _filterHandler($filters = array(),  $scope_id = 0)
	{
		$citations = \Components\Citations\Models\Citation::all();
		// require citations
		if (!$citations)
		{
			return false;
		}

		// get the ones for this group
		$citations->where('scope', '=', 'member');
		$citations->where('scope_id', '=', $scope_id);
		$citations->where('published', '!=', $citations::STATE_DELETED); // don't include deleted citations

		if (count($filters) > 0)
		{
			foreach ($filters as $filter => $value)
			{
				// sanitization
				$value = \Hubzero\Utility\Sanitize::clean($value);

				// we handle things differently in search and sorting
				if ($filter != 'search' && $filter != 'sort' && $filter != 'tag' && $value != "")
				{
					switch ($filter)
					{
						case 'author':
							$citations->where('author', 'LIKE', "%{$value}%", 'and', 1);
						break;
						case 'publishedin':
							$citations->where('date_publish', 'LIKE', "%{$value}-%");
						break;
						case 'year_start':
							$citations->where('year', '>=', $value);
						break;
						case 'year_end':
							$citations->where('year', '<=', $value);
						break;
						case 'filter':
							if ($value == 'aff')
							{
								$value = 1;
							}
							else
							{
								$value = 0;
							}

							$citations->where('affiliated', '=', $value);
						break;
						default:
							$citations->where($filter, '=', $value);
						break;
					}
				} // end if not search & not sort & non-empty value

				// for searching
				if ($filter == "search" && $value != "")
				{
					$terms = preg_split('/\s+/', $value);

					$value = \Hubzero\Utility\Sanitize::clean($value);
					$term = $value;
					$collection = array();
					$columns = array('author', 'title', 'isbn', 'doi', 'publisher', 'abstract');
					foreach ($columns as $column)
					{
						foreach ($terms as $term)
						{
							// copy the original item
							$cite = clone $citations;

							// do some searching
							$cite->where($column, 'LIKE', "%{$term}%");

							foreach ($cite as $c)
							{
								// put for collection later
								array_push($collection, $c->id);
							} // end foreach $cite
						} // end foreach terms
					} // end foreach columns

					// remove duplicates
					$collection = array_unique($collection);

					// pull the appropriate ones.
					$citations->whereIn('id', $collection);
				} // end searching

				// for tags
				if ($filter == "tag" && $value != "")
				{
					$collection = array();
					$cite = clone $citations;
					foreach ($cite as $c)
					{
						foreach ($c->tags as $tag)
						{
							if ($tag->tag == $value)
							{
								array_push($collection, $c->id);
							}
						}
					}

					// remove duplicates
					$collection = array_unique($collection);

					// get the tagged ones
					$citations->whereIn('id', $collection);
				} // end if tags

				if ($filter == "sort" && $value != "")
				{
					$clause = explode(" ", $value);
					$citations->order($clause[0], $clause[1]);
				}
			} // end foreach filters as filter

			return array('citations' => $citations, 'filters' => $filters);
		}
		else
		{
			return array('citations' => $citations, 'filters' => array());
		}
	}

	/**
	* Uses URL to determine OpenURL server
	*
	* @return  mixed
	*/
	private function _handleOpenURL()
	{
		// get the users id to make lookup
		$users_ip = Request::ip();

		// get the param for ip regex to use machine ip
		$ip_regex = array('10.\d{2,5}.\d{2,5}.\d{2,5}');

		$use_machine_ip = false;
		foreach ($ip_regex as $ipr)
		{
			$match = preg_match('/' . $ipr . '/i', $users_ip);
			if ($match)
			{
				$use_machine_ip = true;
			}
		}

		// make url based on if were using machine ip or users
		if ($use_machine_ip)
		{
			$url = 'http://worldcatlibraries.org/registry/lookup?IP=' . $_SERVER['SERVER_ADDR'];
		}
		else
		{
			$url = 'http://worldcatlibraries.org/registry/lookup?IP=' . $users_ip;
		}

		// get the resolver
		$r = null;
		if (function_exists('curl_init'))
		{
			$cURL = curl_init();
			curl_setopt($cURL, CURLOPT_URL, $url );
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($cURL, CURLOPT_TIMEOUT, 10);
			$r = curl_exec($cURL);
			curl_close($cURL);
		}

		// parse the returned xml
		$openurl = array(
			'link' => '',
			'text' => '',
			'icon' => ''
		);

		// parse the return from resolver lookup
		$resolver = null;
		$xml = simplexml_load_string($r);
		if (isset($xml->resolverRegistryEntry))
		{
			$resolver = $xml->resolverRegistryEntry->resolver;
		}

		// if we have resolver set vars for creating open urls
		if ($resolver != null)
		{
			$openURL['link'] = $resolver->baseURL;
			$openURL['text'] = $resolver->linkText;
			$openURL['icon'] = $resolver->linkIcon;

			return $openURL;
		}

		return false;
	}
}
