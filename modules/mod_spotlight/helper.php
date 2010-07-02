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

class modSpotlight
{
		private $attributes = array();

		//-----------
	
		public function __construct( $params ) 
		{
			$this->params = $params;
		}
	
		//-----------
	
		public function __set($property, $value)
		{
			$this->attributes[$property] = $value;
		}
	
		//-----------
	
		public function __get($property)
		{
			if (isset($this->attributes[$property])) {
				return $this->attributes[$property];
			}
		}

		//-----------

		private function niceidformat($someid) 
		{
			$pre = '';
			if ($someid < 0) {
				$pre = 'n';
				$someid = abs($someid);
			}
			while (strlen($someid) < 5) 
			{
				$someid = 0 . "$someid";
			}
			return $pre.$someid;
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
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'profile.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'association.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );
			require_once( JPATH_ROOT.DS.'components'.DS.'com_features'.DS.'features.history.php' );
			
			//ximport('FeatureHistory');
			ximport('xprofile');
			ximport('Hubzero_View_Helper_Html');
			ximport('wiki.wiki');
			
			if (!class_exists('FeaturesHistory')) {
				$this->error = true;
				return false;
			}
			
			$database =& JFactory::getDBO();

			$params =& $this->params;

			//Get the admin configured settings
			$filters = array();
			$filters['limit'] = 5;
			$filters['start'] = 0;
			
			// featured items
			$tbls = array('resources', 'profiles');
			$spots = array();
			$spots[0] = trim($params->get( 'spotone' ));
			$spots[1] = trim($params->get( 'spottwo' ));
			$spots[2] = trim($params->get( 'spotthree' ));
			$spots[3] = trim($params->get( 'spotfour' ));
			$spots[4] = trim($params->get( 'spotfive' ));
			$spots[5] = trim($params->get( 'spotsix' ));
			$numspots = $params->get( 'numspots' ) ? $params->get( 'numspots' ) : 3;			
			
			// some collectors
			$activespots = array();
			$rows = array();
													
			// styling
			$cls = trim($params->get( 'moduleclass_sfx' ));
			$txt_length = trim($params->get( 'txt_length' ));
			
			$start = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 00:00:00";
			$end = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 23:59:59";
			
			$html  = '<div class="spotlightwrap">'."\n";	
			$html .= '<ul>'."\n";			
			$k = 1;
			$out = '';		
						
