<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

$juser 		=& JFactory::getUser();
$item 		= $this->item;
$option 	= $this->option;
$listid 	= $this->listid;
$admin 		= $this->admin;
$page 		= $this->page;
$plugin 	= $this->plugin;
$filters 	= $this->filters;

$title = ($juser->get('id') == $item->proposed_by) ? JText::_('You cannot vote for your own wish') : '';
if($juser->get('guest')) { $title = JText::_('Please login to vote'); }
if($item->vote) { $title = JText::_('You have already voted for this wish'); }
if($item->status==1 or $item->status==3 or $item->status==4) { $title = JText::_('Voting is closed for this wish'); }
			
// are we voting yes or no
$pclass = (isset($item->vote) && $item->vote=="yes" && $item->status!=1) ? 'yes' : 'zero';
$nclass = (isset($item->vote) && $item->vote=="no" && $item->status!=1) ? 'no' : 'zero';
$item->positive = ($item->positive > 0) ? '+'.$item->positive: '&nbsp;&nbsp;'.$item->positive;
$item->negative = ($item->negative > 0) ? '-'.$item->negative: '&nbsp;&nbsp;'.$item->negative;
			
// import filters
$filterln  = isset($filters['filterby']) ? a.'filterby='.$filters['filterby'] : '';
$filterln .= isset($filters['sortby']) ? a.'sortby='.$filters['sortby'] : '';
$filterln .= isset($filters['tag']) ? a.'tags='.$filters['tag'] : '';
$filterln .= isset($filters['limit']) ? a.'limit='.$filters['limit'] : '';
$filterln .= isset($filters['start']) ? a.'limitstart='.$filters['start'] : '';
				
// Begin HTML output
$html  = n.t.t.t.'<span class="thumbsvote" title="'.$title.'">'.n;
$html .= t.t.t.t.'<span class="'.$pclass.'">'.$item->positive.'</span>'.n;
					
if ($juser->get('guest')) {
	$html .= t.t.t.t.'<span class="gooditem r_disabled"><a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=yes'.a.'page='.$page.$filterln.'" >&nbsp;</a></span>'.n;
	$html .= t.t.t.t.'<span class="'.$nclass.'">'.$item->negative.'</span>'.n;
	$html .= t.t.t.t.'<span class="baditem r_disabled"><a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=no'.a.'page='.$page.$filterln.'" >&nbsp;</a></span>'.n;				
}
else {					
	$html .= t.t.t.t.'<span class="gooditem">'.n;
	if($item->vote && $item->vote=="no" or  $juser->get('id') == $item->proposed_by or $item->status==1) {
		$html .= t.t.t.t.'<span class="dis">&nbsp;</span>'.n;
	}
	else if($item->vote) {
		$html .= t.t.t.t.'<span>&nbsp;</span>'.n;
	}
	else {
		$html .= t.t.t.t.t.'<a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=yes'.a.'page='.$page.$filterln.'"  title="'.JText::_('THIS_IS_GOOD').'">&nbsp;</a>'.n;			
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
	else {
		$html .= t.t.t.t.t.'<a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=no'.a.'page='.$page.$filterln.'"  title="'.JText::_('THIS_IS_NOT_GOOD').'">&nbsp;</a>'.n;	
	}
	$html .= t.t.t.t.'</span>'.n;		
}
				
	if($plugin) {
		$html .= t.t.t.t.'<span class="votinghints"><span>&nbsp;</span></span>'.n;
	}
				
	$html .= t.t.t.'</span>'.n;
					
	echo $html;	
?>
