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


class WikiHtml 
{
	public function error( $msg, $tag='p' )
	{
		return '<'.$tag.' class="error">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------
	
	public function warning( $msg, $tag='p' )
	{
		return '<'.$tag.' class="warning">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'."\n";
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}
	
	//-----------
	
	public function subMenu($sub, $option, $pagename, $scope, $state, $task, $params, $authorized) 
	{
		$html = '';
		
		$mode = $params->get( 'mode' );
		
		if ($sub) {
			$hid = 'sub-content-header-extra';
			$uid = 'section-useroptions';
			$sid = 'sub-section-menu';
		} else {
			$hid = 'content-header-extra';
			$uid = 'useroptions';
			$sid = 'sub-menu';
		}
		
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {
			$html .= '<div id="'.$hid.'">'."\n";
			$html .= "\t".'<ul id="'.$uid.'">'."\n";
	
			if (($state == 0 && $authorized) || ($state == 1 && $authorized === 'admin')) {
				$html .= "\t\t".'<li><a href="'.JRoute::_('index.php?option='.$option.'&scope='.$scope.'&pagename='.$pagename.'&task=delete').'">'.JText::_('WIKI_DELETE_PAGE').'</a></li>';
			}
			$html .= "\t\t".'<li class="last"><a href="'.JRoute::_('index.php?option='.$option.'&scope='.$scope.'&task=new').'">'.JText::_('WIKI_NEW_PAGE').'</a></li>';
			$html .= "\t".'</ul>'."\n";
			$html .= '</div><!-- / #content-header-extra -->'."\n";
		}
		
		$html .= '<div id="'.$sid.'">'."\n";
		$html .= "\t".'<ul>'."\n";
		$html .= "\t\t".'<li';
		if ($task == 'view' || !$task) { 
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.'&scope='.$scope.'&pagename='.$pagename).'"><span>'.JText::_('WIKI_TAB_ARTICLE').'</span></a></li>'."\n";
		if (($state == 1 && $authorized === 'admin') || $state != 1) {
		if (($mode == 'knol' && $params->get( 'allow_changes' )) || $authorized || $mode != 'knol') {
			$html .= "\t\t".'<li';
			if ($task == 'edit') { 
				$html .= ' class="active"'."\n";
			}
			$html .= '><a href="'.JRoute::_('index.php?option='.$option.'&scope='.$scope.'&pagename='.$pagename.'&task=edit').'"><span>'.JText::_('WIKI_TAB_EDIT').'</span></a></li>'."\n";
		}
		}
		if (($mode == 'knol' && $params->get( 'allow_comments' )) || $mode != 'knol') {
			$html .= "\t\t".'<li';
			$ctasks = array('comments','addcomment','savecomment','reportcomment','removecomment');
			if (in_array($task,$ctasks)) { 
				$html .= ' class="active"'."\n";
			}
			$html .= '><a href="'.JRoute::_('index.php?option='.$option.'&scope='.$scope.'&pagename='.$pagename.'&task=comments').'"><span>'.JText::_('WIKI_TAB_COMMENTS').'</span></a></li>'."\n";
		}
		$html .= "\t\t".'<li';
		if ($task == 'history' || $task == 'compare') { 
			$html .= ' class="active"'."\n";
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.'&scope='.$scope.'&pagename='.$pagename.'&task=history').'"><span>'.JText::_('WIKI_TAB_HISTORY').'</span></a></li>'."\n";
		$html .= "\t".'</ul>'."\n";
		$html .= "\t".'<div class="clear"></div>'."\n";
		$html .= '</div><!-- / #sub-menu -->'."\n";
		return $html;
    }

	//-----------

	public function tagCloud($tags)
	{
		if (count($tags) > 0) {
			$tagarray = array();
			$tagarray[] = '<ol class="tags">';
			foreach ($tags as $tag)
			{
				$class = '';
				if (isset($tag['admin']) && $tag['admin'] == 1) {
					$class = ' class="admin"';
				}
				$tag['raw_tag'] = str_replace( '&amp;', '&', $tag['raw_tag'] );
				$tag['raw_tag'] = str_replace( '&', '&amp;', $tag['raw_tag'] );
				$tagarray[] = "\t".'<li'.$class.'><a href="'.JRoute::_('index.php?option=com_tags&tag='.$tag['tag']).'" rel="tag">'.$tag['raw_tag'].'</a></li>';
			}
			$tagarray[] = '</ol>';

			$html = implode( "\n", $tagarray );
		} else {
			$html = '<p>'.JText::_('WIKI_PAGE_HAS_NO_TAGS').'</p>';
		}
		return $html;
	}

	//-----------

	public function ranking( $stats, $page, $option )
	{
		$r = (10*$page->ranking);
		if (intval($r) < 10) {
			$r = '0'.$r;
		}
		
		$html  = '<dl class="rankinfo">'."\n";
		$html .= "\t".'<dt class="ranking"><span class="rank-'.$r.'">This page has a</span> '.number_format($page->ranking,1).' Ranking</dt>'."\n";
		$html .= "\t".'<dd>'."\n";
		$html .= "\t\t".'<p>Ranking is calculated from a formula comprised of <a href="'.JRoute::_('index.php?option='.$option.'&scope='.$page->scope.'&pagename='.$page->pagename.'&task=comments').'">user reviews</a> and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a></p>'."\n";
		$html .= "\t\t".'<div>'."\n";
		$html .= $stats;
		$html .= "\t\t".'</div>'."\n";
		$html .= "\t".'</dd>'."\n";
		$html .= '</dl>'."\n";
		return $html;
	}

	//-----------

	public function authors( $page, $params, $contributors=array() )
	{
		$html = '';
		if ($params->get( 'mode' ) == 'knol') {
			$authors = $page->getAuthors();
			
			$author = 'Unknown';
			$ausername = '';
			$auser =& JUser::getInstance($page->created_by);
			if (is_object($auser)) {
				$author = $auser->get('name');
				$ausername = $auser->get('username');
			}
			
			$auths = array();
			$auths[] = '<a href="'.JRoute::_('index.php?option=com_members&id='.$page->created_by).'">'.$author.'</a>';
			foreach ($authors as $auth) 
			{
				if ($auth != $ausername && trim($auth) != '') {
					$zuser =& JUser::getInstance($auth);
					if (is_object($zuser) && $zuser->get('name') != '') {
						$auths[] = '<a href="'.JRoute::_('index.php?option=com_members&id='.$zuser->get('id')).'">'.$zuser->get('name').'</a>';
					}
				}
			}
			$auths = implode(', ',$auths);
			$html .= '<p class="topic-authors">'. JText::_('by') .' '. $auths.'</p>'."\n";
			
			if (count($contributors) > 0) {
				$cons = array();
				foreach ($contributors as $contributor) 
				{
					if ($contributor != $page->created_by) {
						$zuser =& JUser::getInstance($contributor);
						if (is_object($zuser)) {
							if (!in_array($zuser->get('username'),$authors)) {
								$cons[] = '<a href="'.JRoute::_('index.php?option=com_contributors&id='.$contributor).'">'.$zuser->get('name').'</a>';
							}
						}
					}
				}
				$cons = implode(', ',$cons);
				$html .= ($cons) ? '<p class="topic-contributors">'.JText::_('WIKI_PAGE_CONTRIBUTIONS_BY') .' '. $cons.'</p>'."\n" : '';
			}
		}
		return $html;
	}
	
	//-----------

	public function niceidformat($someid) 
	{
		while (strlen($someid) < 5) 
		{
			$someid = 0 . "$someid";
		}
		return $someid;
	}

	//-----------

	public function encode_html($str, $quotes=1)
	{
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
}