			for ($i = 0, $n = $numspots; $i < $numspots; $i++) {
			
				$spot = $spots[$i];
				$row = null;
				$out = '';
				$tbl = '';
				$fh = new FeaturesHistory( $database );		
			
				$tbl = ($spot == 'tools' or $spot == 'nontools') ? 'resources' : '';
				$tbl = $spot == 'members' ? 'profiles' : $tbl;
				$tbl = $spot == 'topics' ? 'topics' : $tbl;
				$tbl = $spot == 'itunes' ? 'itunes' : $tbl;
				$tbl = $spot == 'answers' ? 'answers' : $tbl;
				$tbl = !$tbl ? array_rand($tbls, 1) : $tbl;
						
				// Check the feature history for today's feature
				$fh->loadActive($start, $tbl, $spot.$k);
				
				// Did we find a feature for today?
				if ($fh->id ) {
					
					if($fh->tbl == 'resources') {
						// Yes - load the resource
						$row = new ResourcesResource( $database );
						$row->load( $fh->objectid );
						if ($row) {
							$row->typetitle = $row->getTypetitle();
						}
					}				
					else if( $fh->tbl == 'profiles') {
						// Yes - load the member profile
						$row = new MembersProfile( $database );
						$row->load( $fh->objectid );
					}
					else if( $fh->tbl == 'topics') {
						// Yes - load the topic page
						$row = new WikiPage( $database );
						$row->load( $fh->objectid );
					}
					else if( $fh->tbl == 'itunes') {
						// Yes - load the iTunes course
						$row = new ResourcesResource( $database );
						$row->load( $fh->objectid );
						if ($row) {
							$row->typetitle = $row->getTypetitle();
						}
					}
					else if( $fh->tbl == 'answers') {
						// Yes - load the question
						$row = new AnswersQuestion( $database );
						$row->load( $fh->objectid );
						
						$ar = new AnswersResponse( $database );
						$row->rcount = count($ar->getIds( $row->id ));
					}
				}
				else {
					// No - so we need to randomly choose one					
					if($tbl == 'resources') {
					
						// Initiate a resource object
						$rr = new ResourcesResource( $database );
						$filters['start'] = 0;
						$filters['type'] = $spot;
						$filters['sortby'] = 'random';
						$filters['minranking'] = trim($params->get( 'minranking' ));
						$filters['tag'] = $spot == 'tools' ? trim($params->get( 'tag' )) : ''; // tag is set for tools only
	
						// Get records
						$rows[$spot] = isset($rows[$spot]) ? $rows[$spot] : $rr->getRecords( $filters, false );						
					}
	
					if($tbl == 'profiles') {					
						// No - so we need to randomly choose one
						$filters['start'] = 0;
						$filters['sortby'] = "RAND()";
						$filters['search'] = '';
						$filters['authorized'] = false;
						$filters['tag'] = '';
						$filters['contributions'] = trim($params->get( 'min_contributions' ));
						$filters['show'] = trim($params->get( 'show' ));
					
						$mp = new MembersProfile( $database );
						
						// Get records
						$rows[$spot] = isset($rows[$spot]) ? $rows[$spot] : $mp->getRecords( $filters, false );							
					}
					if($tbl == 'topics') {					
						// No - so we need to randomly choose one
						$topics_tag = trim($params->get( 'topics_tag' ));
						$query  = "SELECT DISTINCT w.id, w.pagename, w.title  ";
						$query .= " FROM #__wiki_page AS w ";
						if($topics_tag) {
						$query .= " JOIN #__tags_object AS RTA ON RTA.objectid=w.id AND RTA.tbl='wiki' ";
						$query .= " INNER JOIN #__tags AS TA ON TA.id=RTA.tagid ";
						}
						$query .= " WHERE w.access!=1 ";
						if($topics_tag) {
						$query .= " AND TA.tag='".$topics_tag."' or TA.tag='".$topics_tag."'";
						}		
						$query .= " ORDER BY RAND() ";
						$database->setQuery( $query );
						$rows[$spot] = isset($rows[$spot]) ? $rows[$spot] : $database->loadObjectList();					
					}
					
					if($tbl == 'itunes') {
					
						// Initiate a resource object
						$rr = new ResourcesResource( $database );
						$filters['start'] = 0;
						$filters['sortby'] = 'random';
						$filters['tag'] = trim($params->get( 'itunes_tag' ));
	
						// Get records
						$rows[$spot] = isset($rows[$spot]) ? $rows[$spot] : $rr->getRecords( $filters, false );						
					}
					if($tbl == 'answers') {
						$query  = "SELECT C.id, C.subject, C.question, C.created, C.created_by, C.anonymous  ";
						$query .= ", (SELECT COUNT(*) FROM #__answers_responses AS a WHERE a.state!=2 AND a.qid=C.id) AS rcount ";
						//$query .= ", (SELECT SUM(tr.amount) FROM #__users_transactions AS tr WHERE tr.category='answers' AND tr.type='hold' AND tr.referenceid=C.id ) AS points";
						$query .= " FROM #__answers_questions AS C ";
						$query .= " WHERE C.state=0 ";	
						$query .= " AND (C.reward=1 OR C.helpful>1) ";		
						$query .= " ORDER BY RAND() ";
						$database->setQuery( $query );
		
						$rows[$spot] = isset($rows[$spot]) ? $rows[$spot] : $database->loadObjectList();	
					}
					
					if ($rows && count($rows[$spot]) > 0) {
						$row = $rows[$spot][0];
					}
					
					// make sure we aren't pulling the same item
					if($k != 1 && in_array($spot, $activespots) && $rows && count($rows[$spot]) > 1 ){
						$row = count($rows[$spot]) < $k ? $rows[$spot][$k-1] : $rows[$spot][1]; // get the next one
					}									
				}
								
				// pull info
				if($row) {					
					$out = $this->composeEntry ($row, $tbl, $txt_length ,$database);
					$itemid = $this->composeEntry ($row, $tbl, 0, $database, 1);
					$activespots[] = $spot;		
				}
			
				// Did we get any results?
				if ($out ) {
					$html .= '<li class="spot_'.$k.'">'.$out.'</li>'."\n";
										
					// Check if this has been saved in the feature history					
					if (!$fh->id) {
						$fh->featured = $start;
						$fh->objectid = $itemid;
						$fh->tbl = $tbl;
						$fh->note = $spot.$k;
						$fh->store();
					}
					
					$k++;
				}		
			}

			$html .= '</ul>'."\n";
			$html .= '</div>'."\n";
				
