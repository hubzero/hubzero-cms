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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$dateFormat = '%d %b %Y';
$timeFormat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$timeFormat = 'H:i p';
	$tz = true;
}

$this->c = ($this->c) ? $this->c : 'odd';
$i = 1;
$html = '';
if (count($this->comments) > 0) {
	ximport('Hubzero_User_Profile_Helper');
	ximport('Hubzero_User_Profile');

	$wikiconfig = array(
		'option'   => $this->option,
		'scope'    => $this->page->scope,
		'pagename' => $this->page->pagename,
		'pageid'   => $this->page->id,
		'filepath' => '',
		'domain'   => $this->page->group_cn
	);
	ximport('Hubzero_Wiki_Parser');
	$parser =& Hubzero_Wiki_Parser::getInstance();

	$html .= '<ol class="comments">'."\n";
	foreach ($this->comments as $comment)
	{
		$author = JText::_('COM_WIKI_AUTHOR_ANONYMOUS');
		$cuser = Hubzero_User_Profile::getInstance($comment->created_by);

		if ($comment->anonymous != 1) {
			$author = JText::_('COM_WIKI_AUTHOR_UNKNOWN');
			//$cuser =& JUser::getInstance($comment->created_by);
			if (is_object($cuser)) {
				$author = $cuser->get('name');
			}
		}

		$html .= "\t".'<li class="comment '.$this->c.'" id="c'.$comment->id.'">'."\n";
		$html .= "\t\t".'<p class="comment-member-photo">'."\n";
		$html .= "\t\t".'	<a name="c'.$comment->id.'"></a>'."\n";
		$html .= "\t\t".'	<img src="'.Hubzero_User_Profile_Helper::getMemberPhoto($cuser, $comment->anonymous).'" alt="" />'."\n";
		$html .= "\t\t".'</p><!-- / .comment-member-photo -->'."\n";
		$html .= "\t\t".'<div class="comment-content">'."\n";
		if ($comment->rating) {
			switch ($comment->rating)
			{
				case 0:   $cls = ' no-stars';        break;
				case 0.5: $cls = ' half-stars';      break;
				case 1:   $cls = ' one-stars';       break;
				case 1.5: $cls = ' onehalf-stars';   break;
				case 2:   $cls = ' two-stars';       break;
				case 2.5: $cls = ' twohalf-stars';   break;
				case 3:   $cls = ' three-stars';     break;
				case 3.5: $cls = ' threehalf-stars'; break;
				case 4:   $cls = ' four-stars';      break;
				case 4.5: $cls = ' fourhalf-stars';  break;
				case 5:   $cls = ' five-stars';      break;
				default:  $cls = ' no-stars';        break;
			}
			$html .= "\t\t\t".'<p><span class="avgrating'.$cls.'"><span>'.JText::sprintf('WIKI_COMMENT_RATING',$comment->rating).'</span></span></p>'."\n";
		}
		$html .= "\t\t".'	<p class="comment-title">'."\n";
		$html .= "\t\t".'		<strong>'. $author.'</strong> '."\n";
		$html .= "\t\t".'		<a class="permalink" href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&' . ($this->sub ? 'action' : 'task') . '=comments#c'.$comment->id).'" title="'. JText::_('Permalink').'">';
		$html .= '<span class="comment-date-at">@</span> <span class="time">'. JHTML::_('date', $comment->created, $timeFormat, $tz).'</span> <span class="comment-date-on">on</span> <span class="date">'.JHTML::_('date',$comment->created, $dateFormat, $tz).'</span>';
		if ($this->level == 1) {
			$html .= ' to revision '.$comment->version;
		}
		$html .= '</a>'."\n";
		$html .= "\t\t".'	</p><!-- / .comment-title -->'."\n";

		if ($comment->ctext) {
			$html .= $parser->parse(stripslashes($comment->ctext), $wikiconfig, false);
		} else {
			$html .= "\t\t\t".'<p class="comment-none">'.JText::_('No comment.').'</p>'."\n";
		}
		$html .= "\t\t\t".'<p class="comment-options">'."\n";

			if ($this->config->get('access-comment-delete'))
			{
				$html .= "\t\t\t\t".'<a class="delete" href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&' . ($this->sub ? 'action' : 'task') . '=removecomment&id='.$comment->id).'" title="'.JText::_('Delete this comment').'">'.JText::_('Delete').'</a>'."\n";
			}
		if ($this->level < 3) {
			$html .= "\t\t\t\t".'<a class="reply" href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&' . ($this->sub ? 'action' : 'task') . '=addcomment&parent='.$comment->id).'" title="'.JText::sprintf('WIKI_COMMENT_REPLY_TO',$author).'">'.JText::_('Reply').'</a>'."\n";
			
		}
		//$html .= t.t.t.' | <a class="abuse" href="'.JRoute::_('index.php?option='.$this->option.'&scope='.$this->page->scope.'&pagename='.$this->page->pagename.'&' . ($this->sub ? 'action' : 'task') . '=reportcomment&id='.$comment->id).'">'.JText::_('COM_WIKI_COMMENT_REPORT').'</a>';
		//$html .= '</p><p class="actions">&nbsp;</p>'.n;
		$html .= "\t\t\t".'</p>'."\n";
		$html .= "\t\t".'</div><!-- .comment-content -->'."\n";
		if (isset($comment->children)) {
			//$html .= WikiHtml::commentList($comment->children,$this->page,$this->option,$c,$level++);
			$view = new JView( array('name'=>'comments','layout'=>'list','base_path'=>JPATH_ROOT.DS.'components'.DS.'com_wiki') );
			$view->option   = $this->option;
			$view->page     = $this->page;
			$view->comments = $comment->children;
			$view->c        = $this->c;
			$view->level    = ($this->level+1);
			$view->config   = $this->config;
			$html .= $view->loadTemplate();
		}
		$html .= "\t".'</li>'."\n";

		$i++;
		$this->c = ($this->c == 'odd') ? 'even' : 'odd';
	}
	$html .= '</ol>'."\n";
}

echo $html;

