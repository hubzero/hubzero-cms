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
	
	public function execute()
	{
		// Load the component config
		/*$component =& JComponentHelper::getComponent( $this->_option );
		if (!trim($component->params)) {
			return $this->abort();
		} else {
			$config =& JComponentHelper::getParams( $this->_option );
		}*/
		$this->config = JComponentHelper::getParams( $this->_option );
		
		$this->database = JFactory::getDBO();
		
		$this->types = array(
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
			'unpublished'=>JText::_('UNPUBLISHED'),
			'patent appl'=>JText::_('PATENT'),
			'chapter'=>JText::_('CHAPTER'),
			'notes'=>JText::_('NOTES'),
			'letter'=>JText::_('LETTER'),
			'xarchive'=>JText::_('XARCHIVE'),
			'manuscript'=>JText::_('MANUSCRIPT')

		);
		
		$this->_task = strtolower(JRequest::getVar('task', ''));
		
		switch ( $this->_task ) 
		{
			case 'download': $this->download(); break;
			case 'save':     $this->save();     break;
			case 'edit':     $this->edit();     break;
			case 'add':      $this->add();      break;
			case 'delete':   $this->delete();   break;
			case 'browse':   $this->browse();   break;
			case 'intro':    $this->intro();    break;

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
	
	private function _getStyles($option='') 
	{
		ximport('xdocument');
		$option = ($option) ? $option : $this->_option;
		XDocument::addComponentStylesheet($option);
	}

	//-----------
	
	private function _getScripts()
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
		$types = $this->types;
		foreach ($types as $t=>$x) 
		{
			$view->typestats[$x] = $row->getCount( array('type'=>$t), false );
		}
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------

	protected function browse()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'browse') );
		$view->title = JText::_(strtoupper($this->_name));
		$view->option = $this->_option;
		$view->database = $this->database;
		
		$view->format = ($this->config->get('format')) ? $this->config->get('format') : 'APA';
		
		// Incoming
		$view->filters = array();
		$view->filters['limit']  = JRequest::getInt( 'limit', 50, 'request' );
		$view->filters['start']  = JRequest::getInt( 'limitstart', 0, 'get' );
		$view->filters['type']   = JRequest::getVar( 'type', '' );
		$view->filters['filter'] = JRequest::getVar( 'filter', '' );
		$view->filters['year']   = JRequest::getInt( 'year', 0 );
		$view->filters['sort']   = JRequest::getVar( 'sort', 'sec_cnt DESC' );
		$view->filters['search'] = JRequest::getVar( 'search', '' );
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
		$view->types = array_merge(array('all'=>JText::_('ALL')), $this->types);
		
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
		
		// Push some styles to the template
		$this->_getStyles();
		
		// Set the page title
		$this->_buildTitle();
		
		// Set the pathway
		$this->_buildPathway();
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}

	//-----------
	
	protected function login() 
	{
		$view = new JView( array('name'=>'login') );
		$view->title = JText::_(strtoupper($this->_name)).': '.JText::_(strtoupper($this->_task));
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
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
		$view->types = $this->types;
		
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
		} else {
			// Get the associations
			$view->assocs = $assoc->getRecords( array('cid'=>$id) );
		}
		
		// Output HTML
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
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
		
		// Bind incoming data to object
		$row = new CitationsCitation( $this->database );
		if (!$row->bind( $_POST )) {
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

	//----------------------------------------------------------
	// Download
	//----------------------------------------------------------
	
	protected function download()
	{
		// Incoming
		$id = JRequest::getInt( 'id', 0, 'request' );
		$format = strtolower(JRequest::getVar( 'format', 'bibtex', 'request' ));
		
		// Esnure we have an ID to work with
		if (!$id) {
			JError::raiseError( 404, JText::_('NO_CITATION_ID') );
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
	
	//-----------
	
	private function _serveup($inline = false, $p, $f, $mime)
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
		
        $this->_readfile_chunked($p.$f);
        // The caller MUST 'die();'
    }
    
	//-----------
	
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

	//-----------

	private function _buildPathway() 
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
	
	//-----------
	
	private function _buildTitle() 
	{
		$title = JText::_(strtoupper($this->_name));
		if ($this->_task) {
			$title .= ': '.JText::_(strtoupper($this->_task));
		}
		$document =& JFactory::getDocument();
		$document->setTitle( $title );
	}

	//----------------------------------------------------------
	// Authorization checks
	//----------------------------------------------------------
	
	private function _authorize()
	{
		// Check if they are logged in
		$juser =& JFactory::getUser();
		if ($juser->get('guest')) {
			return false;
		}
		
		// Check if they're a site admin (from Joomla)
		if ($juser->authorize($this->_option, 'manage')) {
			return true;
		}

		return false;
	}
}
?>