			// Output HTML
			return $html;
		}
		
		//-----------
		
		private function composeEntry( $row, $tbl, $txt_length = 0, $database, $getid = 0 ) 
		{
			$out = '';
			
			// Do we have a picture?
			$thumb = '';
					
			if($tbl == 'profiles') {
					
				// Load their bio
				$profile = new XProfile();
				$profile->load( $row->uidNumber );
				
				if($getid) {
					return $row->uidNumber;
				}
				
				$title = $row->name;
				if (!trim($title)) {
					$title = $row->givenName.' '.$row->surname;
				}
				
				if (isset($row->picture) && $row->picture != '') {
					// Yes - so build the path to it
					$mconfig =& JComponentHelper::getParams( 'com_members' );
					$thumb  = $mconfig->get('webpath');
					if (substr($thumb, 0, 1) != DS) {
						$thumb = DS.$thumb;
					}
					if (substr($thumb, -1, 1) == DS) {
						$thumb = substr($thumb, 0, (strlen($thumb) - 1));
					}
					$thumb .= DS.$this->niceidformat($row->uidNumber).DS.$row->picture;
					
					// No - use default picture
					if (is_file(JPATH_ROOT.$thumb)) {
						
						// Build a thumbnail filename based off the picture name
						$thumb = $this->thumb( $thumb );
						
						if (!is_file(JPATH_ROOT.$thumb)) {
							// Create a thumbnail image
							include_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'members.imghandler.php' );
							$ih = new MembersImgHandler();
							$ih->set('image',$row->picture);
							$ih->set('path',JPATH_ROOT.$config->get('webpath').DS.$this->niceidformat($row->uidNumber).DS);
							$ih->set('maxWidth', 50);
							$ih->set('maxHeight', 50);
							$ih->set('cropratio', '1:1');
							$ih->set('outputName', $ih->createThumbName());
						}
					}
					
					// No - use default picture
					if (!is_file(JPATH_ROOT.$thumb)) {
						$thumb = $mconfig->get('defaultpic');
						if (substr($thumb, 0, 1) != DS) {
							$thumb = DS.$thumb;
						}
						// Build a thumbnail filename based off the picture name
						$thumb = $this->thumb( $thumb );
					}
				}				
				
				$out .= '<span class="spotlight-img"><a href="'.JRoute::_('index.php?option=com_members&id='.$row->uidNumber).'"><img width="30" height="30" src="'.$thumb.'" alt="" /></a></span>'."\n";
				$out .= '<span class="spotlight-item"><a href="'. JRoute::_('index.php?option=com_members&id='.$row->uidNumber).'">'.$title.'</a></span>, '.$row->organization."\n";
				$numcontributions = $this->countContributions( $row->uidNumber, $database );
				//$ave_ranking = $this->getAverageRanking( $row->uidNumber, $database);
				$out .= ' - '.JText::_('Contributions').':&nbsp;'.$numcontributions.''."\n";
				//$out .= ' - '.JText::_('Contributions').': '.$numcontributions.'; '.JText::_('Average resource ranking').': '.round($ave_ranking, 2).''."\n";
				$out .= '<div class="clear"></div>'."\n";			
			}
			// topics
			else if ($tbl == 'topics') {
				if($getid) {
					return $row->id;
				}
				$thumb = trim($this->params->get( 'default_topicpic' ));
				$out .= '<span class="spotlight-img"><a href="'.JRoute::_('index.php?option=com_topics&pagename='.$row->pagename).'"><img width="30" height="30" src="'.$thumb.'" alt="" /></a></span>'."\n";
				$out .= '<span class="spotlight-item"><a href="'.JRoute::_('index.php?option=com_topics&pagename='.$row->pagename).'">'.stripslashes($row->title).'</a></span> ';
				$out .=  ' - '.JText::_('in').' <a href="'.JRoute::_('index.php?option=com_topics').'">'.JText::_('Topics').'</a>'."\n";
				$out .= '<div class="clear"></div>'."\n";
			}
			// questions
			else if ($tbl == 'answers') {
				if($getid) {
					return $row->id;
				}
				$thumb = trim($this->params->get( 'default_questionpic' ));
				
				$name = JText::_('Anonymous');
				if ($row->anonymous == 0) {
					$juser =& JUser::getInstance( $row->created_by );
					if (is_object($juser)) {
						$name = $juser->get('name');
					}
				}
				//$when = Hubzero_View_Helper_Html::timeAgo(Hubzero_View_Helper_Html::mkt($row->created)).' '.JText::_('ago');
				
				$out .= '<span class="spotlight-img"><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$row->id).'"><img width="30" height="30" src="'.$thumb.'" alt="" /></a></span>'."\n";
				$out .= '<span class="spotlight-item"><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$row->id).'">'.stripslashes($row->subject).'</a></span> ';
				$out .=  ' - '.JText::_('asked by').' '.$name.', '.JText::_('in').' <a href="'.JRoute::_('index.php?option=com_answers').'">'.JText::_('Answers').'</a>'."\n";
				$out .= '<div class="clear"></div>'."\n";
			}
			// resources
			else {			
				if($getid) {
					return $row->id;
				}
				
				if($tbl == 'itunes') {
					$thumb = trim($this->params->get( 'default_itunespic' ));
				}	
				else {		
				
					$rconfig =& JComponentHelper::getParams( 'com_resources' );
					$path = $rconfig->get('uploadpath');
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
	
						$versionid = $tv->getVersionIdFromResource( $row->id, 'current' );
	
						$picture = $this->getToolImage( $path, $versionid );
					} else {
						$picture = $this->getImage( $path );
					}
					
					$thumb = $path.DS.$picture;
	
					if (!is_file(JPATH_ROOT.$thumb) or !$picture) {
	
						$thumb = $rconfig->get('defaultpic');
						if (substr($thumb, 0, 1) != DS) {
							$thumb = DS.$thumb;
						}
					}
				}
				
				$normalized = preg_replace("/[^a-zA-Z0-9]/", "", $row->typetitle);
				$normalized = strtolower($normalized);
				
				$chars = strlen ($row->title.$row->typetitle);
				$txt_length = $txt_length ? $txt_length : 100;
				$remaining = $txt_length - $chars;
				$remaining = $remaining <= 0 ? 0 : $remaining;
				$titlecut = $remaining ? 0 : $txt_length - strlen($row->typetitle);

				$row->typetitle = trim(stripslashes($row->typetitle));
				$row->title = stripslashes($row->title);
				
				// resources
				$out .= '<span class="spotlight-img"><a href="'.JRoute::_('index.php?option=com_resources&id='.$row->id).'"><img width="30" height="30" src="'.$thumb.'" alt="" /></a></span>'."\n";
				$out .= '<span class="spotlight-item"><a href="'.JRoute::_('index.php?option=com_resources&id='.$row->id).'">';
				$out .= $titlecut ? Hubzero_View_Helper_Html::shortenText(($row->title), $titlecut, 0) : $row->title;
				$out .= '</a></span>';
				if($row->type == 7 && $remaining > 30) {
					// Show bit of description for tools
					if ($row->introtext) {
						$out .= ': '.Hubzero_View_Helper_Html::shortenText($this->encode_html(strip_tags($row->introtext)), $txt_length, 0);
					}
					else {
						$out .= ': '.Hubzero_View_Helper_Html::shortenText($this->encode_html(strip_tags($row->fulltext)), $txt_length, 0);
					}
				}
				if($tbl == 'itunes') {
					$out .=  ' - '.JText::_('featured on').' <a href="/itunes">'.JText::_('iTunes').'&nbsp;U</a>'."\n";
				}
				else {
					$out .=  ' - '.JText::_('in').' <a href="'.JRoute::_('index.php?option=com_resources'.'&type='.$normalized).'">'.$row->typetitle.'</a>'."\n";
				}
				$out .= '<div class="clear"></div>'."\n";			
			}
		
			return $out;		
		}
		
		//-----------
		
		private function getAverageRanking( $uid, $database ) 
		{
			if ($uid === NULL) {
				 return 0;
			}
			
			// get average ranking of contributed resources
			$query  = "SELECT AVG (R.ranking) ";
			$query .= "FROM #__author_assoc AS AA,  #__resources AS R ";
			$query .= "WHERE AA.authorid = ". $uid ." ";
			$query .= "AND R.id = AA.subid ";
			$query .= "AND AA.subtable = 'resources' ";
			$query .= "AND R.published=1 AND R.standalone=1 AND R.access!=2 AND R.access!=4";
		
			$database->setQuery( $query );
			return $database->loadResult();		
		}
		
		//-----------
		
		private function countContributions( $uid, $database ) 
		{
			if ($uid === NULL) {
				 return 0;
			}
			// get contributions count
			$query  = "SELECT COUNT(*) ";
			$query .= "FROM #__author_assoc AS AA, #__resource_types AS rt, #__resources AS R ";
			$query .= "LEFT JOIN #__resource_types AS t ON R.logical_type=t.id ";
			$query .= "WHERE AA.authorid = ". $uid ." ";
			$query .= "AND R.id = AA.subid ";
			$query .= "AND AA.subtable = 'resources' ";
			$query .= "AND R.published=1 AND R.standalone=1 ";
			//$query .= "AND R.access!=2 AND R.access!=4 ";
			$query .= "AND R.type=rt.id ";
		
			$database->setQuery( $query );
			return $database->loadResult();					
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

		private function thumb( $thumb ) 
		{
			$image = explode('.',$thumb);
			$n = count($image);
			$image[$n-2] .= '_thumb';
			$end = array_pop($image);
			$image[] = $end;
			$thumb = implode('.',$image);
			
			return $thumb;
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