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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

ximport('Hubzero_Controller');

/**
 * Short description for 'XFlashController'
 * 
 * Long description (if any) ...
 */
class XFlashController extends Hubzero_Controller
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
		$this->_task = JRequest::getVar( 'task', '' );

		switch ($this->_task)
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

	//----------------------------------------------------------
	// Views
	//----------------------------------------------------------

	/**
	 * Short description for 'edit'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function edit()
	{
		// Instantiate a new view
		$view = new JView( array('name'=>'edit') );
		$view->option = $this->_option;
		$view->task = $this->_task;

		// Get front-end temlate name
		$this->database->setQuery( "SELECT template FROM #__templates_menu WHERE client_id=0" );
		$view->template = $this->database->loadResult();

		$uploadpath = $this->config->get('uploadpath');
		if (substr($uploadpath, 0, 1) != DS) {
			$uploadpath = DS.$uploadpath;
		}
		if (substr($uploadpath, -1, 1) == DS) {
			$uploadpath = substr($uploadpath, 0, (strlen($uploadpath) - 1));
		}

		$dom = new domDocument;
		$xml_file = is_file(JPATH_ROOT.$uploadpath.DS.'flashdata.xml') ?  JPATH_ROOT.$uploadpath.DS.'flashdata.xml':'../components/'.$this->_option.'/flashdata.xml';
		$dom->load($xml_file);
		if (!$dom) {
			JError::raiseError( 500, JText::_('Error while parsing the document') );
			return;
		}

		$xhub =& Hubzero_Factory::getHub();

		// Store XML data in array 
		$view->xml = simplexml_import_dom($dom);

		$view->num_featured = ($this->config->get('num_featured')) ? intval($this->config->get('num_featured')) : 3;
		$view->url = $xhub->getCfg('hubLongURL');

		// Default settings
		$view->defaults = array ('sBorderCol' => '000000',
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
						  'imagePath' => '..'.$uploadpath.DS,
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

		// Push some styles and scripts to the template
		$document =& JFactory::getDocument();
		$noflashfile = $uploadpath.DS.'noflash.jpg';
		$swffile = '/components'.DS.$this->_option.DS.'flashrotation';

		$document->addScript('../modules/mod_xflash/mod_xflash.js');
		$document->addScriptDeclaration('HUB.ModXflash.admin="1"; HUB.ModXflash.src="'.$swffile.'";');
		$document->addStyleSheet('/administrator/components'.DS.$this->_option.DS.'admin.xflash.css');

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	//----------------------------------------------------------
	// Save 
	//----------------------------------------------------------

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$prefs = array_map('trim', $_POST['prefs']);
		$cols = array('sBorderCol','bColOff','bColOn','bSelected', 'bTextOff','bTextOn','transitionCol','headerCol','subtitleCol','bodyCol','linkCol');
		$slides = $_POST['slide'];
		$res = $_POST['res'];

		$uploadpath = $this->config->get('uploadpath');
		if (substr($uploadpath, 0, 1) != DS) {
			$uploadpath = DS.$uploadpath;
		}
		if (substr($uploadpath, -1, 1) == DS) {
			$uploadpath = substr($uploadpath, 0, (strlen($uploadpath) - 1));
		}

		// Start xml output
		$xml = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n";
		$xml .= " <flashdata>\n";

		// Preferences
		$xml .= "  <Prefs>\n";
		while (list($key, $val) = each($prefs))
		{
			$xml .=" 	<$key>";
			if (in_array($key, $cols) && $val!='') {
				$xml .="0x"; // Colors only, needed for Flash
			}
			$xml .="$val";
			$xml .="</$key>\n";

		}
		$xml .= "  </Prefs>\n";

		// Slides
		$xml .= "  <Slides>\n";
		foreach ($slides as $slide)
		{
			if (($slide['sType']=="regular" && $slide['header'] or $slide['bg']) or $slide['sType']!="regular" ) { // check that slide has some meaningful information
				$xml .=" 	<Slide>\n";
				$xml .=" 		<sType>$slide[sType]</sType>\n";
				$xml .=" 		<bgcol>";
				if ($slide['bgcol']) {
					$xml .="0x$slide[bgcol]";
				}
				$xml .="</bgcol>\n";
				$xml .=" 		<bg>$slide[bg]</bg>\n";
				$xml .=" 		<ima>".htmlspecialchars($slide['ima'])."</ima>\n";
				$xml .=" 		<header>".htmlspecialchars(stripslashes($slide['header']))."</header>\n";
				$xml .=" 		<headerCol>";
				if ($slide['headerCol']) {
					$xml .="0x$slide[headerCol]";
				}
				$xml .="</headerCol>\n";
				$xml .=" 		<subtitle>".htmlspecialchars(stripslashes($slide['subtitle']))."</subtitle>\n";
				$xml .=" 		<subtitleCol>";
				if ($slide['subtitleCol']) {
					$xml .="0x$slide[subtitleCol]";
				}
				$xml .="</subtitleCol>\n";
				$xml .=" 		<body>".htmlspecialchars(stripslashes($slide['body']))."</body>\n";
				$xml .=" 		<bodyCol>";
				if ($slide['bodyCol']) {
					$xml .="0x$slide[bodyCol]";
				}
				$xml .="</bodyCol>\n";
				$xml .=" 		<path>".htmlspecialchars($slide['path'])."</path>\n";
				$xml .=" 		<pathLabel>".htmlspecialchars($slide['pathLabel'])."</pathLabel>\n";
				$xml .=" 	</Slide>\n";
			}
		}
		$xml .= "  </Slides>\n";

		// Get selected quotes in random order
		$this->database->setQuery( "SELECT * FROM #__selected_quotes WHERE flash_rotation=1 ORDER BY RAND()" );
		$quotes = $this->database->loadObjectList();

		$xml .= "  <Quotes>\n";
		if ($quotes) {
			foreach ($quotes as $quote)
			{
				$xml .= " 	<Quote>\n";
				$xml .= " 		<author>".htmlspecialchars($quote->fullname)."</author>\n";
				$xml .= " 		<affiliation>".htmlspecialchars($quote->org)."</affiliation>\n";
				$xml .= " 		<body>".htmlspecialchars($quote->short_quote)."</body>\n";
				$xml .= " 		<ima>".htmlspecialchars($quote->picture)."</ima>\n";
				$xml .= " 	</Quote>\n";
			}
		}
		$xml .= "  </Quotes>\n";

		// Featured resources
		$xml .= "  <Resources>\n";
		foreach ($res as $r)
		{
			if (($r['rTitle'] && $r['body'] && ($r['rid']) or $r['alias'] && $r['category'])) { // check that resource has some meaningful information
				$xml .= " 	<Resource>\n";
				$xml .= " 		<rid>".intval($r['rid'])."</rid>\n";
				$xml .= " 		<alias>".htmlspecialchars($r['alias'])."</alias>\n";
				$xml .= " 		<rTitle>".htmlspecialchars($r['rTitle'])."</rTitle>\n";
				$xml .= " 		<category>".htmlspecialchars($r['category'])."</category>\n";
				$xml .= " 		<tagline>".htmlspecialchars($r['tagline'])."</tagline>\n";
				$xml .= " 		<body>".htmlspecialchars($r['body'])."</body>\n";
				$xml .= " 		<ima>".htmlspecialchars($r['ima'])."</ima>\n";
				$xml .= " 	</Resource>\n";
			}
		}
		$xml .= "  </Resources>\n";

		$xml .= " </flashdata>\n";

		$fh=fopen(JPATH_ROOT.$uploadpath.DS.'flashdata.xml', "w");
		fwrite($fh,utf8_encode($xml));
		fclose($fh);

		$this->edit();
	}

	//----------------------------------------------------------
	// media manager
	//----------------------------------------------------------

	/**
	 * Short description for 'upload'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     unknown Return description (if any) ...
	 */
	protected function upload()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$uploadpath = $this->config->get('uploadpath');
		if (substr($uploadpath, 0, 1) != DS) {
			$uploadpath = DS.$uploadpath;
		}
		if (substr($uploadpath, -1, 1) == DS) {
			$uploadpath = substr($uploadpath, 0, (strlen($uploadpath) - 1));
		}

		if (!is_dir( JPATH_ROOT.$uploadpath )) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( JPATH_ROOT.$uploadpath, 0777 )) {
				$this->setError( JText::_('Error uploading. Unable to create path.') );
				$this->media();
				return;
			}
		}

		$file = JRequest::getVar( 'upload', '', 'files', 'array' );

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);

		if (!JFile::upload($file['tmp_name'], JPATH_ROOT.$uploadpath.DS.$file['name'])) {
			$this->setError( JText::_('Error uploading.') );
		}

		$this->media();
	}

	/**
	 * Short description for 'delete_file'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function delete_file()
	{
		// Check for request forgeries
		JRequest::checkToken('get') or jexit( 'Invalid Token' );

		$delFile = trim(JRequest::getVar( 'delFile', '','get'));

		$uploadpath = $this->config->get('uploadpath');
		if (substr($uploadpath, 0, 1) != DS) {
			$uploadpath = DS.$uploadpath;
		}
		if (substr($uploadpath, -1, 1) == DS) {
			$uploadpath = substr($uploadpath, 0, (strlen($uploadpath) - 1));
		}

		$del_file = JPATH_ROOT.$uploadpath.DS.$delFile;
		unlink($del_file);

		$this->media();
	}

	/**
	 * Short description for 'media'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function media()
	{
		$view = new JView( array('name'=>'edit', 'layout'=>'media') );
		$view->option = $this->_option;
		$view->task = $this->_task;
		$view->config = $this->config;

		// Set any errors
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}

		// Output the HTML
		$view->display();
	}

	/**
	 * Short description for 'list_files'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	protected function list_files()
	{
		$uploadpath = $this->config->get('uploadpath');
		if (substr($uploadpath, 0, 1) != DS) {
			$uploadpath = DS.$uploadpath;
		}
		if (substr($uploadpath, -1, 1) == DS) {
			$uploadpath = substr($uploadpath, 0, (strlen($uploadpath) - 1));
		}

		$d = @dir(JPATH_ROOT.$uploadpath);

		$images  = array();
		$folders = array();
		$docs    = array();

		if ($d) {
			// Loop through all files and separate them into arrays of images, folders, and other
			while (false !== ($entry = $d->read()))
			{
				$img_file = $entry;
				if (is_file(JPATH_ROOT.$uploadpath.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
					if (eregi( "gif|jpg|png", $img_file )) {
						$images[$entry] = $img_file;
					} else {
						$docs[$entry] = $img_file;
					}
				} else if(is_dir(JPATH_ROOT.$uploadpath.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'cvs') {
					$folders[$entry] = $img_file;
				}
			}
			$d->close();

			ksort($images);
			ksort($folders);
			ksort($docs);
		}

		$view = new JView( array('name'=>'edit', 'layout'=>'filelist') );
		$view->option = $this->_option;
		$view->docs = $docs;
		$view->folders = $folders;
		$view->images = $images;
		$view->config = $this->config;
		if ($this->getError()) {
			$view->setError( $this->getError() );
		}
		$view->display();
	}
}

