<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

class XFlashController
{	
	private $_name  = NULL;
	private $_data  = array();
	private $_task  = NULL;
	private $_error = NULL;

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
		$task = JRequest::getVar( 'task', '' );
		$this->_task = $task;
		return $task;
	}
	
	//-----------
	
	public function execute()
	{
		// Get the component parameters
		$fconfig = new XFlashConfig( $this->_option );
		$this->config = $fconfig;
		
		switch( $this->getTask() ) 
		{
			case 'edit': 		 $this->edit();   		 break;
			case 'savedata': 	 $this->save();   		 break;
			
			case 'media':		 $this->media();		 break;
			case 'list':         $this->list_files();    break;
			case 'upload':       $this->upload();        break;
			case 'deletefolder': $this->delete_folder(); break;
			case 'deletefile':   $this->delete_file();   break;


			default: $this->edit(); break;
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
	
	private function getScripts($option='',$name='')
	{
		$document =& JFactory::getDocument();
		if ($option) {
			$name = ($name) ? $name : $option;
			if (is_file(JPATH_ROOT.DS.'components'.DS.'com_'.$option.DS.$name.'.js')) {
				$document->addScript('components'.DS.'com_'.$option.DS.$name.'.js');
			}
		} else {
			if (is_file(JPATH_ROOT.DS.'components'.DS.$this->_option.DS.$this->_name.'.js')) {
				$document->addScript('components'.DS.$this->_option.DS.$this->_name.'.js');
			}
		}
	}
	
	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	protected function edit() 
	{
		$database =& JFactory::getDBO();
		$xhub =& XFactory::getHub();
		
		// get front-end temlate name
		$sql = "SELECT template FROM #__templates_menu WHERE client_id=0";
		$database->setQuery( $sql );
		$template = $database->loadResult();
		
		$dom = new domDocument;
		$xml_file = is_file(JPATH_ROOT.$this->config->parameters['uploadpath'].'flashdata.xml') ?  JPATH_ROOT.$this->config->parameters['uploadpath'].'flashdata.xml':'../components/'.$this->_option.'/flashdata.xml';
		$dom->load($xml_file);
		if (!$dom) {
			echo 'Error while parsing the document';
			exit;
		}
		 
		// store XML data in array 
		$xml = simplexml_import_dom($dom);
		$num_featured = (isset($this->config->parameters['num_featured']) && intval($this->config->parameters['num_featured'])) ? intval($this->config->parameters['num_featured']) : 3;
	
		// default settings
		$defaults = array ('sBorderCol' => '000000',
						  'bAlphaOff' => '30',
						  'bAlphaOn' => '40',
						  'bColOff' => 'FFFFFF',
						  'bColOn' => 'CCCCCC',
						  'bSelected' => 'CCCCCC',
						  'bTextOff' => 'FFFFFF',
						  'bTextOn' => 'FFFFFF',
						  'transition' => 'wave',
						  'transitionCol' => 'FFFFFF',
						  'countdown' => '5',
						  'imagePath' => '..'.$this->config->parameters['uploadpath'],
						  'serverPath' => $xhub->getCfg('hubLongURL'),
						  'watermark' => 'waves',
						  'headerCol' => '000000',
						  'subtitleCol' => '999999',
						  'bodyCol' => '000000',
						  'linkCol' => '2debc1',
						  'headerFont' => 'Trebuchet MS',
						  'bodyFont' => 'Arial',
						  'headerSize' => '24',
						  'subtitleSize' => '12',
						  'bodySize' => '12' );
		
		
		// output HTML
		$document =& JFactory::getDocument();
		$noflashfile = $this->config->parameters['uploadpath'].'noflash.jpg';
		$swffile = '/components'.DS.$this->_option.DS.'flashrotation';
		
		$document->addScript('../modules/mod_xflash/mod_xflash.js');
		$document->addScriptDeclaration('HUB.ModXflash.admin="1"; HUB.ModXflash.src="'.$swffile.'";');
		$document->addStyleSheet('/administrator/components'.DS.$this->_option.DS.'admin.xflash.css');
		XFlashHTML::edit($xml, $xhub->getCfg('hubLongURL'), $defaults, $this->_option, $num_featured, $template);
	}
	
	//----------------------------------------------------------
	// Save 
	//----------------------------------------------------------
	
	protected function save()
	{
		$database 	=& JFactory::getDBO();		
		$prefs 		= array_map('trim', $_POST['prefs']);
		$cols 		= array('sBorderCol','bColOff','bColOn','bSelected', 'bTextOff','bTextOn','transitionCol','headerCol','subtitleCol','bodyCol','linkCol');
		$slides 	= $_POST['slide'];
		$res 		= $_POST['res'];
	
		// start xml output
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml.= " <flashdata>\n";
		
		// preferences
		$xml.= "  <Prefs>\n";
		while (list($key, $val) = each($prefs)) 
		{	
			$xml.=" 	<$key>";
			if (in_array($key, $cols) && $val!='') {
				$xml.="0x"; // colors only, needed for Flash
			}
			$xml.="$val";
			$xml.="</$key>\n";
			
		}
		$xml.= "  </Prefs>\n";
		
		//slides
		$xml.= "  <Slides>\n";
		foreach ($slides as $slide) 
		{
			if (($slide['sType']=="regular" && $slide['header'] or $slide['bg']) or $slide['sType']!="regular" ) { // check that slide has some meaningful information
				$xml.=" 	<Slide>\n";
				$xml.=" 		<sType>$slide[sType]</sType>\n";
				$xml.=" 		<bgcol>";
				if ($slide['bgcol']) {
					$xml.="0x$slide[bgcol]";
				}
				$xml.="</bgcol>\n";
				$xml.=" 		<bg>$slide[bg]</bg>\n";
				$xml.=" 		<ima>".htmlspecialchars($slide['ima'])."</ima>\n";
				$xml.=" 		<header>".htmlspecialchars($slide['header'])."</header>\n";
				$xml.=" 		<headerCol>";
				if ($slide['headerCol']) {
					$xml.="0x$slide[headerCol]";
				}
				$xml.="</headerCol>\n";
				$xml.=" 		<subtitle>".htmlspecialchars($slide['subtitle'])."</subtitle>\n";
				$xml.=" 		<subtitleCol>";
				if ($slide['subtitleCol']) {
					$xml.="0x$slide[subtitleCol]";
				}
				$xml.="</subtitleCol>\n";
				$xml.=" 		<body>".htmlspecialchars(stripslashes($slide['body']))."</body>\n";
				$xml.=" 		<bodyCol>";
				if ($slide['bodyCol']) {
					$xml.="0x$slide[bodyCol]";
				}
				$xml.="</bodyCol>\n";
				$xml.=" 		<path>".htmlspecialchars($slide['path'])."</path>\n";
				$xml.=" 		<pathLabel>".htmlspecialchars($slide['pathLabel'])."</pathLabel>\n";
				$xml.=" 	</Slide>\n";
			}
		}
		$xml.= "  </Slides>\n";
		
		//get selected quotes in random order
		$sql = "SELECT * FROM #__selected_quotes WHERE flash_rotation=1";
		$sql .= "\n ORDER BY RAND()";
		$database->setQuery( $sql );
		$quotes = $database->loadObjectList();
		
		$xml.= "  <Quotes>\n";
		if ($quotes) {			
			foreach ($quotes as $quote) 
			{
				$xml.= " 	<Quote>\n";
				$xml.= " 		<author>".htmlspecialchars($quote->fullname)."</author>\n";
				$xml.= " 		<affiliation>".htmlspecialchars($quote->org)."</affiliation>\n";
				$xml.= " 		<body>".htmlspecialchars($quote->short_quote)."</body>\n";
				$xml.= " 		<ima>".htmlspecialchars($quote->picture)."</ima>\n";
				$xml.= " 	</Quote>\n";
			}	
		}
		$xml.= "  </Quotes>\n";	
		
		// featured resources
		$xml.= "  <Resources>\n";
		foreach ($res as $r) 
		{
			if (($r['rTitle'] && $r['body'] && ($r['rid']) or $r['alias'] && $r['category'])) { // check that resource has some meaningful information
				$xml.= " 	<Resource>\n";
				$xml.= " 		<rid>".intval($r['rid'])."</rid>\n";
				$xml.= " 		<alias>".htmlspecialchars($r['alias'])."</alias>\n";
				$xml.= " 		<rTitle>".htmlspecialchars($r['rTitle'])."</rTitle>\n";
				$xml.= " 		<category>".htmlspecialchars($r['category'])."</category>\n";
				$xml.= " 		<tagline>".htmlspecialchars($r['tagline'])."</tagline>\n";
				$xml.= " 		<body>".htmlspecialchars($r['body'])."</body>\n";
				$xml.= " 		<ima>".htmlspecialchars($r['ima'])."</ima>\n";
				$xml.= " 	</Resource>\n";
			}
		}
		$xml.= "  </Resources>\n";
		
		$xml.= " </flashdata>\n";
		
		//printf($xml);
		
		//$fh=fopen('../components/'.$this->_option.'/flashdata.xml',"w"); 
		$fh=fopen(JPATH_ROOT.$this->config->parameters['uploadpath'].'flashdata.xml', "w");
		fwrite($fh,utf8_encode($xml)); 
		fclose($fh);
		
		$this->edit();	
	}
	
	//----------------------------------------------------------
	// media manager
	//----------------------------------------------------------

	protected function upload()
	{
		ximport('fileupload');
		ximport('fileuploadutils');
		
		$database =& JFactory::getDBO();	
	
		$file_path = JPATH_ROOT.$this->config->parameters['uploadpath'];

		if (!is_dir( $file_path )) {
			FileUploadUtils::make_path( $file_path );
		}
		
		// upload new files
		$upload = new FileUpload;
		$upload->upload_dir     = $file_path;
		$upload->temp_file_name = trim($_FILES['upload']['tmp_name']);
		$upload->file_name      = trim(strtolower($_FILES['upload']['name']));
		$upload->file_name      = str_replace(' ', '_', $upload->file_name);
		$upload->ext_array      = $this->config->parameters['file_ext'];
		$upload->max_file_size  = $this->config->parameters['maxAllowed'];
		
		$result = $upload->upload_file_no_validation();
		
		if (!$result) {
			$this->_error = 'Error uploading. '.$upload->err;
		}
		
		$this->media();
	}

	//-----------

	protected function delete_file() 
	{
		$database =& JFactory::getDBO();	
		
		$delFile = trim(JRequest::getVar( 'delFile', '','get'));

		$del_file = JPATH_ROOT.$this->config->parameters['uploadpath'].$delFile;
		unlink($del_file);
	
		$this->media();
	}

	//-----------

	protected function media() 
	{
		$document =& JFactory::getDocument();
		$document->addStyleSheet('/administrator/components'.DS.$this->_option.DS.'admin.xflash.css');
		
		if ($this->_error) {
			XFlashHTML::error($this->_error);
		}
		
		XFlashHTML::media($this->config);
	}

	//-----------

	protected function recursive_listdir($base) 
	{ 
	    static $filelist = array(); 
	    static $dirlist  = array(); 

	    if (is_dir($base)) { 
	       $dh = opendir($base); 
	       while (false !== ($dir = readdir($dh))) 
		   { 
	           if (is_dir($base .DS. $dir) && $dir !== '.' && $dir !== '..' && strtolower($dir) !== 'cvs') { 
	                $subbase    = $base .DS. $dir; 
	                $dirlist[]  = $subbase; 
	                $subdirlist = $this->recursive_listdir($subbase); 
	            } 
	        } 
	        closedir($dh); 
	    } 
	    return $dirlist; 
	} 

	//-----------
 
	protected function list_files() 
	{
		$d = @dir(JPATH_ROOT.$this->config->parameters['uploadpath']);

		if ($d) {
			$images  = array();
			$folders = array();
			$docs    = array();
	
			while (false !== ($entry = $d->read())) 
			{
				$img_file = $entry; 
				if (is_file(JPATH_ROOT.$this->config->parameters['uploadpath'].$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "gif|jpg|png", $img_file )) {
						$images[$entry] = $img_file;
					} else {
						$docs[$entry] = $img_file;
					}
				} else if(is_dir(JPATH_ROOT.$this->config->parameters['uploadpath'].$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'cvs') {
					$folders[$entry] = $img_file;
				}
			}
			$d->close();	

			XFlashHTML::imageStyle( );	

			if (count($images) > 0 || count($folders) > 0 || count($docs) > 0) {	
				ksort($images);
				ksort($folders);
				ksort($docs);

				XFlashHTML::draw_table_header();

				for ($i=0; $i<count($images); $i++) 
				{
					$image_name = key($images);
					$iconfile = $this->config->parameters['iconpath'].'/'.substr($image_name,-3).'.png';
					if (file_exists($iconfile))	{
						$icon = $iconfile;
					} else {
						$icon = $this->config->parameters['iconpath'].'/unknown.png';
					}

					//$a = $this->getAttachmentID($image_name);

					XFlashHTML::show_doc($images[$image_name], $icon);
					next($images);
				}
				
				XFlashHTML::draw_table_footer();
			} else {
				XFlashHTML::draw_no_results();
			}
		} else {
			XFlashHTML::draw_no_results();
		}
	}
}
?>