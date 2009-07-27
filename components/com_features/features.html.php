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

if (!defined('n')) {
	define('t',"\t");
	define('n',"\n");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}

class FeaturesHtml 
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>';
		$html .= ($txt != '') ? n.$txt.n : '';
		$html .= '</div><!-- / ';
		if ($id) {
			$html .= '#'.$id;
		}
		if ($cls) {
			$html .= '.'.$cls;
		}
		$html .= ' -->'.n;
		return $html;
	}

	//-----------
	
	public function shortenText($text, $chars=300, $p=1) 
	{
		$text = strip_tags($text);
		$text = trim($text);

		if (strlen($text) > $chars) {
			$text = $text.' ';
			$text = substr($text,0,$chars);
			$text = substr($text,0,strrpos($text,' '));
			$text = $text.' ...';
		}
		if ($text == '') {
			$text = '...';
		}
		if ($p) {
			$text = '<p>'.$text.'</p>';
		}

		return $text;
	}

	//-----------

	public function select($name, $array, $value, $class='')
	{
		$out  = '<select name="'.$name.'" id="'.$name.'"';
		$out .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'.n;
		}
		$out .= '</select>'.n;
		return $out;
	}
	
	//-----------
	
	public function cleanUrl( $url ) 
	{
		$url = stripslashes($url);
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('&', '&amp;', $url);
		
		return $url;
	}
	
	//-----------

	public function browse( $database, $rows, $pageNav, $option, $filters, $authorized ) 
	{
		$types = array(''=>JText::_('ALL'),
						'tools'=>JText::_('TOOLS'),
						'resources'=>JText::_('RESOURCES'),
						'answers'=>JText::_('ANSWERS'),
						'profiles'=>JText::_('PROFILES'),
						);
			
		$html  = FeaturesHtml::div( FeaturesHtml::hed(2,JText::_('FEATURES')), 'full', 'content-header').n;
		$html .= '<div class="main section">'.n;
		$html .= '<form action="'. JRoute::_('index.php?option='.$option) .'" id="featureform" method="post">'.n;
		$html .= '<div class="aside">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('TYPE').':'.n;
		$html .= FeaturesHtml::select('type',$types,$filters['type']);
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<input type="submit" name="go" value="'.JText::_('GO').'" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.'</fieldset>'.n;
		if ($authorized) {
			$html .= t.'<p class="add"><a href="'.JRoute::_('index.php?option='.$option.a.'task=add').'">'.JText::_('Add feature').'</a></p>'.n;
		}
		
		$html .= '</div><!-- / .aside -->'.n;
		$html .= '<div class="subject">'.n;
		
		if (count($rows) > 0) {
			$txt_length = 300;
			
			switch ($filters['type']) 
			{
				case 'profiles':
					ximport('xprofile');
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'members.class.php' );
					$mconfig =& JComponentHelper::getParams( 'com_members' );
				break;
				case 'questions':
					$aconfig =& JComponentHelper::getParams( 'com_answers' );
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );
				break;
				case 'tools':
					$rconfig =& JComponentHelper::getParams( 'com_resources' );
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
				break;
				case 'resources':
					$rconfig =& JComponentHelper::getParams( 'com_resources' );
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
				break;
				case 'all':
				default:
					ximport('xprofile');
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'members.class.php' );
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_answers'.DS.'answers.class.php' );
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_contribtool'.DS.'contribtool.version.php' );
					include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_resources'.DS.'resources.resource.php');
					$mconfig =& JComponentHelper::getParams( 'com_members' );
					$aconfig =& JComponentHelper::getParams( 'com_answers' );
					$rconfig =& JComponentHelper::getParams( 'com_resources' );
				break;
			}
			
			$now = date( 'Y-m-d H:i:s' );
			
			$html .= '<ul class="features results">'.n;
			foreach ($rows as $fh) 
			{
				if ($fh->note == 'tools') {
					$fh->tbl = 'tools';
				}
				$html .= t.'<li';
				if ($fh->featured > $now) {
					$html .= ' class="upcoming"';
				}
				$html .= '>'.n;
				switch ($fh->tbl)
				{
					case 'tools':
						$row = new ResourcesResource( $database );
						$row->load( $fh->objectid );
						/*if ($row) {
							$row->typetitle = $row->getTypetitle();
						}*/
						
						$path = $rconfig->get('uploadpath');
						if (substr($path, 0, 1) != DS) {
							$path = DS.$path;
						}
						if (substr($path, -1, 1) == DS) {
							$path = substr($path, 0, (strlen($path) - 1));
						}
						$path = FeaturesHtml::build_path( $row->created, $row->id, $path );

						$tv = new ToolVersion( $database );

						$versionid = $tv->getVersionIdFromResource( $row->id, 'current' );

						$picture = FeaturesHtml::getToolImage( $path, $versionid );

						$thumb = $path.DS.$picture;
						if (!is_file(JPATH_ROOT.$thumb)) {
							$thumb = FeaturesHtml::getContributorImage( $row->id, $database );

							if (!is_file(JPATH_ROOT.$thumb)) {
								$thumb = $rconfig->get('defaultpic');
								if (substr($thumb, 0, 1) != DS) {
									$thumb = DS.$thumb;
								}
							}
						}

						$href  = 'index.php?option=com_resources&id='.$row->id;

						/*$normalized_valid_chars = 'a-zA-Z0-9';
						$normalized = preg_replace("/[^$normalized_valid_chars]/", "", strtolower($row->typetitle));

						$row->typetitle = trim(stripslashes($row->typetitle));
						if (substr($row->typetitle, -1, 1) == 's') {
							$row->typetitle = substr($row->typetitle, 0, strlen($row->typetitle) - 1);
						}*/
						
						if (is_file(JPATH_ROOT.$thumb)) {
							$html .= '<p class="featured-img"><img width="50" height="50" src="'.$thumb.'" alt="" /></p>'.n;
						}
						$html .= '<p class="title"><a href="'.JRoute::_($href).'">'.stripslashes($row->title).'</a></p>'.n;
						$html .= '<p class="details">'.JText::_('FEATURED').' '.JHTML::_('date', $fh->featured, '%d %b. %Y').' '.JText::_('in').' '.JText::_(strtoupper($fh->tbl));
						if ($authorized) {
							$html .= ' <span>|</span> <a class="delete" href="'.JRoute::_('index.php?option='.$option.a.'task=delete'.a.'id='.$fh->id).'">'.JText::_('Delete').'</a>'.n;
							$html .= ' <span>|</span> <a class="edit" href="'.JRoute::_('index.php?option='.$option.a.'task=edit'.a.'id='.$fh->id).'">'.JText::_('Edit').'</a>'.n;
						}
						$html .= '</p>'.n;
						//if ($row->introtext) {
							$html .= FeaturesHtml::shortenText(FeaturesHtml::encode_html(strip_tags($row->introtext)), $txt_length, 1).n;
						//}
					break;
					
					case 'nontools':
					case 'resources':
						$row = new ResourcesResource( $database );
						$row->load( $fh->objectid );
						/*if ($row) {
							$row->typetitle = $row->getTypetitle();
						}*/
					
						$path = $rconfig->get('uploadpath');
						if (substr($path, 0, 1) != DS) {
							$path = DS.$path;
						}
						if (substr($path, -1, 1) == DS) {
							$path = substr($path, 0, (strlen($path) - 1));
						}
						$path = FeaturesHtml::build_path( $row->created, $row->id, $path );

						$picture = FeaturesHtml::getImage( $path );

						$thumb = $path.DS.$picture;
						if (!is_file(JPATH_ROOT.$thumb)) {
							$thumb = FeaturesHtml::getContributorImage( $id, $database );

							if (!is_file(JPATH_ROOT.$thumb)) {
								$thumb = $rconfig->get('defaultpic');
								if (substr($thumb, 0, 1) != DS) {
									$thumb = DS.$thumb;
								}
							}
						}

						$href  = 'index.php?option=com_resources&id='.$row->id;

						/*$normalized_valid_chars = 'a-zA-Z0-9';
						$normalized = preg_replace("/[^$normalized_valid_chars]/", "", strtolower($row->typetitle));

						$row->typetitle = trim(stripslashes($row->typetitle));
						if (substr($row->typetitle, -1, 1) == 's') {
							$row->typetitle = substr($row->typetitle, 0, strlen($row->typetitle) - 1);
						}*/
						
						if (is_file(JPATH_ROOT.$thumb)) {
							$html .= '<p class="featured-img"><img width="50" height="50" src="'.$thumb.'" alt="" /></p>'.n;
						}
						$html .= '<p class="title"><a href="'.JRoute::_($href).'">'.stripslashes($row->title).'</a></p>'.n;
						$html .= '<p class="details">'.JText::_('Featured').' '.JHTML::_('date', $fh->featured, '%d %b. %Y').' '.JText::_('in').' '.JText::_(strtoupper($fh->tbl));
						if ($authorized) {
							$html .= ' <span>|</span> <a class="delete" href="'.JRoute::_('index.php?option='.$option.a.'task=delete'.a.'id='.$fh->id).'">'.JText::_('Delete').'</a>'.n;
							$html .= ' <span>|</span> <a class="edit" href="'.JRoute::_('index.php?option='.$option.a.'task=edit'.a.'id='.$fh->id).'">'.JText::_('Edit').'</a>'.n;
						}
						$html .= '</p>'.n;
						//if ($row->introtext) {
							$html .= FeaturesHtml::shortenText(FeaturesHtml::encode_html(strip_tags($row->introtext)), $txt_length, 1).n;
						//}
					break;
					
					case 'questions':
					case 'answers':
						$row = new AnswersQuestion( $database );
						$row->load( $fh->objectid );
					
						$ar = new AnswersResponse( $database );
						$row->rcount = count($ar->getIds( $row->id ));
					
						$thumb = '/modules/mod_featuredquestion/question_thumb.gif'; //trim($params->get( 'defaultpic' ));

						$name = JText::_('ANONYMOUS');
						if ($row->anonymous == 0) {
							$juser =& JUser::getInstance( $row->created_by );
							if (is_object($juser)) {
								$name = $juser->get('name');
							}
						}

						$row->created = FeaturesHtml::mkt($row->created);
						$when = FeaturesHtml::timeAgo($row->created);
						
						if (is_file(JPATH_ROOT.$thumb)) {
							$html .= '<p class="featured-img"><img width="50" height="50" src="'.$thumb.'" alt="" /></p>'.n;
						}
						$html .= '<p class="title"><a href="'.JRoute::_('index.php?option=com_answers&task=question&id='.$row->id).'">'.stripslashes($row->subject).'</a></p>'.n;
						$html .= '<p class="details">'.JText::_('Featured').' '.JHTML::_('date', $fh->featured, '%d %b. %Y').' '.JText::_('in').' '.JText::_(strtoupper($fh->tbl));
						if ($authorized) {
							$html .= ' <span>|</span> <a class="delete" href="'.JRoute::_('index.php?option='.$option.a.'task=delete'.a.'id='.$fh->id).'">'.JText::_('Delete').'</a>'.n;
							$html .= ' <span>|</span> <a class="edit" href="'.JRoute::_('index.php?option='.$option.a.'task=edit'.a.'id='.$fh->id).'">'.JText::_('Edit').'</a>'.n;
						}
						$html .= '</p>'.n;
						$html .= '<p><span>'.JText::sprintf('ASKED_BY', $name).'</span> - <span>'.$when.' ago</span> - <span>';
						$html .= ($row->rcount == 1) ? JText::sprintf('RESPONSE', $row->rcount) : JText::sprintf('RESPONSES', $row->rcount);
						$html .= '</span></p>'.n;
						//if ($row->question) {
							$html .= FeaturesHtml::shortenText(FeaturesHtml::encode_html(strip_tags($row->question)), $txt_length, 1).n;
						//}
					break;
					
					case 'xprofiles':
					case 'profiles':
						$row = new MembersProfile( $database );
						$row->load( $fh->objectid );

						// Member profile
						$title = $row->name;
						if (!trim($title)) {
							$title = $row->givenName.' '.$row->surname;
						}
						$id = $row->uidNumber;

						// Load their bio
						$profile = new XProfile();
						$profile->load( $row->uidNumber );
						$txt = $profile->get('bio');

						// Do we have a picture?
						$thumb = '';
						if (isset($row->picture) && $row->picture != '') {
							// Yes - so build the path to it
							$thumb  = $mconfig->get('webpath');
							if (substr($thumb, 0, 1) != DS) {
								$thumb = DS.$thumb;
							}
							if (substr($thumb, -1, 1) == DS) {
								$thumb = substr($thumb, 0, (strlen($thumb) - 1));
							}
							$thumb .= DS.FeaturesHtml::niceidformat($row->uidNumber).DS.$row->picture;

							// No - use default picture
							if (is_file(JPATH_ROOT.$thumb)) {
								// Build a thumbnail filename based off the picture name
								$thumb = FeaturesHtml::thumb( $thumb );
							}
						}

						// No - use default picture
						if (!is_file(JPATH_ROOT.$thumb)) {
							$thumb = $mconfig->get('defaultpic');
							if (substr($thumb, 0, 1) != DS) {
								$thumb = DS.$thumb;
							}
							// Build a thumbnail filename based off the picture name
							$thumb = FeaturesHtml::thumb( $thumb );
						}

						if (is_file(JPATH_ROOT.$thumb)) {
							$html .= '<p class="featured-img"><img width="50" height="50" src="'.$thumb.'" alt="" /></p>'."\n";
						}
						$html .= t.t.'<p class="title"><a href="'.JRoute::_('index.php?option=com_members&id='.$id).'">'.stripslashes($title).'</a></p>'.n;
						$html .= '<p class="details">'.JText::_('Featured').' '.JHTML::_('date', $fh->featured, '%d %b. %Y').' '.JText::_('in').' '.JText::_(strtoupper($fh->tbl));
						if ($authorized) {
							$html .= ' <span>|</span> <a class="delete" href="'.JRoute::_('index.php?option='.$option.a.'task=delete'.a.'id='.$fh->id).'">'.JText::_('Delete').'</a>'.n;
							$html .= ' <span>|</span> <a class="edit" href="'.JRoute::_('index.php?option='.$option.a.'task=edit'.a.'id='.$fh->id).'">'.JText::_('Edit').'</a>'.n;
						}
						$html .= '</p>'.n;
						//if ($txt) {
							$html .= FeaturesHtml::shortenText(FeaturesHtml::encode_html(strip_tags($txt)), $txt_length, 1).n;
						//} else {
						//	$html .= '<p></p>'.n;
						//}
					break;
				}
				$html .= t.'</li>'.n;
			}
			$html .= '</ul>'.n;
			$html .= $pageNav->getListFooter();
		} else {
			$html .= FeaturesHtml::warning( JText::_('NO_FEATURES_FOUND') ).n;
		}
		
		$html .= '</div><!-- / .subject -->'.n;
		$html .= '<div class="clear"></div>'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}

	//-----------

	public function niceidformat($someid) 
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
		$str = Featureshtml::ampersands($str);

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

	public function ampersands( $str ) 
	{
		$str = stripslashes($str);
		$str = str_replace('&#','*-*', $str);
		$str = str_replace('&amp;','&',$str);
		$str = str_replace('&','&amp;',$str);
		$str = str_replace('*-*','&#', $str);
		return $str;
	}
	
	//-----------

	public function thumb( $thumb ) 
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
	
	public function getImage( $path ) 
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
	
	public function getToolImage( $path, $versionid=0 ) 
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

	public function thumbnail($pic)
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

	public function build_path( $date, $id, $base='' )
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
		$dir_id = FeaturesHtml::niceidformat( $id );

		return $base.DS.$dir_year.DS.$dir_month.DS.$dir_id;
	}
	
	//-----------

	public function mkt($stime)
	{
		if ($stime && ereg("([0-9]{4})-([0-9]{2})-([0-9]{2})[ ]([0-9]{2}):([0-9]{2}):([0-9]{2})", $stime, $regs )) {
			$stime = mktime( $regs[4], $regs[5], $regs[6], $regs[2], $regs[3], $regs[1] );
		}
		return $stime;
	}
	
	//-----------

	public function timeAgoo($timestamp)
	{
		// Store the current time
		$current_time = time();

		// Determine the difference, between the time now and the timestamp
		$difference = $current_time - $timestamp;

		// Set the periods of time
		$periods = array('second', 'minute', 'hour', 'day', 'week', 'month', 'year', 'decade');

		// Set the number of seconds per period
		$lengths = array(1, 60, 3600, 86400, 604800, 2630880, 31570560, 315705600);

		// Determine which period we should use, based on the number of seconds lapsed.
		// If the difference divided by the seconds is more than 1, we use that. Eg 1 year / 1 decade = 0.1, so we move on
		// Go from decades backwards to seconds
		for ($val = sizeof($lengths) - 1; ($val >= 0) && (($number = $difference / $lengths[$val]) <= 1); $val--);

		// Ensure the script has found a match
		if ($val < 0) $val = 0;

		// Determine the minor value, to recurse through
		$new_time = $current_time - ($difference % $lengths[$val]);

		// Set the current value to be floored
		$number = floor($number);

		// If required create a plural
		if ($number != 1) $periods[$val].= 's';

		// Return text
		$text = sprintf("%d %s ", $number, $periods[$val]);

		// Ensure there is still something to recurse through, and we have not found 1 minute and 0 seconds.
		if (($val >= 1) && (($current_time - $new_time) > 0)) {
			$text .= FeaturesHtml::timeAgoo($new_time);
		}

		return $text;
	}

	//-----------

	public function timeAgo($timestamp) 
	{
		$text = FeaturesHtml::timeAgoo($timestamp);

		$parts = explode(' ',$text);

		$text  = $parts[0].' '.$parts[1];
		$text .= ($parts[2]) ? ' '.$parts[2].' '.$parts[3] : '';
		return $text;
	}
	
	//-----------
	
	private function getContributorImage( $id, $database )
	{
		$thumb = '';
		
		include_once( JPATH_ROOT.DS.'components'.DS.'com_resources'.DS.'resources.extended.php');
		$helper = new ResourcesHelper( $id, $database );
		$ids = $helper->getContributorIDs();
		if (count($ids) > 0) {
			$uid = $ids[0];
		} else {
			return $thumb;
		}

		// Load some needed libraries
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'members.class.php' );
		
		// Load the member profile
		$row = new MembersProfile( $database );
		$row->load( $uid );
		
		// Do they have a picture?
		if (isset($row->picture) && $row->picture != '') {
			$config =& JComponentHelper::getParams( 'com_members' );
			
			// Yes - so build the path to it
			$thumb  = $config->get('webpath');
			if (substr($thumb, 0, 1) != DS) {
				$thumb = DS.$thumb;
			}
			if (substr($thumb, -1, 1) == DS) {
				$thumb = substr($thumb, 0, (strlen($thumb) - 1));
			}
			$thumb .= DS.FeaturesHtml::niceidformat($row->uidNumber).DS.$row->picture;
			
			// No - use default picture
			if (is_file(JPATH_ROOT.$thumb)) {
				// Build a thumbnail filename based off the picture name
				$thumb = FeaturesHtml::thumb( $thumb );
				
				if (!is_file(JPATH_ROOT.$thumb)) {
					// Create a thumbnail image
					include_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'members.imghandler.php' );
					$ih = new MembersImgHandler();
					$ih->set('image',$row->picture);
					$ih->set('path',JPATH_ROOT.$config->get('webpath').DS.FeaturesHtml::niceidformat($row->uidNumber).DS);
					$ih->set('maxWidth', 50);
					$ih->set('maxHeight', 50);
					$ih->set('cropratio', '1:1');
					$ih->set('outputName', $ih->createThumbName());
					if (!$ih->process()) {
						echo '<!-- Error: '. $ih->getError() .' -->';
					}
				}
			}
		}
		
		return $thumb;
	}
	
	public function edit( $option, $row, $title )
	{
		$html  = FeaturesHtml::div( FeaturesHtml::hed( 2, $title ), 'full', 'content-header' );
		$html .= '<div class="main section">'.n;		
		$html .= '<form action="index.php" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p><span class="required">*</span> = '.JText::_('REQUIRED_FIELD').'</p>'.n;
		$html .= t.t.'<p><a href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('Back to Features History').'</a></p>'.n;
		$html .= t.'</div><!-- / .aside -->'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.FeaturesHtml::hed(3,JText::_('FEATURED_ITEM')).n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="save" />'.n;
		$html .= t.t.'<input type="hidden" name="id" value="'.$row->id.'" />'.n;
		$html .= t.t.'<input type="hidden" name="note" value="'.$row->note.'" />'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('OBJECT_ID').': <span class="required">*</span>'.n;
		$html .= t.t.t.'<input type="text" name="objectid" value="'.$row->objectid.'" />'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('OBJECT_TYPE').': <span class="required">*</span>'.n;
		$types = array('content'=>JText::_('CONTENT'),
						'tools'=>JText::_('TOOLS'),
						'resources'=>JText::_('RESOURCES'),
						'answers'=>JText::_('ANSWERS'),
						'profiles'=>JText::_('PROFILES'),
						);
		$html .= FeaturesHtml::select('tbl',$types,$row->tbl);
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('FEATURED_DATE').': YYYY-MM-DD <span class="required">*</span>'.n;
		$html .= t.t.t.'<input type="text" name="featured" value="'.$row->featured.'" />'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.'</fieldset>'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		$html .= '</div><div class="clear"></div>'.n;
		
		return $html;
	}
}
?>