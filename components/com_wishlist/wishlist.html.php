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

if (!defined('n')) {
	define('t',"\t");
	define('n',"\n");
	define('r',"\r");
	define('br','<br />');
	define('sp','&#160;');
	define('a','&amp;');
}


class WishlistHtml 
{
	//-------------------------------------------------------------
	// Misc HTML
	//-------------------------------------------------------------

	public function encode_html($str, $quotes=1)
	{
		$str = stripslashes($str);
		$a = array(
			'&' => '&#38;',
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

	public function txt_unpee($pee)
	{
		$pee = str_replace("\t", '', $pee);
		$pee = str_replace('</p><p>', '', $pee);
		$pee = str_replace('<p>', '', $pee);
		$pee = str_replace('</p>', "\n", $pee);
		$pee = str_replace('<br />', '', $pee);
		$pee = trim($pee);
		return $pee;
	}
	
	//-----------
	
	public function cleanText($text, $desclen=300)
	{
		$elipse = false;

		$text = preg_replace( "'<script[^>]*>.*?</script>'si", "", $text );
		$text = str_replace( '{mosimage}', '', $text );
		$text = str_replace( "\n", ' ', $text );
		$text = str_replace( "\r", ' ', $text );
		$text = preg_replace( '/<a\s+.*href=["\']([^"\']+)["\'][^>]*>([^<]*)<\/a>/i','\\2', $text );
		$text = preg_replace( '/<!--.+?-->/', '', $text);
		$text = preg_replace( '/{.+?}/', '', $text);
		$text = strip_tags( $text );
		if (strlen($text) > $desclen) $elipse = true;
		$text = substr( $text, 0, $desclen );
		if ($elipse) $text .= '...';
		$text = trim($text);
		
		return $text;
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

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------
	
	public function help( $msg, $tag='p' )
	{
		return '<'.$tag.' class="help">'.$msg.'</'.$tag.'>'.n;
	}	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------

	public function title($level, $words, $class='') 
	{
		$html  = t.t.'<h'.$level;
		$html .= ($class) ? ' class="'.$class.'"' : '';
		$html .= '>'.$words.'</h'.$level.'>'.n;
		return $html;
	}

	//-----------

	public function div($txt, $cls='', $id='')
	{
		$html  = '<div';
		$html .= ($cls) ? ' class="'.$cls.'"' : '';
		$html .= ($id) ? ' id="'.$id.'"' : '';
		$html .= '>'.n;
		$html .= $txt.n;
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
	
	public function aside($txt, $id='')
	{
		return WishlistHtml::div($txt, 'aside', $id);
	}
	
	//-----------
	
	public function subject($txt, $id='')
	{
		return WishlistHtml::div($txt, 'subject', $id);
	}

	//-----------
	
	public function hed($level, $txt)
	{
		return '<h'.$level.'>'.$txt.'</h'.$level.'>';
	}

	//-----------

	public function formSelect($name, $array, $value, $class='')
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

	public function tableRow($h,$c='')
	{
		$html  = t.'  <tr>'.n;
		$html .= t.'   <th>'.$h.'</th>'.n;
		$html .= t.'   <td>';
		$html .= ($c) ? $c : '&nbsp;';
		$html .= '</td>'.n;
		$html .= t.'  </tr>'.n;
		
		return $html;
	}
	
	//-------------------------------------------------------------
	// Forms
	//-------------------------------------------------------------
	
	public function convertVote ($rawnum, $category, $output='')
	{
		$rawnum = round($rawnum);
		if($category == 'importance') {
			switch( $rawnum ) 
			{
				case '0':    $output=JText::_('RUBBISH');		break;
				case '1':    $output=JText::_('MAYBE');    		break;
				case '2':    $output=JText::_('INTERESTING');   break;
				case '3':    $output=JText::_('GOODIDEA');   	break;
				case '4':    $output= JText::_('IMPORTANT');   	break;
				case '5':    $output=JText::_('CRITICAL');   	break;
			}
		}
		else if($category == 'effort') {
			switch( $rawnum ) 
			{
				case '0':    $output=JText::_('TWOMONTHS');		break;
				case '1':    $output=JText::_('TWOWEEKS');    	break;
				case '2':    $output=JText::_('ONEWEEK');   	break;
				case '3':    $output=JText::_('TWODAYS');   	break;
				case '4':    $output=JText::_('ONEDAY');   		break;
				case '5':    $output=JText::_('FOURHOURS');   	break;
			}
		}
		
		return $output;
	
	}
	//-----------
	public function convertTime ($rawnum,  $due=array())
	{
		$rawnum = round($rawnum);
		$today = date( 'Y-m-d H:i:s');
	
		switch( $rawnum ) 
			{
				case '0':    
							 $due['immediate'] = date('Y-m-d H:i:s', time() + (62 * 24 * 60 * 60)); 		
							 $due['warning'] = date('Y-m-d H:i:s', time() + (120 * 24 * 60 * 60));
							 break; // 2 months	
										
				case '1':    $due['immediate']= date('Y-m-d H:i:s', time() + (14 * 24 * 60 * 60)); 
							 $due['warning'] = date('Y-m-d H:i:s', time() + (32 * 24 * 60 * 60));   	
							 break; // 2 weeks
							 
				case '2':    $due['immediate'] = date('Y-m-d H:i:s', time() + (7 * 24 * 60 * 60));
							 $due['warning'] = date('Y-m-d H:i:s', time() + (14 * 24 * 60 * 60));   	
							 break; // 1 week
							 
				case '3':    $due['immediate'] = date('Y-m-d H:i:s', time() + (2 * 24 * 60 * 60)); 
							 $due['warning'] = date('Y-m-d H:i:s', time() + (6 * 24 * 60 * 60));  	
							 break; // 2 days
							 
				case '4':    $due['immediate'] = date('Y-m-d H:i:s', time() + (24 * 60 * 60));
							 $due['warning'] = date('Y-m-d H:i:s', time() + (2 * 24 * 60 * 60));  			
							 break; // 1 day
							 
				case '5':    $due['immediate'] = date('Y-m-d H:i:s', time() + (24 * 60 * 60));  
							 $due['warning'] = date('Y-m-d H:i:s', time() + (2 * 24 * 60 * 60)); 			
							 break; // 4 hours
		}
	
		
		return $due;
	
	}

	//-----------
	
	public function rankingForm($option, $wishlist, $task, $myvote)
	{

		$importance = array(''=>JText::_('SELECT_IMP'),'0.0'=>'0 -'.JText::_('RUBBISH'),'1'=>'1 - '.JText::_('MAYBE'),'2'=>'2 - '.JText::_('INTERESTING'), 
		'3'=>'3 - '.JText::_('GOODIDEA'), '4'=>'4 - '.JText::_('IMPORTANT'), '5'=>'5 - '.JText::_('CRITICAL'));
		$effort = array(''=>JText::_('SELECT_EFFORT'),'5'=>JText::_('FOURHOURS'),'4'=>JText::_('ONEDAY'),
		'3'=>JText::_('TWODAYS'),'2'=>JText::_('ONEWEEK'),'1'=>JText::_('TWOWEEKS'),'0.0'=>JText::_('TWOMONTHS'));
		
		$html  = '<form method="post" action="index.php?option='.$option.'" class="rankingform" id="rankForm">'.n;
		
		//$html .= t.'<fieldset class="editbutton">'.n;
		//$html .= t.t.'<input type="submit" class="editopinion" value="'.JText::_('SAVE').'" />';
		//$html .= t.'</fieldset>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.WishlistHtml::formSelect('importance', $importance, $myvote->myvote_imp, 'rankchoices');
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.WishlistHtml::formSelect('effort', $effort, $myvote->myvote_effort, 'rankchoices');
		$html .= t.t.'</label>'.n;		
		$html .= t.t.'<input type="hidden" name="task" value="'.$task.'" />'.n;
		//$html .= t.t.'<input type="hidden" name="id" value="'.$wishlist->id.'" />'.n;
		$html .= t.t.'<input type="hidden" name="category" value="'.$wishlist->category.'" />'.n;
		$html .= t.t.'<input type="hidden" name="rid" value="'.$wishlist->referenceid.'" />'.n;
		$html .= t.t.'<input type="hidden" name="wishid" value="'.$myvote->id.'" />'.n;
		$html .= t.t.'<input type="hidden" name="limitstart" value="0" />'.n;
		$html .= t.t.'<input type="submit"  value="'.JText::_('SAVE').'" />';
		$html .= t.'</fieldset>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	//-----------
	
	public function browseForm($option, $filters, $admin, $id, $total, $wishlist, $pageNav)
	{
		$sortbys = array('ranking'=>JText::_('RANKING'),'date'=>JText::_('DATE'),'feedback'=>JText::_('FEEDBACK'));
		if($wishlist->banking) {
		$sortbys['bonus']=JText::_('Bonus');
		}
		$filterbys = array('all'=>JText::_('ALL'),'open'=>JText::_('Active'),'granted'=>JText::_('GRANTED'), 'accepted'=>JText::_('Accepted'), 'rejected'=>JText::_('Rejected'));
		
		if($admin) { // a few extra options
		$filterbys['private'] = JText::_('PRIVATE');
		$filterbys['public'] = JText::_('PUBLIC');
			if($admin == 2) {
			$filterbys['mine'] = JText::_('Assigned to me');
			}
		}
		$html  = '<div class="wishlist_controls">'.n;
		$html .= t.'<form method="get" action="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid).'">'.n;
		$html .= t.t.'<fieldset>'.n;
		//$html .= t.t.t.'<input type="hidden" name="category" value="'.$wishlist->category.'" />'.n;
		//$html .= t.t.t.'<input type="hidden" name="rid" value="'.$wishlist->referenceid.'" />'.n;
		//$html .= t.t.t.'<input type="hidden" name="task" value="wishlist" />'.n;
		//$html .= t.t.t.'<input type="hidden" name="id" value="'.$id.'" />'.n;
		$html .= t.t.t.'<label >'.JText::_('SHOW').': '.n;
		$html .= WishlistHtml::formSelect('filterby', $filterbys, $filters['filterby'], '', '');
		$html .= t.t.t.'</label>'.n;
		
		if($admin) {
		$html .= t.t.t.' &nbsp; <label> '.JText::_('SORTBY').':'.n;
		$html .= WishlistHtml::formSelect('sortby', $sortbys, $filters['sortby'], '', '');
		$html .= t.t.t.'</label>'.n;
		}
		$html .= t.t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= t.t.t.'<input type="hidden" name="limitstart" value="0" />'.n;
		$html .= t.t.'</fieldset>'.n;
		$html .= t.t.t.'<div class="note_total">'.JText::_('Displaying all').' '.$total.' '.strtolower(JText::_('WISHES')).'</div>'.n;
		$html .= t.'</form>'.n;		
		$html .= '</div>'.n;
		
		return $html;
	}


	//-------------------------------------------------------------
	// Main views
	//-------------------------------------------------------------
	
	public function wishlists( $wishlists, $option) 
	{		
		// will display list of available public wish lists
	}
	
	//----------
	
	public function wishlist( $wishlist, $title, $option, $task, $admin, $error, $filters, $juser, $pageNav, $abuse) 
	{
	
		$html = '';		
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');		
		
		if($wishlist) {	
		  if(!$wishlist->public	 && $admin!= 2) {
		  	//$html .= WishlistHtml::div( WishlistHtml::hed( 2, 'Private Wish List' ), '', 'content-header' );
		  	$html .= '<div class="main section">'.n;
			$html .= WishlistHtml::warning(JText::_('Sorry, you are not authorized to view this private wish list.')).n;
			$html .= '</div>'.n;	
		  }
		  else {
		  if($wishlist->plugin) {
		  	$title = ($admin) ?  JText::_('Prioritized List of Pending Requests') : JText::_('Recent Wishes');
			if(count($wishlist->items) > 0 && (count($wishlist->items) > $filters['limit'])) {
			$title.= ' (<a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid).'">'.JText::_('view all') .' '.count($wishlist->items).'</a>)';
			}
			else {
			$title .= ' ('.count($wishlist->items).')';
			}
		  	$html .= '<div class="wish_plugin">'.n;
			if(count($wishlist->items) > 0) {
			$html .= '<h3>'.$title.'</h3>'.n;
			}
			$html .= '</div>'.n;	
		  }	
		  
		  else  {	
			
			$html .= WishlistHtml::div( WishlistHtml::hed( 2, $title ), '', 'content-header' );
			
			// wish was deleted from the list
			if($task == 'deletewish' && $admin && !$error) {
			$html  .= WishlistHtml::passed(JText::_('Wish successfully deleted.')).n;
			}
			
			// wish was moved to a new list
			if($task == 'movewish' && $admin && !$error) {
			$html  .= WishlistHtml::passed(JText::_('Wish successfully moved.')).n;
			}
			
			if($wishlist->saved==1 && !$error && $admin) {
			$html  .= WishlistHtml::passed(JText::_('List settings successfully saved.')).n;
			}
			
			if($wishlist->saved==3 && !$error) {
			$html  .= WishlistHtml::passed(JText::_('New wish successfully posted. Thank you!')).n;
			}
			
			if($wishlist->saved==2 && !$error && $admin) {
			$html  .= WishlistHtml::passed(JText::_('Changes to the wish successfully saved.')).n;
			}
			
			
			// navigation options
			$html .= '<div id="content-header-extra">'.n;
			$html .= t.'<ul id="useroptions">'.n;
			$html .= t.t.'<li class="last"><a class="add" href="'.JRoute::_('index.php?option='.$option.a.'task=add'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">'.JText::_('TASK_ADD').'</a></li>'.n;
			if(($admin && $wishlist->category=='general') or ($admin && $wishlist->category=='resource') or ($admin==2 && $wishlist->category=='user') or ($admin==2 && $wishlist->category=='group')) {
			$html .= t.t.'<li class="last"><a class="config" href="'.JRoute::_('index.php?option='.$option.a.'task=settings'.a.'id='. $wishlist->id) .'">'.JText::_('List Settings').'</a></li>'.n;
			}
			$html .= t.'</ul>'.n;
			$html .= '</div><!-- / #content-header-extra -->'.n;	
			$html .= '<div class="main section">'.n;
		 } // end if not plugin		
			
			// Display wishlist description
			$html .= t.'<div class="aside">'.n;
				
			if($admin == 2 ) {
			$html .= t.'<p class="info">'.JText::_('You are viewing this wish list as a list administrator.').'</p>'.n;
			}
			else if($admin ==1) {
			$html .= t.'<p class="info">'.JText::_('You are viewing this wish list as a site administrator.').'</p>'.n;
			}		
			if(isset($wishlist->resource) && $wishlist->category== 'resource' && !$wishlist->plugin) {
				//$html .= t.t.'<p>'.JText::_('THIS_LIST_IS_FOR_RES').' #'.$wishlist->referenceid.', '.JText::_('ENTITLED').' "'.$wishlist->resource->title.'."';
				$html .= t.t.'<p>'.JText::_('THIS_LIST_IS_FOR').' ';
				$type  = substr($wishlist->resource->typetitle,0,strlen($wishlist->resource->typetitle) - 1);
				$html .= strtolower($type).' '.JText::_('entitled').' <a href="'.JRoute::_('index.php?option=com_resources'.a.'id='.$wishlist->referenceid).'">'.$wishlist->resource->title.'</a>.';
				$html .= '</p>'.n;
				//$html .= ' '.JText::_('SUGGEST_AND_EARN').' <a href="">'.JText::_('EARN_POINTS').'</a>.</p>'.n;
			}
			else if($wishlist->description && !$wishlist->plugin) {
				$html .= t.t.'<p>'.$wishlist->description.'<p>';
			}
			else {
				$html .= t.t.'<p>'.JText::_('HELP_US_IMPROVE').' '.$hubShortName.' '.JText::_('HELP_IMPROVE_BY_IDEAS').'</p>';
			}
					
			//$html .= t.t.'<p>'.JText::_('Help').' '.$hubShortName.' '.JText::_('HELP_IMPROVE').' <a href="">'.JText::_('EARN_POINTS').'</a>.</p>';
			if($wishlist->plugin) {
				$html .= t.t.t.'<p>';
				$html .= '<a class="add" href="'.JRoute::_('index.php?option='.$option.a.'task=add'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">'.JText::_('ADD_NEW_WISH').'</a></p>'.n;
			}
			$html .= t.'</div><!-- / .aside -->'.n;
			$html .= t.'<div class="subject">'.n;
		
			// Browse form
			 if(!$wishlist->plugin) {	
			$html .= WishlistHtml::browseForm($option, $filters, $admin, $wishlist->id, count($wishlist->items), $wishlist, $pageNav);
			}
			
			// Display items
			if($wishlist->items) {
			
				$html  .= t.'<ul id="wishlist">'.n;
				$y = 1;
			
			foreach ($wishlist->items as $item) {
				
				if(!$wishlist->plugin or ($wishlist->plugin && $y<= $filters['limit'])) {		
				$y++;
				$html  .= t.t.'<li class="reg ';
				$html  .= (isset($item->ranked) && !$item->ranked && $item->status!=1 && $admin==2) ? ' newwish' : '' ;				
				$html  .= ($item->private && $wishlist->public) ? ' private' : '' ;
				$html  .= ($item->status==1) ? ' grantedwish' : '' ;
				$html  .= '">'.n;
				
				// wish icon
				$html .= t.t.'<dl class="comment-details">'.n;
				$html .= t.t.t.'<dt><span class="wish_';
					if($item->reports) {
					$html .= 'outstanding';
					}
					else if(isset($item->ranked) && !$item->ranked && $item->status!=1 && $item->status!=3 && $item->status!=4 && $admin==2)  {
					$html .= 'unranked';
					}	else if($item->status==1) {
					$html .= 'granted';
					}
					else if ($item->urgent==1 && $admin) {
					$html .= 'urgent';
					}
					else if ($item->urgent==2 && $admin) {
					$html .= 'burning';
					}
					else {
					$html .= 'outstanding';
					}	
				$html .='"></span>';		
				$html .='</dt>'.n;
				$html .= t.t.'</dl>'.n;
				
				// do we have comments?
				$commentcount = 0;
				if($filters['comments'] && isset($item->replies)) {
					
					if(count($item->replies) > 0) {
						$commentcount +=  count($item->replies);
						foreach($item->replies as $r) {
							if(count($r->replies) > 0) {
								$commentcount +=  count($r->replies);
								foreach($r->replies as $r2) {
									if(count($r2->replies) > 0) {
										$commentcount +=  count($r2->replies);
									}
								}
							}
						}
					}
				}
				
				// what is submitter name?
				$name = JText::_('ANONYMOUS');
				if ($item->anonymous != 1) {
						$name = JText::_('UNKNOWN');
						$ruser =& XUser::getInstance($item->proposed_by);
						if (is_object($ruser)) {
							$name = $ruser->get('name');
						}
				}	
				
				$html .= t.t.'<div class="ensemble_left">';
				if(!$item->reports) {
				$html .= t.t.'<p class="wishcontent"><a href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id.'" class="wishtitle" title="'.htmlspecialchars(WishlistHtml::cleanText($item->about)).'" >'.WishlistHtml::shortenText($item->subject, 160, 0).'</a></p>'.n;
				
				// display submitter name, time and num of comments
				$html .= t.t.t.'<p class="proposed">'.JText::_('Proposed by').' '.$name.' '.JText::_('on').' '.JHTML::_('date',$item->proposed, '%d %b %Y');
					
				$html .= ', <a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?com=1#comments">'.$commentcount; 
				$html .= $commentcount==1 ? ' '.JText::_('comment') : ' '.JText::_('comments').n;
				$html .= '</a>';
				if($admin) {
						// what is owner login?
						$assigned = JText::_('UNKNOWN');
						$auser =& XUser::getInstance($item->assigned);
						if (is_object($auser)) {
							$assigned = $auser->get('login');
						}
						$html .= $item->assigned ? '<br /> '.JText::_('Assigned to').' '.$assigned : ''.n;
				}
				$html .= '</p>';
				}
				else {
				$html .= t.t.t.'<p class="warning adjust">'.JText::_('NOTICE_POSTING_REPORTED').'</p>';
				}
				$html .= t.t.'</div>';
				
				
				if(!$item->reports) {
				$html .= t.t.'<div class="ensemble_right">';
				
				// admin ranking			
				if(($admin or $item->status==1 or ($item->status==0 && $item->accepted==1) or $item->status==3 or $item->status==4) && !$item->reports) {
					$html .= t.t.'<div class="wishranking">'.n;
					$html .=($item->status==1) ?' <span class="special priority_number">'.JText::_('Granted').'</span>': '' .n;
					$html .=($item->status==1 && $item->granted!='0000-00-00 00:00:00') ?' <span class="mini">'.strtolower(JText::_('ON')).' '.JHTML::_('date',$item->granted, '%d %b %y').'</span>': '' .n;
					
					if(isset($item->ranked) && !$item->ranked && $item->status==0 && $admin==2) {
						$html .= t.t.t.'<a class="rankit" href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id.'">'.JText::_('Rank this').'</a>'.n;
					} else if(isset($item->ranked) && $item->ranked && $item->status==0) {
						$html .= t.t.t.'<span>'.JText::_('Priority').': <span class="priority_number">'.$item->ranking.'</span></span>'.n;
					}
					$html .= ($item->status==0 && $item->accepted==1) ? '<span class="special accepted">'.JText::_('Accepted').'</span>' : '';
					$html .= ($item->status==3) ? '<span class="special rejected">'.JText::_('Rejected').'</span>' : '';
					$html .= ($item->status==4) ? '<span class="special withdrawn">'.JText::_('Withdrawn').'</span>' : '';
					
					/*
					if (isset($item->num_votes) && $item->num_votes!=0 && isset($item->ranked) && $item->ranked && $item->status!=1) {
						$html .= t.t.t.'<span>'.WishlistHtml::convertVote($item->average_imp, 'importance').'</span>'.n;
						$html .= t.t.t.'<span>'.WishlistHtml::convertVote($item->average_effort,'effort').'</span>'.n;			
						
					}*/
					
					$html .=t.t.'</div>'.n;
				}
				
				$html .= t.t.t.'<div id="wishlist_'.$item->id.'" class="'.$option.' intermed">';				
				$html .= WishlistHtml::rateitem($item, $juser, $option, $wishlist->id, 0, 'wishlist', $wishlist->plugin);									
				$html .= t.t.t.'</div>'.n;
				// points
				
				if($wishlist->banking) {
				$html .= t.t.t.'<div class="assign_bonus">'.n;				
					if(isset($item->bonus) && $item->bonus > 0 && ($item->status==0 or $item->status==6)) {
					$html .= t.t.t.'<a class="bonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus#action" title="'.JText::_('Add bonus points for this wish').' ::'.$item->bonusgivenby.' '.JText::_('user(s)').' '.JText::_('contributed a total of ').' '.$item->bonus.' '.JText::_('points ').' '.JText::_('as a bonus for fulfilling this wish.').'">+ '.$item->bonus.'</a>'.n;
					}
					else if($item->status==0 or $item->status==6) {
					$html .= t.t.t.'<a class="nobonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus#action" title="'.JText::_('Add bonus points for this wish').' :: '.JText::_('0 users have contributed.').'">&nbsp;</a>'.n;
					}
					else {
					//$html .= t.t.t.'<span class="bonus_inactive" title="'.JText::_('Bonuses are no longer accepted for this wish.').'">( '.$item->points.' )</span>'.n;
					$html .= t.t.t.'<span class="bonus_inactive" title="'.JText::_('Bonuses are no longer accepted for this wish.').'">&nbsp;</span>'.n;
					}
				$html .= t.t.t.'</div>'.n;
				}
				$html .= t.t.'</div>';
				}
				
				$html .= t.t.'<div style="clear:left;"></div>'.n;
				$html  .= t.t.'</li>'.n;
			  }
			}				
			$html .= t.'</ul>'.n;
			}
			else {
				if($filters['filterby']=="all") {
				$html .= t.t.t.'<p>'.JText::_('There are currently no wishes on this list. Be the first to make a suggestion.').'</p>'.n;
				}
				else {
				$html .= t.t.t.'<p>'.JText::_('There are currently no wishes on this list based on your selection.').'</p>'.n;
				}
			
				$html .= t.t.t.'<p class="add"><a href="'.JRoute::_('index.php?option='.$option.a.'task=add'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">Add a new wish</a></p>'.n;	
			}	
					
			$html .= ($wishlist->plugin) ? '</div>' : '</div><div class="clear"></div></div>'.n;			
		  }
		}
		else {
			// Display error
			$html  .= WishlistHtml::error(JText::_('ERROR_LIST_NOT_FOUND')).n;
		}
		
	
		return $html;
	}
	
	//----------
	
	public function wish( $wishlist, $item,  $title, $option, $task, $error, $admin, $juser, $addcomment, $plan, $abuse=true, $canedit=false) 
	{
		
		$html = ''; 
		
		if($wishlist && $item) {
		
		// What name should we dispay for the submitter?
		$name = JText::_('ANONYMOUS');
		if ($item->anonymous != 1) {
			$name = JText::_('UNKNOWN');
			$ruser =& XUser::getInstance($item->proposed_by);
			if (is_object($ruser)) {
				$name = $ruser->get('name');
			}
		}
		
		if($admin) {
			$assigned = JText::_('UNKNOWN');
			$auser =& XUser::getInstance($item->assigned);
			if (is_object($auser)) {
				$assigned = $auser->get('name');
			}
		}
		$assigned = ($item->assigned && $admin) ? JText::_('assigned to').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">'.$assigned.'</a>' : '';	
		
		if(!$assigned && $admin && $item->status==0) {
		$assigned = '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">'.JText::_('unassigned').'</a>';
		}
		
		
			// who granted the wish?
			$granted_by = JText::_('UNKNOWN');
			$guser =& XUser::getInstance($item->granted_by);
			if (is_object($guser)) {
				$granted_by = $guser->get('name');
			}
		
		// What's the status of the wish?
		//$status = ($item->status==0) ? JText::_('pending'): JText::_('granted');
		
		$item->status = ($item->accepted==1 && $item->status==0) ? 6 : $item->status;
		$due  = ($item->due !='0000-00-00 00:00:00') ? JHTML::_('date',$item->due, '%Y-%m-%d') : '';
			
		switch( $item->status) 
		{
			case 0:    	$status = JText::_('pending');
						$statusnote = JText::_('awaiting for list owner(s) to accept/reject the wish');
			break;
			case 6:    	$status = JText::_('accepted');
						$statusnote = JText::_('list owner(s) agreed to implement the wish');
						$statusnote.= $plan ? '; '.JText::_('implementation plan started') : '';
						$statusnote.= $due ? '; '.JText::_('due date set to ').' '.$due : '';
			break;
			case 3:    	$status = JText::_('rejected');
						$statusnote = JText::_('list owner(s) refused to implement the wish');
			break;
			case 4:    	$status = JText::_('withdrawn');
						$statusnote = JText::_('the author withdrew the wish');
			break;
			case 1:    	$status = JText::_('granted');
						$statusnote = $item->granted!='0000-00-00 00:00:00' ? strtolower(JText::_('ON')).' '.JHTML::_('date',$item->granted, '%d %b %y').' '.strtolower(JText::_('BY')).' '.$granted_by : '';
			break;
		}
		
		
		
		
		// Can't view wishes on a private list if not list admin
		if(!$wishlist->public	 && $admin!= 2) {
		  	$html .= WishlistHtml::div( WishlistHtml::hed( 2, JText::_('PRIVATE_LIST') ), '', 'content-header' );
		  	$html .= '<div class="main section">'.n;
			$html  .= WishlistHtml::error(JText::_('Sorry, you are unauthorized to view this private wish list.')).n;
			$html .= '</div>'.n;	
		 }
		
		 else  {
            $html .='<div id="content-header">'.n;
			$html .='	<h2>'.$title.'</h2>'.n;
			$html .='	<h3>'.JText::_('PROPOSED_ON').' '.JHTML::_('date',$item->proposed, '%d %b %Y').' ';
			$html .= JText::_('AT').' '.JHTML::_('date', $item->proposed, '%I:%M %p').' '.JText::_('BY').' '.$name;
			$html .= $assigned ? '; '.$assigned : '';
			$html .='  </h3>'.n;
			
           // $html .='<span class="'.strtolower($status).'">'.strtoupper($status).'</span>'.n;
           // $html .= ($item->status == 1 && $item->granted!='0000-00-00 00:00:00') ? strtolower(JText::_('ON')).' '.JHTML::_('date',$item->granted, '%d %b, %y') : '';
			
           
			$html .='</div><!-- / #content-header -->'.n;
			
			// Navigation
			$html .= '<div id="content-header-extra">'.n;
			$html .= t.'<ul id="useroptions">'.n;
			$html .= t.t.'<li>'.n;
			if($item->prev) {
				$html .= t.t.t.'<a href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->prev.'"><span>&lsaquo; '.JText::_('PREV').'</span></a>'.n;
			} else {
				$html .=t.t.t.'<span>&lsaquo; '.JText::_('PREV').'</span>'.n;
			}
			$html .= t.t.'</li>'.n;
			$html .= t.t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid).'"><span>'.JText::_('ALL_WISHES_ON_THIS_LIST').'</span></a></li>'.n;
			$html .= t.t.'<li class="last">';
			if($item->next) {
				$html .= t.t.t.'<a href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->next.'"><span>'.JText::_('NEXT').' &rsaquo;</span></a>'.n;
			}
			else {
				$html .=t.t.t.'<span>'.JText::_('NEXT').' &rsaquo; </span>'.n;
			}
			$html .= t.t.'</li>'.n;
			$html .= t.'</ul>'.n;
			$html .= '</div>'.n;
			$html .='  <div id="topstatusbar" class="'.strtolower($status).'">'.n;
			$html .='  '.strtoupper(JText::_('STATUS')).': <span class="'.strtolower($status).'">';
			$html .= $admin==2 ? '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=changestatus#action">' : '';
			$html .= strtoupper($status);
			$html .= $admin==2 ? '</a>' : '';
			$html .= '</span>'.n;
			$html .=' - '.$statusnote.n;
			$html .='  </div>'.n;
           
			
			$html .= '<div class="clear"></div>'.n;	
			$html .= '<div class="main section noborder">'.n;
			if ($item->reports) {
		 	// this wish is abusive
			$html  .= WishlistHtml::error(JText::_('NOTICE_POSTING_REPORTED')).n;	
		 	}
			else {
			// Wish content starts
			$html .= t.'<div id="wishitem"';
			$html .= '>'.n;	
			
			// Wish title and description	
			$html .= t.t.'<div class="wishcontentarea';
			if(!$admin) {
			$html .= ' regview';
			}
			$html .= '">'.n;
			if($item->private && $wishlist->public){
			$html .= ' <div class="privatewish"></div>';
			}
			$html .= t.t.t.'<h3';
			$html .= ' class="wish_';
					if(isset($item->ranked) && !$item->ranked && $item->status!=1 && $item->status!=3 && $item->status!=4 && $admin==2)  {
					$html .= 'unranked';
					}	else if($item->status==1) {
					$html .= 'granted';
					}
					else if ($item->urgent==1 && $admin) {
					$html .= 'urgent';
					}
					else if ($item->urgent==2 && $admin) {
					$html .= 'burning';
					}
					else {
					$html .= 'outstanding';
					}	
			$html .='"';
			$html .= '>'.$item->subject.'</h3>'.n;
			
			if($item->about) {
				$html .= t.t.t.'<p>'.$item->about.'</p>'.n;
			}		
			$html .= t.t.'</div>'.n;
								
			if(!$admin) {
				// only show thumbs ranking
				$html .= t.t.'<div class="intermed" style="padding-top:5px;">'.n;
				$html .= t.t.t.'<p id="wish_'.$item->id.'" class="'.$option.'">'.n;
				$html .= t.t.t.t.WishlistHtml::rateitem($item, $juser, $option, $wishlist->id, $admin, 'wish');				
				$html .= t.t.t.'</p>'.n;
				$html .= t.t.'</div>'.n;
				
				if($wishlist->banking) {
				$html .= t.t.t.'<div class="assign_bonus">'.n;				
					if(isset($item->bonus) && $item->bonus > 0 && ($item->status==0 or $item->status==6)) {
					$html .= t.t.t.'<a class="bonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus#action" title="'.JText::_('Add bonus points for this wish').' ::'.$item->bonusgivenby.' '.JText::_('user(s)').' '.JText::_('contributed a total of ').' '.$item->bonus.' '.JText::_('points ').' '.JText::_('as a bonus for fulfilling this wish.').'">+ '.$item->bonus.'</a>'.n;
					}
					else if($item->status==0 or $item->status==6) {
					$html .= t.t.t.'<a class="nobonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus#action" title="'.JText::_('Add bonus points for this wish').' :: '.JText::_('0 users have contributed.').'">&nbsp;</a>'.n;
					}
					else {
					$html .= t.t.t.'<span class="bonus_inactive" title="'.JText::_('Bonuses are no longer accepted for this wish.').'">&nbsp;</span>'.n;
					}
				$html .= t.t.t.'</div>'.n;
				}
				
			} else {
				// Show priority and ratings
				$html .= t.t.'<div class="wishranked ';
				if($admin!=2) {
				$html .= 'narrow';
				}
				$html .='">'.n;
				$voters = ($item->num_votes <= count($wishlist->owners)) ? count($wishlist->owners) : $item->num_votes;
				$html .= t.t.t.'<div class="wishpriority">'.JText::_('PRIORITY').': '.$item->ranking.' <span>('.$item->num_votes.' out of '.$voters.' votes)</span>';
				if($due && $item->status!=1) {
				$html .= ($item->due <= date( 'Y-m-d H:i:s')) ? '<span class="overdue"><a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">'.JText::_('overdue') : '<span class="due"><a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">'.JText::_('due in ').' '.WishlistHTML::nicetime($item->due);
				$html .= '</a></span>';
				}
				$html .= '</div>'.n;
				
				// My opinion is available for list owners only
				if($admin==2) {
					$html .= t.t.t.'<div class="rankingarea">'.n;
					$html .= t.t.t.t.'<div>'.n;					
					$html .= t.t.t.t.'<h4>'.JText::_('MY_OPINION').':</h4>';
					
					if(isset($item->ranked) && !$item->ranked && ($item->status==0 or $item->status==6) or $item->action=='editvote') {
						// need to rank it
						$html .= t.t.t.t.WishlistHtml::rankingForm($option, $wishlist, 'savevote', $item).n;							
					} else if(isset($item->ranked) && $item->ranked ) {						
						// already ranked						
						$html .= ($item->status==0 or $item->status==6) ? '<span class="editbutton"><a href="index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id.a.'action=editvote" class="editopinion">['.JText::_('EDIT').']</a></span>' : '';						
						$html .= t.t.t.t.'<p>'.WishlistHtml::convertVote ($item->myvote_imp, 'importance').'</p>'.n;
						$html .= t.t.t.t.'<p>'.WishlistHtml::convertVote ($item->myvote_effort, 'effort').'</p>'.n;					
					}
					else {
						$html .= t.t.t.t.'<p>'.JText::_('NA').'</p>'.n;	
					}
					$html .= t.t.t.t.'</div>'.n;	
					$html .= t.t.t.'</div>'.n;
				} 
				
				// Consensus area, show to all admins	
				$html .= t.t.'<div class="consensusarea">'.n;
				$html .= t.t.t.'<div>'.n;
				$html .= t.t.t.t.'<h4>'.JText::_('CONSENSUS').':</h4>'.n;
				if((isset($item->num_votes) && $item->num_votes==0) or !isset($item->num_votes)) {
					// no ranking available
					$html .= t.t.t.t.'<p>'.JText::_('NA').'</p>'.n;	
				}
				else { 
					// have ranking								
					$html .= t.t.t.t.'<p>'.WishlistHtml::convertVote($item->average_imp, 'importance').'</p>'.n;
					$html .= t.t.t.t.'<p>'.WishlistHtml::convertVote($item->average_effort,'effort').'</p>'.n;				
				}
				$html .= t.t.t.'</div>'.n;
				$html .= t.t.'</div>'.n;
				
				$html .= t.t.'<div class="votingarea">'.n;
				$html .= t.t.t.'<div>'.n;
				$html .= t.t.t.t.'<h4>'.JText::_('COMMUNITY_VOTE').':</h4>'.n;
				$html .= t.t.t.t.'<div id="wish_'.$item->id.'" class="'.$option.'">';
				$html .= t.t.t.t.WishlistHtml::rateitem($item, $juser, $option, $wishlist->id, $admin, 'wish');						
				$html .= t.t.t.t.'</div>'.n;
				if($wishlist->banking) {
				$html .= t.t.t.'<div class="assign_bonus">'.n;				
					if(isset($item->bonus) && $item->bonus > 0 && ($item->status==0 or $item->status==6)) {
					$html .= t.t.t.'<a class="bonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus#action" title="'.JText::_('Add bonus points for this wish').' ::'.$item->bonusgivenby.' '.JText::_('user(s)').' '.JText::_('contributed a total of ').' '.$item->bonus.' '.JText::_('points ').' '.JText::_('as a bonus for fulfilling this wish.').'">+ '.$item->bonus.'</a>'.n;
					}
					else if($item->status==0 or $item->status==6) {
					$html .= t.t.t.'<a class="nobonus tooltips" href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=addbonus#action" title="'.JText::_('Add bonus points for this wish').' :: '.JText::_('0 users have contributed.').'">&nbsp;</a>'.n;
					}
					else {
					$html .= t.t.t.'<span class="bonus_inactive" title="'.JText::_('Bonuses are no longer accepted for this wish.').'">&nbsp;</span>'.n;
					}
				$html .= t.t.t.'</div>'.n;
				}
				
				$html .= t.t.t.'</div>'.n;
				$html .= t.t.'</div>'.n;		
				$html .= t.'</div>'.n;
				}
				// end admin						
				$html .= t.'<div class="clear"></div>'.n;
				$html .= t.'</div>'.n;
				
				
				$html .= t.t.t.'<p class="comment-options">';
				
				// some extra admin options
				if($admin) {				
				if($item->status!=1) {
				$html .= t.t.t.t.'<a class="changestatus" href="';
				$html .= JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=changestatus#action">'.JText::_('Change status').'</a>  '.n;
				}
				$html .= t.t.t.t.'<a class="transfer" href="';
				$html .= JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=move#action">'.JText::_('MOVE').'</a>'.n;
				/*	
				if($item->status != 1) {
					$html .= t.t.t.t.' | <a class="grant" href="';
					$html .= JRoute::_('index.php?option='.$option.a.'task=grantwish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">'.JText::_('MARK_GRANTED').'</a>'.n;	
				}*/
				// add extra privacy controls if this is a public list & wish is submitted by list owners
				//if($wishlist->public && in_array($item->proposed_by, $wishlist->owners)) {
					if($item->private) {
						$html .= t.t.t.t.'  <a class="makepublic" href="';
						$html .= JRoute::_('index.php?option='.$option.a.'task=editprivacy'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?private=0">'.JText::_('MAKE_PUBLIC').'</a>'.n;	
					}
					else {
						$html .= t.t.t.t.'  <a class="makeprivate" href="';
						$html .= JRoute::_('index.php?option='.$option.a.'task=editprivacy'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?private=1">'.JText::_('MAKE_PRIVATE').'</a>'.n;	
					}
				//}
				/*
				$html .= t.t.t.t.' | <a class="deletewish" href="';
				$html .= JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=delete#action">'.JText::_('DELETE').'</a>'.n;
				*/
				
				// site admins can edit wishes
				if($canedit) {
				$html .= t.t.t.t.'<a class="editwish" href="';
				$html .= JRoute::_('index.php?option='.$option.a.'task=editwish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">'.ucfirst(JText::_('Edit')).'</a>  '.n;
				}
				
				}
				// Report abuse option is for everyone
				$html .= '<a href="index.php?option=com_support'.a.'task=reportabuse'.a.'category=wish'.a.'id='.$item->id.a.'parent='.$wishlist->id.'" class="abuse">'.JText::_('REPORT_ABUSE').'</a>'.n;
				// withdraw wish option if author
				if($juser->get('id') == $item->proposed_by && $item->status==0) {
				$html .= t.t.t.t.' | <a class="deletewish" href="';
				$html .= JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=delete#action">'.JText::_('Withdraw my wish').'</a>'.n;
				}
				
				
				$html .= t.t.t.'</p>'.n;
				
	
				// delete wish?
				if($item->action == 'delete') {
					$html .= t.'<a name="action"></a>'.n;
					$html .= t.t.'<div class="error">'.n;
					$html .= t.t.'<h4>'.JText::_('ARE_YOU_SURE_DELETE_WISH').'</h4>'.n;
					$html .= t.t.'<p><span class="say_yes">';
					$html .='<a href="index.php?option='.$option.a.'task=withdraw'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id.'">';
					$html .= strtoupper(JText::_('YES')).'</a></span> <span class="say_no">';
					$html .='<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">';
					$html .= strtoupper(JText::_('NO')).'</a></span></p>'.n;
					$html .= t.t.'</div>'.n;
				}
				// change status?
				if($item->action == 'changestatus') {
					$html .= t.'<a name="action"></a>'.n;
					$html .= t.t.'<div class="takeaction">'.n;
					$html .= t.t.'<div class="aside_note">'.n;
					$html .= t.t.'<p>'.JText::_('The status of a wish serves to indicate to the general site users whether the wish was accepted/rejected for implementation and when to expect the proposed feature online.').'</p>'.n;
					$html .= t.t.'</div>'.n;
					$html .= t.t.'<h4>'.JText::_('Change the status of this wish to').':</h4>'.n;
					$html .= t.t.t.' <form id="changeStatus" method="post" action="index.php?option='.$option.'">'.n;
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <input type="hidden"  name="task" value="editwish" />'.n;
					$html .= t.t.t.'	  <input type="hidden" id="wishlist" name="wishlist" value="'.$wishlist->id.'" />'.n;
					$html .= t.t.t.'	  <input type="hidden" id="category" name="category" value="'.$wishlist->category.'" />'.n;
					$html .= t.t.t.'	  <input type="hidden" id="rid" name="rid" value="'.$wishlist->referenceid.'" />'.n;
					$html .= t.t.t.'	  <input type="hidden" id="wishid" name="wishid" value="'.$item->id.'" />'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'      <input class="option" type="radio" name="status" value="pending" ';
					if($item->status==0) {
					$html .= 'checked="checked"';
					}
					$html .= ' /> '.JText::_('Pending').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'      <input class="option" type="radio" name="status" value="accepted" ';
					if($item->status==6) {
					$html .= 'checked="checked"';
					}
					$html .=' /> '.JText::_('Accepted').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'      <input class="option" type="radio" name="status" value="rejected" ';
					if($item->status==3) {
					$html .= 'checked="checked"';
					}
					$html .=' /> '.JText::_('Rejected').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					$html .= t.t.t.'<fieldset';
					$html .= ($wishlist->category=='resource' ) ? ' class="grantstatus">'.n : '>';
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'      <input class="option" type="radio" name="status" value="granted" ';
					if($item->status==1) {
					$html .= 'checked="checked"';
					}
					if($item->assigned && $item->assigned!=$juser->get('id') ) {
					$html .= 'disabled="disabled"';
					}
					$html .=' /> '.JText::_('Granted').n;
					if($item->assigned && $item->assigned!=$juser->get('id') ) {
					$html .= ' <span class="forbidden"> - '.JText::_('This wish can be granted only by the person to whom it is assigned').n;
					}
					else if($wishlist->category=='resource' ) {
					$html .= t.t.t.'<label class="doubletab">'.n;
					$html .= t.t.t.'	  </label>'.n;
					}
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
				
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'     <input type="submit" value="'.strtolower(JText::_('Change status')).'" /> <span class="cancelaction">';
					$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">';
					$html .= JText::_('CANCEL').'</a></span>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;			
					$html .= t.t.t.' </form>'.n;
					$html .= t.t.'</div>'.n;
				}
				// assign a bonus to the wish?
				if($item->action == 'addbonus' && $item->status!=1 && $wishlist->banking) {
					//$item->funds = 0;
					$html .= t.'<a name="action"></a>'.n;
					$html .= t.t.'<div class="addbonus">'.n;
					$html .= t.t.'<div class="aside_note">'.n;
					$html .= t.t.'<p>'.JText::_('WHY_ADDBONUS').'</p>'.n;
					$html .= t.t.'</div>'.n;
					$bonus = $item->bonus ? $item->bonus : 0; 
					$html .= t.t.'<h4>'.JText::_('Add bonus points for fulfilling this wish').'</h4>'.n;
					$html .= t.t.'<h4 class="summary">'.$item->bonusgivenby.' '.JText::_('user(s)').' '.JText::_('contributed a total of ').' '.$bonus.' '.JText::_('points ').' '.JText::_('as a bonus for fulfilling this wish').'</h4>'.n;
					$html .= t.t.t.' <form id="addBonus" method="post" action="index.php?option='.$option.'">'.n;
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <input type="hidden"  name="task" value="addbonus" />'.n;
					$html .= t.t.t.'	  <input type="hidden" id="wishlist" name="wishlist" value="'.$wishlist->id.'" />'.n;
					$html .= t.t.t.'	  <input type="hidden" id="wish" name="wish" value="'.$item->id.'" />'.n;
					$html .= t.t.t.'	  <label>'.JText::_('Add').n;
					$html .= t.t.t.'	  <span class="price"></span> '.n;
					$html .= t.t.t.'      <input class="secondary_option" type="text" maxlength="4" name="amount" value=""';
					if($item->funds <= 0) {
					$html .= ' disabled="disabled"';
					}
					$html .= '" />'.n;
					$html .= t.t.t.'	  <span>('.JText::_('out of').' '.$item->funds.' '.JText::_('points available on your').' <a href="members'.DS.$juser->get('id').DS.'points">'.JText::_('Account').'</a>)</span>'.n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= ($item->funds > 0) ? t.t.t.'     <input type="submit" class="process" value="'.strtolower(JText::_('Add Points')).'" /> '.n : '';
					$html .= t.t.t.'<span class="cancelaction">';
					$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">';
					$html .= JText::_('CANCEL').'</a></span>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;			
					$html .= t.t.t.' </form>'.n;
					$html .= ($item->funds <= 0) ? t.t.t.'<p class="nofunds">'.JText::_('Sorry, you have no funds available for this transaction.').'</p>'.n : '';
					$html .= t.t.'<div class="clear"></div>'.n;
					$html .= t.t.'</div>'.n;
				}
				// move wish?
				if($item->action=='move') {
					$html .= t.'<a name="action"></a>'.n;
					$html .= t.t.'<div class="moveitem">'.n;
					if($error) {
					$html  .= WishlistHtml::error($error).n;
					}
					$html .= t.t.'<h4>'.JText::_('WISH_BELONGS_TO').':</h4>'.n;
					$html .= t.t.t.' <form id="moveWish" method="post" action="index.php?option='.$option.'">'.n;
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <input type="hidden"  name="task" value="movewish" />'.n;
					$html .= t.t.t.'	  <input type="hidden" id="wishlist" name="wishlist" value="'.$wishlist->id.'" />'.n;
					$html .= t.t.t.'	  <input type="hidden" id="wish" name="wish" value="'.$item->id.'" />'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'      <input class="option" type="radio" name="type" value="general" ';
					if($wishlist->category=='general') {
					$html .= 'checked="checked"';
					}
					$html .= ' /> '.JText::_('Main general wish list').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'      <input class="option" type="radio" name="type" value="resource" ';
					if($wishlist->category=='resource') {
					$html .= 'checked="checked"';
					}
					$html .=' /> '.JText::_('Wish list for resource (id)').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'      <input class="secondary_option" type="text" name="resource" id="acresource" value="';
					if($wishlist->category=='resource') {
					$html .= $wishlist->referenceid;
					}
					$html .= '" autocomplete="off" />'.n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					if(isset($item->cats) && preg_replace("/group/", "", $item->cats) != $item->cats) {
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'      <input class="option" type="radio" name="type" value="group" ';
					if($wishlist->category=='group') {
					$html .= 'checked="checked"';
					}
					$html .=' /> '.JText::_('Wish list for group (name)').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	  <label>'.n;
					
					$document =& JFactory::getDocument();
					$document->addScript('components'.DS.'com_support'.DS.'observer.js');
					$document->addScript('components'.DS.'com_wishlist'.DS.'autocompleter.js');
					$document->addStyleSheet('components'.DS.'com_support'.DS.'autocompleter.css');
					
					$html .= t.t.t.'<input type="text" name="group" value="';
					if($wishlist->category=='group') {
					$html .= $wishlist->cn;
					}
					$html .= '" id="acgroup"  class="secondary_option" />'.n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					}
					$html .= t.t.t.'	 <fieldset class="separated">'.n;
					$html .= t.t.'<h4>'.JText::_('Transfer Options').':</h4>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'       <input class="option" type="checkbox" name="keepcomments" value="1" checked="checked" /> ';
					$html .= JText::_('Preserve comments').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'       <input class="option" type="checkbox" name="keepplan" value="1" checked="checked" /> ';
					$html .= JText::_('Preserve implementation plan').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'       <input class="option" type="checkbox" name="keepstatus" value="1" checked="checked" /> ';
					$html .= JText::_('Preserve wish status').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'       <input class="option" type="checkbox" name="keepfeedback" value="1" checked="checked" /> ';
					$html .= JText::_('Preserve community votes').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					
					//$html .= t.t.t.WishlistHtml::formSelect('group', $wishlist->groups, $wishlist->referenceid, '');
					/*
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'      <input class="option" type="radio" name="type" value="ticket" ';
					$html .= ' /> '.JText::_('Support Ticket').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					$html .= t.t.t.'	 <fieldset>'.n;
					$html .= t.t.t.'	  <label>'.n;
					$html .= t.t.t.'      <input class="option" type="radio" name="type" value="question" ';
					$html .= ' /> '.JText::_('Question in the Answers forum').n;
					$html .= t.t.t.'	  </label>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;
					*/
					$html .= t.t.t.'	 <fieldset class="finalblock">'.n;
					$html .= t.t.t.'     <input type="submit" value="'.strtolower(JText::_('Move this wish')).'" /> <span class="cancelaction">';
					$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">';
					$html .= JText::_('CANCEL').'</a></span>'.n;
					$html .= t.t.t.'	 </fieldset>'.n;			
					$html .= t.t.t.' </form>'.n;		
					$html .= t.t.'</div>'.n;
					
				}
							
				// Comments
				// Do we have any comments?
				$commentcount = 0;
					if(isset($item->replies) && count($item->replies) > 0) {
						$commentcount +=  count($item->replies);
						foreach($item->replies as $r) {
							if(count($r->replies) > 0) {
								$commentcount +=  count($r->replies);
								foreach($r->replies as $r2) {
									if(count($r2->replies) > 0) {
										$commentcount +=  count($r2->replies);
									}
								}
							}
						}
					}
					
				// Display comments block
				$html .= t.'<a name="comments"></a>'.n;
				$html .= t.'<div id="section-comments">'.n;
				$html .= t.t.'<h3><a href="javascript:void(0);" class="';
				//$html .= ($admin==2 && !is_object($addcomment) && !$item->com)  ? 'expand' : 'collapse';
				$html .= 'collapse';
				$html .= '" id="part_com">&nbsp;</a> '.JText::_('COMMENTS').' (';
				$html .= $commentcount;
				$html .= ')</h3>'.n;
				$html .= t.t.'<div class="aside_note">'.n;
				$html .= t.t.t.'<p class="add">';
				$html .= '<a href="index.php?option='.$option.a.'task=reply'.a.'cat=wish'.a.'id='.$wishlist->id.a.'refid='.$item->id.a.'wishid='.$item->id.'">';
				$html .= JText::_('ADD_A_COMMENT').'</a></p>'.n;
				$html .= t.t.'</div><!-- / .aside -->'.n;
				$html .= t.t.'<div class="section-content ';
				//$html .= ($admin==2 && !is_object($addcomment) && !$item->com) ? 'collapsed' : 'expanded';
				$html .= '" id="full_com">'.n;
				$html .= WishlistHtml::addcomment($item->id, 0, $juser, $option, $addcomment, $wishlist->id, $item->id);
				if(isset($item->replies)) {
					$html .= t.t.t.WishlistHtml::comments($item->replies, $item->id, $juser, $wishlist->id, $option, $addcomment, $item->proposed_by, $abuse).n;
				}
				if(!isset($item->replies) or count($item->replies)==0) {
					$html .= t.t.t.'<p>'.JText::_('NO_COMMENTS').' <a href="index.php?option='.$option.a.'task=reply'.a.'cat=wish'.a.'id='.$wishlist->id.a.'refid='.$item->id.a.'wishid='.$item->id.'">'.JText::_('MAKE_A_COMMENT').'</a>.</p>'.n;
				}
				$html .= t.t.'</div>'.n;
				$html .= t.'</div>'.n;
			
				// Implementation plan block for list administrators
				if($admin) {
					$html .=t.t.'<a name="plan"></a>'.n;
					$html .=t.t.'<div id="section-plan">'.n;
					$html .=t.t.'<h3><a href="javascript:void(0);" class="';
					$html .= 'collapse';
					$html .='" id="part_plan">&nbsp;</a> '.JText::_('IMPLEMENTATION_PLAN').' ';
					$html .= ($plan) ? '(<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">edit</a>)' : '(not started)';
					$html .= '</h3>'.n;
					$html .= '<div class="aside_note">'.n;
					$html .= '<p class="add"><a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">'.JText::_('ADD_TO_THE_PLAN').'</a></p>'.n;
					$html .= '</div><!-- / .aside -->'.n;
					$html .=t.t.t.'<div class="section-content" id="full_plan">'.n;
					
					// we are editing
					if($item->action=='editplan') {
						
						$document =& JFactory::getDocument();
						$document->addScript('components'.DS.'com_events'.DS.'js'.DS.'calendar.rc4.js');
						$document->addScript('components'.DS.'com_events'.DS.'js'.DS.'events.js');
		
						$html .= '<form action="index.php" method="post" id="hubForm">'.n;
						$html .= t.'<div class="explaination">'.n;
						$html .= t.'	 <p>'.JText::_('You can set a deadline and describe the implementation plan for this wish.').'</p>'.n;
						$html .= t.'</div>'.n;
						$html .= t.'<fieldset style="padding-top:1.5em;">'.n;
						
						// due date						
						$html .= t.t.t.'<label style="display:inline;">'.JText::_('Due').': '.n;
						$html .= t.t.t.t.'<input class="option" type="radio" name="isdue" id="nodue" value="0" ';
						if($due=='') {
						$html .= 'checked="checked"';
						}
						$html .=' /> '.JText::_('never').n;
						$html .= t.t.t.'</label>'.n;
						$html .= t.t.t.'<span class="or">or</span>'.n;
						$html .= t.t.t.'<label  style="display:inline;">'.n;
						$html .= t.t.t.t.'<input class="option" type="radio" id="isdue" name="isdue" value="1" ';
						if($due!='') {
						$html .= 'checked="checked"';
						}
						$html .=' /> '.n;
						$html .= t.t.t.'</label>'.n;
						$html .= t.t.t.'<label  style="display:inline;">'.JText::_('on').n;
						//$html .= JHTML::_('calendar', $due, 'due', 'due', '%Y-%m-%d', array('class'=>'option inputbox', 'size'=>'10',  'maxlength'=>'10'));
						$html .= t.t.t.t.'<input class="option" type="text" name="publish_up" id="publish_up" size="10" maxlength="10" value="'.$due.'" />'.n;
						$html .= t.t.t.'</label>'.n;
						
						// Assigned to
						$html .= '<label>'.JText::_('Assigned to').':'.n;
						$html .= $item->assignlist;
						$html .= '</label>'.n;
						
						$html .= t.t.'<label>'.n;
						$html .= t.t.t.JText::_('Insert text below').' ('.JText::_('please use').' <a href="/topics/Help:WikiFormatting" rel="external">'.JText::_('Wiki Formatting').'</a>) '.n;	
						if($plan) {
						$html .= t.t.'<div class="newrev">'.n;
						$html .= '<input type="checkbox" class="option" name="create_revision" value="1" />';
						$html .= JText::_('New revision').n;
						$html .= t.t.'</div>'.n;
						}
						else {
						$html .= '<input type="hidden" name="create_revision" value="0" />';
						}
						$html .= t.t.'</label>'.n;
						$html .= t.t.'<label style="margin-top:-2em;">'.n;
						$html .= t.t.t.'<ul id="wiki-toolbar" class="hidden"></ul>'.n;
						$html .= t.t.t.'<textarea name="pagetext" id="pagetext" rows="40" cols="35">';
						$html .= isset($plan->pagetext) ? $plan->pagetext : '';
						$html .= '</textarea>'.n;
						$html .= t.t.'</label>'.n;
						$html .= '<input type="hidden" name="pageid" value="';
						$html .= isset($plan->id) ? $plan->id : '';
						$html .= '" />'.n;
						$html .= '<input type="hidden" name="version" value="';
						$html .= isset($plan->version) ? $plan->version : 1;
						$html .= '" />'.n;
						$html .= t.t.'<input type="hidden" name="wishid" value="'. $item->id .'" />'.n;
						$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
						$html .= t.t.'<input type="hidden" name="created_by" value="'. $juser->get('id').'" />'.n;
						$html .= t.t.'<input type="hidden" name="task" value="saveplan" />'.n;
						$html .= t.'<p class="submit"><input type="submit" name="submit" value="'.JText::_('Save').'" /><span class="cancelaction">';
						$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'">';
						$html .= JText::_('CANCEL').'</a></span></p>'.n;
						$html .= t.'</fieldset>'.n;						
						$html .= '</form>'.n;
						$html .= t.'<div class="clear"></div>'.n;
					}
					else if(!$plan) {
						// there is no plan yet
						$html .=t.t.t.'<p>'.JText::_('THERE_IS_NO_PLAN').' <a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">'.JText::_('START_PLAN').'</a>.</p>'.n;
						if($item->status==0 or $item->status==6) {
						$html .= t.t.t.'<p>';
						$html .= JText::_('This plan is ').$assigned;
						$html .= ' '.JText::_('and is due ').'<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">';
						$html .= ($due) ? ' '.$due : JText::_('never');
						$html .= '</a>.';
						$html .=t.t.t.'</p>'.n;
						}
					}
					else {
						// we have a plan!
						if($item->status==0 or $item->status==6) {
						$html .= t.t.t.'<p>';
						$html .= JText::_('This plan is ').$assigned;
						$html .= ' '.JText::_('and is due ').'<a href="'.JRoute::_('index.php?option='.$option.a.'task=wish'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid.a.'wishid='.$item->id).'?action=editplan#plan">';
						$html .= ($due) ? ' '.$due : JText::_('never');
						$html .= '</a>.';
						$html .=t.t.t.'</p>'.n;
						}
						
						$xuser =& XUser::getInstance ( $plan->created_by );
						if (is_object($xuser)) {
							$login = $xuser->get('login');
						} else {
							$login = JText::_('UNKNOWN'); 
						}
						$html .=t.t.t.'<div class="planbody">'.n;
						$html .=t.t.t.'<p class="plannote">'.JText::_('Last edit on ').JHTML::_('date', $plan->created, '%d %b %Y').' at '.JHTML::_('date',$plan->created, '%I:%M %p').' '.JText::_('by').' '.$login.'</p>'.n;
						ximport('wiki.parser');
						$p = new WikiParser( $wishlist->title, $option, 'wishlist'.DS.$wishlist->id, 'resources', $wishlist->id);
						$maintext = $p->parse( n.stripslashes($plan->pagetext) );					
						$html .=t.t.t.$maintext.n;
						$html .=t.t.t.'</div>'.n;
					}
					$html .=t.t.t.'</div>'.n;
					$html .=t.t.'</div>'.n;		
				}
			  } // end if not abusive
			
				$html .= t.'<div class="clear"></div></div>'.n;
			}		
		}
		else {
			// throw error, shouldn't be here
		}
		
		return $html;
	
	}
	
	//----------
	
	public function settings( $wishlist, $title, $option, $error, $admin, $juser)
	{
		$html  ='';
		
		// Can't view wishes on a private list if not list admin
		if(!$wishlist->public	&& $admin!= 2) {
		  	$html .= WishlistHtml::div( WishlistHtml::hed( 2, JText::_('PRIVATE_LIST') ), '', 'content-header' );
		  	$html .= '<div class="main section">'.n;
			$html  .= WishlistHtml::error(JText::_('Sorry, you are unauthorized to view this private wish list.')).n;
			$html .= '</div>'.n;	
		 }
		 else {	
		 $html .= WishlistHtml::div( WishlistHtml::hed( 2, $title ), '', 'content-header' );
		
		// navigation options
		$html .= '<div id="content-header-extra">'.n;
		$html .= t.'<ul id="useroptions">'.n;
		$html .= t.t.'<li class="last"><a class="nav_wishlist" href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">'.JText::_('All Wishes').'</a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= '</div><!-- / #content-header-extra -->'.n;
		$html .= '<div class="main section">'.n;
		$html .= t.' <form id="hubForm" method="post"  action="'.JRoute::_('index.php?option='.$option.a.'task=savesettings').'?listid='.$wishlist->id.'">'.n;
		$html .= t.'	 <div class="explaination">'.n;
		$html .= t.'	 <p>'.JText::_('Use this screen to modify wishlist settings. Depending on the wishlist type, certain options are not available.').'</p>'.n;
		$html .= t.'	 </div>'.n;
		$html .= t.'	 <fieldset>'.n;
		$html .= t.'	 <h3>'.JText::_('Wishlist Information').'</h3>'.n;
		
		//if($wishlist->category=='general' && $wishlist->referenceid=='1') {
		//$html .= t.'	 	<p class="highighted">'.JText::_('This is the primary site wishlist.').'</p>'.n;
		//}
		
		$html .= t.'	 	<label>'.JText::_('Title').': '.n;
		if($wishlist->category== 'resource') {
			// Cannot change title of resource wish list
			$html .= t.'	 	<span class="highighted">'.$wishlist->title.'</span>'.n;
			$html .= t.'	 	<input name="title" id="title" type="hidden" value="'.$wishlist->title.'" /></label>'.n;
			$html .= t.'	 	<p class="hint">'.JText::_('Title for a resource wishlist cannot change.').'</p>'.n;
		}
		else {
			$html .= t.'	 	<input name="title" id="title" type="text" value="'.$wishlist->title.'" /></label>'.n;
		}
		
		$html .= t.'	 	<label>'.JText::_('Description').' ('.JText::_('optional').'):'.n;
		$html .= t.'	 	<textarea name="description" rows="10" cols="50">'.$wishlist->description.'</textarea></label>'.n;
		$html .= t.'	  <label>'.JText::_('This list is').': '.n;
		$html .= t.'      <input class="option" type="radio" name="public" value="1" ';
		if($wishlist->public==1) {
			$html .= ' checked="checked"';
		}
		if($wishlist->category=='resource' or ($wishlist->category=='general' && $wishlist->referenceid=='1')) {
			$html .= ' disabled="disabled"';
		}
		$html .= ' /> '.JText::_('Public').n;
		$html .= t.t.t.'      <input class="option" type="radio" name="public" value="0" ';
		if($wishlist->public==0) {
			$html .= ' checked="checked"';
		}
		if($wishlist->category=='resource' or ($wishlist->category=='general' && $wishlist->referenceid=='1')) {
			$html .= ' disabled="disabled"';
		}
		$html .=' /> '.JText::_('Private').n;
		$html .= t.t.t.'	  </label>'.n;
		$html .= t.'	 </fieldset>'.n;
		$html .= t.'	 <div class="clear"></div>'.n;
		$html .= t.'	 <div class="explaination">'.n;
		$html .= t.'	 <p>'.JText::_('Use this screen to add/delete groups who are allowed to manage the wishlist. Depending on the wishlist type, certain owner groups cannot be deleted. Every list should have at least one owner.').'</p>'.n;
		$html .= t.'	 </div>'.n;
		$html .= t.'	 <fieldset>'.n;
		$html .= t.'	 <h3>'.JText::_('Wishlist Owner Groups').'</h3>'.n;
		$html .= t.'	<table class="tktlist">'.n;
		$html .= t.' 		<thead>'.n;
		$html .= t.' 			<tr>'.n;
		$html .= t.' 			 <th style="width:20px;"></th>'.n;
		$html .= t.' 			 <th>'.JText::_('Group CN').'</th>'.n;
		$html .= t.' 			 <th>'.JText::_('Num of Members').'</th>'.n;
		$html .= t.' 			 <th style="width:80px;">'.JText::_('Options').'</th>'.n;
		$html .= t.' 			</tr>'.n;
		$html .= t.'		</thead>'.n;
		$html .= t.'		<tbody>'.n;
		
		$allmembers = array();
		if(count($wishlist->groups)>0) {
			$k=1;
			for ($i=0, $n=count( $wishlist->groups ); $i < $n; $i++) {
			$instance = new XGroup($wishlist->groups[$i]);
			$cn = $instance->get('cn');
			$members = $instance->get('members');
			$managers = $instance->get('managers');
			$members = array_merge($members, $managers);
			$members = array_unique($members);
			
			$allmembers = array_merge($allmembers, $members);
			$html .= t.' 			<tr>'.n;
			$html .= t.' 			 <td>'.$k.'.</td>'.n;
			$html .= t.' 			 <td>'.$cn.'</td>'.n;
			$html .= t.' 			 <td>'.count($members).'</td>'.n;
			$html .= t.' 			 <td>';
			$html .= ($n>1 && !in_array($wishlist->groups[$i], $wishlist->nativegroups)) ? '<a href="'.JRoute::_('index.php?option='.$option.a.'task=savesettings').'?listid='.$wishlist->id.a.'action=delete'.a.'group='.$wishlist->groups[$i].'" class="delete">'.JText::_('remove').'</a>' : '' ;
			$html .= t.'			 </td>'.n;
			$html .= t.' 			</tr>'.n;
			$k++;
			}
		}
		else {
			$html .= t.' 			<tr>'.n;
			$html .= t.' 			 <td colspan="4">'.JText::_('No owner groups found.').'.</td>'.n;
			$html .= t.' 			</tr>'.n;
		}
		$html .= t.' 		</tbody>'.n;				
		$html .= t.'	</table>'.n;
		$html .= t.'	 <label>'.JText::_('Add groups to manage this wishlist').': '.n;
		$html .= t.'	 	<input name="newgroups"  type="text" value="" /></label>'.n;		
		$html .= t.'	 </fieldset>'.n;
		$html .= t.'	 <div class="clear"></div>'.n;
		$html .= t.'	 <div class="explaination">'.n;
		$html .= t.'	 <p>'.JText::_('Add/delete individual list owners who are not part of owner groups.').'</p>'.n;
		$html .= t.'	 </div>'.n;
		$html .= t.'	 <fieldset>'.n;
		$html .= t.'	 <h3>'.JText::_('Wishlist Individual Owners').'</h3>'.n;
		$html .= t.'	<table class="tktlist">'.n;
		$html .= t.' 		<thead>'.n;
		$html .= t.' 			<tr>'.n;
		$html .= t.' 			 <th style="width:20px;"></th>'.n;
		$html .= t.' 			 <th>'.JText::_('Name').'</th>'.n;
		$html .= t.' 			 <th>'.JText::_('Login').'</th>'.n;
		$html .= t.' 			 <th style="width:80px;">'.JText::_('Options').'</th>'.n;
		$html .= t.' 			</tr>'.n;
		$html .= t.'		</thead>'.n;
		$html .= t.'		<tbody>'.n;
		
		$allmembers = array_unique($allmembers);	
		
		// if we have people outside of groups
		if(count($wishlist->owners) > count($allmembers)) {
			$k=1;
					
			for ($i=0, $n=count( $wishlist->owners ); $i < $n; $i++) {
				
				if(!in_array($wishlist->owners[$i], $allmembers)) {
				$kuser =& XUser::getInstance ( $wishlist->owners[$i]);
				$html .= t.' 			<tr>'.n;
				$html .= t.' 			 <td>'.$k.'.</td>'.n;
				$html .= t.' 			 <td>'.$kuser->get('name').'</td>'.n;
				$html .= t.' 			 <td>'.$kuser->get('login').'</td>'.n;
				$html .= t.' 			 <td>';
				$html .= ($n> 1 && !in_array($wishlist->owners[$i], $wishlist->nativeowners))  ? '<a href="'.JRoute::_('index.php?option='.$option.a.'task=savesettings').'?listid='.$wishlist->id.a.'action=delete'.a.'user='.$wishlist->owners[$i].'" class="delete">'.JText::_('remove').'</a>' : '' ;
				$html .= t.'			 </td>'.n;
				$html .= t.' 			</tr>'.n;
				$k++;
				}
			}
		}
		else {
			$html .= t.' 			<tr>'.n;
			$html .= t.' 			 <td colspan="4">'.JText::_('No individual owners outside of owner groups found').'.</td>'.n;
			$html .= t.' 			</tr>'.n;
		}
		$html .= t.' 		</tbody>'.n;				
		$html .= t.'	</table>'.n;
		$html .= t.'	 <label>'.JText::_('Add individuals to manage this wishlist').': '.n;
		$html .= t.'	 	<input name="newowners" id="newowners" type="text" value="" /></label>'.n;
		
		if($wishlist->category=='resource' or ($wishlist->category=='general' && $wishlist->referenceid=='1')) {
		$html .= t.'    <input type="hidden" name="public" value="'.$wishlist->public.'" />'.n;
		}		
		$html .= t.'	 </fieldset>'.n;
		$html .= t.'    <div class="clear"></div>'.n;
		
		$html .= t.'<p class="submit"><input type="submit" name="submit" value="'.JText::_('Save').'" /><span class="cancelaction">';
						$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='.$wishlist->category.a.'rid='.$wishlist->referenceid).'">';
						$html .= JText::_('CANCEL').'</a></span></p>'.n;
		$html .= t.'  </form>'.n;
		$html .= t.'</div>';
		
		} // end if authorized
		return $html;	
	
	}
	
	
	//-------------------------------------------------------------
	// Comments
	//-------------------------------------------------------------

	function comments($replies, $wishid, $juser, $listid, $option, $addcomment, $wishauthor, $abuse=true) 
	{
		$o = 'even';
		
		$html = '';
		if (count($replies) > 0) {
			$html .= t.t.t.'<ol class="comments pass2">'.n;
			foreach ($replies as $reply) 
			{
				$o = ($o == 'odd') ? 'even' : 'odd';
				
				// Comment
				$html .= t.'<li class="comment '.$o;
				if ($abuse && $reply->reports > 0) {
					$html .= ' abusive';
				}
				$html .= '" id="c'.$reply->id.'r">';
				$html .= WishlistHtml::comment($reply, $juser, $option, $listid, $wishid, $addcomment,$wishauthor, 1, $abuse, $o).n;
				// Another level? 
				if (count($reply->replies) > 0) {
					$html .= t.t.t.'<ol class="comments pass3">'.n;
					foreach ($reply->replies as $r) 
					{
						$o = ($o == 'odd') ? 'even' : 'odd';
						
						$html .= t.'<li class="comment '.$o;
						if ($abuse && $r->reports > 0) {
							$html .= ' abusive';
						}
						$html .= '" id="c'.$r->id.'r">';
						$html .= WishlistHtml::comment($r, $juser, $option, $listid, $wishid, $addcomment, $wishauthor, 2, $abuse, $o).n;
		
						// Yet another level?? 
						if (count($r->replies) > 0) {
							$html .= t.t.t.'<ol class="comments pass4">'.n;
							foreach ($r->replies as $rr) 
							{
								$o = ($o == 'odd') ? 'even' : 'odd';
								
								$html .= t.'<li class="comment '.$o;
								if ($abuse && $rr->reports > 0) {
									$html .= ' abusive';
								}
								$html .= '" id="c'.$rr->id.'r">';
								$html .= WishlistHtml::comment($rr, $juser, $option, $listid, $wishid, $addcomment, $wishauthor, 3, $abuse, $o).n;
								$html .= t.'</li>'.n;
							}
							$html .= t.t.t.'</ol><!-- end pass4 -->'.n;
						}
						$html .= t.'</li>'.n;
					}
					$html .= t.t.t.'</ol><!-- end pass3 -->'.n;
				}
				$html .= t.'</li>'.n;
			}
			$html .= t.t.t.'</ol><!-- end pass2 -->'.n;
		}
		return $html;
	
	}
	
	//-----------

	function comment($reply, $juser, $option, $listid, $wishid, $addcomment, $wishauthor, $level, $abuse, $o='') 
	{
		// Set the name of the reviewer
		$name = JText::_('ANONYMOUS');
		if ($reply->anonymous != 1) {
			$name = JText::_('UNKNOWN');
			$ruser =& XUser::getInstance($reply->added_by);
			if (is_object($ruser)) {
				$name = $ruser->get('name');
			}
		}
		
		$html  = t.t.'<dl class="comment-details">'.n;
		$html .= t.t.t.'<dt class="type">';
		$html .= '<span class="';
		if($reply->added_by == $wishauthor && $reply->anonymous != 1) {
		$html .= 'submittercomment';
		}
		else if($reply->admin && $reply->anonymous != 1) {
		$html .= 'admincomment';
		}
		else {
		$html .= 'plaincomment';
		}	
		
		$html .= '"><span>'.JText::sprintf('COMMENT').'</span></span></dt>'.n;
		$html .= t.t.t.'<dd class="date">'.JHTML::_('date',$reply->added, '%d %b %Y').'</dd>'.n;
		$html .= t.t.t.'<dd class="time">'.JHTML::_('date',$reply->added, '%I:%M %p').'</dd>'.n;
		$html .= t.t.'</dl>'.n;
		$html .= t.t.'<div class="comwrap">'.n;
		$html .= t.t.t.'<p class="name"><strong>'.$name.'</strong> '.JText::_('SAID').':</p>'.n;

		if ($abuse && $reply->reports > 0) {
			$html .= t.t.t.WishlistHtml::warning( JText::_('NOTICE_POSTING_REPORTED') ).n;
		} else {
			// Add the comment
			$html .= t.t.t.'<p>'.stripslashes($reply->comment).'</p>'.n;			
			$html .= t.t.t.'<p class="comment-options">'.n;
			
			// Cannot reply at third level
			if ($level < 3) {
				$html .= t.t.t.t.'<a ';
				if (!$juser->get('guest')) {
					$html .= 'class="showreplyform" href="javascript:void(0);"';
				}
				else {
					$html .= 'class="rep" href="index.php?option='.$option.a.'task=reply'.a.'cat=wishcomment'.a.'id='.$listid.a.'refid='.$reply->id.a.'wishid='.$wishid.'" ';
				}
				$html .= ' id="rep_'.$reply->id.'">'.JText::_('REPLY').'</a>'.n;
			}
			// Add the "report abuse" link if the abuse component exist
			if ($abuse) {
				$html .= t.t.t.t.'<span class="abuse"><a href="'.JRoute::_('index.php?option=com_support'.a.'task=reportabuse'.a.'category=comment'.a.'id='.$reply->id.a.'parent='.$wishid).'">'.JText::_('REPORT_ABUSE').'</a></span> '.n;
			}
			$html .= t.t.t.'</p>'.n;
			
			// Add the reply form if needed
			if ($level < 3 && !$juser->get('guest')) {
				$html .= WishlistHtml::addcomment($reply->id, $level, $juser, $option, $addcomment, $listid, $wishid);
			}
		}
		
		$html .= t.t.'</div>'.n;
		
		return $html;
	}
	
	//-----------
	
	function addcomment($refid, $level, $juser, $option, $addcomment, $listid, $wishid) 
	{
		$html = '';
		if (!$juser->get('guest')) {
			$category = ($level==0) ? 'wish': 'wishcomment';
			
			$class = ' hide';
			if (is_object($addcomment)) {
				$class = ($addcomment->referenceid == $refid && $addcomment->category==$category) ? ' show' : ' hide';
				
			}
			
			$html .= t.t.t.'<div class="addcomment'.$class.'" id="comm_'.$refid.'">'.n;
			$html .= t.t.t.'<h3>'.JText::_('Add a comment').'</h3>'.n;
			$html .= t.t.t.t.'<form action="index.php" method="post" id="commentform_'.$refid.'">'.n;
			$html .= t.t.t.t.t.'<fieldset>'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="listid" value="'. $listid .'" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="wishid" value="'. $wishid .'" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="task" value="savereply" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="referenceid" value="'.$refid.'" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="cat" value="'.$category.'" />'.n;
			$html .= t.t.t.t.t.t.'<label><input class="option" type="checkbox" name="anonymous" value="1" /> '.JText::_('POST_COMMENT_ANONYMOUSLY').'</label>'.n;
			$html .= t.t.t.t.t.t.'<label><textarea name="comment" rows="4" cols="50" class="commentarea">'.JText::_('Enter your comments...').'</textarea></label>'.n;
			$html .= t.t.t.t.t.'</fieldset>'.n;
			$html .= t.t.t.t.t.'<fieldset class="postcomment">'.n;
			$html .= t.t.t.t.t.'<input type="submit" value="'.JText::_('POST_COMMENT').'" /> <a href="javascript:void(0);" class="closeform" id="close_'.$refid.'">'.JText::_('CANCEL').'</a>'.n;
			$html .= t.t.t.t.t.'</fieldset>'.n;
			$html .= t.t.t.t.'</form>'.n;
			$html .= t.t.t.'</div>'.n;
		}
		
		return $html;
	}

	
	//-------------------------------------------------------------
	// Thumbs voting
	//-------------------------------------------------------------
	
	public function rateitem($item, $juser, $option, $listid, $admin = 0, $page='wishlist', $plugin=0) {
			
			
			$title = ($juser->get('id') == $item->proposed_by) ? JText::_('You cannot vote for your own wish') : '';
			if($juser->get('guest')) { $title = JText::_('Please login to vote'); }
			if($item->vote) { $title = JText::_('You have already voted for this wish'); }
			if($item->status==1 or $item->status==3 or $item->status==4) { $title = JText::_('Voting is closed for this wish'); }
			
			$html = n.t.t.t.'<span class="thumbsvote" title="'.$title.'">'.n;
			
			$pclass = (isset($item->vote) && $item->vote=="yes" && $item->status!=1) ? 'yes' : 'zero';
			$nclass = (isset($item->vote) && $item->vote=="no" && $item->status!=1) ? 'no' : 'zero';
			$item->positive = ($item->positive > 0) ? '+'.$item->positive: '&nbsp;&nbsp;'.$item->positive;
			$item->negative = ($item->negative > 0) ? '-'.$item->negative: '&nbsp;&nbsp;'.$item->negative;
			

				$html .= t.t.t.t.'<span class="'.$pclass.'">'.$item->positive.'</span>'.n;
					
				if ($juser->get('guest')) {
					$html .= t.t.t.t.'<span class="gooditem r_disabled"><a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=yes'.a.'page='.$page.'" >&nbsp;</a></span>'.n;
					$html .= t.t.t.t.'<span class="'.$nclass.'">'.$item->negative.'</span>'.n;
					$html .= t.t.t.t.'<span class="baditem r_disabled"><a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=no'.a.'page='.$page.'" >&nbsp;</a></span>'.n;	
			
				}
				else {					
					$html .= t.t.t.t.'<span class="gooditem">'.n;
				if($item->vote && $item->vote=="no" or  $juser->get('id') == $item->proposed_by or $item->status==1) {
					$html .= t.t.t.t.'<span class="dis">&nbsp;</span>'.n;
				}
				else if($item->vote) {
					$html .= t.t.t.t.'<span>&nbsp;</span>'.n;
				}
				else if($plugin) { // no ajax
					$html .= t.t.t.t.t.'<a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=yes'.a.'page='.$page.'"  title="'.JText::_('THIS_IS_GOOD').'">&nbsp;</a>'.n;
				}
				else {
					$html .= t.t.t.t.t.'<a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=yes'.a.'page='.$page.'"  title="'.JText::_('THIS_IS_GOOD').'">&nbsp;</a>'.n;			
					//$html .= t.t.t.t.t.'<a href="javascript:void(0);" class="revvote" title="'.JText::_('THIS_IS_GOOD').'">&nbsp;</a>'.n;
				}
				$html .= t.t.t.t.'</span>'.n;
				$html .= t.t.t.t.'<span class="'.$nclass.'">'.$item->negative.'</span>'.n;
				$html .= t.t.t.t.'<span class="baditem">'.n;
				if($item->vote && $item->vote=="yes" or $juser->get('id') == $item->proposed_by or $item->status==1) {
					$html .= t.t.t.t.'<span class="dis">&nbsp;</span>'.n;
				}
				else if($item->vote) {
					$html .= t.t.t.'<span>&nbsp;</span>'.n;
				}
				else if($plugin) { // no ajax
					$html .= t.t.t.t.t.'<a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=no'.a.'page='.$page.'"  title="'.JText::_('THIS_IS_NOT_GOOD').'">&nbsp;</a>'.n;
				}
				else {
					$html .= t.t.t.t.t.'<a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=no'.a.'page='.$page.'"  title="'.JText::_('THIS_IS_NOT_GOOD').'">&nbsp;</a>'.n;	
					//$html .= t.t.t.t.t.'<a href="javascript:void(0);" class="revvote" title="'.JText::_('THIS_IS_NOT_GOOD').'">&nbsp;</a>'.n;
				}
				$html .= t.t.t.t.'</span>'.n;
				
				}
				
				if($plugin) {
				$html .= t.t.t.t.'<span class="votinghints"><span>&nbsp;</span></span>'.n;
				}
				//$html .= t.t.t.t.'<span class="votinghints"><span><a href="index.php?option=com_support'.a.'task=reportabuse'.a.'category=wish'.a.'id='.$item->id.a.'parent='.$listid.'" class="abuse">'.JText::_('REPORT_ABUSE').'</a></span></span>'.n;
						
				$html .= t.t.t.'</span>'.n;
					
			return $html;	
	}
	
	//-------------------------------------------------------------
	// Add wish
	//-------------------------------------------------------------
	
	public function wish_form( $title, $wishlist, $wish, $error, $option, $task, $admin, $funds, $banking, $infolink) {
	
		$html = '';
		
		if($wishlist) {
		
			// what is submitter name?
			if($task=='editwish') {
				$login  = JText::_('UNKNOWN');
				$ruser =& XUser::getInstance($wish->proposed_by);
				if (is_object($ruser)) {
					$login = $ruser->get('login');
				}
			}
			
			$wish->about = trim(stripslashes($wish->about));
			$wish->about = preg_replace('/<br\\s*?\/??>/i', "", $wish->about);
			$wish->about = WishlistHtml::txt_unpee($wish->about);
	
			$html .= WishlistHtml::div( WishlistHtml::hed( 2, $title ), '', 'content-header' );
			// navigation options
			$html .= '<div id="content-header-extra">'.n;
			$html .= t.'<ul id="useroptions">'.n;
			$html .= t.t.'<li class="last"><a class="nav_wishlist" href="'.JRoute::_('index.php?option='.$option.a.'task=wishlist'.a.'category='. $wishlist->category.a.'rid='.$wishlist->referenceid) .'">'.JText::_('All Wishes').'</a></li>'.n;
			$html .= t.'</ul>'.n;
			$html .= '</div><!-- / #content-header-extra -->'.n;
			$html .= '<div class="main section">'.n;	
			$html .= t.'<div class="aside">'.n;
			$html .= t.'<p>'.JText::_('TEXT_ADD_WISH').'</p>'.n;
			if($banking && $task!='editwish') {
			$html .= t.'<p class="help" style="margin-top: 2em;"><strong>'.JText::_('WHAT_IS_REWARD').'</strong><br />'.n;
			$html .= t.JText::_('WHY_ADDBONUS').' <a href="'.$infolink.'">'.JText::_('LEARN_MORE').'</a> '.JText::_('ABOUT_POINTS').'.</p>'.n;
			}
			$html .= t.'</div><!-- / .aside -->'.n;
			$html .= t.'<div class="subject">'.n;
			if($error) { WishlistHtml::error($error).n; }
			$html .= t.t.t.' <form id="hubForm" method="post" action="index.php?option='.$option.'">'.n;
			$html .= t.t.t.'	 <fieldset>'.n;
			if($task=='editwish') {
			$html .= t.t.t.'	  <label>Proposed by: <span class="required">required</span>'.n;
			$html .= t.t.t.'	  <input name="by" maxlength="50" id="by" type="text" value="'.$login.'" /></label>'.n;
			}			
			$html .= t.t.t.'	  <input type="hidden" id="proposed_by" name="proposed_by" value="'.$wish->proposed_by.'" />'.n;
			$html .= t.t.t.'	  <label><input class="option" type="checkbox" name="anonymous" value="1" ';
			$html .= ($wish->anonymous) ? 'checked="checked"' : '';
			$html .= '/>Post anonymously</label>'.n;
			if($admin == 2 && $wishlist->public) { // list owner
			$html .= t.t.t.'	  <label><input class="option" type="checkbox" name="private" value="1" ';
			$html .= ($wish->private) ? 'checked="checked"' : '';
			$html .= '/>Make this wish private to list owners (hide from public) </label>'.n;
			}
			$html .= t.t.t.'	  <input type="hidden"  name="task" value="savewish" />'.n;					
			$html .= t.t.t.'	  <input type="hidden" id="wishlist" name="wishlist" value="'.$wishlist->id.'" />'.n;
			$html .= t.t.t.'	  <input type="hidden" id="status" name="status"  value="'.$wish->status.'" />'.n;
			$html .= t.t.t.'	  <input type="hidden" id="id" name="id" value="'.$wish->id.'" />'.n;	
			$html .= t.t.t.'	  <label>'.n;
			$html .= t.t.t.'	  <label>Summary of your wish: <span class="required">required</span>'.n;
			$html .= t.t.t.'	  <input name="subject" maxlength="120" id="subject" type="text" value="'.$wish->subject.'" /></label>'.n;
			$html .= t.t.t.'	  <label>Explain in more detail: '.n;
			$html .= t.t.t.'	  <textarea name="about" rows="10" cols="50">'.$wish->about.'</textarea></label>'.n;
			if($banking && $task!='editwish') {
			$html .= t.t.'<label>'.JText::_('ASSIGN_REWARD').':<br />'.n;
			$html .= t.t.'<input type="text" name="reward" value="" size="5" ';
			if ($funds <= 0 ) {
				$html .= 'disabled style="background:#e2e2e2;" ';		
			}
			$html .= '/> '.JText::_('YOU_HAVE').' <strong>'.$funds.'</strong> '.JText::_('POINTS_TO_SPEND').'.</label>'.n;
			$html .= t.t.t.'	  <input type="hidden"  name="funds" value="'.$funds.'" />'.n;
			}		
			$html .= t.t.t.'      <p class="submit"><input type="submit" value="Submit" /></p>'.n;
			$html .= t.t.t.'	 </fieldset>'.n;			
			$html .= t.t.t.' </form>'.n;			
			$html .= t.t.t.'</div>'.n;
			$html .= '</div><div class="clear"></div></div>'.n;				
		
		}
		else {
			$html  = WishlistHtml::error('Wishlist not found').n;
		}
		
		return $html;
	}


	//-----------
	
	function nicetime($date)
	{
		if(empty($date)) {
			return "No date provided";
		}
		
		$periods         = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths         = array("60","60","24","7","4.35","12","10");
		
		$now             = time();
		$unix_date         = strtotime($date);
		
		   // check validity of date
		if(empty($unix_date)) {    
			return "Bad date";
		}
	
		// is it future date or past date
		if($now > $unix_date) {    
			$difference     = $now - $unix_date;
			$tense         = "ago";
			
		} else {
			$difference     = $unix_date - $now;
			//$tense         = "from now";
			$tense         = "";
		}
		
		for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}
		
		$difference = round($difference);
		
		if($difference != 1) {
			$periods[$j].= "s";
		}
		
		return "$difference $periods[$j] {$tense}";
	}

	
}
?>
