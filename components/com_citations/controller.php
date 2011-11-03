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
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

/**
 * Short description for 'CitationsController'
 * 
 * Long description (if any) ...
 */
class CitationsController extends Hubzero_Controller
{

	/**
	 * Short description for 'execute'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function execute()
	{

		$this->_task = strtolower(JRequest::getVar('task', ''));

		switch ($this->_task)
		{
			case 'intro':    $this->intro();    break;
			case 'browse':   $this->browse();   break;
			case 'add':      $this->add();      break;
			case 'edit':     $this->edit();     break;
			case 'save':     $this->save();     break;
			case 'delete':   $this->delete();   break;

			//download
			case 'download': 		$this->download(); 			break;
			case 'downloadbatch':	$this->download_batch();	break;

			//import
			case 'import':			$this->import();			break;
			case 'import_upload':	$this->import_upload();		break;
			case 'import_review':	$this->import_review();		break;
			case 'import_save':		$this->import_save();		break;
			case 'import_saved':	$this->import_saved();		break;

			//ajax 			
			case 'getformat':		$this->getFormatTemplate();	break;

			default: $this->intro(); break;
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	/**
	 * Short description for 'intro'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function intro()
	{
		// Push some styles to the template
		$this->_getStyles();
		$this->_getStyles('com_usage');

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Instantiate a new view
		$view = new JView( array('name'=>'intro') );
		$view->title = JText::_(strtoupper($this->_name));

		$view->database = $this->database;

		// Load the object
		$row = new CitationsCitation( $this->database );
		$view->yearlystats = $row->getStats();

		// Get some stats
		$view->typestats = array();
		$ct = new CitationsType( $this->database );
		$types = $ct->getType();
		foreach ($types as $t)
		{
			$view->typestats[$t['type_title']] = $row->getCount( array('type'=>$t['id']), false );
		}

		//are we allowing importing
		$view->allow_import = $this->config->get("citation_import", 1);

		// Output HTML
		$view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

		$view->display();
	}

	/**
	 * Short description for 'browse'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'browse') );
		$view->title = JText::_(strtoupper($this->_name));
		$view->option = $this->_option;
		$view->database = $this->database;
		$view->config = $this->config;
		//$view->format = ($this->config->get('format')) ? $this->config->get('format') : 'APA';

		// Incoming
		$view->filters = array();
		$view->filters['limit']  = JRequest::getInt( 'limit', 50, 'request' );
		$view->filters['start']  = JRequest::getInt( 'limitstart', 0, 'get' );
		$view->filters['type']   = JRequest::getVar( 'type', '' );
		$view->filters['filter'] = JRequest::getVar( 'filter', '' );
		$view->filters['year']   = JRequest::getInt( 'year', 0 );
		$view->filters['sort']   = JRequest::getVar( 'sort', 'sec_cnt DESC' );
//		$view->filters['search'] = JRequest::getVar( 'search', '' );
		$view->filters['search'] = $this->database->getEscaped( JRequest::getVar( 'search', '' ));
		$view->filters['reftype'] = JRequest::getVar( 'reftype', array('research'=>1,'education'=>1,'eduresearch'=>1,'cyberinfrastructure'=>1) );
		$view->filters['geo']    = JRequest::getVar( 'geo', array('us'=>1,'na'=>1,'eu'=>1,'as'=>1) );
		$view->filters['aff']    = JRequest::getVar( 'aff', array('university'=>1,'industry'=>1,'government'=>1) );

		$view->filters['type']   = ($view->filters['type'] == 'all')   ? '' : $view->filters['type'];
		$view->filters['filter'] = ($view->filters['filter'] == 'all') ? '' : $view->filters['filter'];

		// Instantiate a new citations object
		$obj = new CitationsCitation( $this->database );

		// Get a record count
		$total = $obj->getCount( $view->filters, false );

		// Initiate paging
		jimport('joomla.html.pagination');
		$view->pageNav = new JPagination( $total, $view->filters['start'], $view->filters['limit'] );

		// Get records
		$view->citations = $obj->getRecords( $view->filters, false );

		// Add some data to our view for form filtering/sorting
		$ct = new CitationsType( $this->database );
		$view->types = $ct->getType();

		$view->filter = array(
			'all'=>JText::_('ALL'),
			'aff'=>JText::_('AFFILIATE'),
			'nonaff'=>JText::_('NONAFFILIATE')
		);

		$view->sorts = array(
			'sec_cnt DESC'=>JText::_('Cited by'),
			'year DESC'=>JText::_('YEAR'),
			'created DESC'=>JText::_('NEWEST'),
			'title ASC'=>JText::_('TITLE'),
			'author ASC'=>JText::_('AUTHORS'),
			'journal ASC'=>JText::_('JOURNAL')
		);

		//get the resolver
		$cURL = curl_init();
		curl_setopt( $cURL, CURLOPT_URL, "http://worldcatlibraries.org/registry/lookup?IP=requestor" );
		curl_setopt( $cURL, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $cURL, CURLOPT_TIMEOUT, 10 );
		$r = curl_exec( $cURL );
		curl_close( $cURL );

		//parse the returned xml
		if($r) {
			$xml = simplexml_load_string( $r );
			$resolver = $xml->resolverRegistryEntry->resolver;

			//set some needed urls
			$view->openurl['link'] = $resolver->baseURL;
			$view->openurl['text'] = $resolver->linkText;
			$view->openurl['icon'] = $resolver->linkIcon;
		}

		// Push some styles to the template
		$this->_getStyles();

		//push jquery to doc
		$document =& JFactory::getDocument();
		$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js');

		//push scripts
		$this->_getScripts();

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		//get any messages
		$view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

		$view->display();
	}

	/**
	 * Short description for 'login'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function login()
	{
		$view = new JView( array('name'=>'login') );
		$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	/**
	 * Short description for 'add'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function add()
	{
		$this->edit();
	}

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function edit()
	{
		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts();

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login();
			return;
		}

		// Instantiate a new view
		$view = new JView( array('name'=>'edit') );
		$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$view->option = $this->_option;
		$view->config = $this->config;

		// Incoming - expecting an array id[]=4232
		$id = JRequest::getVar( 'id', array() );

		// Get the single ID we're working with
		if (is_array($id) && !empty($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}

		// Load the object
		$view->row = new CitationsCitation( $this->database );
		$view->row->load( $id );

		// Load the associations object
		$assoc = new CitationsAssociation( $this->database );

		// No ID, so we're creating a new entry
		// Set the ID of the creator
		if (!$id) {
			$juser =& JFactory::getUser();
			$view->row->uid = $juser->get('id');

			// It's new - no associations to get
			$view->assocs = array();

			//tags & badges
			$view->tags = array();
			$view->badges = array();
		} else {
			// Get the associations
			$view->assocs = $assoc->getRecords( array('cid'=>$id) );

			//get the citations tags and badges
			$t = new TagsTag( $this->database );
			$view->tags = $t->getCloud( "citations", "", $id);
			$view->badges = $t->getCloud( "citations", "badges", $id);
		}

		//get the citation types
		$ct = new CitationsType( $this->database );
		$view->types = $ct->getType();

		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function save()
	{
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->intro();
			return;
		}

		//get the posted vars
		$c = $_POST;

		//get tags
		$tags = trim(JRequest::getVar("tags", ""));
		unset($c['tags']);

		//get badges
		$badges = trim(JRequest::getVar("badges",""));
		unset($c['badges']);

		// Bind incoming data to object
		$row = new CitationsCitation( $this->database );
		if (!$row->bind( $c )) {
			$this->setError( $row->getError() );
			$this->edit();
			return;
		}

		// New entry so set the created date
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );
		}

		// Field named 'uri' due to conflict with existing 'url' variable
		$row->url = JRequest::getVar( 'uri', '', 'post' );

		// Check content for missing required data
		if (!$row->check()) {
			$this->setError( $row->getError() );
			$this->edit();
			return;
		}

		// Store new content
		if (!$row->store()) {
			$this->setError( $row->getError() );
			$this->edit();
			return;
		}

		// Incoming associations
		$arr = JRequest::getVar( 'assocs', array() );

		$ignored = array();

		foreach ($arr as $a)
		{
			$a = array_map('trim',$a);

			// Initiate extended database class
			$assoc = new CitationsAssociation( $this->database );

			if (!$this->_isempty($a, $ignored)) {
				$a['cid'] = $row->id;

				// bind the data
				if (!$assoc->bind( $a )) {
					$this->setError( $assoc->getError() );
					$this->edit();
					return;
				}

				// Check content
				if (!$assoc->check()) {
					$this->setError( $assoc->getError() );
					$this->edit();
					return;
				}

				// Store new content
				if (!$assoc->store()) {
					$this->setError( $assoc->getError() );
					$this->edit();
					return;
				}
			} elseif ($this->_isempty($a, $ignored) && !empty($a['id'])) {
				// Delete the row
				if (!$assoc->delete( $a['id'] )) {
					$this->setError( $assoc->getError() );
					$this->edit();
					return;
				}
			}
		}

		//check if we are allowing tags
		if($this->config->get("citation_allow_tags","no") == "yes") {
			$ct = new CitationTags( $this->database );
			$ct->tag_object($this->juser->get("id"), $row->id, $tags, 1, false, "");
		}

		//check if we are allowing badges
		if($this->config->get("citation_allow_badges","no") == "yes") {
			$ct = new CitationTags( $this->database );
			$ct->tag_object($this->juser->get("id"), $row->id, $badges, 1, false, "badge");
		}

		// Redirect
		$this->addComponentMessage( "You have successfully added a new citation.", "passed" );
		$this->_redirect = 'index.php?option='.$this->_option."&task=browse";
		return;
	}

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function delete()
	{
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->intro();
			return;
		}

		// Incoming (we're expecting an array)
		$ids = JRequest::getVar('id', array());
		if (!is_array($ids)) {
			$ids = array();
		}

		// Make sure we have IDs to work with
		if (count($ids) > 0) {
			// Loop through the IDs and delete the citation
			$citation = new CitationsCitation( $this->database );
			$assoc = new CitationsAssociation( $this->database );
			$author = new CitationsAuthor( $this->database );
			foreach ($ids as $id)
			{
				// Fetch and delete all the associations to this citation
				$assocs = $assoc->getRecords( array('cid'=>$id) );
				foreach ($assocs as $a)
				{
					$assoc->delete( $a->id );
				}

				// Fetch and delete all the authors to this citation
				$authors = $author->getRecords( array('cid'=>$id) );
				foreach ($authors as $a)
				{
					$author->delete( $a->id );
				}

				// Delete the citation
				$citation->delete( $id );
			}
		}

		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//---------------------------------------------------------------------
	//	Import
	//---------------------------------------------------------------------

	protected function import()
	{
		//get user object
        $juser =& JFactory::getUser();

		//are we allowing importing
		$import_param = $this->config->get("citation_import", 1);

		//if importing is turned off go to intro page
		if($import_param == 0) {
			return $this->intro();
		}

		//Check if they're logged in
        if ($juser->get('guest')) {
        	return $this->login();
        }

		//are we only allowing admins?
		$isAdmin = $juser->authorize('com_citations','manage');
		if($import_param == 2 && !$isAdmin) {
			$this->addComponentMessage( "You must be a site administrator to import citations.", "warning" );
			$this->_redirect = JRoute::_('index.php?option=com_citations');
			return;
		}

		// Push some styles to the template
		$this->_getStyles();

		// Push some scripts to the template
		$this->_getScripts();

		// Set the page title
		$this->_buildTitle();

		// Set the pathway
		$this->_buildPathway();

		//citation temp file cleanup
		$this->citation_cleanup();

		// Instantiate a new view
		$view = new JView( array('name'=>'import') );
		$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));

		//import the plugins
		JPluginHelper::importPlugin( 'citation' );
        $dispatcher =& JDispatcher::getInstance();

		//call the plugins
		$view->accepted_files = $dispatcher->trigger( 'onImportAcceptedFiles' , array() );

		//get any messages
		$view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

		//display view
		$view->display();
	}

	protected function import_upload()
	{
		//get user object
        $juser =& JFactory::getUser();

		//Check if they're logged in
        if ($juser->get('guest')) {
			return $this->intro();
        }

		//get file
		$file = JRequest::getVar("citations_file", null, "files", "array");

		//make sure we have a file
		if(!$file['name']) {
			$this->addComponentMessage( "You must upload a file.", "error" );
			$this->_redirect = JRoute::_('index.php?option=com_citations&task=import');
			return;
		}

		//make sure file is under 4MB
		if($file['size'] > 4000000) {
			$this->addComponentMessage( "The file you uploaded exceeds the maximum file size of 4MB.", "error" );
			$this->_redirect = JRoute::_('index.php?option=com_citations&task=import');
			return;
		}

		//make sure we dont have any file errors
		if($file['error'] > 0) {
			JError::raiseError(	500, "An error occurred while trying to upload the file." );
		}

		//load citation import plugins
		JPluginHelper::importPlugin( 'citation' );
        $dispatcher =& JDispatcher::getInstance();

		//call the plugins
		$citations = $dispatcher->trigger( 'onImport' , array($file) );
		$citations = array_values(array_filter($citations));

		//did we get citations from the citation plugins
		if(!$citations) {
			$this->addComponentMessage( "An error occurred while trying to process your file. Your citations file is currently not in the right format", "error" );
			$this->_redirect = JRoute::_('index.php?option=com_citations&task=import');
			return;
		}

		//get the session object
		$session =& JFactory::getSession();
		$sessionid = $session->getId();

		//write the citation data to files
		$p1 = JPATH_ROOT . DS . 'tmp' . DS . 'citations' . DS . 'citations_require_attention_' . $sessionid . '.txt';
		$p2 = JPATH_ROOT . DS . 'tmp' . DS . 'citations' . DS . 'citations_require_no_attention_' . $sessionid . '.txt';
		$file1 = JFile::write($p1, serialize($citations[0]['attention']));
		$file2 = JFile::write($p2, serialize($citations[0]['no_attention']));

		//review imported citations
		$this->_redirect = JRoute::_('index.php?option=com_citations&task=import_review');
		return;
	}

	protected function import_review()
	{
		//get user object
        $juser =& JFactory::getUser();

		//get the session object
		$session =& JFactory::getSession();
		$sessionid = $session->getId();

		//get the citations
		$p1 = JPATH_ROOT . DS . 'tmp' . DS . 'citations' . DS . 'citations_require_attention_' . $sessionid . '.txt';
		$p2 = JPATH_ROOT . DS . 'tmp' . DS . 'citations' . DS . 'citations_require_no_attention_' . $sessionid . '.txt';
		$citations_require_attention = unserialize(JFile::read($p1));
		$citations_require_no_attention = unserialize(JFile::read($p2));

		//make sure we have some citations
		if(!$citations_require_attention && !$citations_require_no_attention) {
			$this->addComponentMessage( "You must upload a citations file before continuing.", "error" );
			$this->_redirect = JRoute::_('index.php?option=com_citations&task=import');
			return;
		}

		//push jquery to doc
		$document =& JFactory::getDocument();
		$document->addScript('https://ajax.googleapis.com/ajax/libs/jquery/1.6.3/jquery.min.js');

		// Push some styles to the template
		$this->_getStyles();
		// Push some scripts to the template
		$this->_getScripts();
		// Set the page title
		$this->_buildTitle();
		// Set the pathway
		$this->_buildPathway();

		//include tag handler
		require_once(JPATH_ROOT . DS . 'components' . DS . 'com_tags' . DS . 'helpers' . DS . 'handler.php');

		// Instantiate a new view
		$view = new JView( array('name'=>'import', 'layout' => 'import_review') );
		$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$view->citations_require_attention = $citations_require_attention;
		$view->citations_require_no_attention = $citations_require_no_attention;
		//$view->session = $session;

		//get any messages
		$view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

		//display view
		$view->display();
	}

	protected function import_save()
	{
		//get user object
        $juser =& JFactory::getUser();

		//get the session object
		$session =& JFactory::getSession();
		$sessionid = $session->getId();

		//read in contents of citations file
		$p1 = JPATH_ROOT . DS . 'tmp' . DS . 'citations' . DS . 'citations_require_attention_' . $sessionid . '.txt';
		$p2 = JPATH_ROOT . DS . 'tmp' . DS . 'citations' . DS . 'citations_require_no_attention_' . $sessionid . '.txt';
		$cites_require_attention = unserialize(JFile::read($p1));
		$cites_require_no_attention = unserialize(JFile::read($p2));

		//action for citations needing attention
		$citations_action_attention = JRequest::getVar("citation_action_attention", array());

		//action for citations needing no attention
		$citations_action_no_attention = JRequest::getVar("citation_action_no_attention", array());

		//check to make sure we have citations
		if(!$cites_require_attention && !$cites_require_no_attention) {
			$this->addComponentMessage( "You must upload a citations file before continuing.", "error" );
			$this->_redirect = JRoute::_('index.php?option=com_citations&task=import');
			return;
		}

		//vars
		$citations_saved = array();
		$citations_not_saved = array();
		$now = date("Y-m-d H:i:s");
		$user = $this->juser->get("id");
		$allow_tags = $this->config->get("citation_allow_tags","no");
		$allow_badges = $this->config->get("citation_allow_badges","no");

		//loop through each citation that required attention from user
		if($cites_require_attention)
		{
			foreach($cites_require_attention as $k => $cra)
			{

				//new citation object
				$cc = new CitationsCitation( $this->database );

				//add a couple of needed keys
				$cra['uid'] = $user;
				$cra['created'] = $now;

				//remove errors
				unset( $cra['errors'] );

				//if tags were sent over
				if(array_key_exists("tags", $cra)) {
					$tags = $cra['tags'];
					unset($cra['tags']);
				}

				//if badges were sent over
				if(array_key_exists("badges", $cra)) {
					$badges = $cra['badges'];
					unset($cra['badges']);
				}

				//take care fo type
				$ct = new CitationsType( $this->database );
				$types = $ct->getType();

				$type = "";
				foreach($types as $t) {
					if( strtolower($t['type_title']) == strtolower($cra['type']) ) {
						$type = $t['id'];
					}
				}
				$cra['type'] = ($type) ? $type : "";

				switch ($citations_action_attention[$k])
				{
					case 'overwrite':
						$cra['id'] = $cra['duplicate'];
						break;
					case 'both':
						break;
					case 'discard':
						$citations_not_saved[] = $cra;
						continue 2;
						break;
				}

				//remove duplicate flag
				unset( $cra['duplicate'] );

				//save the citation
				if(!$cc->save( $cra )) {
					echo "houston we have a problem.";
					$citations_not_saved[] = $cra;
					return;
				} else{
					//tags
					if($allow_tags == "yes") {
						$this->tag_citation( $user, $cc->id, $tags, "" );
					}

					//badges
					if($allow_badges == "yes") {
						$this->tag_citation( $user, $cc->id, $badges, "badge" );
					}

					//add the citattion to the saved 
					$citations_saved[] = $cc->id;
				}
			}
		}

		//
		foreach($cites_require_no_attention as $k => $crna)
		{
			$tags = "";
			$badges = "";

			//new citation object
			$cc = new CitationsCitation( $this->database );

			//add a couple of needed keys
			$crna['uid'] = $user;
			$crna['created'] = $now;

			//remove errors
			unset( $crna['errors'] );

			//if tags were sent over
			if(array_key_exists("tags", $crna)) {
				$tags = $crna['tags'];
				unset($crna['tags']);
			}

			//if badges were sent over
			if(array_key_exists("badges", $crna)) {
				$badges = $crna['badges'];
				unset($crna['badges']);
			}

			//verify we haad this one checked to be submitted
			if($citations_action_no_attention[$k] != 1) {
				$citations_not_saved[] = $crna;
				continue;
			}

			//take care fo type
			$ct = new CitationsType( $this->database );
			$types = $ct->getType();

			$type = "";
			foreach($types as $t) {
				if( strtolower($t['type_title']) == strtolower($crna['type']) ) {
					$type = $t['id'];
				}
			}
			$crna['type'] = ($type) ? $type : "";

			//remove duplicate flag
			unset( $crna['duplicate'] );

			//save the citation
			if(!$cc->save( $crna )) {
				echo "houston we have a problem.";
				$citations_not_saved[] = $crna;
				return;
			} else{
				//tags
				if($allow_tags == "yes") {
					$this->tag_citation( $user, $cc->id, $tags, "" );
				}

				//badges
				if($allow_badges == "yes") {
					$this->tag_citation( $user, $cc->id, $badges, "badge" );
				}

				//add the citattion to the saved 
				$citations_saved[] = $cc->id;
			}
		}

		//success message a redirect
		$this->addComponentMessage( "You have successfully uploaded <strong>" . count($citations_saved) . "</strong> new citation(s). Your citation(s) can be viewed below.", "passed" );

		//if we have citations not getting saved
		if(count($citations_not_saved) > 0) {
			$this->addComponentMessage( "<strong>" . count($citations_not_saved) . "</strong> citation(s) NOT uploaded.", "warning" );
		}

		//get the session object
		$session =& JFactory::getSession();

		//ids of sessions saved and not saved
		$session->set("citations_saved", $citations_saved);
		$session->set("citations_not_saved", $citations_not_saved);

		//delete the temp files that hold citation data
		JFile::delete($p1);
		JFile::delete($p2);

		//redirect
		$this->_redirect = JRoute::_('index.php?option=com_citations&task=import_saved');
		return;
	}

	protected function import_saved()
	{
		//get the session object
		$session =& JFactory::getSession();

		//get the citations
		$citations_saved = $session->get("citations_saved");
		$citations_not_saved = $session->get("citations_not_saved");

		//check to make sure we have citations
		if(!$citations_saved && !$citations_not_saved) {
			$this->addComponentMessage( "You must upload a citations file before continuing.", "error" );
			$this->_redirect = JRoute::_('index.php?option=com_citations&task=import');
			return;
		}

		// Push some styles to the template
		$this->_getStyles();
		// Push some scripts to the template
		$this->_getScripts();
		// Set the page title
		$this->_buildTitle();
		// Set the pathway
		$this->_buildPathway();

		//filters for gettiung jsut previously uploaded
		$filters = array();
		$filters['start'] = 0;
		$filters['search'] = "";

		// Instantiate a new view
		$view = new JView( array('name'=>'import', 'layout' => 'import_saved') );
		$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$view->config = $this->config;
		$view->database = $this->database;
		$view->filters = $filters;
		$view->citations = array();

		foreach($citations_saved as $cs) {
			$cc = new CitationsCitation( $this->database );
			$cc->load($cs);
			$view->citations[] = $cc;
		}

		$view->openurl['link'] = "";
		$view->openurl['text'] = "";
		$view->openurl['icon'] = "";

		//take care fo type
		$ct = new CitationsType( $this->database );
		$view->types = $ct->getType();

		//get any messages
		$view->messages = ($this->getComponentMessage()) ? $this->getComponentMessage() : array();

		//display view
		$view->display();
	}

	protected function tag_citation( $userid, $objectid, $tag_string, $label)
	{
		if($tag_string) {
			$ct = new CitationTags( $this->database );
			$ct->tag_object( $userid, $objectid, $tag_string, 1, false, $label );
		}
	}

	protected function citation_cleanup()
	{
		$p = JPATH_ROOT . DS . 'tmp' . DS . 'citations';

		if(is_dir($p)) {
			$tmp = JFolder::files($p);

			if($tmp) {
				foreach($tmp as $t) {
					$ft= filemtime( $p . DS . $t);

					if($ft < strtotime("-1 DAY")) {
						JFile::delete( $p . DS . $t );
					}
				}
			}
		}
	}

	//---------------------------------------------------------------------
	//	Export
	//---------------------------------------------------------------------

	/**
	 * Short description for 'download'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function download()
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		$format = strtolower(JRequest::getVar( 'format', 'bibtex', 'request' ));

		// Esnure we have an ID to work with
		if (!$id) {
			JError::raiseError( 500, JText::_('NO_CITATION_ID') );
			return;
		}

		// Load the citation
		$row = new CitationsCitation( $this->database );
		$row->load( $id );

		// Set the write path
		$path = JPATH_ROOT;
		if ($this->config->get('uploadpath')) {
			if (substr($this->config->get('uploadpath'), 0, 1) != DS) {
				$path .= DS;
			}
			$path .= $this->config->get('uploadpath').DS;
		} else {
			$path .= DS.'site'.DS.'citations'.DS;
		}

		// Instantiate the download helper
		include_once( JPATH_COMPONENT.DS.'citations.download.php' );

		$formatter = new CitationsDownload;
		$formatter->setFormat($format);

		// Set some vars
		$doc  = $formatter->formatReference($row);
		$mime = $formatter->getMimeType();
		$file = 'download_'.$id.'.'.$formatter->getExtension();

		// Ensure we have a directory to write files to
		if (!is_dir( $path )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( $path, 0777 )) {
				JError::raiseError( 500, JText::_('UNABLE_TO_CREATE_UPLOAD_PATH') );
				return;
			}
		}

		// Write the contents to a file
		$fp = fopen($path.$file, "w") or die("can't open file");
		fwrite($fp, $doc);
		fclose($fp);

		$this->_serveup(false, $path, $file, $mime);

		die; // REQUIRED
	}

	protected function download_batch()
	{
		//get the submit buttons value
		$download = JRequest::getVar("download", "", "post");

		//get the citations we want to export
		$citations = JRequest::getVar("download_marker", array(), "post");

		//return to browse mode if we really dont wanna download
		if(strtolower($download) != "endnote" && strtolower($download) != "bibtex") {
			return $this->browse();
		}

		//load the downloader
		include_once( JPATH_COMPONENT.DS.'helpers'.DS.'citations.download.php' );

		//var to hold output
		$doc = "";

		//for each citation we want to downlaod
		foreach($citations as $c)
		{
			$cc = new CitationsCitation( $this->database );
			$cc->load( $c );

			$cd = new CitationsDownload();
			$cd->setFormat( strtolower($download) );
			$doc .= $cd->formatReference( $cc ) . "\r\n\r\n";

			$mine = $cd->getMimeType();
		}

		$ext = (strtolower($download) == 'bibtex') ? ".bib" : ".enw";

		//filename
		$filename = "citations_export_" . strtolower($download) . "_" . date("Y_m_d") . $ext;

		//output file
		header("Content-Type: application/octet-stream");
		header("Content-Disposition: Attachment; filename={$filename}");
		header("Pragma: no-cache");
		echo $doc;
		exit();
	}

	//----------------------------------------------------------
	// 	Utilites
	//----------------------------------------------------------

	private function _isempty($b, $ignored=array())
	{
		foreach ($ignored as $ignore)
		{
			if (array_key_exists($ignore,$b)) {
				$b[$ignore] = NULL;
			}
		}
		if (array_key_exists('id',$b)) {
			$b['id'] = NULL;
		}
		$values = array_values($b);
		$e = true;
		foreach ($values as $v)
		{
			if ($v) {
				$e = false;
			}
		}
		return $e;
	}

	private function _serveup($inline = false, $p, $f, $mime)
	{
		// Clean all output buffers (needs PHP > 4.2.0)
		while (@ob_end_clean());

		$fsize = filesize( $p.$f );
		$mod_date = date('r', filemtime( $p.$f ) );

		$cont_dis = $inline ? 'inline' : 'attachment';

        header("Pragma: public");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Expires: 0");

        header("Content-Transfer-Encoding: binary");
		header('Content-Disposition:' . $cont_dis .';'
			. ' filename="' . $f . '";'
			. ' modification-date="' . $mod_date . '";'
			. ' size=' . $fsize .';'
			); //RFC2183
        header("Content-Type: "    . $mime ); // MIME type
        header("Content-Length: "  . $fsize);

 		// No encoding - we aren't using compression... (RFC1945)
		//header("Content-Encoding: none");
		//header("Vary: none");

        $this->_readfile_chunked($p.$f);
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
	private function _readfile_chunked($filename,$retbytes=true)
	{
		$chunksize = 1*(1024*1024); // How many bytes per chunk
		$buffer = '';
		$cnt =0;
		$handle = fopen($filename, 'rb');
		if ($handle === false) {
			return false;
		}
		while (!feof($handle))
		{
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			if ($retbytes) {
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status) {
			return $cnt; // Return num. bytes delivered like readfile() does.
		}
		return $status;
	}

	/**
	 * Short description for '_buildPathway'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _buildPathway()
	{
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();

		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(
				JText::_(strtoupper($this->_name)),
				'index.php?option='.$this->_option
			);
		}
		if ($this->_task) {
			$pathway->addItem(
				JText::_(strtoupper($this->_task)),
				'index.php?option='.$this->_option.'&task='.$this->_task
			);
		}
	}

	/**
	 * Short description for '_buildTitle'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function _buildTitle()
	{
		$title = JText::_(strtoupper($this->_name));
		if ($this->_task) {
			$title .= ': '.JText::_(strtoupper($this->_task));
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
	}

	public function getFormatTemplate()
	{
		$format = JRequest::getVar("format", "apa");
		echo "format" . $format;
	}
}

