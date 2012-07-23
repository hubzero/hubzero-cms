<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Citations controller class for citation entries
 */
class CitationsControllerCitations extends Hubzero_Controller
{
	/**
	 * Execute a task
	 * 
	 * @return     void
	 */
	public function execute()
	{
		$this->registerTask('intro', 'display');

		parent::execute();
	}

	/**
	 * Default component view
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		$this->view->setLayout('display');

		// Push some styles to the template
		$this->_getStyles();
		$this->_getStyles('com_usage');

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate a new view
		$this->view->title = JText::_(strtoupper($this->_name));

		$this->view->database = $this->database;

		// Load the object
		$row = new CitationsCitation($this->database);
		$this->view->yearlystats = $row->getStats();

		// Get some stats
		$this->view->typestats = array();
		$ct = new CitationsType($this->database);
		$types = $ct->getType();
		foreach ($types as $t)
		{
			$this->view->typestats[$t['type_title']] = $row->getCount(array('type' => $t['id']), false);
		}

		//are we allowing importing
		$this->view->allow_import = $this->config->get('citation_import', 1);
		$this->view->allow_bulk_import = $this->config->get('citation_bulk_import', 1);
		$this->view->isAdmin = ($this->juser->get('usertype') == 'Super Administrator') ? true : false;

		// Output HTML
		$this->view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

		$this->view->display();
	}

	/**
	 * Browse entries
	 * 
	 * @return     void
	 */
	public function browseTask()
	{
		// Instantiate a new view
		$this->view->title    = JText::_(strtoupper($this->_name));
		$this->view->database = $this->database;
		$this->view->config   = $this->config;

		// Incoming
		$this->view->filters = array();
		$this->view->filters['limit']   = JRequest::getInt('limit', 50, 'request');
		$this->view->filters['start']   = JRequest::getInt('limitstart', 0, 'get');
		$this->view->filters['type']    = JRequest::getVar('type', '');
		$this->view->filters['filter']  = JRequest::getVar('filter', '');
		$this->view->filters['year']    = JRequest::getInt('year', 0);
		$this->view->filters['sort']    = JRequest::getVar('sort', 'sec_cnt DESC');
		$this->view->filters['search']  = $this->database->getEscaped(JRequest::getVar('search', ''));
		$this->view->filters['reftype'] = JRequest::getVar('reftype', array('research' => 1, 'education' => 1, 'eduresearch' => 1, 'cyberinfrastructure' => 1));
		$this->view->filters['geo']     = JRequest::getVar('geo', array('us' => 1, 'na' => 1,'eu' => 1, 'as' => 1));
		$this->view->filters['aff']     = JRequest::getVar('aff', array('university' => 1, 'industry' => 1, 'government' => 1));

		$this->view->filters['type']    = ($this->view->filters['type'] == 'all')   ? '' : $this->view->filters['type'];
		$this->view->filters['filter']  = ($this->view->filters['filter'] == 'all') ? '' : $this->view->filters['filter'];

		// Instantiate a new citations object
		$obj = new CitationsCitation($this->database);

		// Get a record count
		$total = $obj->getCount($this->view->filters, false);

		// Initiate paging
		jimport('joomla.html.pagination');
		$this->view->pageNav = new JPagination(
			$total, 
			$this->view->filters['start'], 
			$this->view->filters['limit']
		);

		// Get records
		$this->view->citations = $obj->getRecords($this->view->filters, false);

		// Add some data to our view for form filtering/sorting
		$ct = new CitationsType($this->database);
		$this->view->types = $ct->getType();

		$this->view->filter = array(
			'all'    => JText::_('ALL'),
			'aff'    => JText::_('AFFILIATE'),
			'nonaff' => JText::_('NONAFFILIATE')
		);

		$this->view->sorts = array(
			'sec_cnt DESC' => JText::_('Cited by'),
			'year DESC'    => JText::_('YEAR'),
			'created DESC' => JText::_('NEWEST'),
			'title ASC'    => JText::_('TITLE'),
			'author ASC'   => JText::_('AUTHORS'),
			'journal ASC'  => JText::_('JOURNAL')
		);

		//get the resolver
		$r = null;
		if (function_exists('curl_init'))
		{
			$cURL = curl_init();
			curl_setopt($cURL, CURLOPT_URL, 'http://worldcatlibraries.org/registry/lookup?IP=requestor');
			curl_setopt($cURL, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($cURL, CURLOPT_TIMEOUT, 10);
			$r = curl_exec($cURL);
			curl_close($cURL);
		}

		//parse the returned xml
		$this->view->openurl = array(
			'link' => '',
			'text' => '',
			'icon' => ''
		);
		if ($r) 
		{
			$xml = simplexml_load_string($r);
			$resolver = $xml->resolverRegistryEntry->resolver;

			//set some needed urls
			$this->view->openurl['link'] = $resolver->baseURL;
			$this->view->openurl['text'] = $resolver->linkText;
			$this->view->openurl['icon'] = $resolver->linkIcon;
		}

		// Push some styles to the template
		$this->_getStyles();

		//push jquery to doc
		if (!JPluginHelper::isEnabled('system', 'jquery'))
		{
			$document =& JFactory::getDocument();
			$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js');
		}

		//push scripts
		$this->_getScripts('assets/js/' . $this->_name);

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		//get any messages
		$this->view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

		$this->view->display();
	}

	/**
	 * Redirect to login form
	 * 
	 * @return     void
	 */
	public function loginTask()
	{
		$this->setRedirect(
			JRoute::_('index.php?option=com_login&return=' . base64_encode(JRoute::_('index.php?option=' . $this->_option . '&task=' . $this->_task))),
			JText::_('You must be a logged in to access this area.'),
			'warning'
		);
		return;
	}

	/**
	 * Show a form for adding an entry
	 * 
	 * @return     void
	 */
	public function addTask()
	{
		$this->editTask();
	}

	/**
	 * Show a form for editing an entry
	 * 
	 * @return     void
	 */
	public function editTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask();
			return;
		}

