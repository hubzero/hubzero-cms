<?php
/**
 * @package     hubzero-cms
 * @author      Alissa Nedossekina <alisa@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'tables'.DS.'resource.php');
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'profile.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'association.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'question.php' );
		require_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'tables'.DS.'response.php' );
		require_once( JPATH_ROOT.DS.'components'.DS.'com_features'.DS.'tables'.DS.'history.php' );
		include_once( JPATH_ROOT.DS.'components'.DS.'com_blog'.DS.'tables'.DS.'blog.entry.php' );
		include_once( JPATH_ROOT.DS.'components'.DS.'com_blog'.DS.'tables'.DS.'blog.comment.php' );	
		
		ximport('Hubzero_User_Profile');
		ximport('Hubzero_View_Helper_Html');
		
		if (!class_exists('FeaturesHistory')) {
			$this->error = true;
			return false;
		}
		
		$database =& JFactory::getDBO();

		$params =& $this->params;

		// Get the admin configured settings
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
		$spots[6] = trim($params->get( 'spotseven' ));
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
			if($spot == '') {
				continue;
			}
			$row = null;
			$out = '';
			$tbl = '';
			$fh = new FeaturesHistory( $database );		
		
			$tbl = ($spot == 'tools' or $spot == 'nontools') ? 'resources' : '';
			$tbl = $spot == 'members' ? 'profiles' : $tbl;
			$tbl = $spot == 'topics' ? 'topics' : $tbl;
			$tbl = $spot == 'itunes' ? 'itunes' : $tbl;
			$tbl = $spot == 'answers' ? 'answers' : $tbl;
			$tbl = $spot == 'blog' ? 'blog' : $tbl;
			$tbl = !$tbl ? array_rand($tbls, 1) : $tbl;
					
			// Check the feature history for today's feature
			$fh->loadActive($start, $tbl, $spot.$k);
			
			// Did we find a feature for today?
		
			if ($fh->id && $fh->objectid) {
				
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
					include_once(JPATH_ROOT.DS.'components'.DS.'com_wiki'.DS.'tables'.DS.'page.php');
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
				else if( $fh->tbl == 'blog') {			
					// Yes - load the blog
					$row = new BlogEntry( $database );
					$row->load( $fh->objectid );
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
					$filters['state'] = 'public';
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
					$query  = "SELECT DISTINCT w.id, w.pagename, w.title ";
					$query .= " FROM #__wiki_page AS w ";
					if($topics_tag) {
						$query .= " JOIN #__tags_object AS RTA ON RTA.objectid=w.id AND RTA.tbl='wiki' ";
						$query .= " INNER JOIN #__tags AS TA ON TA.id=RTA.tagid ";
					}
					else {
						$query .= ", #__wiki_version AS v ";
					}
					$query .= " WHERE w.access!=1 AND w.scope = ''  ";
					if($topics_tag) {
						$query .= " AND (TA.tag='".$topics_tag."' OR TA.raw_tag='".$topics_tag."') ";
					}		
					else {
						$query .= " AND v.pageid=w.id AND v.approved = 1 AND v.pagetext != '' ";
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
					$query .= " FROM #__answers_questions AS C ";
					$query .= " WHERE C.state=0 ";	
					$query .= " AND (C.reward > 0 OR C.helpful > 0) ";		
					$query .= " ORDER BY RAND() ";
					$database->setQuery( $query );
	
					$rows[$spot] = isset($rows[$spot]) ? $rows[$spot] : $database->loadObjectList();	
				}
				if($tbl == 'blog') {
					$filters = array();
					$filters['limit'] = 1;
					$filters['start'] = 0;
					$filters['state'] = 'public';
					$filters['order'] = "RAND()";
					$filters['search'] = '';
					$filters['scope'] = 'member';
					$filters['group_id'] = 0;
					$filters['authorized'] = false;
					$filters['sql'] = '';
					$mp = new BlogEntry( $database );
					$entry = $mp->getRecords( $filters );

					$rows[$spot] = isset($rows[$spot]) ? $rows[$spot] : $entry;	
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
				if (!$fh->id or !$fh->objectid) {
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
			
			if($getid) {
				return $row->uidNumber;
			}
				
			// Load their bio
			$profile = new Hubzero_User_Profile();
			$profile->load( $row->uidNumber );
			
			$mconfig =& JComponentHelper::getParams( 'com_members' );
			
			if (isset($row->picture) && $row->picture != '') {
				// Yes - so build the path to it
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
						include_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'helpers'.DS.'imghandler.php' );
						$ih = new MembersImgHandler();
						$ih->set('image',$row->picture);
						$ih->set('path',JPATH_ROOT.$config->get('webpath').DS.$this->niceidformat($row->uidNumber).DS);
						$ih->set('maxWidth', 50);
						$ih->set('maxHeight', 50);
						$ih->set('cropratio', '1:1');
						$ih->set('outputName', $ih->createThumbName());
					}
				}
			}	
			// No - use default picture
			if (!$thumb or !is_file(JPATH_ROOT.$thumb)) {
				$thumb = $mconfig->get('defaultpic');
				if (substr($thumb, 0, 1) != DS) {
					$thumb = DS.$thumb;
				}
			}	
			
			$title = $row->name;
			if (!trim($title)) {
				$title = $row->givenName.' '.$row->surname;
			}
			$out .= '<span class="spotlight-img"><a href="'.JRoute::_('index.php?option=com_members&id='.$row->uidNumber).'"><img width="30" height="30" src="'.$thumb.'" alt="'.htmlentities($title).'" /></a></span>'."\n";
			$out .= '<span class="spotlight-item"><a href="'. JRoute::_('index.php?option=com_members&id='.$row->uidNumber).'">'.$title.'</a></span>, '.$row->organization."\n";
			$numcontributions = $this->countContributions( $row->uidNumber, $row->username, $database );
			$out .= ' - '.JText::_('Contributions').':&nbsp;'.$numcontributions.''."\n";
			$out .= '<div class="clear"></div>'."\n";
												
		}
		// blog
		else if ($tbl == 'blog') {
			$thumb = trim($this->params->get( 'default_blogpic', 'modules/mod_spotlight/default.gif' ));

			$profile = new Hubzero_User_Profile();
			$profile->load( $row->created_by );
			
			if($getid) {
				return $row->id;
			}				
			if(!$row->title) {
				$out = '';
			}
			else {
				$out .= '<span class="spotlight-img"><a href="'.JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias).'"><img width="30" height="30" src="'.$thumb.'" alt="'.htmlentities(stripslashes($row->title)).'" /></a></span>'."\n";
				$out .= '<span class="spotlight-item"><a href="'.JRoute::_('index.php?option=com_members&id='.$row->created_by.'&active=blog&task='.JHTML::_('date',$row->publish_up, '%Y', 0).'/'.JHTML::_('date',$row->publish_up, '%m', 0).'/'.$row->alias).'">'.$row->title.'</a></span> ';
				$out .=  ' by <a href="'. JRoute::_('index.php?option=com_members&id='.$row->created_by).'">'.$profile->get('name').'</a> - '.JText::_('in Blogs')."\n";
				$out .= '<div class="clear"></div>'."\n";
			}
		}
		// topics
		else if ($tbl == 'topics') {
			if($getid) {
				return $row->id;
			}
			$url = $row->group && $row->scope ? 'groups'.DS.$row->scope.DS.$row->pagename : 'topics'.DS.$row->pagename;

			$thumb = trim($this->params->get( 'default_topicpic', 'modules/mod_spotlight/default.gif' ));
			$out .= '<span class="spotlight-img"><a href="'.JRoute::_('index.php?option=com_topics&pagename='.$row->pagename).'"><img width="30" height="30" src="'.$thumb.'" alt="'.htmlentities(stripslashes($row->title)).'" /></a></span>'."\n";
			$out .= '<span class="spotlight-item"><a href="'.$url.'">'.stripslashes($row->title).'</a></span> ';
			$out .=  ' - '.JText::_('in').' <a href="'.JRoute::_('index.php?option=com_topics').'">'.JText::_('Topics').'</a>'."\n";
			$out .= '<div class="clear"></div>'."\n";
		}
		// questions
		else if ($tbl == 'answers') {
			if($getid) {
				return $row->id;
			}
			$thumb = trim($this->params->get( 'default_questionpic', 'modules/mod_spotlight/default.gif' ));
			
			$name = JText::_('Anonymous');
			if ($row->anonymous == 0) {
				$juser =& JUser::getInstance( $row->created_by );
				if (is_object($juser)) {
					$name = $juser->get('name');
				}
			}
			$out .= '<span class="spotlight-img"><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$row->id).'"><img width="30" height="30" src="'.$thumb.'" alt="'.htmlentities(stripslashes($row->subject)).'" /></a></span>'."\n";
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
				$thumb = trim($this->params->get( 'default_itunespic', 'modules/mod_spotlight/default.gif' ));
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

					$thumb = $rconfig->get('defaultpic', 'modules/mod_spotlight/default.gif');
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
			$out .= '<span class="spotlight-img"><a href="'.JRoute::_('index.php?option=com_resources&id='.$row->id).'"><img width="30" height="30" src="'.$thumb.'" alt="'.htmlentities($row->title).'" /></a></span>'."\n";
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
				$out .=  ' - '.JText::_('in').' <a href="'.JRoute::_('index.php?option=com_resources&type='.$normalized).'">'.$row->typetitle.'</a>'."\n";
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
	
	private function countContributions( $uid, $username, $database ) 
	{
		if ($uid === NULL) {
			 return 0;
		}
		$count = 0;
		
/*
		$query  = "SELECT COUNT(*) as resources ";
		if($username) {
			$query .= ", (SELECT COUNT(*) FROM jos_wiki_page AS w WHERE (w.created_by=$uid OR w.authors LIKE '%".$username."%')) as topics ";
		}
		$query .= "FROM #__author_assoc AS AA, #__resources AS R ";
		//$query .= "FROM #__author_assoc AS AA, #__resource_types AS rt, #__resources AS R ";
		//$query .= "LEFT JOIN #__resource_types AS t ON R.logical_type=t.id ";
		$query .= "WHERE AA.authorid = ". $uid ." ";
		$query .= "AND R.id = AA.subid ";
		$query .= "AND AA.subtable = 'resources' ";
		$query .= "AND R.published=1 AND R.standalone=1 ";
		//$query .= "AND R.access!=2 AND R.access!=4 ";
		//$query .= "AND R.type=rt.id "; */
		
		$query  = "SELECT count(DISTINCT r.id) FROM jos_resources AS r ";
		$query .= "LEFT JOIN jos_resource_types AS rt ON r.type=rt.id ";
		$query .= "LEFT JOIN jos_author_assoc AS aa ON aa.subid=r.id AND aa.subtable='resources' ";
		$query .= "WHERE r.standalone=1 AND r.published=1 AND (aa.authorid='".$uid."') AND (r.access=0 OR r.access=3)";
	
		$database->setQuery( $query );
		$resources = $database->loadResult();
		
		$query  = "SELECT COUNT(*) FROM (SELECT COUNT(DISTINCT v.pageid) FROM jos_wiki_page AS w, jos_wiki_version AS v ";
		  
		$query .= "WHERE w.id=v.pageid AND v.approved=1 AND (w.created_by='".$uid."' ";
		$query .= $username ? "OR w.authors LIKE '%".$username."%')" : ") ";		  
		$query .= "	AND w.access!=1 GROUP BY pageid ) AS f ";
		
		$database->setQuery( $query );
		$topics = $database->loadResult();
		$count = $resources + $topics;
		
		return $count;					
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
					if (preg_match( "/^bmp|gif|jpg|png$/i", $img_file )) {
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
					if (preg_match( "/^bmp|gif|jpg|png$/i", $img_file )) {
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
		if ( $date && preg_match("/([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})/", $date, $regs ) ) {
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
