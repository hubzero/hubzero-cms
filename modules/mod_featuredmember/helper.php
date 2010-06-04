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

class modFeaturedmember
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

	public function encode_html($str, $quotes=1)
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

	public function display() 
	{
		//ximport('featurehistory');
		require_once( JPATH_ROOT.DS.'components'.DS.'com_features'.DS.'features.history.php' );
		ximport('xprofile');
		
		$this->error = false;
		if (!class_exists('FeaturesHistory')) {
			$this->error = true;
			return false;
		}
		
		$database =& JFactory::getDBO();

		$params =& $this->params;
		
		$filters = array();
		$filters['limit'] = 1;
		$filters['show']  = trim($params->get( 'show' ));
		
		$this->cls = trim($params->get( 'moduleclass_sfx' ));
		$this->txt_length = trim($params->get( 'txt_length' ));
		$catid = trim($params->get( 'catid' ));
		$min = trim($params->get( 'min_contributions' ));
		$show = trim($params->get( 'show' ));
		
		$start = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 00:00:00";
		$end = date('Y-m-d', mktime(0,0,0,date('m'),date('d'), date('Y')))." 23:59:59";
		
		$row = null;
		
		$fh = new FeaturesHistory( $database );
		
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
		$profile = null;
		// Do we have an article to display?
		if (!$row) {
			// No - so we need to display a member
			
			// Load some needed libraries
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'profile.php' );
			include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'association.php' );
			
			// Check the feature history for today's feature
			$fh->loadActive($start, 'profiles');
			
			// Did we find a feature for today?
			if ($fh->id && $fh->tbl == 'profiles') {
				// Yes - load the member profile
				$row = new MembersProfile( $database );
				$row->load( $fh->objectid );
			} else {
				// No - so we need to randomly choose one
				$filters['start'] = 0;
				$filters['sortby'] = "RAND()";
				$filters['search'] = '';
				$filters['authorized'] = false;
				$filters['show'] = $show;
				if ($min) {
					$filters['contributions'] = $min;
				}
				
				$mp = new MembersProfile( $database );

				$rows = $mp->getRecords( $filters, false );
				if (count($rows) > 0) {
					$row = $rows[0];
				}
				
				// Load their bio
				$profile = new XProfile();
				$profile->load( $row->uidNumber );
				
				if (trim(strip_tags($profile->get('bio'))) == '') {
					return $this->display();
				}
			}
		}

		// Did we have a result to display?
		if ($row) {
			$this->row = $row;
			
			$config =& JComponentHelper::getParams( 'com_members' );
			
			// Is this a content article or a member profile?
			if (isset($row->catid)) {
				// Content article
				$this->title = $row->title;
				$this->id = $row->created_by_alias;
				$this->txt = $row->introtext;
				
				$profile = new XProfile();
				$profile->load( $id );
				$row->picture = $profile->get('picture');
				
				// Check if the article has been saved in the feature history
				$fh->loadObject($row->id, 'content');
				if (!$fh->id) {
					$fh->featured = $start;
					$fh->objectid = $row->id;
					$fh->tbl = 'content';
					$fh->store();
				}
			} else {
				if (!isset($profile) && !is_object($profile)) {
					$profile = new XProfile();
					$profile->load( $row->uidNumber );
				}
				$this->txt = $profile->get('bio');
				
				// Member profile
				$this->title = $row->name;
				if (!trim($title)) {
					$title = $row->givenName.' '.$row->surname;
				}
				$this->id = $row->uidNumber;
				
				// Check if this has been saved in the feature history
				if (!$fh->id) {
					$fh->featured = $start;
					$fh->objectid = $row->uidNumber;
					$fh->tbl = 'profiles';
					$fh->store();
				}
			}
			
			// Do we have a picture?
			$thumb = '';
			if (isset($row->picture) && $row->picture != '') {
				// Yes - so build the path to it
				$thumb  = $config->get('webpath');
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
						/*if (!$ih->process()) {
							$html .= '<!-- Error: '. $ih->getError() .' -->';
						}*/
					}
				}
			}

			// No - use default picture
			if (!is_file(JPATH_ROOT.$thumb)) {
				$thumb = $config->get('defaultpic');
				if (substr($thumb, 0, 1) != DS) {
					$thumb = DS.$thumb;
				}
				// Build a thumbnail filename based off the picture name
				$thumb = $this->thumb( $thumb );
			}
			
			$this->thumb = $thumb;
			$this->filters = $filters;
		}
	}
}
