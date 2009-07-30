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

class AnswersHtml 
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

	public function info( $msg, $tag='p' )
	{
		return '<'.$tag.' class="info">'.$msg.'</'.$tag.'>'.n;
	}

	//-----------

	public function passed( $msg, $tag='p' )
	{
		return '<'.$tag.' class="passed">'.$msg.'</'.$tag.'>'.n;
	}
	
	//-----------
	
	public function alert( $msg )
	{
		return '<script type="text/javascript"> alert(\''.$msg.'\'); window.history.go(-1); </script>'.n;
	}
	
	//-----------

	public function tableRow($h,$c='', $e, $class='')
	{
		$html  = t.'  <tr';
		if($class) {
		$html .= t.'  class="'.$class.'"';
		}
		$html .= '>'.n;
		$html .= t.'   <th>'.$h.'</th>'.n;
		$html .= t.'   <td>';
		//$html .= ($c) ? $c : '&nbsp;';
		$html .= $c;
		$html .= '</td>'.n;
		$html .= t.'   <td>';
		$html .= ($e) ? $e : '&nbsp;';
		$html .= '</td>'.n;
		$html .= t.'  </tr>'.n;
		
		return $html;
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
		return ResourcesHtml::div($txt, 'aside', $id);
	}
	
	//-----------
	
	public function subject($txt, $id='')
	{
		return ResourcesHtml::div($txt, 'subject', $id);
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

	public function searchform($filters, $option, $task='search', $banking)
	{
		$task = (isset($filters['mine']) && $filters['mine']!='0') ? 'myquestions' : 'search';
		$html  = '';
		$html .= '<form method="get" action="'.JRoute::_('index.php?option='.$option).'" id="adminForm" >'.n;
		$html .= '<div class="filters">'.n;		
		$html .= ' <fieldset>'.n;
		$html .= t.'<label>'.JText::_('Find phrase').': '.n;
		$html .= t.'<input type="text" name="q" value="'.$filters['q'].'" /></label> '.n;
		$html .= t.t.'<label class="tagdisplay">'.JText::_('and/or tag').': '.n;

		JPluginHelper::importPlugin( 'tageditor' );
		$dispatcher =& JDispatcher::getInstance();	
		$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$filters['tag'],'')) );
		
		if (count($tf) > 0) {
			$html .= $tf[0];
		} else {
			$html .= t.t.t.'<input type="text" name="tag" id="tags-men" value="'.$filters['tag'].'" />'.n;
		}
		$html .= '</label>';
		$html .= t.'<label>'.JText::_('in').': '.n;
		$html .= t.'<select name="filterby">'.n;
		$html .= t.' <option value="all"';
		$html .= ($filters['filterby'] == 'all') ? ' selected="selected"' : '';
		$html .= '>'.JText::_('ALL_QUESTIONS').'</option>'.n;
		$html .= t.' <option value="open"';
		$html .= ($filters['filterby'] == 'open') ? ' selected="selected"' : '';
		$html .= '>'.JText::_('OPEN_QUESTIONS').'</option>'.n;
		$html .= t.' <option value="closed"';
		$html .= ($filters['filterby'] == 'closed') ? ' selected="selected"' : '';
		$html .= '>'.JText::_('CLOSED_QUESTIONS').'</option>'.n;
		if($task != 'myquestions') {
		$html .= t.' <option value="mine"';
		$html .= ($filters['filterby'] == 'mine') ? ' selected="selected"' : '';
		$html .= '>'.JText::_('MY_QUESTIONS').'</option>'.n;
		}
		$html .= t.'</select></label>'.n;
		
		$html .= t.'<label>'.JText::_('SORTBY').': '.n;
		$html .= t.'<select name="sortby">'.n;
		$html .= t.' <option value="rewards"';
		$html .= ($filters['sortby'] == 'rewards') ? ' selected="selected"' : '';
		if($banking) {
		$html .= '>'.JText::_('REWARDS').'</option>'.n;
		}
		else {
		$html .= '>'.JText::_('MOST_RECENT').'</option>'.n;
		}
		$html .= t.' <option value="votes"';
		$html .= ($filters['sortby'] == 'votes') ? ' selected="selected"' : '';
		$html .= '>'.JText::_('RECOMMENDATIONS').'</option>'.n;
		$html .= t.' <option value="status"';
		$html .= ($filters['sortby'] == 'status') ? ' selected="selected"' : '';
		$html .= '>'.JText::_('Open/Closed').'</option>'.n;
		$html .= t.' <option value="responses"';
		$html .= ($filters['sortby'] == 'responses') ? ' selected="selected"' : '';
		$html .= '>'.JText::_('Number of Responses').'</option>'.n;
		$html .= t.' <option value="date"';
		$html .= ($filters['sortby'] == 'date') ? ' selected="selected"' : '';
		$html .= '>'.JText::_('Date').'</option>'.n;
		$html .= t.'</select></label>'.n;
		
		$html .= isset($filters['interest']) ? '<input type="hidden" name="interest" value="'.$filters['interest'].'" />' : '';
		$html .= isset($filters['interest']) ? '<input type="hidden" name="assigned" value="'.$filters['assigned'].'" />' : '';
		
		$html .= t.'<input type="hidden" name="task" value="'.$task.'" />'.n;
		//$html .= t.'<input type="hidden" name="limitstart" value="0" />'.n;
		$html .= t.'<input type="submit" value="'.JText::_('GO').'" />'.n;
		$html .= ' </fieldset>'.n;
		//$html .= '</form>'.n;
		$html .= '</div>'.n;
		return $html;
	}

	//-----------

	public function introduction($title, $results, $pageNav, $option, $filters, $infolink, $banking)
	{
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$juser 	  =& JFactory::getUser();
		
		$html = AnswersHtml::div( AnswersHtml::hed( 2, $title ), '', 'content-header' );
			
		$html .= '<div id="content-header-extra">'.n;
		$html .= ' <ul id="useroptions">'.n;
		//if(!$juser->get('guest')) {
		$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=myquestions').'" class="myquestions"><span>'.JText::_('MY_QUESTIONS').'</span></a></li>';
		//}
		$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=new').'" class="add"><span>'.JText::_('NEW_QUESTION').'</span></a></li>';
		$html .= ' </ul>'.n;
		$html .= '</div>'.n;
		$html .= '<div class="clear"></div>'.n;
		$html .= '<div class="main section">'.n;
		$html .= t.'<div class="aside">'.n;
		$html .= t.t.'<p>'.JText::_('CANT_FIND_ANSWER').' <a href="kb/">'.JText::_('KNOWLEDGE_BASE').'</a> '.JText::_('OR_BY').' '.JText::_('SEARCH').'? '.JText::_('ASK_YOUR_FELLOW').' '.$hubShortName.' '.JText::_('MEMBERS').'!</p>'.n;
		if($banking) {
		$html .= t.t.'<p>'.JText::_('START_EARNING').' '.$hubShortName.' '.JText::_('COMMUNITY').'. <a href="'.$infolink.'">'.JText::_('EARN_MORE').'</a>.</p>'.n;
		}		
		$html .= t.'</div><!-- / .aside -->'.n;
		$html .= t.'<div class="subject">'.n;
		$html .= AnswersHtml::div( AnswersHtml::hed( 3, JText::_('LATEST_QUESTIONS') ), '', 'content-header' );
		$html .= AnswersHtml::searchform($filters, $option, '', $banking);
		$html .= AnswersHtml::htmlQuestions( $results, $option, $infolink, $banking );
		$html .= $pageNav->getListFooter();
		$html .= '</form>'.n;
		$html .= '</div><div class="clear"></div></div>'.n;	
		
		return $html;
	}

	//-----------
	
	public function search($title, $results, $pageNav, $option, $filters, $infolink, $banking, $task)
	{
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$juser 	  =& JFactory::getUser();
		
		$html = AnswersHtml::div( AnswersHtml::hed( 2, $title ), '', 'content-header' );
		$html .= '<div id="content-header-extra">'.n;
		$html .= ' <ul id="useroptions">'.n;
		if($task!='myquestions') {
		$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=myquestions').'" class="myquestions"><span>'.JText::_('MY_QUESTIONS').'</span></a></li>';
		}
		$html .= t.'<li class="last"><a href="'.JRoute::_('index.php?option='.$option.a.'task=new').'" class="add"><span>'.JText::_('NEW_QUESTION').'</span></a></li>';
		$html .= ' </ul>'.n;
		$html .= '</div>'.n;
		
		if(!$juser->get('guest') && $task=='myquestions') {
			$html .= t.'<ul class="breadcrumbtrail">'.n;
			$html .= t.'<li class="first"><span "class="myquestions"></span></li>'.n;
			$html .= t.'<li class="first">';
			$html .= ($filters['interest'] == 0 && $filters['assigned'] == 0) ? '<strong>' : '<a href="'.JRoute::_('index.php?option='.$option.'&task=myquestions').'">';
			$html .= JText::_('Questions I asked');
			$html .= ($filters['interest'] == 0 && $filters['assigned'] == 0) ? '</strong>' : '</a>';
			$html .= '</li> '.n;
			$html .= t.'<li>';
			$html .= ($filters['assigned'] == 1) ? '<strong>' : '<a href="'.JRoute::_('index.php?option='.$option.'&task=myquestions').'?assigned=1">';
			$html .= JText::_('Questions related to my contributions');
			$html .= ($filters['assigned'] == 1) ? '</strong>' : '</a>';
			$html .= '</li> '.n;
			$html .= t.'<li>';
			$html .= ($filters['interest'] == 1) ? '<strong>' : '<a href="'.JRoute::_('index.php?option='.$option.'&task=myquestions').'?interest=1">';
			$html .= JText::_('Questions tagged with my interests');
			$html .= ($filters['interest'] == 1) ? '</strong>' : '</a>';
			$html .= '</li>'.n;
			$html .= t.'</ul>'.n;		
		}
		
		// Display question list
		$html .= '<div class="main section">'.n;
		$html .= AnswersHtml::searchform($filters, $option, '', $banking);	
		$html .= t.'<div class="aside">'.n;
		if(!$juser->get('guest') && $task=='myquestions') {
			$html .= ' <p class="info">';
			$html .= ($filters['interest'] == 0 && $filters['assigned'] == 0) ? JText::_('Please do not forget to close the questions you asked by selecting the best answer.') : '';
			if($filters['interest'] == 1)  {
			$html .= JText::_('This selection of questions is based on tags of interest as indicated in your').' <a href="'.JRoute::_('index.php?option=com_members&task=edit&id='.$juser->get('id')).'">User Profile</a>.';
				if(!$filters['tag']) { $intext= JText::_('NA'); }
				else {
				$intext = $this->formatTags($filters['tag']);
				}
			}
			$html .= ($filters['assigned'] == 1) ? JText::_('These questions were tagged with names of resources that you have <a href="/members/'.$juser->get('id').'/contributions">contributed</a>.')  : '';
			$html .= ' <p>'.n;
		}
		else {
			$html .= t.t.'<p class="info_ask">'.JText::_('CANT_FIND_ANSWER').' <a href="kb/">'.JText::_('KNOWLEDGE_BASE').'</a> '.JText::_('OR_BY').' '.JText::_('SEARCH').'? '.JText::_('ASK_YOUR_FELLOW').' '.$hubShortName.' '.JText::_('MEMBERS').'!';
			if($banking) {
			$html .= '<br /><br />'.JText::_('START_EARNING').' '.$hubShortName.' '.JText::_('COMMUNITY').'. <a href="'.$infolink.'">'.JText::_('LEARN_MORE').'</a>';
			}	
			$html .= '</p>'.n;
		}	
		$html .= t.'</div><!-- / .aside -->'.n;
		
		$html .= t.'<div class="subject">'.n;
		
		if (count($results) > 0) {
			$html .= AnswersHtml::htmlQuestions( $results, $option, $infolink, $banking );
			$html .= $pageNav->getListFooter();
		} else {
			if($filters['q']) {
			$html .= AnswersHtml::warning( JText::_('NO_RESULTS_FOR_TERM').' '.$filters['q'] ).n;
			}
			else {
			$html .= AnswersHtml::warning( JText::_('NO_RESULTS')).n;
			}
		}
	
		$html .= t.'</div><!-- / .subject -->'.n;
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}


	//-----------
	
	public function question(&$juser, &$question, &$responses, $id, $option, &$tags, $responding, $reward, $voted, $note, $infolink, $banking, $title, $addcomment, $showcomments=1)
	{
		if ($question->anonymous == 0) {			
			$wuser =& XUser::getInstance( $question->created_by );
			if (is_object($wuser)) {
				$name = $wuser->get('name');
			} else {
				$name = JText::_('UNKNOWN'); 
			}	
		}
		
		$reports = (isset($question->reports)) ? $question->reports: '0';
		$votes = ($question->helpful) ? $question->helpful: '0';
		
		$sef = JRoute::_('index.php?option='.$option.a.'task=question'.a.'id='.$question->id);
		
		$html = AnswersHtml::div( AnswersHtml::hed( 2, $title ), '', 'content-header' );
			
		$html .= '<div id="content-header-extra">'.n;
		$html .= ' <ul id="useroptions">'.n;
		
		//if(!$juser->get('guest')) {
		$html .= t.'<li class="last"><a href="'.JRoute::_('index.php?option='.$option.a.'task=myquestions').'" class="myquestions"><span>'.JText::_('MY_QUESTIONS').'</span></a></li>';
		//}
		$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=search').'"><span>'.JText::_('ALL_QUESTIONS').'</span></a></li>'.n;
		//$html .= t.'<li class="last"><a href="'.JRoute::_('index.php?option='.$option.a.'task=new').'" class="add"><span>'.JText::_('NEW_QUESTION').'</span></a></li>';
		$html .= ' </ul>'.n;
		$html .= '</div>'.n;
		$html .= '<div class="main section">'.n;				
		if ($question->state == 0 && $id!=0) {
			$html .= AnswersHtml::title(3,'Open Question','firstheader');
		} 
		else if($question->state == 2 or $id==0) {
			$html .= AnswersHtml::title(3, JText::_('ERROR_QUESTION_NOT_FOUND'),'firstheader');		
			if($note['msg']!='') {
			$html .= '<p class="help">'.urldecode($note['msg']).'</p>'.n; }
			else {
			$html .= '<p class="error">'.JText::_('NOTICE_QUESTION_REMOVED').'.</p>';}
			$html .= '</div><div class="clear"></div>'.n;
			return $html;
		}
		else {
			$html .= AnswersHtml::title(3, JText::_('CLOSED_QUESTION'),'firstheader');
		}
		
		$html .= t.'<div class="aside">'.n;
		if ($question->state == 0 && $responding!=1 && $reports == 0) {
			$html .= '<p id="primary-document" ><a href="index.php?option='.$option.a.'task=answer'.a.'id='.$question->id.'">'.JText::_('ANSWER_THIS').'</a></p>'.n;
		}
		$html.= '<div class="status_display">';
		if ($question->state == 0 && $reports == 0) {
			$html .= '<p class="intro">'.JText::_('STATUS').': <span class="open">'.JText::_('STATUS_ACCEPTING_ANSWERS').'</span> </p>';
		} else if ($reports > 0) {
			$html .= '<p class="intro">'.JText::_('STATUS').': <span class="underreview">'.JText::_('STATUS_UNDER_REVIEW').'</span></p>';
		} else {
			$html .= '<p class="intro">'.JText::_('STATUS').': <span class="closed">'.JText::_('STATUS_CLOSED').'</span></p>';
		}
		
		if( $reward > 0 && $question->state == 0 && $banking) {
			$html .= ' <p class="intro">'.JText::_('BONUS').': <span class="pointvalue"><a href="'.$infolink.'" title="'.JText::_('WHAT_ARE_POINTS').'">&nbsp;</a>'.$reward.' '.JText::_('POINTS').'</span></p>';
		}
		if(isset($question->maxaward) && $question->state == 0 && $banking) {
			$html .= ' <p class="youcanearn">';
			/*
			if($reward > 0 ) {
			$add = intval($question->maxaward - $reward);
			}
			*/
			$html .= JText::_('In total, earn up to ').$question->maxaward.' '.JText::_('points for the best answer to this question!').' <a href="'.JRoute::_('index.php?option='.$option.'&task=math&id='.$question->id).'">'.JText::_('Details...').'</a>';
			/*
			else {
			$html .= JText::_('With ').$question->marketvalue.JText::_(' activity points this question generated, ').JText::_('best answer may now win as much as ').intval($question->maxaward).' '.JText::_('POINTS');
			}*/
			$html .= '</p>'.n;
		}
		
		$html.= '</div>';		
		$html .= t.'</div><!-- / .aside -->'.n;
		$question->created = AnswersController::mkt($question->created);
		$when = AnswersController::timeAgo($question->created);
		$html .= '<div class="subject">'.n;
		$html .= '<div id="questionwrap" >'.n;
		$html .= ' <div id="question">'.n;
		$html .= '  <div style="position:relative;overflow:visible;">'.n;
		
		if ($reports > 0) {
			// abuse report received
			$html .= AnswersHtml::title(4, JText::_('NOTICE_QUESTION_REPORTED'),'');
			$html .= t.'<p class="details">'.JText::_('ASKED_BY').' ';
			$html .= ($question->anonymous != 0) ? JText::_('ANONYMOUS') : $name;
			$html .= ' - '.$when.' ago';
			$html .= '</p>'.n;
		} 
		else {
		
			$html .= AnswersHtml::title(4,$question->subject,'');
			if ($question->question) {
				$html .= t.'<p>'. stripslashes($question->question) .'</p>'.n;
			}
			$html .= t.'<p class="details">'.JText::_('ASKED_BY').' ';
			$html .= ($question->anonymous != 0) ? JText::_('ANONYMOUS') : $name;
			$html .= ' - '.$when.' '.JText::_('AGO').' - <a href="'.$sef.'#answers" title="'.JText::_('READ_RESPONSES').'">'.count($responses).' ';
			$html .= (count($responses) == 1) ? JText::_('RESPONSE') : JText::_('RESPONSES');
			$html .= '</a></p>'.n;
			if(count($tags) > 0) {
			$html .= t.'<p class="details tagged">'.JText::_('TAGS').':'.n;			
			$html .= AnswersHtml::getTagCloud($tags, $option);
			$html .= '</p>'.n;
			}
			
		}
		$html .= '  </div>'.n;
		$html .= ' </div>'.n;
		
		$html.='<p id="questionstatus">';
		$url = $sef.'?id='.$question->id.a.'vote=1';
		if(!$juser->get('guest')) {
			$addon =' title="'.JText::_('CLICK_TO_RECOMMEND').'"';
			if($voted) {
				$addon =' class="voted" title="'.JText::_('NOTICE_ALREADY_RECOMMENDED').'"';
			}
		}
		else {
			$addon =' title="'.JText::_('LOGIN_TO_RECOMMEND_QUESTION').'"';
		}
		if($reports == 0) {
			$html .= '<span class="question_vote">'.$votes;
			if(!$voted) {
			$html .= '<a href="'.$url.'" '.$addon.'>'.JText::_('GOOD_QUESTION').'</a>';
			}
			else {
			$html .= '<span '.$addon.'>'.JText::_('GOOD_QUESTION').'</span>';
			}
			$html .= '</span>'.n;
			
			$html .= '<span class="abuse"><a href="index.php?option=com_support'.a.'task=reportabuse'.a.'category=question'.a.'id='.$question->id.'" title="'.JText::_('TITLE_REPORT_ABUSE').'">'.JText::_('REPORT_ABUSE').'</a></span>'.n;
			if ($question->created_by == $juser->get('username') && $question->state == 0) {
			$html .= '<span class="deleteq"><a href="index.php?option='.$option.a.'task=delete'.a.'id='.$question->id.'" title="'.JText::_('DELETE_QUESTION').'">'.JText::_('DELETE').'</a></span>'.n;
			}
		}
		$html .= '</p>'.n;	
				
		if($note['msg']!='') {
			switch($note['class']) {
				case 'info':      	$html .= AnswersHtml::info( urldecode($note['msg'])).n;  break;
				case 'passed':      $html .= AnswersHtml::passed( urldecode($note['msg'])).n;  break;
				case 'warning':
				default: 			$html .= AnswersHtml::warning( urldecode($note['msg'])).n;  break;
			}
		}
		
		if ($responding == 1 && $reports == 0) { // answer form
			$html .= '</div><div class="clear"></div></div>'.n;
			$html .= AnswersHtml::title(3, JText::_('YOUR_ANSWER') ,'');
			
			$html .= '<div class="main section">'.n;
			$html .= '<form action="index.php" method="post" id="hubForm">'.n;		
			$html .= t.'<div class="aside">'.n;
			$html .= t.t.'<p>'.JText::_('ANSWER_BE_POLITE').'</p>'.n;
			$html .= t.t.'<p>'.JText::_('ANSWER_NO_HTML').'</p>'.n;
			$html .= t.'</div><!-- / .aside -->'.n;
			$html .= t.'<div class="subject">'.n;
			$html .= t.'<fieldset>'.n;
			$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
			$html .= t.t.'<input type="hidden" name="task" value="savea" />'.n;
			$html .= t.t.'<input type="hidden" name="qid" value="'. $question->id .'" />'.n;
			$html .= t.t.'<label><input class="option" type="checkbox" name="anonymous" value="1" /> '.JText::_('ANSWER_POST_ANON').'</label>'.n;
			$html .= t.t.'<label>'.JText::_('YOUR_RESPONSE').':<br />'.n;
			$html .= t.t.'<textarea name="answer" rows="10" cols="50"></textarea></label>'.n;
			$html .= t.'</fieldset>'.n;
			$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;		
			$html .= t.'</div><!-- / .subject -->'.n;
			$html .= t.'<div class="clear"></div>'.n;
			$html .= '</form>'.n;
			$html .= '</div><!-- / .main section -->'.n;
			$html .= '</div><div class="clear"></div></div>'.n;
			
		} 
	
		else if ($responding == 4 && $question->state == 0 && $reports == 0) { // delete question
			$html .= '</div><div class="clear"></div></div>'.n;
			$html .= '<div class="main section">'.n;
			$html .= t.'<div class="subject">'.n;		
			$html .= t.t.'<p class="error">'.JText::_('NOTICE_CONFIRM_DELETE').'</p>'.n;
			$html .= t.t.'<a  href="index.php?option='.$option.a.'task=delete_q'.a.'qid='.$question->id.'">'.JText::_('YES_DELETE').'</a>';
			$html .= ' | '.n;
			$html .= '<a  href="'.$sef.'">'.JText::_('NO_DELETE').'</a>'.n;
			$html .= t.'</div><!-- / .subject -->'.n;
			$html .= t.'<div class="clear"></div>'.n;
			$html .= '</div><!-- / .main section -->'.n;
			$html .= '</div><div class="clear"></div></div>'.n;
			
		}
			
		else if ($reports == 0) {
		
			$html .= '</div><div class="clear"></div></div>'.n;
			$html .= '<div class="main section">'.n;
			if ($responding == 6 && $question->state == 0 && $reports == 0 && $banking) { // show how points are awarded
			//$html .= '</div><div class="clear"></div></div>'.n;
			
			$html .= t.'<div class="subject">'.n;		
			$html .= t.t.'<h3>'.JText::_('POINTS_BREAKDOWN').'</h3>'.n;
			$html .= t.t.'<p>'.JText::_('The table below shows the current "market value" of the question and estimated amount of points that you can earn, when the question is closed.').'</p>'.n;
			$html .= t.t.'<table>'.n;
			$html .= AnswersHtml::tableRow('', ucfirst(JText::_('POINTS')), JText::_('Details'));
			$html .= AnswersHtml::tableRow(JText::_('ACTIVITY').'*',$question->marketvalue,'');
			$html .= AnswersHtml::tableRow(JText::_('BONUS'),$reward,'');
			$html .= AnswersHtml::tableRow(JText::_('Total market value'),intval($question->marketvalue + $reward), '','total');
			$html .= AnswersHtml::tableRow(JText::_('Asker will earn'),round($question->marketvalue/3), JText::_('1/3 of activity points'));
			$html .= AnswersHtml::tableRow(JText::_('Asker will pay'),$reward, JText::_('Reward for best answer assigned by asker'));
			$html .= AnswersHtml::tableRow(JText::_('Best answer may earn'),(round(($question->marketvalue)/3) + $reward).' &mdash; '.(round(2*(($question->marketvalue)/3)) + $reward), JText::_('Up to 2/3 of activity points plus the bonus'));
			$html .= t.t.'</table>'.n;
			$html .= t.t.'<p>* '.JText::_('Activity points are calculated based on summing up the weighted number of answers, recommendations and answer votes. ').'<a href="'.$infolink.'">'.JText::_('Read further details').'</a>.</p>'.n;			
			$html .= t.'</div><!-- / .subject -->'.n;
			$html .= t.'<div class="clear"></div>'.n;
			$html .= '</div><!-- / .main section -->'.n;
			
			}
			
	
			$html .= t.'<a name="answers"></a>'.n;			
		
			if ($juser->get('username') == $question->created_by && $question->state == 0) {
			$html .= '<div class="aside">'.n;
			$html .= t.'<div class="sidenote">'.n;
			$html .= AnswersHtml::info(JText::_('DO_NOT_FORGET_TO_CLOSE')).n;
			$html .= t.'</div>'.n;
			$html .= '</div >'.n;
			}
	
			$html .= AnswersHtml::title(3, JText::_('ANSWERS').' ('.count($responses).')','');
			$html .= '<div class="subject">'.n;
			
			if ($responses) {
			$html .= AnswersHtml::answers( $responses, $option, $question, $addcomment, $showcomments);
			} else {
				$html .= t.'<p>'.JText::_('NO_ANSWERS_BE_FIRST').' <a href="index.php?option='.$option.a.'task=answer'.a.'id='.$question->id.'">'.JText::_('BE_FIRST_ANSWER_THIS').'</a>.</p>'.n;
				if($banking) {
				$html .= t.'<p class="help"><strong>'.JText::_('DID_YOU_KNOW_ABOUT_POINTS').'</strong><br />'.n;
				$html .= t.'<a href="'.$infolink.'">'.JText::_('LEARN_MORE').'</a> '.JText::_('LEARN_HOW_POINTS_AWARDED').'.</p>'.n;
				}
			}
			$html .= '</div><div class="clear"></div>'.n;
			$html .= '</div><div class="clear"></div></div>'.n;
			
		}
		else if($reports > 0) {
			$html .= '</div><div class="clear"></div></div>'.n;
		}
		
		
		return $html;
	}
	//-----------
	
	public function answers( &$responses, $option, $question, $addcomment, $showcomments)
	{
		$juser=& JFactory::getUser();
		$html = '';
		$o = 'even';
	
			$html .= t.'<ol class="comments">'.n;
			foreach ($responses as $row) 
			{
				// Set the name of the reviewer
					$name = JText::_('ANONYMOUS');
					if ($row->anonymous != 1) {
						$name = JText::_('UNKNOWN');
						$ruser =& XUser::getInstance($row->created_by);
						if (is_object($ruser)) {
							$name = $ruser->get('name');
						}
					}
				$abuse = isset($row->reports) ? $row->reports : '0';
				$o = ($o == 'odd') ? 'even' : 'odd';
				$html .= t.' <li class="comment '.$o.'';
				$html .= ($abuse) ? ' abusive' : '';
				if($question->state == 1 && $row->state == 1) {
				$html .= ' chosen';
				}
				$html .= '">'.n;

				$html .= t.t.'<dl class="comment-details">'.n;
					$html .= t.t.t.'<dt class="type_answer"><span class="';
					if($question->state == 1 && $row->state == 1) {
					$html .= 'accepted';
					}	else {
					$html .= 'regular';
					}	
					$html .='"></span>';		
					$html .='</dt>'.n;
					$html .= t.t.t.'<dd class="date">'.JHTML::_('date',$row->created, '%d %b, %Y').'</dd>'.n;
					$html .= t.t.t.'<dd class="time">'.JHTML::_('date',$row->created, '%I:%M %p').'</dd>'.n;
					$html .= t.t.'</dl>'.n;
					$html .= t.t.'<div class="cwrap">'.n;
					$html .= t.t.t.'<p class="name"><strong>'.$name.'</strong> '.JText::_('SAID').':</p>'.n;				
			
				if(!$abuse) {
					$html .= t.t.t.'<p id="answers_'.$row->id.'" class="'.$option.'">';
					$html .= AnswersHtml::rateitem($row, $juser, $option, $question->id);					
					$html .= t.t.t.'</p>'.n;
					$html .= t.t.t.'<p class="comment-options">'.n;
					if (!$juser->get('guest')) {
					$html .= '<a href="javascript:void(0);" ';
					}
					else {
					$html .= '<a href="'.JRoute::_('index.php?option='.$option.a.'task=reply').'?category=answer'.a.'id='.$question->id.a.'refid='.$row->id.'" ';
					}
					$html .= 'class="showreplyform" id="rep_'.$row->id.'">'.JText::_('REPLY').'</a> ';
					$html .= '<span class="abuse"><a href="index.php?option=com_support'.a.'task=reportabuse'.a.'category=answer'.a.'id='.$row->id.a.'parent='.$question->id.'">'.JText::_('REPORT_ABUSE').'</a></span>';
					if ($juser->get('username') == $question->created_by && $question->state == 0) {
						$html .= ' <span class="accept"><a href="index.php?option='.$option.a.'task=accept'.a.'id='.$question->id.a.'rid='.$row->id.'">'.JText::_('ACCEPT_ANSWER').'</a></span>'.n;
					}
					$html .= t.t.t.'</p>'.n;	
					$html .= AnswersHtml::addcomment($row->id, 0, $juser, $option, $addcomment, $question->id);
					$html .= t.t.'</div>'.n;
					if($showcomments && isset($row->replies)) {					
						$html .= AnswersHtml::comments($row->replies, $row->id, $juser, $question->id, $option, $addcomment).n;					
					}
			
	 			}
				else if($abuse) {
					$html .= t.t.t.'<p class="condensed">'.JText::_('NOTICE_POSTING_REPORTED').'</p>';
				}
	
				$html .= t.' </li>'.n;
			}
			$html .= t.'</ol>'.n;
	
	
		return $html;
	}
	
	
	//-----------

	function comments($replies, $revid, $juser, $id, $option, $addcomment, $abuse=true) 
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
				$html .= AnswersHtml::comment($reply, $juser, $option, $id, $addcomment, 1, $abuse, $o).n;
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
						$html .= AnswersHtml::comment($r, $juser, $option, $id, $addcomment, 2, $abuse, $o).n;
		
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
								$html .= AnswersHtml::comment($rr, $juser, $option, $id, $addcomment, 3, $abuse, $o).n;
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

	function comment($reply, $juser, $option, $id, $addcomment, $level, $abuse, $o='') 
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
		$html .= t.t.t.'<dt class="type"><span class="plaincomment"><span>'.JText::sprintf('COMMENT').'</span></span></dt>'.n;
		$html .= t.t.t.'<dd class="date">'.JHTML::_('date',$reply->added, '%d %b, %Y').'</dd>'.n;
		$html .= t.t.t.'<dd class="time">'.JHTML::_('date',$reply->added, '%I:%M %p').'</dd>'.n;
		$html .= t.t.'</dl>'.n;
		$html .= t.t.'<div class="cwrap">'.n;
		$html .= t.t.t.'<p class="name"><strong>'.$name.'</strong> '.JText::_('SAID').':</p>'.n;

		if ($abuse && $reply->reports > 0) {
			$html .= t.t.t.ResourcesHtml::warning( JText::_('NOTICE_POSTING_REPORTED') ).n;
		} else {
			// Add the comment
			if ($reply->comment) {
				$html .= t.t.t.'<p>'.stripslashes($reply->comment).'</p>'.n;
			} else {
				$html .= t.t.t.'<p>'.JText::_('NO_COMMENT').'</p>'.n;
			}
			
			$html .= t.t.t.'<p class="comment-options">'.n;
			
			// Cannot reply at third level
			if ($level < 3) {
				$html .= t.t.t.t.'<a ';
				if (!$juser->get('guest')) {
					$html .= 'class="showreplyform" href="javascript:void(0);"';
				}
				else {
					$html .= 'href="'.JRoute::_('index.php?option='.$option.a.'task=reply').'?category=answercomment'.a.'id='.$id.a.'refid='.$reply->id.'" ';
				}
				$html .= '" id="rep_'.$reply->id.'">'.JText::_('REPLY').'</a>'.n;
			}
			// Add the "report abuse" link if the abuse component exist
			if ($abuse) {
				$html .= t.t.t.t.'<span class="abuse"><a href="'.JRoute::_('index.php?option=com_support'.a.'task=reportabuse'.a.'category=comment'.a.'id='.$reply->id.a.'parent='.$id).'">'.JText::_('REPORT_ABUSE').'</a></span> '.n;
			}
			$html .= t.t.t.'</p>'.n;
			
			// Add the reply form if needed
			if ($level < 3 && !$juser->get('guest')) {
				$html .= AnswersHtml::addcomment($reply->id, $level, $juser, $option, $addcomment, $id);
			}
		}
		
		$html .= t.t.'</div>'.n;
		
		return $html;
	}
	
	//-----------
	
	function addcomment($refid, $level, $juser, $option, $addcomment, $id) 
	{
		$html = '';
		if (!$juser->get('guest')) {
			$category = ($level==0) ? 'answer': 'answercomment';
			
			$class = ' hide';
			if (is_object($addcomment)) {

				$class = ($addcomment->referenceid == $refid && $addcomment->category==$category) ? '' : ' hide';
			}
			
			$html .= t.t.t.'<div class="addcomment'.$class.'">'.n;
			$html .= t.t.t.t.'<form action="index.php" method="post" id="commentform_'.$refid.'">'.n;
			$html .= t.t.t.t.t.'<fieldset>'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="rid" value="'. $id .'" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="active" value="answers" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="task" value="savereply" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="referenceid" value="'.$refid.'" />'.n;
			$html .= t.t.t.t.t.t.'<input type="hidden" name="category" value="'.$category.'" />'.n;
			$html .= t.t.t.t.t.t.'<label><input class="option" type="checkbox" name="anonymous" value="1" /> '.JText::_('POST_COMMENT_ANONYMOUSLY').'</label>'.n;
			$html .= t.t.t.t.t.t.'<label><textarea name="comment" rows="4" cols="50" class="commentarea">'.JText::_('ENTER_COMMENTS').'</textarea></label>'.n;
			$html .= t.t.t.t.t.'</fieldset>'.n;
			$html .= t.t.t.t.t.'<p><input type="submit" value="'.JText::_('POST_COMMENT').'" /> <a href="javascript:void(0);" class="closeform">'.JText::_('CANCEL').'</a></p>'.n;
			$html .= t.t.t.t.'</form>'.n;
			$html .= t.t.t.'</div>'.n;
		}
		
		return $html;
	}


	//-----------
	
	public function rateitem($item, $juser, $option, $qid) {
			
			$html = n.t.t.t.'<span class="thumbsvote">'.n;
			
			$pclass = (isset($item->vote) && $item->vote=="yes") ? 'yes' : 'zero';
			$nclass = (isset($item->vote) && $item->vote=="no") ? 'no' : 'zero';
			$item->helpful = ($item->helpful > 0) ? '+'.$item->helpful: '&nbsp;&nbsp;'.$item->helpful;
			$item->nothelpful = ($item->nothelpful > 0) ? '-'.$item->nothelpful: '&nbsp;&nbsp;'.$item->nothelpful;
			

				$html .= t.t.t.t.'<span class="'.$pclass.'">'.$item->helpful.'</span>'.n;
					
				if ($juser->get('guest')) {
					$html .= t.t.t.t.'<span class="gooditem r_disabled"><a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=yes" >&nbsp;</a></span>'.n;
					$html .= t.t.t.t.'<span class="'.$nclass.'">'.$item->nothelpful.'</span>'.n;
					$html .= t.t.t.t.'<span class="baditem r_disabled"><a href="index.php?option='.$option.a.'task=rateitem'.a.'refid='.$item->id.a.'vote=no" >&nbsp;</a></span>'.n;	
					$html .= t.t.t.t.'<span class="votinghints"><span>Login to vote</span></span>'.n;				
				}
				else {					
					$html .= t.t.t.t.'<span class="gooditem">'.n;
				if($item->vote && $item->vote=="no" or  $juser->get('username') == $item->created_by) {
					$html .= t.t.t.t.'<span class="dis">&nbsp;</span>'.n;
				}
				else if($item->vote) {
					$html .= t.t.t.t.'<span>&nbsp;</span>'.n;
				}
				else {
					$html .= t.t.t.t.t.'<a href="javascript:void(0);" class="revvote" title="'.JText::_('THIS_HELPFUL').'">&nbsp;</a>'.n;
				}
				$html .= t.t.t.t.'</span>'.n;
				$html .= t.t.t.t.'<span class="'.$nclass.'">'.$item->nothelpful.'</span>'.n;
				$html .= t.t.t.t.'<span class="baditem">'.n;
				if($item->vote && $item->vote=="yes" or $juser->get('username') == $item->created_by) {
					$html .= t.t.t.t.'<span class="dis">&nbsp;</span>'.n;
				}
				else if($item->vote) {
					$html .= t.t.t.'<span>&nbsp;</span>'.n;
				}
				else {
					$html .= t.t.t.t.t.'<a href="javascript:void(0);" class="revvote" title="'.JText::_('THIS_NOT_HELPFUL').'">&nbsp;</a>'.n;
				}
				$html .= t.t.t.t.'</span>'.n;
				$html .= t.t.t.t.'<span class="votinghints"><span></span></span>'.n;
				
				}
						
				$html .= t.t.t.'</span>'.n;
				
				$html .= t.t.t.'<span class="itemtxt">'.stripslashes($item->answer);	
				$html .= '</span>'.n;
				
								
	
			return $html;	
	}
	
	
	//-----------
	
	public function create( $option, $funds, $infolink, $banking, $tag, $title, $html ='' )
	{
		$juser =& JFactory::getUser();
		
		$html .= AnswersHtml::div( AnswersHtml::hed( 2, $title ), '', 'content-header' );
		$html .= '<div id="content-header-extra">'.n;
		$html .= ' <ul id="useroptions">'.n;
		//$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=search').'"><span>'.JText::_('ALL_QUESTIONS').'</span></a></li>'.n;
		//if (!$juser->get('guest')) {
		$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=myquestions').'" class="myquestions"><span>'.JText::_('MY_QUESTIONS').'</span></a></li>';
		$html .= t.'<li class="last"><a href="'.JRoute::_('index.php?option='.$option.a.'task=search').'"><span>'.JText::_('ALL_QUESTIONS').'</span></a></li>'.n;
		//}
		$html .= ' </ul>'.n;
		$html .= '</div>'.n;
		$html .= '<div class="main section">'.n;		
		$html .= '<form action="index.php" method="post" id="hubForm">'.n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p><span class="required">*</span> = '.JText::_('REQUIRED_FIELD').'</p>'.n;
		$html .= t.t.'<p>'.JText::_('ANSWER_BE_POLITE').'</p>'.n;
		$html .= t.t.'<p>'.JText::_('ANSWER_NO_HTML').'</p>'.n;
		if($banking) {
		$html .= t.'<p class="help" style="margin-top: 2em;"><strong>'.JText::_('WHAT_IS_REWARD').'</strong><br />'.n;
		$html .= t.JText::_('EXPLAINED_MARKET_VALUE').' <a href="'.$infolink.'">'.JText::_('LEARN_MORE').'</a> '.JText::_('ABOUT_POINTS').'.</p>'.n;
		}
		$html .= t.'</div><!-- / .explaination -->'.n;
		//$html .= t.'<div class="subject">'.n;	
		$html .= t.'<fieldset>'.n;
		$html .= t.t.'<input type="hidden" name="option" value="'. $option .'" />'.n;
		$html .= t.t.'<input type="hidden" name="task" value="saveq" />'.n;
		$html .= t.t.'<input type="hidden" name="funds" value="'.$funds.'" />'.n;
		$html .= t.t.'<label><input class="option" type="checkbox" name="anonymous" value="1" /> '.JText::_('POST_QUESTION_ANON').'</label>'.n;
		$html .= t.t.'<label>'.JText::_('TAGS').': <span class="required">*</span><br />'.n;

		JPluginHelper::importPlugin( 'tageditor' );
		$dispatcher =& JDispatcher::getInstance();	
		$tf = $dispatcher->trigger( 'onTagsEdit', array(array('tags','actags','',$tag,'')) );
		
		if (count($tf) > 0) {
			$html .= $tf[0];
		} else {
			$html .= t.t.t.'<textarea name="tags" id="tags-men" rows="6" cols="35">'. $tag .'</textarea>'.n;
		}
		$html .= '</label>';
		
		//$html .= t.t.'<span>To link a question to a tool, add "tool:" to its name. For example, tool:padre</span>'.n;
		$html .= t.t.'<label>'.JText::_('ASK_ONE_LINER').': <span class="required">*</span><br />'.n;
		$html .= t.t.'<input type="text" name="subject" value="" /></label>'.n;
		$html .= t.t.'<label>'.JText::_('ASK_DETAILS').':<br />'.n;
		$html .= t.t.'<textarea name="question" rows="10" cols="50"></textarea></label>'.n;
		if ($banking) {
			$html .= t.t.'<label>'.JText::_('ASSIGN_REWARD').':<br />'.n;
			$html .= t.t.'<input type="text" name="reward" value="" size="5" ';
			if ($funds <= 0) {
				$html .= 'disabled style="background:#e2e2e2;" ';		
			}
			$html .= '/> '.JText::_('YOU_HAVE').' <strong>'.$funds.'</strong> '.JText::_('POINTS_TO_SPEND').'.</label>'.n;
		} else {
			$html .= t.t.'<input type="hidden" name="reward" value="0">';
		}
		//$html .= t.t.'<label><input class="option" type="checkbox" name="email" value="1" checked="checked" /> '.JText::_('NOTIFY_ME').'</label>'.n;
		$html .= t.'<input class="option" type="hidden" name="email" value="1" checked="checked" />'.n;
		$html .= t.'</fieldset>'.n;
		$html .= t.'<p class="submit"><input type="submit" value="'.JText::_('SUBMIT').'" /></p>'.n;
		$html .= '</form>'.n;
		$html .= '</div><div class="clear"></div>'.n;
		
		return $html;
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

	public function getTagCloud(&$tags, $option)
	{		
		if (count($tags) > 0) {
			$tagarray = array();
			$tagarray[] = '<ol class="tags">';
			if (!empty($tags))
			foreach ($tags as $tag)
			{
				$tag['raw_tag'] = str_replace( '&amp;', '&', $tag['raw_tag'] );
				$tag['raw_tag'] = str_replace( '&', '&amp;', $tag['raw_tag'] );
				$tagarray[] = ' <li><a href="'.JRoute::_('index.php?option=com_tags'.a.'tag='.$tag['tag']).'" rel="tag">'.$tag['raw_tag'].'</a></li>';
			}
			$tagarray[] = '</ol>';

			$alltags = implode( "\n", $tagarray );
		} else {
			$alltags = '&nbsp;';
		}
		return $alltags;
	}

	//-----------

	public function htmlQuestions(&$rows, $option, $infolink, $banking)
	{
					
		if ($rows) {
			//$html  ='<div class="clear"></div>';
			$html  = t.'<ul class="questions plugin">'.n;
	
				$i=1;
				foreach ($rows as $row) 
				{
				
				$row->reports = (isset($row->reports)) ? $row->reports : 0;	
				$row->created = $this->mkt($row->created);
				$row->when = $this->timeAgo($row->created);
				$row->points = $row->points ? $row->points : 0;
				
				if(!$row->reports) {
					$i++;	
					$link_on = JRoute::_('index.php?option=com_answers'.a.'task=question'.a.'id='.$row->id);
					$tags    = AnswersHtml::getTagCloud($row->tags, $option);
					$alt_r  = ($row->rcount == 0) ? 'No' : $row->rcount;
					$alt_r .= ' '.JText::_('RESPONSE');
					$alt_r .= ($row->rcount == 1) ? '' : 's';
					$alt_r .= ' '.JText::_('TO_THIS_QUESTION');
					
					$alt_v  = ($row->helpful == 0) ? 'No' : $row->helpful;
					$alt_v .= ' '.JText::_('RECOMMENDATION');
					$alt_v .= ($row->helpful == 1) ? '' : 's';
					$alt_v .= ' '.JText::_('AS_A_GOOD_QUESTION');
					
					// author name
					$name = JText::_('ANONYMOUS');
					if ($row->anonymous == 0) {
						$xuser =& XUser::getInstance( $row->created_by );
						if (is_object($xuser)) {
							$name = $xuser->get('name');
						} else {
							$name = JText::_('Unknown');
						}
					}
			
					
					$html .= t.' <li class="reg';
					$html .= (isset($row->reward) && $row->reward == 1 && $banking) ? ' hasreward' : '';
					$html .= ($row->state == 1) ? ' answered' : '';
					$html .= '"';
					//$html .= ($row->rcount > 0) ? ' hasanswers"' : '"';
					$html .= '>'.n;
					/*
					if ($row->state == 1) {
						$html .= t.t.'<p class="acceptanswer"><span>'.JText::_('ANSWERED').'</span></p>'.n;
					}
					else if (isset($row->reward) && $row->reward == 1 && $banking) {
						$html .= t.t.'<p class="rewardset">+ '.$row->points.' <a href="'.$infolink.'" title="There is a '.$row->points.' point reward for answering this question.">&nbsp;</a></p>'.n;
					}*/
					$html .= t.t.'<div class="ensemble_left">';
					if ($row->question != '') {
						$row->question = stripslashes($row->question);
						$fulltext = htmlspecialchars(AnswersHtml::cleanText($row->question));
					}
					else {
					 	$fulltext = stripslashes($row->subject);
					}
					$html .= t.t.'<h4><a href="'. $link_on .'" title="'.$fulltext.'">'.stripslashes($row->subject).'</a></h4>'.n;
					
					$html .= t.t.'<p class="supplemental">'.JText::_('ASKED_BY').' '.$name;
					$html .= ' - '.$row->when.' ago';
					$html .= '</p>'.n;
					/*
					if($tags) {
					$html .= t.t.'<p>Tags:</p> '.$tags.n;
					}
					*/
					
					$html .= t.t.'</div>';
					$html .= t.t.'<div class="ensemble_right">';
					$html .= t.t.'<div class="statusupdate">'.n;
					
					$html .= t.t.'<p>'.$row->rcount.'<span class="responses_';
					$html .= ($row->rcount == 0) ? 'no' : 'yes';
					$html .= '"><a href="'.$link_on.'#answers" title="'.$alt_r.'">&nbsp;</a></span>';
					$html .= '  '.$row->helpful.' <span class="votes_';
					$html .= ($row->helpful == 0) ? 'no' : 'yes';
					$html .= '"><a href="'.$link_on.'?vote=1" title="'.$alt_v.'">&nbsp;</a></span>';
					$html .= t.t.'</p>';
					//$html .= ($row->state==1) ? '<span class="update_answered">'.JText::_('ANSWERED').'</span>' : '<span class="update_unanswered"><a href="">'.JText::_('Answer this').'</a></span>';
					$html .= ($row->state==1) ? '<span class="update_answered">'.JText::_('ANSWERED').'</span>' : '';
					$html .= t.t.'</div>';
					$html .= t.t.'<div class="rewardarea">'.n;
					if (isset($row->reward) && $row->reward == 1 && $banking) {
						$html .= t.t.'<p>+ '.$row->points.' <a href="'.$infolink.'" title="There is a '.$row->points.' point reward for answering this question.">&nbsp;</a></p>'.n;
					}
					$html .= t.t.'</div>';
					$html .= t.t.'</div>';
					$html .= t.t.'<div style="clear:left"></div>';
					$html .= t.'&nbsp; </li>'.n;
				  }
				  else if($row->reports) {
				  // do not display
					$html .= t.' <li class="reg under_review"> ';
					$html .= t.'<h4 class="review">'.JText::_('QUESTION_UNDER_REVIEW').'</h4>'.n;
					$html .= t.t.'<p class="supplemental">'.JText::_('ASKED_BY').' '.$name;
					$html .= ' - '.$row->when.' ago';
					$html .= '</p>'.n;
					$html .= t.'&nbsp; </li>'.n;
				  }
				
				}
				
				$html .= t.t.'</ul>'.n;
		} else {
			$html  = t.'<p>'.JText::_('NO_QUESTIONS_FOUND').'</p>'.n;
		}
		
		return $html;
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
	
}
?>