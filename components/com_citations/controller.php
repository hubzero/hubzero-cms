<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class CitationsController extends JObject
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;

	//-----------
	
	public function __construct( $config=array() )
	{
		$this->_redirect = NULL;
		$this->_message = NULL;
		$this->_messageType = 'message';
		
		// Set the controller name
		if (empty( $this->_name )) {
			if (isset($config['name'])) {
				$this->_name = $config['name'];
			} else {
				$r = null;
				if (!preg_match('/(.*)Controller/i', get_class($this), $r)) {
					echo "Controller::__construct() : Can't get or parse class name.";
				}
				$this->_name = strtolower( $r[1] );
			}
		}
		
		// Set the component name
		$this->_option = 'com_'.$this->_name;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}
		
	//-----------
	
	private function getTask()
	{
		$task = strtolower(JRequest::getVar('task', ''));
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{	
		switch ( $this->getTask() ) 
		{
			case 'download': $this->download(); break;

			case 'save':   $this->save();   break;
			case 'edit':   $this->edit();   break;
			case 'add':    $this->add();    break;
			case 'delete': $this->delete(); break;
			//case 'view':   $this->view();   break;
			
			case 'browse': $this->browse(); break;
			case 'intro':  $this->intro();  break;

			default: $this->intro(); break;
		}
	}
	
	//-----------

	public function redirect()
	{
		if ($this->_redirect != NULL) {
			$app =& JFactory::getApplication();
			$app->redirect( $this->_redirect, $this->_message, $this->_messageType );
		}
	}

	//-----------
	
	private function getStyles($option='') 
	{
		ximport('xdocument');
		$option = ($option) ? $option : $this->_option;
		XDocument::addComponentStylesheet($option);
	}

	//-----------
	
	private function getScripts()
	{
		$document =& JFactory::getDocument();
		if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
			$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
		}
	}

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function intro() 
	{
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)) );
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		
		// Push some styles to the template
		$this->getStyles();
		$this->getStyles('com_usage');
		
		$database =& JFactory::getDBO();
		
		// Load the object
		$row = new CitationsCitation( $database );
		$yearlystats = $row->getStats();
		
		$types = array(
			'article'=>JText::_('ARTICLE'),
			'book'=>JText::_('BOOK'),
			'booklet'=>JText::_('BOOKLET'),
			'conference'=>JText::_('CONFERENCE'),
			'inbook'=>JText::_('INBOOK'),
			'incollection'=>JText::_('INCOLLECTION'),
			'inproceedings'=>JText::_('INPROCEEDINGS'),
			'journal'=>JText::_('JOURNAL'),
			'magazine'=>JText::_('MAGAZINE'),
			'manual'=>JText::_('MANUAL'),
			'mastersthesis'=>JText::_('MASTERSTHESIS'),
			'misc'=>JText::_('MISC'),
			'phdthesis'=>JText::_('PHDTHESIS'),
			'proceedings'=>JText::_('PROCEEDINGS'),
			'techreport'=>JText::_('TECHREPORT'),
			'unpublished'=>JText::_('UNPUBLISHED')
		);
		$typestats = array();
		foreach ($types as $t=>$x) 
		{
			$typestats[$x] = $row->getCount( array('type'=>$t), false );
		}
		
		// Output HTML
		jimport( 'joomla.application.component.view');

		// Output HTML
		$view = new JView( array('name'=>'intro') );
		$view->title = JText::_(strtoupper($this->_name));
		$view->yearlystats = $yearlystats;
		$view->typestats = $typestats;
		$view->config = $this->config;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function browse()
	{
		// Incoming
		$filters = array();
		$filters['limit']  = JRequest::getInt( 'limit', 25, 'request' );
		$filters['start']  = JRequest::getInt( 'limitstart', 0, 'get' );
		$filters['type']   = JRequest::getVar( 'type', '' );
		$filters['filter'] = JRequest::getVar( 'filter', '' );
		$filters['year']   = JRequest::getInt( 'year', 0 );
		$filters['sort']   = JRequest::getVar( 'sortby', 'created DESC' );
		$filters['search'] = JRequest::getVar( 'search', '' );

		$filters['type']   = ($filters['type'] == 'all')   ? '' : $filters['type'];
		$filters['filter'] = ($filters['filter'] == 'all') ? '' : $filters['filter'];
		
		$database =& JFactory::getDBO();
		
		$obj = new CitationsCitation( $database );
		
		// Get a record count
		$total = $obj->getCount( $filters, false );

		// Initiate paging
		jimport('joomla.html.pagination');
		$pageNav = new JPagination( $total, $filters['start'], $filters['limit'] );

		// Get records
		$citations = $obj->getRecords( $filters, false );
		
		// Push some styles to the template
		$this->getStyles();
		
		// Set the page title
		$document =& JFactory::getDocument();
		$document->setTitle( JText::_(strtoupper($this->_name)) );
		
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
		
		// Output HTML
		echo CitationsHtml::browse( $database, $citations, $pageNav, $this->_option, $this->_task, $filters );
	}

	//-----------
	
	protected function login($title='') 
	{
		ximport('xmodule');
		
		$title = ($title) ? $title : JText::_(strtoupper($this->_name));
		
		$html  = CitationsHtml::div( CitationsHtml::hed( 2, $title ), 'full', 'content-header');
		$h  = CitationsHtml::warning( JText::_('CITATIONS_NOT_LOGGEDIN') );
		$h .= XModuleHelper::renderModules('force_mod');
		$html .= CitationsHtml::div( $h, 'main section');
		
		echo $html;
	}

	//-----------
	
	protected function add() 
	{
		$this->edit();
	}
	
	//-----------
	
	protected function edit()
	{
		// Push some styles to the template
		$this->getStyles();
		
		// Push some scripts to the template
		$this->getScripts();
		
		// Set the page title
		$title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
		
		// Set the pathway
		$app =& JFactory::getApplication();
		$pathway =& $app->getPathway();
		if (count($pathway->getPathWay()) <= 0) {
			$pathway->addItem(JText::_(strtoupper($this->_name)),'index.php?option='.$this->_option);
		}
		$pathway->addItem(JText::_(strtoupper($this->_task)),'index.php?option='.$this->_option.a.'task='.$this->_task);
		
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->login( $title );
			return;
		}
		
		// Incoming - expecting an array id[]=4232
		$id = JRequest::getVar( 'id', array() );
		
		// Get the single ID we're working with
		if (is_array($id) && !empty($id)) {
			$id = $id[0];
		} else {
			$id = 0;
		}
		
		$database =& JFactory::getDBO();
		
		// Load the object
		$row = new CitationsCitation( $database );
		$row->load( $id );
		
		// Load the associations object
		$assoc = new CitationsAssociation( $database );
		
		// No ID, so we're creating a new entry
		// Set the ID of the creator
		if (!$id) {
			$juser =& JFactory::getUser();
			$row->uid = $juser->get('id');
			
			// It's new - no associations to get
			$assocs = array();
		} else {
			// Get the associations
			$assocs = $assoc->getRecords( array('cid'=>$id) );
		}
		
		// Output HTML
		CitationsHtml::edit( $row, $assocs, $this->_option, $title );
	}
	
	//----------------------------------------------------------
	// Processors
	//----------------------------------------------------------
	
	protected function save()
	{
		// Check if they're logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			$this->intro();
			return;
		}
		
		$database =& JFactory::getDBO();

		// Bind incoming data to object
		$row = new CitationsCitation( $database );
		if (!$row->bind( $_POST )) {
			echo CitationsHtml::alert( $row->getError() );
			exit();
		}
	
		// New entry so set the created date
		if (!$row->id) {
			$row->created = date( 'Y-m-d H:i:s', time() );
		}
		
		// Field named 'uri' due to conflict with existing 'url' variable
		$row->url = JRequest::getVar( 'uri', '', 'post' );
		
		// Check content for missing required data
		if (!$row->check()) {
			echo CitationsHtml::alert( $row->getError() );
			exit();
		}

		// Store new content
		if (!$row->store()) {
			echo CitationsHtml::alert( $row->getError() );
			exit();
		}
		
		// Incoming associations
		$arr = JRequest::getVar( 'assocs', array() );
		
		$ignored = array();
		
		foreach ($arr as $a)
		{
			$a = array_map('trim',$a);

			// Initiate extended database class
			$assoc = new CitationsAssociation( $database );
			
			if (!$this->_isempty($a, $ignored)) {
				$a['cid'] = $row->id;
			
				// bind the data
				if (!$assoc->bind( $a )) {
					echo CitationsHtml::alert( $assoc->getError() );
					exit();
				}
		
				// Check content
				if (!$assoc->check()) {
					echo CitationsHtml::alert( $assoc->getError() );
					exit();
				}

				// Store new content
				if (!$assoc->store()) {
					echo CitationsHtml::alert( $assoc->getError() );
					exit();
				}
			} elseif ($this->_isempty($a, $ignored) && !empty($a['id'])) {
				// Delete the row
				if (!$assoc->delete( $a['id'] )) {
					echo CitationsHtml::alert( $assoc->getError() );
					exit();
				}
			}
		}
		
		// Redirect
		$this->_redirect = 'index.php?option='.$this->_option;
	}

	//-----------
	
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

	//-----------
	
	private function delete()
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
			$database =& JFactory::getDBO();
			
			// Loop through the IDs and delete the citation
			$citation = new CitationsCitation( $database );
			$assoc = new CitationsAssociation( $database );
			$author = new CitationsAuthor( $database );
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

	//----------------------------------------------------------
	// Download
	//----------------------------------------------------------
	
	protected function download()
	{
		$database =& JFactory::getDBO();
		
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		$format = JRequest::getVar( 'format', 'bibtex', 'request' );
		
		if (!$id) {
			echo CitationsHtml::error( JText::_('NO_CITATION_ID') );
			return;
		}
		
		// Load the citation
		$row = new CitationsCitation( $database );
		$row->load( $id );
	
		$path = 'site'.DS.'citations'.DS;
	
		// Choose the format
		switch ($format) 
		{
			case 'endnote':
				$doc = '';
				switch ($row->type) 
				{
					case 'article':
						$doc .= "%0 ".JText::_('JOURNAL_ARTICLE')."\r\n";
						if ($row->journal) $doc .= "%J " . trim(stripslashes($row->journal)) . "\r\n";
						break; // journal
					case 'conference':
						$doc .= "%0 ".JText::_('CONFERENCE_PAPER')."\r\n";
						if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
						break;
					case 'proceedings':
					case 'inproceedings':
						$doc .= "%0 ".JText::_('CONFERENCE_PROCEEDINGS')."\r\n";
						if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
						break; // conference proceedings 
					case 'techreport':
						$doc .= "%0 ".JText::_('TECHREPORT')."\r\n";
						break; // report
					case 'book':
						$doc .= "%0 ".JText::_('BOOK')."\r\n";
						break; // book
					case 'inbook':
						$doc .= "%0 ".JText::_('BOOK_SECTION')."\r\n";
						if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
						break; // book section
					case 'mastersthesis':
					case 'phdthesis':
						$doc .= "%0 ".JText::_('THESIS')."\r\n";
						if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
						break; // thesis
					case 'patent':
						$doc .= "%0 ".JText::_('PATENT')."\r\n";
						if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
						break; // patent
					
					case 'booklet':
					case 'manual':
					case 'misc':
					case 'unpublished':
					default:
						$doc .= "%0 ".JText::_('GENERIC')."\r\n";
						if ($row->booktitle) $doc .= "%B " . trim(stripslashes($row->booktitle)) . "\r\n";
						if ($row->journal) $doc .= "%B " . trim(stripslashes($row->journal)) . "\r\n";
						break; // generic
				} 
				$doc .= "%D " . trim($row->year) . "\r\n";
				$doc .= "%T " . trim(stripslashes($row->title)) . "\r\n";

				$author_array = explode(";", stripslashes($row->author));
				foreach ($author_array as $auth) 
				{
					$auth = preg_replace( '/{{(.*?)}}/s', '', $auth );
					if (!strstr($auth,',')) {
						$bits = explode(' ',$auth);
						$n = array_pop($bits).', ';
						$bits = array_map('trim',$bits);
						$auth = $n.trim(implode(' ',$bits));
					}
					$doc .= "%A " . trim($auth) . "\r\n";
				} 

				if ($row->address) $doc .= "%C " . trim(stripslashes($row->address)) . "\r\n";
				if ($row->editor) {
					$author_array = explode(";", stripslashes($row->editor));
					foreach ($author_array as $auth) 
					{
						$doc .= "%E " . trim($auth) . "\r\n";
					} 
				}
				if ($row->publisher) $doc .= "%I " . trim(stripslashes($row->publisher)) . "\r\n";
				if ($row->number)    $doc .= "%N " . trim($row->number) . "\r\n";
				if ($row->pages)     $doc .= "%P " . trim($row->pages) . "\r\n";
				if ($row->url)       $doc .= "%U " . trim($row->url) . "\r\n";
				if ($row->volume)    $doc .= "%V " . trim($row->volume) . "\r\n";
				if ($row->note)      $doc .= "%Z " . trim($row->note) . "\r\n";
				if ($row->edition)   $doc .= "%7 " . trim($row->edition) . "\r\n";
				if ($row->month)     $doc .= "%8 " . trim($row->month) . "\r\n";
				if ($row->isbn)      $doc .= "%@ " . trim($row->isbn) . "\r\n";
				if ($row->doi)       $doc .= "%1 " . trim($row->doi) . "\r\n";

				$doc .= "\r\n";
  
				$file = 'download_'.$id.'.enw';
				$mime = 'application/x-endnote-refer';
				break;
			
			case 'bibtex':
			default:
				include_once(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.'BibTex.php');

				$bibtex = new Structures_BibTex();
				$addarray = array();
				$addarray['type']    = $row->type;
				$addarray['cite']    = $row->cite;
				$addarray['title']   = $row->title;
				$addarray['address'] = $row->address;
				$auths = explode(';',$row->author);
				for ($i=0, $n=count( $auths ); $i < $n; $i++)
				{
					$author = trim($auths[$i]);
					$author_arr = explode(',',$author);
					$author_arr = array_map('trim',$author_arr);
					
					$addarray['author'][$i]['first'] = (isset($author_arr[1])) ? $author_arr[1] : '';
					$addarray['author'][$i]['last']  = (isset($author_arr[0])) ? $author_arr[0] : '';
				}
				$addarray['booktitle']    = $row->booktitle;
				$addarray['chapter']      = $row->chapter;
				$addarray['edition']      = $row->edition;
				$addarray['editor']       = $row->editor;
				$addarray['eprint']       = $row->eprint;
				$addarray['howpublished'] = $row->howpublished;
				$addarray['institution']  = $row->institution;
				$addarray['journal']      = $row->journal;
				$addarray['key']          = $row->key;
				$addarray['location']     = $row->location;
				$addarray['month']        = ($row->month != 0 || $row->month != '0') ? $row->month : '';
				$addarray['note']         = $row->note;
				$addarray['number']       = $row->number;
				$addarray['organization'] = $row->organization;
				$addarray['pages']        = ($row->pages != 0 || $row->pages != '0') ? $row->pages : '';
				$addarray['publisher']    = $row->publisher;
				$addarray['series']       = $row->series;
				$addarray['school']       = $row->school;
				$addarray['url']          = $row->url;
				$addarray['volume']       = $row->volume;
				$addarray['year']         = $row->year;
				if ($row->journal != '') {
					$addarray['issn']     = $row->isbn;
				} else {
					$addarray['isbn']     = $row->isbn;
				}
				$addarray['doi']          = $row->doi;
				
				$bibtex->addEntry($addarray);

				$file = 'download_'.$id.'.bib';
				$mime = 'application/x-bibtex';
				$doc = $bibtex->bibTex();
				break;
		}
		
		// Write the contents to a file
		$fp = fopen($path.$file, "w") or die("can't open file"); 
		fwrite($fp, $doc);
		fclose($fp);
		
		$this->serveup(false, $path, $file, $mime);
		
		die; // REQUIRED
	}
	
	//-----------
	
	private function serveup($inline = false, $p, $f, $mime)
	{
		$user_agent = (isset($_SERVER["HTTP_USER_AGENT"]) ) 
					? $_SERVER["HTTP_USER_AGENT"] 
					: $HTTP_USER_AGENT;

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
		
        $this->readfile_chunked($p.$f);
        // The caller MUST 'die();'
    }
    
	//-----------
	
	private function readfile_chunked($filename,$retbytes=true) 
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
	
	//----------------------------------------------------------
	// Authorization checks
	//----------------------------------------------------------
	
	private function authorize()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
	
		// Check if they're a site admin (from LDAP)
		$xuser =& XFactory::getUser();
		if (is_object($xuser)) {
			$app =& JFactory::getApplication();
			if (in_array(strtolower($app->getCfg('sitename')), $xuser->get('admin'))) {
				return true;
			}
		}
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return true;
		}

		return false;
	}
}
?>