		//are we allowing user to add citation
		$allowImport = $this->config->get('citation_import', 1);
		if ($allowImport == 0 
		 || ($allowImport == 2 && $this->juser->get('usertype') != 'Super Administrator'))
		{
			$this->displayTask();
			return;
		}

		$this->view->setLayout('edit');

		//get the citation types
		$ct = new CitationsType($this->database);
		$types = $ct->getType();

		$fields = array();
		foreach ($types as $type)
		{
			if (isset($type['fields']))
			{
				$f = $type['fields'];
				if (strpos($f, ',') !== false) 
				{
					$f = str_replace(',', "\n", $f);
				}

				$f = array_map('trim', explode("\n", $f));
				$f = array_values(array_filter($f));

				$fields[strtolower(str_replace(' ', '', $type['type_title']))] = $f;
			}
		} 

		//add an empty value for the first type
		array_unshift($types, array(
			'type'       => '', 
			'type_title' => ' - Select a Type &mdash;'
		));

		//push jquery to doc
		$document =& JFactory::getDocument();
		if (!JPluginHelper::isEnabled('system', 'jquery'))
		{
			$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js');
		}
		$document->addScriptDeclaration('var fields = ' . json_encode($fields) . ';');

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts('assets/js/' . $this->_name);

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate a new view
		$this->view->title  = JText::_(strtoupper($this->_name)) . ': ' . JText::_(strtoupper($this->_task));
		$this->view->config = $this->config;

		// Incoming - expecting an array id[]=4232
		$id = JRequest::getVar('id', array());

		// Get the single ID we're working with
		if (is_array($id) && !empty($id)) 
		{
			$id = $id[0];
		} 
		else 
		{
			$id = 0;
		}

		// Load the object
		$this->view->row = new CitationsCitation($this->database);
		$this->view->row->load($id);

		// Load the associations object
		$assoc = new CitationsAssociation($this->database);

		// No ID, so we're creating a new entry
		// Set the ID of the creator
		if (!$id) 
		{
			$this->view->row->uid = $this->juser->get('id');

			// It's new - no associations to get
			$this->view->assocs = array();

			//tags & badges
			$this->view->tags   = array();
			$this->view->badges = array();
		} 
		else 
		{
			// Get the associations
			$this->view->assocs = $assoc->getRecords(array('cid' => $id));

			//get the citations tags and badges
			$t = new TagsTag($this->database);
			$this->view->tags   = $t->getCloud('citations', '', $id);
			$this->view->badges = $t->getCloud('citations', 'badges', $id);
		}

		//get the citation types
		$ct = new CitationsType($this->database);
		$this->view->types = $ct->getType();

