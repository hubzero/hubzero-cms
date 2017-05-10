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
 * Projects Links plugin
 */
class plgProjectsLinks extends \Hubzero\Plugin\Plugin
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
	 * Event call to determine if this plugin should return data
	 *
	 * @param   string  $alias
	 * @return  array   Plugin name and title
	 */
	public function &onProjectAreas($alias = null)
	{
		$area = array(
			'name'    => 'links',
			'title'   => 'Links',
			'submenu' => null,
			'show'    => false
		);

		return $area;
	}

	/**
	 * Event call to return count of items
	 *
	 * @param   object   $model  Project
	 * @return  boolean
	 */
	public function onProjectCount($model)
	{
		// Not counting
		return false;
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
		// What's the task?
		$this->_task = $action ? $action : Request::getVar('action');

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

		// Model
		$this->model = $model;

		$tasks = array(
			'browser', 'select' , 'parseurl',
			'parsedoi', 'addcitation', 'deletecitation',
			'newcite', 'editcite', 'savecite'
		);

		// Publishing?
		if (in_array($this->_task, $tasks))
		{
			// Set vars
			$this->_database = App::get('db');
			$this->_uid      = User::get('id');

			// Load component configs
			$this->_config    = $model->config();
			$this->_pubconfig = Component::params('com_publications');

			// Actions
			switch ($this->_task)
			{
				case 'browser':
				default:
					$html = $this->browser();
					break;

				case 'parseurl':
					$html = $this->parseUrl();
					break;

				case 'parsedoi':
					$html = $this->parseDoi();
					break;

				case 'addcitation':
					$html = $this->addCitation();
					break;

				case 'deletecitation':
					$html = $this->deleteCitation();
					break;

				case 'select':
				case 'newcite':
					$html = $this->select();
					break;
				case 'editcite':
					$html = $this->editcite();
					break;
				case 'savecite':
					$html = $this->savecite();
					break;
			}

			$arr = array(
				'html'     => $html,
				'metadata' => ''
			);

			return $arr;
		}

		// Nothing to return
		return false;
	}

	/**
	 * Delete a citation from a publication
	 *
	 * @return  string
	 */
	public function deleteCitation()
	{
		// Incoming
		$cid     = Request::getInt('cid', 0);
		$vid     = Request::getInt('vid', 0);
		$version = Request::getVar('version', 'dev');
		$pid     = Request::getInt('pid', 0);

		if (!$cid || !$pid)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_LINKS_ERROR_CITATION_DELETE'));
		}

		// Make sure this publication belongs to this project
		$objP = new \Components\Publications\Tables\Publication($this->_database);
		if (!$objP->load($pid) || $objP->project_id != $this->model->get('id'))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_LINKS_ERROR_CITATION_DELETE'));
		}

		// Remove citation
		if (!$this->getError())
		{
			// Unattach citation
			if ($this->unattachCitation($pid, $cid))
			{
				\Notify::message(Lang::txt('PLG_PROJECTS_LINKS_CITATION_DELETED'), 'success', 'projects');
			}
		}

		// Pass success or error message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}

		// Build pub url
		$route = $this->model->isProvisioned()
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias=' . $this->model->get('alias') . '&active=publications';

		$url = Route::url($route . '&pid=' . $pid . ($vid ? '&vid=' . $vid : '&version=' . $version) . '&section=citations');

		App::redirect($url);
		return;
	}

	/**
	 * Attach a citation to a publication
	 *
	 * @return  string
	 */
	public function addCitation()
	{
		// Incoming
		$url     = Request::getVar('citation-doi', '');
		$url     = $url ? $url : urldecode(Request::getVar('url'));
		$vid     = Request::getInt('vid', 0);
		$version = Request::getVar('version', 'dev');
		$pid     = Request::getInt('pid', 0);

		if (!$url || !$pid)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_LINKS_NO_DOI'));
		}

		$parts  = explode("doi:", $url);
		$doi    = count($parts) > 1 ? $parts[1] : $url;
		$format = $this->_pubconfig->get('citation_format', 'apa');

		// Attach citation
		if ($this->attachCitation($pid, $doi, $format, $this->_uid))
		{
			\Notify::message(Lang::txt('PLG_PROJECTS_LINKS_CITATION_SAVED'), 'success', 'projects');
		}

		// Pass success or error message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}

		// Build pub url
		$route = $this->model->isProvisioned()
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias=' . $this->model->get('alias') . '&active=publications';

		App::redirect(Route::url($route .'&pid=' . $pid . ($vid ? '&vid=' . $vid : '&version=' . $version) . '&section=citations'));
		return;
	}

	/**
	 * Attach a citation to a publication (in non-curated flow)
	 *
	 * @return  string
	 */
	public function savecite()
	{
		// Incoming
		$cite    = Request::getVar('cite', array(), 'post', 'none', 2);
		$vid     = Request::getInt('vid', 0);
		$pid     = Request::getInt('pid', 0);
		$version = Request::getVar('version', 'dev');

		$new  = $cite['id'] ? false : true;

		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php';
		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php';

		if (!$pid || !$cite['type'] || !$cite['title'])
		{
			$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATIONS_ERROR_MISSING_REQUIRED'));
		}
		else
		{
			$citation = new \Components\Citations\Tables\Citation($this->_database);
			if (!$citation->bind($cite))
			{
				$this->setError($citation->getError());
			}
			else
			{
				$citation->created   = $new == true ? Date::toSql() : $citation->created;
				$citation->uid       = $new == true ? $this->_uid : $citation->uid;
				$citation->published = 1;

				if (!$citation->store(true))
				{
					// This really shouldn't happen.
					$this->setError(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CITATIONS_ERROR_SAVE'));
				}
			}
			// Create association
			if (!$this->getError() && $new == true && $citation->id)
			{
				$assoc = new \Components\Citations\Tables\Association($this->_database);
				$assoc->oid  = $pid;
				$assoc->tbl  = 'publication';
				$assoc->type = 'owner';
				$assoc->cid  = $citation->id;

				// Store new content
				if (!$assoc->store())
				{
					$this->setError($assoc->getError());
				}
			}

			\Notify::message(Lang::txt('PLG_PROJECTS_LINKS_CITATION_SAVED'), 'success', 'projects');
		}

		// Pass success or error message
		if ($this->getError())
		{
			\Notify::message($this->getError(), 'error', 'projects');
		}

		// Build pub url
		$route = $this->model->isProvisioned()
			? 'index.php?option=com_publications&task=submit'
			: 'index.php?option=com_projects&alias=' . $this->model->get('alias') . '&active=publications';

		App::redirect(Route::url($route .'&pid=' . $pid . ($vid ? '&vid=' . $vid : '&version=' . $version) . '&section=citations'));
		return;
	}

	/**
	 * Remove citation
	 *
	 * @param   integer  $pid
	 * @param   integer  $cid
	 * @param   boolean  $returnStatus
	 * @return  boolean
	 */
	public function unattachCitation($pid = 0, $cid = 0, $returnStatus = false)
	{
		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php';
		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php';
		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php';

		if (!$cid || !$pid)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_LINKS_NO_DOI'));

			if ($returnStatus)
			{
				$out['error'] = $this->getError();
				return $out;
			}
			return false;
		}

		$database = App::get('db');
		$c        = new \Components\Citations\Tables\Citation($database);
		$assoc    = new \Components\Citations\Tables\Association($database);

		// Fetch all associations
		$aPubs = $assoc->getRecords(array('cid' => $cid));

		// Remove citation if only one association
		if (count($aPubs) == 1)
		{
			// Delete the citation
			$c->delete($cid);
		}

		// Remove association
		foreach ($aPubs as $aPub)
		{
			if ($aPub->oid == $pid && $aPub->tbl = 'publication')
			{
				$assoc->delete($aPub->id);
			}
		}

		if ($returnStatus)
		{
			$out['success'] = true;
			return $out;
		}

		return true;
	}

	/**
	 * Attach citation
	 *
	 * @param   integer  $pid
	 * @param   string   $doi
	 * @param   string   $format
	 * @param   integer  $actor
	 * @param   boolean  $returnStatus
	 * @return  boolean
	 */
	public function attachCitation($pid = 0, $doi = null, $format = 'apa', $actor = 0, $returnStatus = false)
	{
		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php';
		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'association.php';
		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'type.php';
		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'helpers' . DS . 'format.php';

		$out = array('error' => null, 'success' => null);

		if (!$doi || !$pid)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_LINKS_NO_DOI'));

			if ($returnStatus)
			{
				$out['error'] = $this->getError();
				return $out;
			}
			return false;
		}

		$database = App::get('db');
		$c        = new \Components\Citations\Tables\Citation($database);

		if ($c->loadPubCitation($doi, $pid))
		{
			$this->setError(Lang::txt('PLG_PROJECTS_LINKS_CITATION_ALREADY_ATTACHED'));

			if ($returnStatus)
			{
				$out['error'] = $this->getError();
				return $out;
			}

			return false;
		}
		else
		{
			// Get DOI preview
			$output = self::parseUrl($doi, true, true, $format);
			$output = json_decode($output);

			if (isset($output->error) && $output->error)
			{
				$this->setError($output->error);

				if ($returnStatus)
				{
					$out['error'] = $this->getError();
					return $out;
				}

				return false;
			}
			elseif (isset($output->preview) && $output->preview)
			{
				// Load citation record with the same DOI if present
				if (!$c->loadByDoi($doi))
				{
					$c->created    = Date::toSql();
					$c->title      = $doi;
					$c->uid        = $actor;
					$c->affiliated = 1;
				}
				$c->formatted = $output->preview;
				$c->format    = $format;
				$c->doi       = $doi;

				// Try getting more metadata
				$url = '';
				$data = self::getDoiMetadata($doi, false, $url);

				// Save available data
				if ($data)
				{
					foreach ($c as $key => $value)
					{
						$column = strtolower($key);
						if (isset($data->$column))
						{
							$c->$column = $data->$column;
						}
					}

					// Some extra mapping hacks
					$c->pages = $data->page;

					// Get type ID
					$ct = new \Components\Citations\Tables\Type($database);
					$types = $ct->getType();
					$dType = isset($data->type) ? $data->type : 'article';

					// Hub types don't match library types
					// Trying to match the best we can
					$validTypes = array();
					foreach ($types as $type)
					{
						if ($type['type'] == $dType)
						{
							$c->type = $type['id'];
						}
						elseif ($type['type'] == 'article')
						{
							$validTypes['journal-article'] = $type['id'];
						}
						elseif ($type['type'] == 'chapter')
						{
							$validTypes['book-chapter'] = $type['id'];
						}
						elseif ($type['type'] == 'inproceedings')
						{
							$validTypes['proceedings'] = $type['id'];
						}
					}

					if (isset($validTypes[$dType]))
					{
						$c->type = $validTypes[$dType];
					}
					elseif (!intval($c->type))
					{
						// Default to article
						$c->type = $validTypes['journal-article'];
					}
				}

				if (!$c->store())
				{
					$this->setError(Lang::txt('PLG_PROJECTS_LINKS_CITATION_ERROR_SAVE'));

					if ($returnStatus)
					{
						$out['error'] = $this->getError();
						return $out;
					}

					return false;
				}

				// Create association
				if ($c->id)
				{
					$assoc = new \Components\Citations\Tables\Association($database);
					$assoc->oid  = $pid;
					$assoc->tbl  = 'publication';
					$assoc->type = 'owner';
					$assoc->cid  = $c->id;

					// Store new content
					if (!$assoc->store())
					{
						$this->setError($assoc->getError());
						if ($returnStatus)
						{
							$out['error'] = $this->getError();
							return $out;
						}

						return false;
					}
				}
			}
			else
			{
				$this->setError(Lang::txt('PLG_PROJECTS_LINKS_CITATION_COULD_NOT_LOAD'));

				if ($returnStatus)
				{
					$out['error'] = $this->getError();
					return $out;
				}

				return false;
			}
		}

		if ($returnStatus)
		{
			$out['success'] = true;
			return $out;
		}

		return true;
	}

	/**
	 * Browser within publications NEW
	 *
	 * @return  string
	 */
	public function select()
	{
		// Incoming
		$props  = Request::getVar('p', '');
		$ajax   = Request::getInt('ajax', 0);
		$pid    = Request::getInt('pid', 0);
		$vid    = Request::getInt('vid', 0);
		$filter = urldecode(Request::getVar('filter', ''));

		// Parse props for curation
		$parts   = explode('-', $props);
		$block   = isset($parts[0]) ? $parts[0] : 'content';
		$step    = (isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0) ? $parts[1] : 1;
		$element = (isset($parts[2]) && is_numeric($parts[2]) && $parts[2] > 0) ? $parts[2] : 1;

		$layout = $this->_task == 'newcite' ? 'edit' : 'default';

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'links',
				'name'    =>'selector',
				'layout'  => $layout
			)
		);

		$view->publication = new \Components\Publications\Models\Publication($pid, null, $vid);

		// On error
		if (!$view->publication->exists())
		{
			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'files',
					'name'    =>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError(Lang::txt('PLG_PROJECTS_FILES_SELECTOR_ERROR_NO_PUBID'));
			return $view->loadTemplate();
		}

		$view->publication->attachments();

		// Get curation model
		$view->publication->setCuration();

		// Make sure block exists, else use default
		if (!$view->publication->_curationModel->setBlock($block, $step))
		{
			$block = 'content';
			$step  = 1;
		}

		// Add css?
		if (!$ajax)
		{
			\Hubzero\Document\Assets::addPluginStylesheet('projects', 'publications', 'selector');
		}

		if ($this->_task == 'newcite')
		{
			// Incoming
			$cid    = Request::getInt('cid', 0);

			include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'type.php';
			include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php';

			// Load the object
			$view->row = new \Components\Citations\Tables\Citation($this->_database);
			$view->row->load($cid);

			// get the citation types
			$ct = new \Components\Citations\Tables\Type($this->_database);
			$view->types = $ct->getType();
		}

		$view->option   = $this->_option;
		$view->database = $this->_database;
		$view->model    = $this->model;
		$view->uid      = $this->_uid;
		$view->ajax     = $ajax;
		$view->task     = $this->_task;
		$view->element  = $element;
		$view->block    = $block;
		$view->step     = $step;
		$view->props    = $props;
		$view->filter   = $filter;

		// Get messages	and errors
		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Edit citation view
	 *
	 * @return  string
	 */
	public function editcite()
	{
		// Incoming
		$cid = Request::getInt('cid', 0);
		$pid = Request::getInt('pid', 0);
		$vid = Request::getInt('vid', 0);

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'links',
				'name'    =>'selector',
				'layout'  =>'edit'
			)
		);

		// Load classes
		$objP = new \Components\Publications\Tables\Publication($this->_database);
		$view->version = new \Components\Publications\Tables\Version($this->_database);

		// Load publication version
		$view->version->load($vid);
		if (!$view->version->id)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_LINKS_SELECTOR_ERROR_NO_PUBID'));
		}

		// Get publication
		$view->publication = $objP->getPublication($view->version->publication_id, $view->version->version_number, $this->model->get('id'));

		if (!$view->publication)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_LINKS_SELECTOR_ERROR_NO_PUBID'));
		}

		// On error
		if ($this->getError())
		{
			// Output error
			$view = new \Hubzero\Plugin\View(
				array(
					'folder'  =>'projects',
					'element' =>'files',
					'name'    =>'error'
				)
			);

			$view->title  = '';
			$view->option = $this->_option;
			$view->setError($this->getError());
			return $view->loadTemplate();
		}

		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'type.php';
		include_once PATH_CORE . DS . 'components' . DS . 'com_citations' . DS . 'tables' . DS . 'citation.php';

		// Load the object
		$view->row = new \Components\Citations\Tables\Citation($this->_database);
		$view->row->load($cid);

		// get the citation types
		$ct = new \Components\Citations\Tables\Type($this->_database);
		$view->types = $ct->getType();

		$view->option   = $this->_option;
		$view->database = $this->_database;
		$view->model    = $this->model;
		$view->uid      = $this->_uid;
		$view->task     = $this->_task;
		$view->ajax     = Request::getInt('ajax', 0);

		// Get messages	and errors
		if ($this->getError())
		{
			$view->setError($this->getError());
		}
		return $view->loadTemplate();
	}

	/**
	 * Browser for within publications
	 *
	 * @return  string
	 */
	public function browser()
	{
		// Incoming
		$ajax      = Request::getInt('ajax', 0);
		$primary   = Request::getInt('primary', 1);
		$versionid = Request::getInt('versionid', 0);

		if (!$ajax)
		{
			return false;
		}

		// Output HTML
		$view = new \Hubzero\Plugin\View(
			array(
				'folder'  =>'projects',
				'element' =>'links',
				'name'    =>'browser'
			)
		);

		// Get current attachments
		$pContent = new \Components\Publications\Tables\Attachment($this->_database);
		$role  = $primary ? '1' : '0';
		$other = $primary ? '0' : '1';

		$view->attachments = $pContent->getAttachments($versionid, $filters = array('role' => $role, 'type' => 'link'));

		// Output HTML
		$view->option    = $this->_option;
		$view->database  = $this->_database;
		$view->model     = $this->model;
		$view->uid       = $this->_uid;
		$view->config    = $this->_config;
		$view->primary   = $primary;
		$view->versionid = $versionid;

		// Get messages	and errors
		if ($this->getError())
		{
			$view->setError($this->getError());
		}

		return $view->loadTemplate();
	}

	/**
	 * Parse DOI
	 *
	 * @return  string
	 */
	public function parseDoi()
	{
		// Incoming
		$url = Request::getVar('url', '');

		// Is this a DOI?
		$parts = explode("doi:", $url);
		$doi   = count($parts) > 1 ? $url : 'doi:' . $url;

		// Get format from config
		$format = $this->_pubconfig->get('citation_format', 'apa');

		return $this->parseUrl($doi, true, true, $format);
	}

	/**
	 * Parse input
	 *
	 * @return     string
	 */
	public function parseUrl($url = '', $citation = true, $incPreview = true, $format = 'apa')
	{
		// Incoming
		$url = $url ? $url : urldecode(Request::getVar('url', $url));
		$output = array('rtype' => 'url', 'message' => '');

		if (!$url)
		{
			$output['error'] = Lang::txt('PLG_PROJECTS_LINKS_EMPTY_URL');
			return json_encode($output);
		}

		// Is this a DOI?
		$parts = explode("doi:", $url);
		$doi   = count($parts) > 1 ? $parts[1] : null;

		// Treat url starting with numbers as DOI
		if (preg_match('#[0-9]#', substr($url, 0, 2)))
		{
			$doi = $url;
		}

		$data = null;

		// Pull DOI metadata
		if ($doi)
		{
			$output['rtype'] = 'doi';
			$data = self::getDoiMetadata($doi, $citation, $url, $format);

			if ($this->getError())
			{
				$output['error'] = $this->getError();
				return json_encode($output);
			}
		}

		if (!$doi && filter_var($url, FILTER_VALIDATE_URL) == false)
		{
			$output['error'] = Lang::txt('Please enter a valid URL starting with http:// or https://');
			return json_encode($output);
		}

		// DOI metadata
		if ($data)
		{
			$output['url'] 	= $url;

			if ($incPreview)
			{
				$output['preview'] 	= $data;
			}

			if ($citation == false && is_object($data))
			{
				$output['data'] = array();
				foreach ($data as $key => $value)
				{
					$output['data'][$key] = $value;
				}
			}
		}
		else
		{
			$ch = curl_init($url);
			$options = array(
				CURLOPT_RETURNTRANSFER => true,     // return web page
				CURLOPT_HEADER         => false,    // don't return headers
				CURLOPT_FOLLOWLOCATION => true,     // follow redirects
				CURLOPT_ENCODING       => '',       // handle all encodings
				CURLOPT_USERAGENT      => '',       // who am i
				CURLOPT_AUTOREFERER    => true,     // set referer on redirect
				CURLOPT_CONNECTTIMEOUT => 5,        // timeout on connect
				CURLOPT_TIMEOUT        => 5,        // timeout on response
				CURLOPT_MAXREDIRS      => 10,       // stop after 10 redirects
			);

			curl_setopt_array($ch, $options);
			curl_setopt($ch, CURLOPT_FAILONERROR, true);

			$content  = curl_exec($ch);
			$finalUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
			$finalUrl = str_replace("HTTP", "http", $finalUrl);
			$finalUrl = str_replace("HTTPS", "https", $finalUrl);
			curl_close($ch);

			if (!$finalUrl || !$content)
			{
				$output['message'] = Lang::txt('PLG_PROJECTS_LINKS_NO_PREVIEW');
				return json_encode($output);
			}
			else
			{
				$output['url'] = $finalUrl;
			}

			if ($content)
			{
				require_once PATH_CORE . DS . 'plugins' . DS . 'projects' . DS . 'links' . DS . 'helpers' . DS . 'simple_html_dom.php';

				$out = '';

				// Create DOM from URL or file
				$html = file_get_html($finalUrl);

				$title = $html->find('title', 0)->innertext; //Title Of Page

				$out .= $title ? stripslashes('<h5>' . addslashes($title) . '</h5>') : '<h5>' . \Components\Projects\Helpers\Html::shortenText($finalUrl, 100) . '</h5>';

				//Get all images found on this page
				$jpgs = $html->find('img[src$=jpg],img[src$=png]');
				$images = array();

				if ($jpgs)
				{
					foreach ($jpgs as $jpg)
					{
						$src   = $jpg->getAttribute('src');
						$width = $jpg->getAttribute('width');

						$pathCounter = substr_count($src, "../");
						$src = self::getImgSrc($src);

						// Must be larger than 25px
						if ($width && $width <= 100)
						{
							continue;
						}

						if (!$src)
						{
							continue;
						}

						if (!preg_match("/https?\:\/\//i", $src))
						{
							$src = self::getImageUrl($pathCounter, self::getLink($src, $finalUrl));
						}

						// Can only show images served via https
						//$src = str_replace('http://', 'https://', $src);
						if (preg_match("/https/i", $src))
						{
							$images[] = $src;
						}
					}
				}

				if ($images)
				{
					$out .= '<div id="link-image"><img src="' . $images[0] . '" alt="" /></div>';
				}

				$description = null;

				// Get description from paragraphs
				$pars = $html->find('body div p');
				if ($pars)
				{
					foreach ($pars as $p)
					{
						if (strlen($p->plaintext) > 200)
						{
							$description = $p->plaintext;
							break;
						}
					}
				}

				if (!$description)
				{
					// Set description if desc meta tag found else grab a little plain text of the page
					if ($html->find('meta[name="description"]', 0))
					{
						$description = $html->find('meta[name="description"]', 0)->content;
					}
					else
					{
						$description = $html->find('body', 0)->plaintext;
					}
				}

				$out .= $description
						? stripslashes('<p>' . \Hubzero\Utility\String::truncate(addslashes($description), 200) . '</p>')
						: '<p>' . \Hubzero\Utility\String::truncate(addslashes($finalUrl), 200) . '</p>';

				if ($images)
				{
					$out .= '<span class="clear"></span>';
				}

				// Preview of the url
				if ($incPreview)
				{
					$output['preview'] = $out;
				}
				$output['description'] = $description;
				$output['title']       = $title;
			}
			else
			{
				$output['error'] = Lang::txt('PLG_PROJECTS_LINKS_FAILED_TO_LOAD_URL');
				return json_encode($output);
			}
		}

		return json_encode($output);
	}

	/**
	 * Get DOI Metadata
	 *
	 * @return  string
	 */
	public function getDoiMetadata($doi, $citation = false, &$url, $rawData = false, $format = 'apa')
	{
		// Include metadata model
		include_once PATH_CORE . DS . 'components' . DS . 'com_publications' . DS . 'models' . DS . 'metadata.php';

		$format = in_array($format, array('apa', 'ieee')) ? $format : 'apa';

		$ch  = curl_init();
		$url = 'http://dx.doi.org/doi:' . $doi;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		$data = new \Components\Publications\Models\Metadata();

		if ($citation == true)
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER,
			array (
				"Accept: text/x-bibliography; style=" . $format
			));
		}
		else
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER,
			array (
				"Accept: application/x-datacite+xml;q=0.9, application/citeproc+json;q=1.0"
			));
		}

		$metadata    = curl_exec($ch);
		$status      = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$contenttype = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
		curl_close($ch);

		// Error
		if ($status != 200)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_LINKS_DOI_NOT_FOUND'));
			return;
		}

		// Error - redirected instead of printing metadata
		if (strpos($contenttype, 'text/html;') !== false)
		{
			$this->setError(Lang::txt('PLG_PROJECTS_LINKS_DOI_NOT_FOUND'));
			return;
		}

		// Error - redirected instead of printing metadata
		if ($citation == true)
		{
			return $metadata;
		}

		// JSON
		if ($contenttype == "application/citeproc+json")
		{
			if ($rawData == true)
			{
				return $metadata;
			}

			// crossref DOI
			$metadata = json_decode($metadata, true);

			// Parse data
			$data = self::parseDoiData($data, $metadata);
		}
		else
		{
			// XML
			if ($rawData == true)
			{
				return $metadata;
			}
		}

		return $data;

	}

	/**
	 * Parse DOI metadata
	 *
	 * @return     string
	 */
	public function parseDoiData($data, $metadata)
	{
		// Pull applicable data
		foreach ($data as $key => $value)
		{
			$altKey = strtoupper($key);
			if (isset($metadata[$key]) || isset($metadata[$altKey]))
			{
				$which = isset($metadata[$key]) ? $key : $altKey;
				$data->$key = $metadata[$which];
			}

			// Parse authors
			if ($key == 'author' && isset($metadata['author']))
			{
				$authors = $metadata['author'];
				$authString = '';
				if (is_array($authors) && !empty($authors))
				{
					foreach ($authors as $author)
					{
						if (isset($author['family']) && isset($author['given']))
						{
							$authString .=  $author['family'] . ', ' . $author['given'] . ', ';
						}
						elseif (isset($author['literal']))
						{
							$authString .=  $author['literal'] . ', ';
						}
					}
					$authString = substr($authString, 0, strlen($authString) - 2);
				}
				$data->author = $authString;
			}

			// Parse date
			if ($key == 'issued')
			{
				if (isset($metadata['issued']) && isset($metadata['issued']['date-parts']))
				{
					$data->year = $metadata['issued']['date-parts'][0][0];
				}
			}

			// More custom parsing
			if (isset($metadata['container-title']))
			{
				$data->journal = $metadata['container-title'];
			}
		}

		return $data;
	}

	/**
	 * Parse image source
	 *
	 * @param   string  $imgSrc
	 * @return  string
	 */
	public function getImgSrc($imgSrc)
	{
		$imgSrc = str_replace("../", "", $imgSrc);
		$imgSrc = str_replace("./", "", $imgSrc);
		$imgSrc = str_replace(" ", "%20", $imgSrc);

		return $imgSrc;
	}

	/**
	 * Get image url
	 *
	 * @param   integer  $pathCounter
	 * @param   string   $url
	 * @return  string
	 */
	public function getImageUrl($pathCounter, $url)
	{
		$src = '';
		if ($pathCounter > 0)
		{
			$urlBreaker = explode('/', $url);
			for ($j = 0; $j < $pathCounter + 1; $j++)
			{
				if (isset($urlBreaker[$j]))
				{
					$src .= $urlBreaker[$j] . '/';
				}

			}
		}
		else
		{
			$src = $url;
		}
		return $src;
	}

	/**
	 * Get link
	 *
	 * @param   string  $imgSrc
	 * @param   string  $referer
	 * @return  string
	 */
	public function getLink($imgSrc, $referer)
	{
		if (strpos($imgSrc, "//") === 0)
		{
			$imgSrc = "https:" . $imgSrc;
		}
		elseif (strpos($imgSrc, "/") === 0)
		{
			$imgSrc = "https://" . $this->getPage($referer) . $imgSrc;
		}
		else
		{
			$imgSrc = "https://" . $this->getPage($referer) . '/' . $imgSrc;
		}
		return $imgSrc;
	}

	/**
	 * Get page
	 *
	 * @param   string  $url
	 * @return  string
	 */
	public function getPage($url)
	{
		$cannonical = "";

		if (substr_count($url, 'http://') > 1 || substr_count($url, 'https://') > 1
			|| (strpos($url, 'http://') !== false && strpos($url, 'https://') !== false))
		{
			return $url;
		}

		if (strpos($url, 'http://') !== false)
		{
			$url = substr($url, 7);
		}
		elseif (strpos($url, 'https://') !== false)
		{
			$url = substr($url, 8);
		}

		for ($i = 0; $i < strlen($url); $i++)
		{
			if ($url[$i] != '/')
			{
				$cannonical .= $url[$i];
			}
			else
			{
				break;
			}
		}

		return $cannonical;
	}
}
