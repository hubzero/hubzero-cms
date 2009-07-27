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

if (!class_exists('modFeaturedresource')) {
	class modFeaturedresource
	{
		private $params;

		//-----------

		public function __construct( $params ) 
		{
			$this->params = $params;
		}

		//-----------

		private function niceidformat($someid) 
		{
			while (strlen($someid) < 5) 
			{
				$someid = 0 . "$someid";
			}
			return $someid;
		}

		//-----------

		private function shortenText($text, $chars=300, $p=1) 
		{
			$text = strip_tags($text);
			$text = trim($text);

			if (strlen($text) > $chars) {
				$text = $text.' ';
				$text = substr($text,0,$chars);
				$text = substr($text,0,strrpos($text,' '));
				$text = $text.' &#8230;';
			}

			if ($text == '') {
				$text = '&#8230;';
			}

			if ($p) {
				$text = '<p>'.$text.'</p>';
			}

			return $text;
		}

		//-----------

		private function encode_html($str, $quotes=1)
		{
			$str = $this->ampersands($str);

			$a = array(
				//'&' => '&#38;',
				'<' => '&#60;',
				'>' => '&#62;',
			);
			if ($quotes) $a = $a + array(
				"'" => '&#39;',
				'"' => '&#34;',
			);

			return strtr($str, $a);
		}

		//-----------

		private function ampersands( $str ) 
		{
			$str = stripslashes($str);
			$str = str_replace('&#','*-*', $str);
			$str = str_replace('&amp;','&',$str);
			$str = str_replace('&','&amp;',$str);
			$str = str_replace('*-*','&#', $str);
			return $str;
		}

		//-----------

		public function display() 
		{
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
			ximport('featurehistory');
			
			if (!class_exists('FeatureHistory')) {
				return JText::_('Error: Missing FeatureHistory class.');
			}
			
			$database =& JFactory::getDBO();

			$params =& $this->params;

			//Get the admin configured settings
			$filters = array();
			$filters['limit'] = 1;
			$filters['start'] = 0;
			$filters['type'] = trim($params->get( 'type' ));
			$filters['sortby'] = 'random';
			$filters['minranking'] = trim($params->get( 'minranking' ));
			$filters['tag'] = trim($params->get( 'tag' ));
			
			$cls = trim($params->get( 'moduleclass_sfx' ));
			$txt_length = trim($params->get( 'txt_length' ));
			$catid = trim($params->get( 'catid' ));
			
			$start = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 00:00:00";
			$end = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 23:59:59";

			$row = null;
			
			$fh = new FeatureHistory( $database );

			// Is a specific content category set?
			if ($catid) {
				// Yes - so we need to check if there's an active article to display
				$juser =& JFactory::getUser();
				$aid = $juser->get('aid', 0);
				
				$contentConfig =& JComponentHelper::getParams( 'com_content' );
				$noauth = !$contentConfig->get('shownoauth');

				$date =& JFactory::getDate();
				$now = $date->toMySQL();

				$nullDate = $database->getNullDate();
				
				// Load an article
				$query = 'SELECT a.*,' .
					' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'.
					' CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug'.
					' FROM #__content AS a' .
					' INNER JOIN #__categories AS cc ON cc.id = a.catid' .
					' INNER JOIN #__sections AS s ON s.id = a.sectionid' .
					' WHERE a.state = 1 ' .
					($noauth ? ' AND a.access <= ' .(int) $aid. ' AND cc.access <= ' .(int) $aid. ' AND s.access <= ' .(int) $aid : '').
					' AND (a.publish_up = '.$database->Quote($nullDate).' OR a.publish_up <= '.$database->Quote($now).' ) ' .
					' AND (a.publish_down = '.$database->Quote($nullDate).' OR a.publish_down >= '.$database->Quote($now).' )' .
					' AND cc.id = '. (int) $catid .
					' AND cc.section = s.id' .
					' AND cc.published = 1' .
					' AND s.published = 1' .
					' ORDER BY a.ordering';
				$database->setQuery($query, 0, $filters['limit']);
				$rows = $database->loadObjectList();
				if (count($rows) > 0) {
					$row = $rows[0];
				}
			}
			
			// Do we have an article to display?
			if (!$row) {
				// No - so we need to display a resource
				
				// Check the feature history for today's feature
				$fh->loadActive($start, 'resources', $filters['type']);
				
				// Did we find a feature for today?
				if ($fh->id && $fh->tbl == 'resources') {
					// Yes - load the resource
					$row = new ResourcesResource( $database );
					$row->load( $fh->objectid );
					if ($row) {
						$row->typetitle = $row->getTypetitle();
					}
				} else {
					// No - so we need to randomly choose one
					
					// Initiate a resource object
					$rr = new ResourcesResource( $database );

					// Get records
					$rows = $rr->getRecords( $filters, false );
					if (count($rows) > 0) {
						$row = $rows[0];
					}
				}
			}
			
			$html = '';

			// Did we get any results?
			if ($row) {
				$config =& JComponentHelper::getParams( 'com_resources' );
				
				// Is this a content article or a member profile?
				if (isset($row->catid)) {
					// Content article
					$id = $row->created_by_alias;
					
					// Check if the article has been saved in the feature history
					$fh->loadObject($row->id, 'content');
					if (!$fh->id) {
						$fh->featured = $start;
						$fh->objectid = $row->id;
						$fh->tbl = 'content';
						$fh->store();
					}
					$rr = new ResourcesResource( $database );
					$rr->load( $id );
					//include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.type.php');
					//$type = new ResourcesType( $rr->type );
					//echo $rr->type;
					$row->typetitle = $rr->getTypetitle();
					$row->type = $rr->type;
				} else {
					// Resource
					$id = $row->id;
					
					// Check if this has been saved in the feature history
					if (!$fh->id) {
						$fh->featured = $start;
						$fh->objectid = $row->id;
						$fh->tbl = 'resources';
						$fh->note = $filters['type'];
						$fh->store();
					}
				}
				
				$path = $config->get('uploadpath');
				if (substr($path, 0, 1) != DS) {
					$path = DS.$path;
				}
				if (substr($path, -1, 1) == DS) {
					$path = substr($path, 0, (strlen($path) - 1));
				}
				$path = $this->build_path( $row->created, $row->id, $path );
				
				if ($row->type == 7) {
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );

					$tv = new ToolVersion( $database );

					$versionid = $tv->getVersionIdFromResource( $id, 'current' );

					$picture = $this->getToolImage( $path, $versionid );
				} else {
					$picture = $this->getImage( $path );
				}
				
				$thumb = $path.DS.$picture;

				if (!is_file(JPATH_ROOT.$thumb)) {
					$thumb = $config->get('defaultpic');
					if (substr($thumb, 0, 1) != DS) {
						$thumb = DS.$thumb;
					}
				}

				$href  = 'index.php?option=com_resources&id='.$id;

				$normalized_valid_chars = 'a-zA-Z0-9';
				$normalized = preg_replace("/[^$normalized_valid_chars]/", "", strtolower($row->typetitle));

				$row->typetitle = trim(stripslashes($row->typetitle));
				if (substr($row->typetitle, -1, 1) == 's' && substr($row->typetitle, -3, 3) != 'ies') {
					$row->typetitle = substr($row->typetitle, 0, strlen($row->typetitle) - 1);
				}

				$html .= '<div class="'.$cls.'">'."\n";
				//$html .= '<h3><a href="'.JRoute::_('index.php?option=com_resources&type='.$normalized).'">'.JText::_('Featured').' '.$row->typetitle.'</a></h3>'."\n";
				$html .= '<h3>'.JText::_('Featured').' '.$row->typetitle.'</h3>'."\n";
				if (is_file(JPATH_ROOT.$thumb)) {
					$html .= '<p class="featured-img"><a href="'.JRoute::_($href).'"><img width="50" height="50" src="'.$thumb.'" alt="" /></a></p>'."\n";
				}
				$html .= '<p><a href="'.JRoute::_($href).'">'.stripslashes($row->title).'</a>: '."\n";
				if ($row->introtext) {
					$html .= $this->shortenText($this->encode_html(strip_tags($row->introtext)), $txt_length, 0)."\n";
				}
				$html .= '</p>'."\n";
				$html .= '</div>'."\n";
			}

			// Output HTML
			return $html;
		}
		
		//-----------
		
		private function getImage( $path ) 
		{
			$d = @dir(JPATH_ROOT.$path);

			$images = array();
			
			if ($d) {
				while (false !== ($entry = $d->read())) 
				{			
					$img_file = $entry; 
					if (is_file(JPATH_ROOT.$path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
						if (eregi( "bmp|gif|jpg|png", $img_file )) {
							$images[] = $img_file;
						}
					}
				}

				$d->close();
			}

			$b = 0;
			if ($images) {
				foreach ($images as $ima) 
				{
					$bits = explode('.',$ima);
					$type = array_pop($bits);
					$img = implode('.',$bits);
					
					if ($img == 'thumb') {
						return $ima;
					}
				}
			}
		}
		
		//-----------
		
		private function getToolImage( $path, $versionid=0 ) 
		{
			// Get contribtool parameters
			$tconfig =& JComponentHelper::getParams( 'com_contribtool' );
			$allowversions = $tconfig->get('screenshot_edit');
			
			if ($versionid && $allowversions) { 
				// Add version directory
				//$path .= DS.$versionid;
			}

			$d = @dir(JPATH_ROOT.$path);

			$images = array();
			$tns = array();
			$all = array();
			$ordering = array();
			$html = '';

			if ($d) {
				while (false !== ($entry = $d->read())) 
				{			
					$img_file = $entry; 
					if (is_file(JPATH_ROOT.$path.DS.$img_file) && substr($entry,0,1) != '.' && strtolower($entry) !== 'index.html') {
						if (eregi( "bmp|gif|jpg|png", $img_file )) {
							$images[] = $img_file;
						}
					}
				}

				$d->close();
			}

			$b = 0;
			if ($images) {
				foreach ($images as $ima) 
				{
					$bits = explode('.',$ima);
					$type = array_pop($bits);
					$img = implode('.',$bits);
					
					if ($img == 'thumb') {
						return $ima;
					}
				}
			}
		}

		//-----------

		private function thumbnail($pic)
		{
			$pic = explode('.',$pic);
			$n = count($pic);
			$pic[$n-2] .= '-tn';
			$end = array_pop($pic);
			$pic[] = 'gif';
			$tn = implode('.',$pic);
			return $tn;
		}
		
		//-----------

		private function build_path( $date, $id, $base='' )
		{
			if ( $date && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $date, $regs ) ) {
				$date = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
			}
			if ($date) {
				$dir_year  = date('Y', $date);
				$dir_month = date('m', $date);
			} else {
				$dir_year  = date('Y');
				$dir_month = date('m');
			}
			$dir_id = $this->niceidformat( $id );

			return $base.DS.$dir_year.DS.$dir_month.DS.$dir_id;
		}
	}
}

//-------------------------------------------------------------

$modfeaturedresource = new modFeaturedresource( $params );

require( JModuleHelper::getLayoutPath('mod_featuredresource') );