		// Output HTML
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}
		$this->view->display();
	}

	/**
	 * Save an entry
	 * 
	 * @return     void
	 */
	public function saveTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask();
			return;
		}

		//get the posted vars
		$c = $_POST;

		//get tags
		$tags = trim(JRequest::getVar('tags', ''));
		unset($c['tags']);

		//get badges
		$badges = trim(JRequest::getVar('badges', ''));
		unset($c['badges']);

		// Bind incoming data to object
		$row = new CitationsCitation($this->database);
		if (!$row->bind($c)) 
		{
			$this->setError($row->getError());
			$this->editTask();
			return;
		}

		// New entry so set the created date
		if (!$row->id) 
		{
			$row->created = date('Y-m-d H:i:s', time());
		}

		// Field named 'uri' due to conflict with existing 'url' variable
		$row->url = JRequest::getVar('uri', '', 'post');

		// Check content for missing required data
		if (!$row->check()) 
		{
			$this->setError($row->getError());
			$this->editTask();
			return;
		}

		// Store new content
		if (!$row->store()) 
		{
			$this->setError($row->getError());
			$this->editTask();
			return;
		}

		// Incoming associations
		$arr = JRequest::getVar('assocs', array());

		$ignored = array();

		foreach ($arr as $a)
		{
			$a = array_map('trim', $a);

			// Initiate extended database class
			$assoc = new CitationsAssociation($this->database);

			if (!$this->_isempty($a, $ignored)) 
			{
				$a['cid'] = $row->id;

				// bind the data
				if (!$assoc->bind($a)) 
				{
					$this->setError($assoc->getError());
					$this->editTask();
					return;
				}

				// Check content
				if (!$assoc->check()) 
				{
					$this->setError($assoc->getError());
					$this->editTask();
					return;
				}

				// Store new content
				if (!$assoc->store()) 
				{
					$this->setError($assoc->getError());
					$this->editTask();
					return;
				}
			} 
			elseif ($this->_isempty($a, $ignored) && !empty($a['id'])) 
			{
				// Delete the row
				if (!$assoc->delete($a['id'])) 
				{
					$this->setError($assoc->getError());
					$this->editTask();
					return;
				}
			}
		}

		//check if we are allowing tags
		if ($this->config->get('citation_allow_tags', 'no') == 'yes') 
		{
			$ct = new CitationTags($this->database);
			$ct->tag_object($this->juser->get('id'), $row->id, $tags, 1, false, '');
		}

		//check if we are allowing badges
		if ($this->config->get('citation_allow_badges', 'no') == 'yes') 
		{
			$ct = new CitationTags($this->database);
			$ct->tag_object($this->juser->get('id'), $row->id, $badges, 1, false, 'badge');
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option . '&task=browse',
			JText::_('You have successfully added a new citation.')
		);
		return;
	}

	/**
	 * Delete one or more entries
	 * 
	 * @return     void
	 */
	public function deleteTask()
	{
		// Check if they're logged in
		if ($this->juser->get('guest')) 
		{
			$this->loginTask();
			return;
		}

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) 
		{
			$ids = array();
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0) 
		{
			// Loop through the IDs and delete the citation
			$citation = new CitationsCitation($this->database);
			$assoc    = new CitationsAssociation($this->database);
			$author   = new CitationsAuthor($this->database);
			foreach ($ids as $id)
			{
				// Fetch and delete all the associations to this citation
				$assocs = $assoc->getRecords(array('cid' => $id));
				foreach ($assocs as $a)
				{
					$assoc->delete($a->id);
				}

				// Fetch and delete all the authors to this citation
				$authors = $author->getRecords(array('cid' => $id));
				foreach ($authors as $a)
				{
					$author->delete($a->id);
				}

				// Delete the citation
				$citation->delete($id);
			}
		}

		// Redirect
		$this->setRedirect(
			'index.php?option=' . $this->_option
		);
	}

	/**
	 * Download a citation
	 * 
	 * @return     string
	 */
	public function downloadTask()
	{
		// Incoming
		$id = JRequest::getInt('id', 0, 'request');
		$format = strtolower(JRequest::getVar('format', 'bibtex', 'request'));

		// Esnure we have an ID to work with
		if (!$id) 
		{
			JError::raiseError(500, JText::_('NO_CITATION_ID'));
			return;
		}

		// Load the citation
		$row = new CitationsCitation($this->database);
		$row->load($id);

		// Set the write path
		$path = JPATH_ROOT . DS . trim($this->config->get('uploadpath', '/site/citations'), DS);

		$formatter = new CitationsDownload;
		$formatter->setFormat($format);

		// Set some vars
		$doc  = $formatter->formatReference($row);
		$mime = $formatter->getMimeType();
		$file = 'download_' . $id . '.' . $formatter->getExtension();

		// Ensure we have a directory to write files to
		if (!is_dir($path)) 
		{
			jimport('joomla.filesystem.folder');
			if (!JFolder::create($path, 0777)) 
			{
				JError::raiseError(500, JText::_('UNABLE_TO_CREATE_UPLOAD_PATH'));
				return;
			}
		}

		// Write the contents to a file
		$fp = fopen($path . $file, "w") or die("can't open file");
		fwrite($fp, $doc);
		fclose($fp);

		$this->_serveup(false, $path, $file, $mime);

		die; // REQUIRED
	}

	/**
	 * Download a batch of entries
	 * 
	 * @return     void
	 */
	public function downloadbatchTask()
	{
		//get the submit buttons value
		$download = JRequest::getVar('download', '', 'post');

		//get the citations we want to export
		$citations = JRequest::getVar('download_marker', array(), 'post');

		//return to browse mode if we really dont wanna download
		if (strtolower($download) != 'endnote' 
		 && strtolower($download) != 'bibtex') 
		{
			return $this->displayTask();
		}

		//var to hold output
		$doc = '';

		//for each citation we want to downlaod
		foreach($citations as $c)
		{
			$cc = new CitationsCitation($this->database);
			$cc->load($c);

			$cd = new CitationsDownload();
			$cd->setFormat(strtolower($download));
			$doc .= $cd->formatReference($cc) . "\r\n\r\n";

			$mine = $cd->getMimeType();
		}

		$ext = (strtolower($download) == 'bibtex') ? '.bib' : '.enw';

		//filename
		$filename = 'citations_export_' . strtolower($download) . '_' . date("Y_m_d") . $ext;

		//output file
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: Attachment; filename=' . $filename);
		header('Pragma: no-cache');
		echo $doc;
		exit();
	}

	/**
	 * Check if an array is empty, ignoring keys in the $ignored list
	 * 
	 * @param      array $b       Array of data to check
	 * @param      array $ignored Array of keys to bypass
	 * @return     boolean True if empty, false if not
	 */
	private function _isempty($b, $ignored=array())
	{
		foreach ($ignored as $ignore)
		{
			if (array_key_exists($ignore, $b)) 
			{
				$b[$ignore] = NULL;
			}
		}
		if (array_key_exists('id', $b)) 
		{
			$b['id'] = NULL;
		}
		$values = array_values($b);
		$e = true;
		foreach ($values as $v)
		{
			if ($v) 
			{
				$e = false;
			}
		}
		return $e;
	}

	/**
	 * Short description for '_serveup'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $inline Parameter description (if any) ...
	 * @param      string $p Parameter description (if any) ...
	 * @param      string $f Parameter description (if any) ...
	 * @param      string $mime Parameter description (if any) ...
	 * @return     void
	 */
	private function _serveup($inline = false, $p, $f, $mime)
	{
		// Clean all output buffers (needs PHP > 4.2.0)
		while (@ob_end_clean());

		$fsize = filesize($p . $f);
		$mod_date = date('r', filemtime($p . $f));

		$cont_dis = $inline ? 'inline' : 'attachment';

		header("Pragma: public");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Expires: 0");

		header("Content-Transfer-Encoding: binary");
		header(
			'Content-Disposition:' . $cont_dis . ';'
			. ' filename="' . $f . '";'
			. ' modification-date="' . $mod_date . '";'
			. ' size=' . $fsize . ';'
		); //RFC2183
		header("Content-Type: " . $mime); // MIME type
		header("Content-Length: " . $fsize);

		// No encoding - we aren't using compression... (RFC1945)
		//header("Content-Encoding: none");
		//header("Vary: none");

		$this->_readfile_chunked($p . $f);
		// The caller MUST 'die();'
	}

	/**
	 * Short description for '_readfile_chunked'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $filename Parameter description (if any) ...
	 * @param      boolean $retbytes Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	private function _readfile_chunked($filename, $retbytes=true)
	{
		$chunksize = 1*(1024*1024); // How many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) 
		{
			return false;
		}
		while (!feof($handle))
		{
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			if ($retbytes) 
			{
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status) 
		{
			return $cnt; // Return num. bytes delivered like readfile() does.
		}
		return $status;
	}

	/**
	 * Return the citation format
	 * 
	 * @return     void
	 */
	public function getformatTask()
	{
		echo 'format' . JRequest::getVar('format', 'apa');
	}
}

