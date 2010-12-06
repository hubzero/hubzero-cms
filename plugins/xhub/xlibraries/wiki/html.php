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

if (!defined("n")) {
	define("t","\t");
	define("n","\n");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class WikiHtml 
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

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'.n;
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
	
	public function aside($txt, $id='')
	{
		return WikiHtml::div($txt, 'aside', $id);
	}
	
	//-----------
	
	public function subject($txt, $id='')
	{
		return WikiHtml::div($txt, 'subject', $id);
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
			$html .= '<div id="'.$hid.'">'.n;
			$html .= '<ul id="'.$uid.'">'.n;
	
			if (($state == 0 && $authorized) || ($state == 1 && $authorized === 'admin')) {
				$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$scope.a.'pagename='.$pagename.a.'task=delete').'">'.JText::_('WIKI_DELETE_PAGE').'</a></li>';
			}
			$html .= t.'<li class="last"><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$scope.a.'task=new').'">'.JText::_('WIKI_NEW_PAGE').'</a></li>';
			$html .= '</ul>'.n;
			$html .= '</div><!-- / #content-header-extra -->'.n;
		}
		
		$html .= '<div id="'.$sid.'">'.n;
		$html .= t.'<ul>'.n;
		$html .= t.t.'<li';
		if ($task == 'view') { 
			$html .= ' class="active"';
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$scope.a.'pagename='.$pagename).'"><span>'.JText::_('WIKI_TAB_ARTICLE').'</span></a></li>'.n;
		if (($mode == 'knol' && $params->get( 'allow_changes' )) || $authorized || $mode != 'knol') {
			$html .= t.t.'<li';
			if ($task == 'edit') { 
				$html .= ' class="active"'.n;
			}
			$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$scope.a.'pagename='.$pagename.a.'task=edit').'"><span>'.JText::_('WIKI_TAB_EDIT').'</span></a></li>'.n;
		}
		if (($mode == 'knol' && $params->get( 'allow_comments' )) || $mode != 'knol') {
			$html .= t.t.'<li';
			$ctasks = array('comments','addcomment','savecomment','reportcomment','removecomment');
			if (in_array($task,$ctasks)) { 
				$html .= ' class="active"'.n;
			}
			$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$scope.a.'pagename='.$pagename.a.'task=comments').'"><span>'.JText::_('WIKI_TAB_COMMENTS').'</span></a></li>'.n;
		}
		$html .= t.t.'<li';
		if ($task == 'history' || $task == 'compare') { 
			$html .= ' class="active"'.n;
		}
		$html .= '><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$scope.a.'pagename='.$pagename.a.'task=history').'"><span>'.JText::_('WIKI_TAB_HISTORY').'</span></a></li>'.n;
		$html .= t.'</ul>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / #sub-menu -->'.n;
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
				$tagarray[] = ' <li'.$class.'><a href="'.JRoute::_('index.php?option=com_tags'.a.'tag='.$tag['tag']).'" rel="tag">'.$tag['raw_tag'].'</a></li>';
			}
			$tagarray[] = '</ol>';

			$html = implode( n, $tagarray );
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
		
		$html  = '<dl class="rankinfo">'.n;
		$html .= ' <dt class="ranking"><span class="rank-'.$r.'">This page has a</span> '.number_format($page->ranking,1).' Ranking</dt>'.n;
		$html .= ' <dd>'.n;
		$html .= t.'<p>'.n;
		$html .= t.t.'Ranking is calculated from a formula comprised of <a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=comments').'">user reviews</a> ';
		$html .= 'and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a>'.n;
		$html .= t.'</p>'.n;
		$html .= t.'<div>'.n;
		$html .= $stats;
		$html .= t.'</div>'.n;
		$html .= ' </dd>'.n;
		$html .= '</dl>'.n;
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
			$auths[] = '<a href="'.JRoute::_('index.php?option=com_members'.a.'id='.$page->created_by).'">'.$author.'</a>';
			foreach ($authors as $auth) 
			{
				if ($auth != $ausername && trim($auth) != '') {
					$zuser =& JUser::getInstance($auth);
					if (is_object($zuser) && $zuser->get('name') != '') {
						$auths[] = '<a href="'.JRoute::_('index.php?option=com_members'.a.'id='.$zuser->get('id')).'">'.$zuser->get('name').'</a>';
					}
				}
			}
			$auths = implode(', ',$auths);
			$html .= '<p class="topic-authors">'. JText::_('by') .' '. $auths.'</p>'.n;
			
			if (count($contributors) > 0) {
				$cons = array();
				foreach ($contributors as $contributor) 
				{
					if ($contributor != $page->created_by) {
						$zuser =& JUser::getInstance($contributor);
						if (is_object($zuser)) {
							if (!in_array($zuser->get('username'),$authors)) {
								$cons[] = '<a href="'.JRoute::_('index.php?option=com_contributors'.a.'id='.$contributor).'">'.$zuser->get('name').'</a>';
							}
						}
					}
				}
				$cons = implode(', ',$cons);
				$html .= ($cons) ? '<p class="topic-contributors">'.JText::_('WIKI_PAGE_CONTRIBUTIONS_BY') .' '. $cons.'</p>'.n : '';
			}
		}
		return $html;
	}
	
	//-----------

	public function view( $sub, $name, $pagetitle, $page, $revision, $option, $tags, $output, $task, $authorized, $contributors, $q='' ) 
	{
		$params =& new JParameter( $page->params );
		$mode = $params->get( 'mode', 'wiki' );
		//'<a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope).'">'.$name.'</a>: '.
		if ($sub) {
			$hid = 'sub-content-header';
		} else {
			$hid = 'content-header';
		}
		
		$html  = '<div id="'.$hid.'">'.n;
		$html .= WikiHtml::hed( 2, $pagetitle ).n;
		if (!$mode || ($mode && $mode != 'static')) {
			$html .= WikiHtml::authors( $page, $params );
		}
		$html .= '</div><!-- /#content-header -->'.n;
		
		if (!$mode || ($mode && $mode != 'static')) {
			$html .= WikiHtml::subMenu( $sub, $option, $page->pagename, $page->scope, $page->state, $task, $params, $authorized );
			
			$aside = '';
			if ($output['toc']) {
				$aside .= WikiHtml::div( WikiHtml::hed(3,JText::_('WIKI_PAGE_TABLE_OF_CONTENTS')).$output['toc'], 'article-toc' );
			}
			$aside .= WikiHtml::div( WikiHtml::hed(3,JText::_('WIKI_PAGE_TAGS')).WikiHtml::tagcloud( $tags ), 'article-tags' );
			
			$first = $page->getRevision(1);
			$output['text'] .= '<p class="timestamp">'.JText::_('WIKI_PAGE_CREATED').' '.JHTML::_('date',$first->created, '%d %b %Y').', '.JText::_('WIKI_PAGE_LAST_MODIFIED').' '.JHTML::_('date',$revision->created, '%d %b %Y').'</p>';

			$c2  = WikiHtml::div( $aside, 'aside' );
			$c2 .= WikiHtml::subject( $output['text'] );
			
			$html .= WikiHtml::div( $c2, 'main section' );
		} else {
			$html .= $output['text'];
		}
		
		$html .= WikiHtml::div( '', 'clear' );
		return $html;
	}
	
	//-----------
	
	public function doesNotExist( $pagetitle, $pagename, $scope, $option ) 
	{
		$html  = WikiHtml::div( WikiHtml::hed(2,$pagetitle), 'full', 'content-header' );
		$html .= '<div class="main section">'.n;
		$html .= WikiHtml::warning('This page does not exist. Would you like to <a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$scope.a.'pagename='.$pagename.a.'task=new').'">create it?</a>');
		$html .= WikiHtml::div( '', 'clear' );
		$html .= '</div><!-- / .main section -->'.n;
		return $html;
	}
	
	//-----------
	
	public function delete( $sub, $name, $authorized, $pagetitle, $page, $option, $task ) 
	{
		$xparams =& new JParameter( $page->params );
		
		$html  = WikiHtml::div( WikiHtml::hed(2,$pagetitle), 'full', 'content-header' );
		if ($page->id) {
			$html .= WikiHtml::subMenu( $sub, $option, $page->pagename, $page->scope, $page->state, $task, $xparams, $authorized );
		}
		if ($page->state == 1 && $authorized !== 'admin') {
			$html .= WikiHtml::div( WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH_EDITOR') ), 'main section' );
			return $html;
		}
		
		$html .= '<form action="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope).'" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('WIKI_DELETE_PAGE_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= WikiHtml::hed(3,JText::_('WIKI_DELETE_PAGE'));
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'<input class="option" type="checkbox" name="confirm" value="1" />'.n;
		$html .= t.t.t.JText::_('WIKI_FIELD_CONFIRM_DELETE').' <strong>'.JText::_('WIKI_FIELD_CONFIRM_DELETE_HINT').'</strong>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<input type="hidden" name="pagename" value="'. $page->pagename .'" />'.n;
		$html .= t.t.'<input type="hidden" name="scope" value="'. $page->scope .'" />'.n;
		$html .= t.t.'<input type="hidden" name="pageid" value="'. $page->id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="delete" />'.n;

		if ($sub) {
			$html .= t.t.'<input type="hidden" name="active" value="'.$sub.'" />'.n;
		}

		$html .= t.'</fieldset>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function renamepage( $sub, $name, $authorized, $pagetitle, $page, $option, $task ) 
	{
		$xparams =& new JParameter( $page->params );
		
		$html  = WikiHtml::div( WikiHtml::hed(2,$pagetitle), 'full', 'content-header' );
		if ($page->id) {
			$html .= WikiHtml::subMenu( $sub, $option, $page->pagename, $page->scope, $page->state, $task, $xparams, $authorized );
		}
		if ($page->state == 1 && $authorized !== 'admin') {
			$html .= WikiHtml::div( WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH_EDITOR') ), 'main section' );
			return $html;
		}
		
		$html .= '<form action="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope).'" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('WIKI_PAGENAME_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= WikiHtml::hed(3,JText::_('WIKI_CHANGE_PAGENAME'));
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('WIKI_FIELD_PAGENAME').':'.n;
		$html .= t.t.t.'<input type="text" name="newpagename" value="'. $page->pagename .'" size="38" />'.n;
		$html .= t.t.t.'<span>'.JText::_('WIKI_FIELD_PAGENAME_HINT').'</span>'.n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<input type="hidden" name="oldpagename" value="'. $page->pagename .'" />'.n;
		$html .= t.t.'<input type="hidden" name="scope" value="'. $page->scope .'" />'.n;
		$html .= t.t.'<input type="hidden" name="pageid" value="'. $page->id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="saverename" />'.n;

		if ($sub) {
			$html .= t.t.'<input type="hidden" name="active" value="'.$sub.'" />'.n;
		}

		$html .= t.'</fieldset>'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function edit( $sub, $name, $authorized, $pagetitle, $page, $revision, $authors, $option, $tags, $task, $params, $preview=NULL ) 
	{
		$xparams =& new JParameter( $page->params );
		
		$html  = '<div id="content-header">'.n;
		$html .= WikiHtml::hed( 2, $pagetitle );
		if ($page->id) {
			$html .= WikiHtml::authors( $page, $xparams );
		}
		$html .= '</div><!-- /#content-header -->'.n;
		
		if ($page->id) {
			$html .= WikiHtml::subMenu( $sub, $option, $page->pagename, $page->scope, $page->state, $task, $xparams, $authorized );
		}
		if ($page->id && !$authorized) {
			if ($params->get( 'allow_changes' ) == 1) {
				$html .= '<p> </p>'.WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH_EDITOR_SUGGESTED') );
			} else {
				$html .= '<p> </p>'.WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH_EDITOR') );
				return $html;
			}
		}
		if ($page->state == 1 && $authorized !== 'admin') {
			$html .= WikiHtml::div( WikiHtml::warning( JText::_('WIKI_WARNING_NOT_AUTH_EDITOR') ), 'main section' );
			return $html;
		}
		
		if ($preview) {
			$html .= '<div id="preview">'.n;
			$html .= t.'<div class="main section">'.n;
			$html .= t.t.'<div class="aside">'.n;
			$html .= t.t.t.'<p>This a preview only. Changes will not take affect until saved.</p>'.n;
			$html .= t.t.'</div><!-- / .aside -->'.n;
			$html .= t.t.'<div class="subject">'.n;
			$html .= $preview->pagehtml;
			$html .= t.t.'</div><!-- / .subject -->'.n;
			$html .= t.'</div><!-- / .section -->'.n;
			$html .= '</div><div class="clear"></div>'.n;
		}
		
		$html .= '<form action="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope).'" method="post" id="hubForm">'.n;
		if ($page->id) {
			$lid = $page->id;
		} else {
			$num = time().rand(0,10000);
			$lid = JRequest::getInt( 'lid', $num, 'post' );
		}
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>To change the page name (the portion used for URLs), go <a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=renamepage').'">here</a>.</p>'.n;
		$html .= t.t.'<p><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename=Help:WikiMacros#image').'">[[Image(filename.jpg)]]</a> to include an image.</p>'.n;
		$html .= t.t.'<p><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename=Help:WikiMacros#file').'">[[File(filename.pdf)]]</a> to include a file.</p>'.n;
		if ($sub) {
			//$src = 'index.php?option='.$option.a.'no_html=1'.a.'active=wiki'.a.'task=media'.a.'listdir='. $lid;
			//$src = JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=media'.a.'listdir='.$lid.a.'no_html=1');
			$src = 'index.php?option=com_topics'.a.'no_html=1'.a.'task=media'.a.'listdir='. $lid;
		} else {
			$src = 'index.php?option='.$option.a.'no_html=1'.a.'task=media'.a.'listdir='. $lid;
		}
		$html .= t.t.'<iframe width="100%" height="370" name="filer" id="filer" style="border:2px solid #eee;margin-top: 0;" src="'.$src.'"></iframe>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		//$html .= WikiHtml::hed(3,'Edit page');
		if ($authorized) {
			$html .= t.t.'<label>'.n;
			$html .= t.t.t.JText::_('WIKI_FIELD_TITLE').':'.n;
			if (!$page->pagename) {
				$html .= ' <span class="required">'.JText::_('WIKI_REQUIRED').'</span>';
			}
			$html .= t.t.t.'<input type="text" name="title" value="'. $page->title .'" size="38" />'.n;
			$html .= t.t.'</label>'.n;
		} else {
			$html .= t.t.t.'<input type="hidden" name="title" value="'. $page->title .'" />'.n;
		}
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('WIKI_FIELD_PAGETEXT').': <span class="required">'.JText::_('WIKI_REQUIRED').'</span>'.n;
		$html .= t.t.t.'<ul id="wiki-toolbar" class="hidden"></ul>'.n;
		$html .= t.t.t.'<textarea name="pagetext" id="pagetext" rows="40" cols="35">'. $revision->pagetext .'</textarea>'.n;
		$html .= t.t.'</label>'.n;
		$html .= t.t.'<p class="ta-right hint">See <a class="popup" href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename=Help:WikiFormatting').'">Help: Wiki Formatting</a> for help on editing content.</p>'.n;
		$mode = $params->get( 'mode', 'wiki' );
		if ($authorized) {
			$cls = '';
			if ($mode && $mode != 'knol') {
				$cls = ' class="hide"';
			}

			if (!$page->id) {
				$html .= t.t.'<label>'.n;
				$html .= t.t.t.JText::_('WIKI_FIELD_MODE').': <span class="required">'.JText::_('WIKI_REQUIRED').'</span>'.n;
				$html .= t.t.t.'<select name="params[mode]" id="params_mode" ';
				$html .= '>'.n;
				$html .= t.t.t.t.'<option value="knol"';
				if ($mode == 'knol') {
					$html .= ' selected="selected"';
				}
				$html .= '>Knowledge article with specific authors</option>'.n;
				$html .= t.t.t.t.'<option value="wiki"';
				if ($mode == 'wiki') {
					$html .= ' selected="selected"';
				}
				$html .= '>Wiki page anyone can edit</option>'.n;
				$html .= t.t.t.'</select>'.n;
				$html .= t.t.'</label>'.n;
			} else {
				$html .= '<input type="hidden" name="params[mode]" value="'.$mode.'" />'.n;
			}
			
			$html .= t.t.'<label'.$cls.'>'.n;
			$html .= t.t.t.JText::_('WIKI_FIELD_AUTHORS').':'.n;
			$html .= t.t.t.'<input type="text" name="authors" id="params_authors" value="'.$authors.'" />'.n;
			$html .= t.t.'</label>'.n;
			
			$html .= t.t.'<label'.$cls.'>'.n;
			$html .= t.t.t.'<input class="option" type="checkbox" name="params[allow_changes]" id="params_allow_changes" ';
			if ($params->get( 'allow_changes' ) == 1) {
				$html .= ' checked="checked"';
			}
			$html .= 'value="1" />'.n;
			$html .= t.t.t.JText::_('WIKI_FIELD_ALLOW_CHANGES').n;
			$html .= t.t.'</label>'.n;
			
			$html .= t.t.'<label'.$cls.'>'.n;
			$html .= t.t.t.'<input class="option" type="checkbox" name="params[allow_comments]" id="params_allow_comments" ';
			if ($params->get( 'allow_comments' ) == 1) {
				$html .= ' checked="checked"';
			}
			$html .= 'value="1" />'.n;
			$html .= t.t.t.JText::_('WIKI_FIELD_ALLOW_COMMENTS').n;
			$html .= t.t.'</label>'.n;
			
			/*if ($mode && $mode != 'knol') {
				$html .= t.t.'<label>'.n;
				$html .= t.t.t.'Restrict access to group:'.n;
				$html .= t.t.t.'<input type="text" name="group" id="group" value="'.$page->group.'" />'.n;
				$html .= t.t.'</label>'.n;
				$html .= t.t.'<label>'.n;
				$html .= t.t.t.'<input class="option" type="checkbox" name="access" id="access" ';
				if ($page->access == 1) {
					$html .= ' checked="checked"';
				}
				$html .= 'value="1" />'.n;
				$html .= t.t.t.'Private page (only group members can view)'.n;
				$html .= t.t.'</label>'.n;
			} else {
				$html .= '<input type="hidden" name="access" value="'.$page->access.'" />'.n;
				$html .= '<input type="hidden" name="group" value="'.$page->group.'" />'.n;
			}*/
		} else {
			$html .= '<input type="hidden" name="params[mode]" value="'.$mode.'" />'.n;
			$html .= '<input type="hidden" name="params[allow_changes]" value="';
			if ($params->get( 'allow_changes' ) == 1) {
				$html .= '1';
			} else {
				$html .= '0';
			}
			$html .= '" />'.n;
			$html .= '<input type="hidden" name="params[allow_comments]" value="';
			if ($params->get( 'allow_comments' ) == 1) {
				$html .= '1';
			} else {
				$html .= '0';
			}
			$html .= '" />'.n;
			$html .= '<input type="hidden" name="authors" id="params_authors" value="'.$authors.'" />'.n;
			$html .= '<input type="hidden" name="access" value="'.$page->access.'" />'.n;
			//$html .= '<input type="hidden" name="group" value="'.$page->group.'" />'.n;
		}
		//if ($sub && $page->group) {
			$html .= '<input type="hidden" name="group" value="'.$page->group.'" />'.n;
		/*} else {
			$html .= t.t.'<label>'.n;
			$html .= t.t.t.JText::_('WIKI_FIELD_GROUP').':'.n;
			$html .= t.t.t.'<input type="text" name="group" id="group" value="'.$page->group.'" />'.n;
			$html .= t.t.'</label>'.n;*/
		//}
		if ($sub && $page->group) {
			$html .= t.t.'<label>'.n;
			$html .= t.t.t.'<input class="option" type="checkbox" name="access" id="access" ';
			if ($page->access == 1) {
				$html .= ' checked="checked"';
			}
			$html .= 'value="1" />'.n;
			$html .= t.t.t.JText::_('WIKI_FIELD_ACCESS').n;
			$html .= t.t.'</label>'.n;
		} else if ($authorized === 'admin') {
			$html .= t.t.'<label>'.n;
			$html .= t.t.t.'<input class="option" type="checkbox" name="state" id="state" ';
			if ($page->state == 1) {
				$html .= ' checked="checked"';
			}
			$html .= 'value="1" />'.n;
			$html .= t.t.t.JText::_('WIKI_FIELD_STATE').n;
			$html .= t.t.'</label>'.n;
		}
		$html .= t.'</fieldset>'.n;
		$html .= t.'<div class="clear"></div>'.n;

		if ($authorized) {
			$html .= t.'<div class="explaination">'.n;
			$html .= t.t.'<p>'.JText::_('WIKI_FIELD_TAGS_EXPLANATION').'</p>'.n;
			$html .= t.'</div>'.n;
			$html .= t.'<fieldset>'.n;
			//$html .= WikiHtml::hed(3,'Edit tags');
			$html .= t.t.'<label>'.n;
			$html .= t.t.t.JText::_('WIKI_FIELD_TAGS').':'.n;

			JPluginHelper::importPlugin( 'tageditor' );
			$dispatcher =& JDispatcher::getInstance();
			$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$tags,'')) );
			if (count($tf) > 0) {
				$html .= $tf[0];
			} else {
				$html .= t.t.t.'<input type="text" name="tags" value="'. $tags .'" size="38" />'.n;
			}
			
			$html .= t.t.t.'<span class="hint">'.JText::_('WIKI_FIELD_TAGS_HINT').'</span>'.n;
			$html .= t.t.'</label>'.n;
			$html .= t.'</fieldset>'.n;
			$html .= t.'<div class="clear"></div>'.n;
		} else {
			$html .= t.t.t.'<input type="hidden" name="tags" value="'. $tags .'" />'.n;
		}
	
		$html .= t.t.'<fieldset>'.n;
		//$html .= WikiHtml::hed(3,'Edit summary');
		$html .= t.t.t.'<label>'.n;
		$html .= t.t.t.t.JText::_('WIKI_FIELD_EDIT_SUMMARY').':'.n;
		$html .= t.t.t.t.'<input type="text" name="summary" value="'.$revision->summary.'" size="38" />'.n;
		$html .= t.t.t.t.'<span class="hint">'.JText::_('WIKI_FIELD_EDIT_SUMMARY_HINT').'</span>'.n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.t.'<input type="hidden" name="minor_edit" value="1" />'.n;
		$html .= t.t.'</fieldset>'.n;
		
		$html .= t.t.'<input type="hidden" name="lid" value="'. $lid .'" />'.n;
		$html .= t.t.'<input type="hidden" name="pagename" value="'. $page->pagename .'" />'.n;
		$html .= t.t.'<input type="hidden" name="scope" value="'. $page->scope .'" />'.n;
		$html .= t.t.'<input type="hidden" name="pageid" value="'. $page->id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="version" value="'. $revision->version .'" />'.n;
		$html .= t.t.'<input type="hidden" name="created_by" value="'. $revision->created_by .'" />'.n;
		$html .= t.t.'<input type="hidden" name="created" value="'. $revision->created .'" />'.n;
		$html .= t.t.'<input type="hidden" name="id" value="'. $revision->id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="save" />'.n;

		if ($sub) {
			$html .= t.t.'<input type="hidden" name="active" value="'.$sub.'" />'.n;
		}

		$html .= t.'</fieldset>'.n;
		$html .= t.'<p class="submit"><input type="submit" name="preview" value="'.JText::_('PREVIEW').'" /> &nbsp; <input type="submit" name="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		
		return $html;
	}
	
	//-----------
	
	public function comments( $sub, $name, $pagetitle, $page, $comments, $option, $task, $mycomment, $authorized, $versions, $v )
	{	
		$params =& new JParameter( $page->params );
		
		if ($comments) {
			$html = WikiHtml::commentList( $comments, $page, $option, '', 1 );
		} else {
			if ($v) {
				$html = '<p>No comments found for this version.</p>'.n;
			} else {
				$html = '<p>No comments found. Be the first to add a comment!</p>'.n;
			}
		}
		
		$frm  = '<form action="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename).'" method="get">'.n;
		$frm .= t.'<fieldset class="controls">'.n;
		$frm .= t.t.'<label>'.n;
		$frm .= t.t.t.JText::_('WIKI_COMMENT_REVISION').':'.n;
		$frm .= t.t.t.'<select name="version">'.n;
		$frm .= t.t.t.t.'<option value="">'.JText::_('ALL').'</option>'.n;
		if (count($versions) > 1) {
			foreach ($versions as $ver) 
			{
				$frm .= t.t.t.t.'<option value="'.$ver->version.'"';
				$frm .= ($v == $ver->version) ? ' selected="selected"' : '';
				$frm .= '>Version '.$ver->version.'</option>'.n;
			}
		}
		$frm .= t.t.t.'</select>'.n;
		$frm .= t.t.'</label>'.n;
		//$frm .= t.t.'<input type="hidden" name="pagename" value="'. $page->pagename .'" />'.n;
		//$frm .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$frm .= t.t.'<input type="hidden" name="task" value="comments" />'.n;
		$frm .= t.t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		
		if ($sub) {
			$html .= t.t.'<input type="hidden" name="active" value="'.$sub.'" />'.n;
		}
		
		$frm .= t.'</fieldset>'.n;
		$frm .= '</form>'.n;
		
		$o  = WikiHtml::hed( 3, JText::_('COMMENTS') ).n;
		$o .= WikiHtml::aside( $frm );
		$o .= WikiHtml::subject( $html );
		
		$s  = WikiHtml::aside( '<p><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=addcomment#commentform').'" class="add">'.JText::_('WIKI_ADD_COMMENT').'</a></p>' );
		$s .= WikiHtml::subject( '<p>'.JText::_('WIKI_COMMENTS_EXPLANATION').'</p>' );
		
		$out  = '<div id="content-header">'.n;
		$out .= WikiHtml::hed( 2, $pagetitle );
		$out .= WikiHtml::authors( $page, $params );
		$out .= '</div><!-- /#content-header -->'.n;
		$out .= WikiHtml::subMenu( $sub, $option, $page->pagename, $page->scope, $page->state, $task, $params, $authorized );
		// intro
		$out .= WikiHtml::div( $s, 'section' );
		$out .= WikiHtml::div( '', 'clear' );
		// comments;
		$out .= WikiHtml::div( $o, 'main section' );
		$out .= WikiHtml::div( '', 'clear' );
		// comment form
		if (is_object($mycomment)) {
			$f  = WikiHtml::aside(
					'<p>Please use <a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename=Help:WikiFormatting').'">Wiki syntax</a> for formatting. Raw <abbr title="HyperText Markup Language">HTML</abbr> is frowned upon.</p>'.
					'<p>Please keep comments polite and on topic. We reserve the right to remove any offensive material or spam.</p>'
				);
			$f .= WikiHtml::subject( WikiHtml::commentForm( $sub, $mycomment, $option, $page->pagename, $page->scope ) );
		
			$out .= WikiHtml::div( $f, 'section' );
			$out .= WikiHtml::div( '', 'clear' );
		}
		
		return $out;
	}

	//-----------
	
	public function commentList( $comments, $page, $option, $c='', $level=1 ) 
	{
		$c = ($c) ? $c : 'odd';
		$i = 1;
		$html = '';
		if (count($comments) > 0) {
			$html .= '<ol class="comments">'.n;
			foreach ($comments as $comment) 
			{
				if ($comment->anonymous) {
					$author = JText::_('WIKI_AUTHOR_ANONYMOUS');;
				} else {
					$author = JText::_('WIKI_AUTHOR_UNKNOWN');
					$cuser =& JUser::getInstance($comment->created_by);
					if (is_object($cuser)) {
						$author = $cuser->get('name');
					}
				}

				$html .= t.'<li class="comment '.$c.'" id="c'.$comment->id.'">'.n;
				$html .= t.t.'<dl class="comment-details">'.n;
				$html .= t.t.t.'<dt class="type">';
				if ($comment->rating) {
					$cls = WikiHtml::getRatingClass($comment->rating);
					$html .= '<span class="avgrating '.$cls.'">&nbsp;<span>'.JText::sprintf('WIKI_COMMENT_RATING',$comment->rating).'</span></span>';
				} else {
					if ($page->created_by == $comment->created_by) {
						$html .= '<span class="authorcomment"><span>author comment</span></span>';
					} else {
						$html .= '<span class="plaincomment"><span>plain comment</span></span>';
					}
				}

				$chtml = stripslashes($comment->chtml);
				$chtml = str_replace("<p><br />\n</p>",'',$chtml);
				$chtml = trim($chtml);

				$html .= '</dt>'.n;
	        	$html .= t.t.t.'<dd class="date">'.JHTML::_('date',$comment->created, '%d %b, %Y').'</dd>'.n;
				$html .= t.t.t.'<dd class="time">'.JHTML::_('date',$comment->created, '%I:%M %p').'</dd>'.n;
	        	$html .= t.t.t.'<dd class="revision">'.JText::_('WIKI_COMMENT_REVISION').' '.$comment->version.'</dd>'.n;
	        	$html .= t.t.'</dl>'.n;
				$html .= t.t.'<div class="cwrap">'.n;
				$html .= t.t.t.'<p class="name"><strong>'.$author.'</strong> '.JText::_('SAID').':</p>'.n;
				$html .= t.t.t;
				$html .= (trim($chtml)) ? trim($chtml).n : JText::_('(No comment.)').n;
				$html .= t.t.t.'<p class="actions">';
				if (!$level < 3) {
					$html .= t.t.t.'<a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=addcomment'.a.'parent='.$comment->id).'">'.JText::sprintf('WIKI_COMMENT_REPLY_TO',$author).'</a>'.n;
				}
				//$html .= t.t.t.' | <a class="abuse" href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=reportcomment'.a.'id='.$comment->id).'">'.JText::_('WIKI_COMMENT_REPORT').'</a>';
				$html .= '</p><p class="actions">&nbsp;</p>'.n;
				$html .= t.t.'</div>'.n;
				if (isset($comment->children)) {
					$html .= WikiHtml::commentList($comment->children,$page,$option,$c,$level++);
				}
				$html .= t.'</li>'.n;

				$i++;
				$c = ($c == 'odd') ? 'even' : 'odd';
			}
			$html .= '</ol>'.n;
		}
		
		return $html;
	}
	
	//-----------

	public function getRatingClass($rating=0)
	{
		switch ($rating) 
		{
			case 0:   $class = ' no-stars';      break;
			case 0.5: $class = ' half-stars';      break;
			case 1:   $class = ' one-stars';       break;
			case 1.5: $class = ' onehalf-stars';   break;
			case 2:   $class = ' two-stars';       break;
			case 2.5: $class = ' twohalf-stars';   break;
			case 3:   $class = ' three-stars';     break;
			case 3.5: $class = ' threehalf-stars'; break;
			case 4:   $class = ' four-stars';      break;
			case 4.5: $class = ' fourhalf-stars';  break;
			case 5:   $class = ' five-stars';      break;
			default:  $class = ' no-stars';      break;
		}
		return $class;
	}

	//-----------
	
	public function commentForm( $sub, $comment, $option, $pagename, $scope ) 
	{
		$app =& JFactory::getApplication();
		
		$html  = '<form action="'.JRoute::_('index.php?option='.$option.a.'scope='.$scope).'" method="post" id="hubForm" class="full">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= WikiHtml::hed(3,'<a name="commentform"></a>'.JText::_('WIKI_ADD_COMMENT'));
		$html .= t.t.'<fieldset>'.n;
		$html .= t.t.t.'<legend>'.JText::_('WIKI_FIELD_RATING').':</legend>'.n;
		$html .= t.t.t.'<label><input class="option" id="review_rating_1" name="rating" type="radio" value="1"';
		if ($comment->rating == 1) { $html .= ' checked="checked"'; } 
		$html .= ' /> <img src="/templates/'. $app->getTemplate() .'/images/stars/1.gif" alt="1 star" /> '.JText::_('WIKI_FIELD_RATING_ONE').'</label>'.n;
		$html .= t.t.t.'<label><input class="option" id="review_rating_2" name="rating" type="radio" value="2"';
		if ($comment->rating == 2) { $html .= ' checked="checked"'; }
		$html .= ' /> <img src="/templates/'. $app->getTemplate() .'/images/stars/2.gif" alt="2 stars" /></label>'.n;
		$html .= t.t.t.'<label><input class="option" id="review_rating_3" name="rating" type="radio" value="3"';
		if ($comment->rating == 3) { $html .= ' checked="checked"'; }
		$html .= ' /> <img src="/templates/'. $app->getTemplate() .'/images/stars/3.gif" alt="3 stars" /></label>'.n;
		$html .= t.t.t.'<label><input class="option" id="review_rating_4" name="rating" type="radio" value="4"';
		if ($comment->rating == 4) { $html .= ' checked="checked"'; }
		$html .= ' /> <img src="/templates/'. $app->getTemplate() .'/images/stars/4.gif" alt="4 stars" /></label>'.n;
		$html .= t.t.t.'<label><input class="option" id="review_rating_5" name="rating" type="radio" value="5"';
		if ($comment->rating == 5) { $html .= ' checked="checked"'; }
		$html .= ' /> <img src="/templates/'. $app->getTemplate() .'/images/stars/5.gif" alt="5 stars" /> '.JText::_('WIKI_FIELD_RATING_FIVE').'</label>'.n;
		$html .= t.t.'</fieldset>'.n;

		$html .= t.t.'<label>'.n;
		$html .= t.t.t.'<input class="option" type="checkbox" name="anonymous" value="1"';
		if ($comment->anonymous != 0) { $html .= ' checked="checked"'; }
		$html .= ' />'.n;
		$html .= t.t.t.JText::_('WIKI_FIELD_ANONYMOUS').n;
		$html .= t.t.'</label>'.n;

		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('WIKI_FIELD_COMMENTS').':'.n;
		$html .= t.t.t.'<textarea name="ctext" rows="10" cols="35">'. $comment->ctext .'</textarea>'.n;
		$html .= t.t.'</label>'.n;

		$html .= t.t.'<input type="hidden" name="created" value="'. $comment->created .'" />'.n;
		$html .= t.t.'<input type="hidden" name="id" value="'. $comment->id .'" />'.n;
		$html .= t.t.'<input type="hidden" name="created_by" value="'. $comment->created_by .'" />'.n;
		$html .= t.t.'<input type="hidden" name="status" value="'. $comment->status .'" />'.n;
		$html .= t.t.'<input type="hidden" name="version" value="'. $comment->version .'" />'.n;
		$html .= t.t.'<input type="hidden" name="parent" value="'. $comment->parent .'" />'.n;
		$html .= t.t.'<input type="hidden" name="pageid" value="'. $comment->pageid .'" />'.n;
		$html .= t.t.'<input type="hidden" name="pagename" value="'. $pagename .'" />'.n;
		$html .= t.t.'<input type="hidden" name="scope" value="'. $scope .'" />'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="savecomment" />'.n;

		if ($sub) {
			$html .= t.t.'<input type="hidden" name="active" value="'.$sub.'" />'.n;
		}

		$html .= t.t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= '</form>'.n;

		return $html;
	}

	//-----------
	
	public function history( $sub, $name, $pagetitle, $page, $revisions, $option, $task, $authorized )
	{
		$params =& new JParameter( $page->params );
		
		$html  = '<table id="revisionhistory" summary="'.JText::_('WIKI_HISTORY_TBL_SUMMARY').'">'.n;
		$html .= t.'<thead>'.n;
		$html .= t.t.'<tr>'.n;
		$html .= t.t.t.'<th>'.JText::_('WIKI_HISTORY_COL_VERSION').'</th>'.n;
		$html .= t.t.t.'<th colspan="2">'.JText::_('WIKI_HISTORY_COL_COMPARE').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('WIKI_HISTORY_COL_WHEN').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('WIKI_HISTORY_COL_MADE_BY').'</th>'.n;
		$html .= t.t.t.'<th>'.JText::_('WIKI_HISTORY_COL_STATUS').'</th>'.n;
		if ($authorized) {
			$html .= t.t.t.'<th>&nbsp;</th>'.n;
		}
		$html .= t.t.'</tr>'.n;
		$html .= t.'</thead>'.n;
		$html .= t.'<tbody>'.n;
		$i = 0;
		$cur = 0;
		$cls = 'even';
		foreach ($revisions as $revision) 
		{
			$i++;
			$cls = ($cls == 'odd') ? 'even' : 'odd';
			$level = ($revision->minor_edit) ? 'minor' : 'major';
			
			switch ($revision->approved) 
			{
				case 1: $status = 'approved'; break;
				case 0:
				default: 
					$status = 'suggested';
					if ($authorized) {
						$status .= '<br /><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=approve'.a.'oldid='.$revision->id).'">'.JText::_('WIKI_ACTION_APPROVED').'</a>';
					}
					break;
			}
			
			$html .= t.t.'<tr class="'.$cls.'">'.n;
			$html .= t.t.t.'<td>';
			if ($i == 1) {
				$lastedit = $revision->created;
				$html .= '(cur)';
				$cur = $revision->version;
			} else {
				$html .= '(<a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=compare'.a.'oldid='.$revision->version.a.'diff='.$cur).'">'.JText::_('WIKI_HISTORY_CURRENT').'</a>)';
			}
			$html .= '&nbsp;';
			if ($revision->version != 1) {
				$html .= '(<a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=compare'.a.'oldid='.($revision->version - 1).a.'diff='.$revision->version).'">'.JText::_('WIKI_HISTORY_LAST').'</a>)';
			} else {
				$html .= '(last)';
			}
			$html .= '</td>'.n;
			if ($i == 1) {
				$html .= t.t.t.'<td>&nbsp;</td>'.n;
				$html .= t.t.t.'<td><input type="radio" name="diff" value="'.$revision->version.'" checked="checked" /></td>'.n;
			} else {
				$html .= t.t.t.'<td><input type="radio" name="oldid" value="'.$revision->version.'"';
				if ($i == 2) {
					$html .= ' checked="checked"';
				}
				$html .= ' /></td>'.n;
				$html .= t.t.t.'<td>&nbsp;</td>'.n;		
			}
			
			$xname = JText::_('WIKI_AUTHOR_UNKNOWN');
			$juser =& JUser::getInstance( $revision->created_by );
			if (is_object($juser)) {
				$xname = $juser->get('name');
			}
			
			if ($revision->summary) {
				$summary = WikiHtml::encode_html($revision->summary);
			} else {
				$summary = JText::_('WIKI_REVISION_NO_SUMMARY');
			}
			
			$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'version='.$revision->version).'" class="tooltips" title="'.JText::_('WIKI_REVISION_SUMMARY').' :: '.$summary.'">'.$revision->created.'</a></td>'.n;
			$html .= t.t.t.'<td>'.$xname.'</td>'.n;
			$html .= t.t.t.'<td>'.$status.'</td>'.n;
			if ($authorized) {
				$html .= t.t.t.'<td><a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=deleterevision'.a.'oldid='.$revision->id).'" title="'.JText::_('WIKI_REVISION_DELETE').'"><img src="/components/'.$option.'/images/icons/trash.gif" alt="'.JText::_('DELETE').'" /></a></td>'.n;
			}
			$html .= t.t.'</tr>'.n;
		}
		$html .= t.'</tbody>'.n;
		$html .= '</table>'.n;
		
		$f  = '<form action="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope).'" method="post">'.n;
		$f .= WikiHtml::aside( '<p><input type="submit" value="'.JText::_('WIKI_HISTORY_COMPARE').'" /></p>' );
		$f .= WikiHtml::subject( $html );
		$f .= t.t.'<input type="hidden" name="pagename" value="'. $page->pagename .'" />'.n;
		$f .= t.t.'<input type="hidden" name="scope" value="'. $page->scope .'" />'.n;
		$f .= t.t.'<input type="hidden" name="pageid" value="'. $page->id .'" />'.n;
		$f .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		
		if ($sub) {
			$f .= t.t.'<input type="hidden" name="active" value="'.$sub.'" />'.n;
		}
		
		$f .= t.t.'<input type="hidden" name="task" value="compare" />'.n;
		$f .= '</form>'.n;
		
		$s  = WikiHtml::aside( '<p>This article has '.count($revisions).' versions, was created on '.$revision->created.' and last edited on '.$lastedit.'.</p>' );
		$s .= WikiHtml::subject( 
					'<p>Versions are listed in reverse-chronological order (newest to oldest). For any version listed below, click on its date to view it. For more help, see <a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename=Help:PageHistory').'">Help:Page history</a>.</p>'. 
					'<p>(cur) = difference from current version<br />(last) = difference from preceding version</p>'
				);
		//'<a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope).'">'.$name.'</a>: '.
		$out  = '<div id="content-header">'.n;
		$out .= WikiHtml::hed( 2, $pagetitle );
		$out .= WikiHtml::authors( $page, $params );
		$out .= '</div><!-- /#content-header -->'.n;
		$out .= WikiHtml::subMenu( $sub, $option, $page->pagename, $page->scope, $page->state, $task, $params, $authorized );
		$out .= WikiHtml::div( $s, 'section' );
		$out .= WikiHtml::div( '', 'clear' );
		$out .= WikiHtml::div( $f, 'main section' );
		$out .= WikiHtml::div( '', 'clear' );
		
		return $out;
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

	//-----------

	public function compare( $sub, $name, $pagetitle, $page, $content, $option, $task, $or, $dr, $authorized ) 
	{
		$params =& new JParameter( $page->params );
		
		$html  = '<div id="content-header">'.n;
		//$html .= WikiHtml::hed(2,'<a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope).'">'.$name.'</a>: '.$pagetitle);
		$html .= WikiHtml::hed(2,$pagetitle);
		$html .= '</div><!-- /#content-header -->'.n;
		$html .= WikiHtml::subMenu( $sub, $option, $page->pagename, $page->scope, $page->state, $task, $params, $authorized );
		
		/*if ($params->get( 'show_history' ) || $authorized) {
			$b = '<a href="'.JRoute::_('index.php?option='.$option.a.'scope='.$page->scope.a.'pagename='.$page->pagename.a.'task=history').'">'.JText::_('WIKI_BACK_TO_HISTORY').'</a>';
		} else {
			$b = '&nbsp;';
		}*/
		
		$orauthor = JText::_('Unknown');
		$oruser =& JUser::getInstance($or->created_by);
		if (is_object($oruser)) {
			$orauthor = $oruser->get('name');
		}
		
		$drauthor = JText::_('Unknown');
		$druser =& JUser::getInstance($dr->created_by);
		if (is_object($druser)) {
			$drauthor = $druser->get('name');
		}
		
		$a  = '<dl class="diff-versions">'.n;
		$a .= t.'<dt>'.JText::_('WIKI_VERSION').' '.$or->version.'<dt>'.n;
		$a .= t.'<dd>'.$or->created.'<dd>'.n;
		$a .= t.'<dd>'.$orauthor.'<dd>'.n;
		$a .= t.'<dt>'.JText::_('WIKI_VERSION').' '.$dr->version.'<dt>'.n;
		$a .= t.'<dd>'.$dr->created.'<dd>'.n;
		$a .= t.'<dd>'.$drauthor.'<dd>'.n;
		$a .= '</dl>'.n;
		
		$c1  = WikiHtml::aside( $a );
		$c1 .= WikiHtml::subject( 
					//'<p>Comparing Version '.$or->version.' to Version '.$dr->version.'.</p>' .
					'<p class="diff-deletedline"><del class="diffchange">Deletions</del> or items before changed</p>' .
					'<p class="diff-addedline"><ins class="diffchange">Additions</ins> or items after changed</p>'
				);
		
		$html .= WikiHtml::div( $c1, 'section' );
		$html .= WikiHtml::div( '', 'clear' );
		
		//$c2  = WikiHtml::aside( $a );
		//$c2 .= WikiHtml::subject( $content );
		
		$html .= WikiHtml::div( $content, 'main section' );
		$html .= WikiHtml::div( '', 'clear' );
		return $html;
	}
	
	//-------------------------------------------------------------
	// Media manager functions
	//-------------------------------------------------------------
	
	public function attachTop( $option, $name ) 
	{
		$app =& JFactory::getApplication();
		
		$html  = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'.n;
		$html .= '<html xmlns="http://www.w3.org/1999/xhtml">'.n;
		$html .= '<head>'.n;
		$html .= t.'<title>'.JText::_('WIKI_ATTACHMENTS').'</title>'.n;
		$html .= t.'<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />'.n;
		$html .= t.'<link rel="stylesheet" type="text/css" media="screen" href="/templates/'. $app->getTemplate() .'/css/main.css" />'.n;
		if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.$name.'.css')) {
			$html .= t.'<link rel="stylesheet" href="'.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.$name.'.css" type="text/css" />'.n;
		} else {
			$html .= t.'<link rel="stylesheet" href="'.DS.'components'.DS.$option.DS.$name.'.css" type="text/css" />'.n;
		}
		$html .= '</head>'.n;
		$html .= '<body id="attachments">'.n;
		return $html;
	}
	
	//-----------
	
	public function media( &$config, $listdir, $option, $name ) 
	{

		$html  = WikiHtml::attachTop( $option, $name );
		$html .= '<form action="/index.php" id="adminForm" method="post" enctype="multipart/form-data">'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<div id="themanager" class="manager">'.n;
		$html .= t.t.t.'<iframe style="border:1px solid #eee;margin-top: 0;" src="/index.php?option='. $option .a.'no_html=1'.a.'task=list'.a.'listdir='. $listdir .'" name="imgManager" id="imgManager" width="98%" height="180"></iframe>'.n;
		$html .= t.t.'</div>'.n;
		$html .= t.'</fieldset>'.n;
			
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<table>'.n;
		$html .= t.t.' <tbody>'.n;
		$html .= t.t.'  <tr>'.n;
		$html .= t.t.'   <td><input type="file" name="upload" id="upload" /></td>'.n;
		$html .= t.t.'  </tr>'.n;
		$html .= t.t.'  <tr>'.n;
		$html .= t.t.'   <td><input type="submit" value="'.JText::_('UPLOAD').'" /></td>'.n;
		$html .= t.t.'  </tr>'.n;
		$html .= t.t.' </tbody>'.n;
		$html .= t.t.'</table>'.n;

		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="listdir" id="listdir" value="'. $listdir .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="upload" />'.n;
		$html .= t.t.'<input type="hidden" name="no_html" value="1" />'.n;
		$html .= t.'</fieldset>'.n;
		$html .= '</form>'.n;
		$html .= WikiHtml::attachBottom();
		return $html;
	}
	
	//-----------
	
	public function attachBottom() 
	{
		$html  = '</body>'.n;
		$html .= '</html>'.n;
		return $html;
	}

	//-----------

	public function dir_name($dir)
	{
		$lastSlash = intval(strrpos($dir, DS));
		if ($lastSlash == strlen($dir)-1) {
			return substr($dir, 0, $lastSlash);
		} else {
			return dirname($dir);
		}
	}

	//-----------
	
	public function draw_no_results()
	{
		return '<p>'.JText::_('NO_FILES_FOUND').'</p>'.n;
	}

	//-----------

	public function draw_no_dir( $dir ) 
	{
		return WikiHtml::error('Configuration Problem: &quot;'. $dir .'&quot; does not exist.').n;
	}

	//-----------

	public function draw_table_header() 
	{
		$html  = t.t.'<form action="index2.php" method="post" id="filelist">'.n;
		$html .= t.t.'<table>'.n;
		return $html;
	}

	//-----------

	public function draw_table_footer() 
	{
		$html  = t.t.'</table>'.n;
		$html .= t.t.'</form>'.n;
		return $html;
	}

	//-----------

	public function show_dir( $path, $dir, $listdir, $option) 
	{
		$num_files = WikiHtml::num_files( JPATH_ROOT.$path );

		if ($listdir == '/') {
			$listdir = '';
		}
		
		$html  = ' <tr>'.n;
		$html .= '  <td><img src="/components/'. $option .'/images/icons/folder.gif" alt="'. $dir .'" width="16" height="16" /></td>'.n;
		$html .= '  <td width="100%" style="padding-left: 0;">'. $dir .'</td>'.n;
	    $html .= '  <td><a href="index2.php?option='. $option .'&amp;task=deletefolder&amp;delFolder='. $path .'&amp;listdir='. $listdir .'" target="filer" onclick="return deleteFolder(\''. $dir .'\', '. $num_files .');" title="Delete this folder"><img src="/components/'. $option .'/images/icons/trash.gif" width="15" height="15" alt="Delete" /></a></td>'.n;
		$html .= ' </tr>'.n;
		
		return $html;
	}

	//-----------

	public function show_doc($option, $doc, $listdir, $icon) 
	{
		$html  = ' <tr>'.n;
		$html .= '  <td><img src="'. $icon .'" alt="'. $doc .'" width="16" height="16" /></td>'.n;
		$html .= '  <td width="100%" style="padding-left: 0;">'. $doc .'</td>'.n;
		$html .= '  <td><a href="index2.php?option='. $option .'&amp;task=deletefile&amp;delFile='. $doc .'&amp;listdir='. $listdir .'" target="filer" onclick="return deleteImage(\''. $doc .'\');" title="Delete this document"><img src="/components/'. $option .'/images/icons/trash.gif" width="15" height="15" alt="Delete" /></a></td>'.n;
		$html .= ' </tr>'.n;
		
		return $html;
	}

	//-----------

	public function parse_size($size)
	{
		if ($size < 1024) {
			return $size.' bytes';
		} else if ($size >= 1024 && $size < 1024*1024) {
			return sprintf('%01.2f',$size/1024.0).' Kb';
		} else {
			return sprintf('%01.2f',$size/(1024.0*1024)).' Mb';
		}
	}

	//-----------

	public function num_files($dir)
	{
		$total = 0;

		if (is_dir($dir)) {
			$d = @dir($dir);

			while (false !== ($entry = $d->read()))
			{
				if (substr($entry,0,1) != '.') {
					$total++;
				}
			}
			$d->close();
		}
		return $total;
	}
	
	//-----------
	
	public function imageStyle($listdir)
	{
		?>
		<script type="text/javascript">
		function updateDir()
		{
			var allPaths = window.top.document.forms[0].dirPath.options;
			for (i=0; i<allPaths.length; i++)
			{
				allPaths.item(i).selected = false;
				if ((allPaths.item(i).value)== '<?php if (strlen($listdir)>0) { echo $listdir ;} else { echo '/';}  ?>') {
					allPaths.item(i).selected = true;
				}
			}
		}

		function deleteImage(file)
		{
			if (confirm("Delete file \""+file+"\"?")) {
				return true;
			}

			return false;
		}
		
		function deleteFolder(folder, numFiles)
		{
			if (numFiles > 0) {
				alert('There are '+numFiles+' files/folders in "'+folder+'".\n\nPlease delete all files/folder in "'+folder+'" first.');
				return false;
			}
	
			if (confirm('Delete folder "'+folder+'"?')) {
				return true;
			}
	
			return false;
		}
		</script>
		<?php
	}
}
?>
