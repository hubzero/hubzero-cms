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

if (!defined("n")) {
	define('n',"\n");
	define('t',"\t");
	define('r',"\r");
	define("br","<br />");
	define("sp","&#160;");
	define("a","&amp;");
}

class FeedbackHtml 
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
	
	//-------------------------------------------------------------
	// Form components
	//-------------------------------------------------------------
	
	public function formStart($name, $action) 
	{
		return '<form id="'.$name.'" method="post" action="'.$action.'" enctype="multipart/form-data">'.n;
	}
	
	//-----------

	public function formEnd() 
	{
		return '</form>'.n;
	}

	//-----------

	public function formInput($type, $name, $value, $size='', $id='', $class='', $tab='', $style='' ) 
	{
		$html  = '<input type="'.$type.'" name="'.$name.'"'; 
		$html .= ($id)  ? ' id="'.$id.'"' : '';
		$html .= ' value="'.$value.'"';
		$html .= ($size)  ? ' size="'.$size.'"'    : '';
		$html .= ($class) ? ' class="'.$class.'"'  : '';
		$html .= ($tab)   ? ' tabindex="'.$tab.'"' : '';
		$html .= ($style) ? ' style="'.$style.'"'  : '';
		$html .= ' />';
		return $html;
	}
	
	//-----------

	public function formSelect($name, $array, $value, $id='', $class='')
	{
		$html  = '<select name="'.$name.'"';
		$html .= ($id)  ? ' id="'.$id.'"' : '';
		$html .= ($class) ? ' class="'.$class.'">'."\n" : '>'.n;
		foreach ($array as $avalue => $alabel) 
		{
			$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected"'
					  : '';
			$html .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'.n;
		}
		$html .= '</select>'.n;
		return $html;
	}
	
	//-----------

	public function formTextarea($name, $value, $rows=6, $cols=45, $id='')
	{	
		$html  = '<textarea name="'.$name.'"';
		$html .= ($id)  ? ' id="'.$id.'"' : '';
		$html .= ($rows) ? ' rows="'.$rows.'"' : '';
		$html .= ($cols) ? ' cols="'.$cols.'"' : '';
		$html .= '>'.$value.'</textarea>';
		return $html;
	}

	//-------------------------------------------------------------
	// Misc.
	//-------------------------------------------------------------

	public function writeTitle($words, $class) 
	{
		$html = t.t.'<h3 class="'.$class.'">'.$words.'</h3>'.n;

		return $html;
	}
	
	//-----------
	
	public function reportError()
	{
		$html  = '<div>'.n;
		$html .= t.'<p>Error processing form.</p>'.n;
		$html .= t.'<p><a href="javascript:HUB.ReportProblem.reshowForm();" title="Edit report">Edit report</a></p>'.n;
		$html .= '</div>'.n;
		$html .= '<h3>Error!</h3>';
		$html .= '<p>An error occurred while processing the from you submitted. Please edit your report, make sure all proper fields are filled in, and try submitting once more.</p>';
		return $html;
	}

	//-----------

	public function breadcrumbs( $option, $active='' ) 
	{
		jimport('joomla.application.module.helper');
		
		$html  = '<ul class="breadcrumbtrail">'.n;
		if ($active == '') {
			$html .= t.'<li><strong>'.JText::_('FEEDBACK').'</strong></li>'.n;
		} else {
			$html .= t.'<li class="first"><a href="'.JRoute::_('index.php?option='.$option).'">'.JText::_('FEEDBACK').'</a> &raquo;</li>'.n;
		}
		if ($active == 'success_story') {
			$html .= t.'<li><strong>'.JText::_('HAVE_A_SUCCESS_STORY').'</strong></li>'.n;
		} else {
			$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=success_story').'">'.JText::_('HAVE_A_SUCCESS_STORY').'</a></li>'.n;
		}
		if ($active == 'report_problems') {
			$html .= t.'<li><strong>'.JText::_('HAVE_A_PROBLEM').'</strong></li>'.n;
		} else {
			$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=report_problems').'">'.JText::_('HAVE_A_PROBLEM').'</a></li>'.n;
		}
		if ($active == 'suggestions') {
			$html .= t.'<li><strong>'.JText::_('CAN_WE_DO_BETTER').'</strong></li>'.n;
		} else {
			$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=suggestions').'">'.JText::_('CAN_WE_DO_BETTER').'</a></li>'.n;
		}
		if (count(JModuleHelper::getModules('poll')) > 0 || $active == 'poll') {
			if ($active == 'poll') {
				$html .= t.'<li><strong>'.JText::_('HAVE_AN_OPINION').'</strong></li>'.n;
			} else {
				$html .= t.'<li><a href="'.JRoute::_('index.php?option='.$option.a.'task=poll').'">'.JText::_('HAVE_AN_OPINION').'</a></li>'.n;
			}
		}
		$html .= '</ul>'.n;
		
		return $html;
	}

	//-------------------------------------------------------------
	// Views
	//-------------------------------------------------------------

	public function main( $option, $title, $wishlist=0, $xpoll=0 )
	{
		jimport('joomla.application.module.helper');
		
		if ($xpoll && $wishlist) {
		$numcol = 'four';
		}
		else if(($xpoll && !$wishlist) or ($wishlist && !$xpoll)) {
		$numcol = 'three';
		}
		else {
		$numcol = 'two';
		}	
		
					
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');

		$html  = FeedbackHtml::div(FeedbackHtml::hed(2, $title), 'full', 'content-header').n;
		$html .= '<div class="main section">'.n;
		$html .= t.t.'<p>'.JText::sprintf('FEEDBACK_INTRO', $hubShortName).'</p>'.n;
		$html .= '<div class="'.$numcol.' columns first">'.n;
		$html .= t.'<div class="mainsection" id="story">'.n;
		$html .= t.t.FeedbackHtml::hed(3,'<a href="'.JRoute::_('index.php?option='.$option.a.'task=success_story').'">'.JText::_('Write a Success Story').'</a>').n;
		$html .= t.t.'<p>'.JText::_('FEEDBACK_STORY_OTHER_OPTIONS').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= '</div>'.n;
		if($wishlist) {
		$html .= '<div class="'.$numcol.' columns second">'.n;
		$html .= t.'<div class="mainsection" id="wish">'.n;
		$html .= t.t.FeedbackHtml::hed(3,'<a href="/wishlist/general/1/add">'.JText::_('Add to the Wish List').'</a>').n;
		$html .= t.t.'<p>'.JText::_('Have an idea for how to improve the site? Feel we could be doing something better? Add your suggestion to our').' <a href="/wishlist">'.JText::_( 'Wish List').'</a> '.JText::_('of new features.').'</p>'.n;
		$html .= t.'</div>'.n;		
		$html .= '</div>'.n;
		}
		if($xpoll) {
		$html .= '<div class="'.$numcol.' columns ';
		$html .= ($wishlist) ? 'third' : 'second' ;
		$html .= '">'.n;
		$html .= t.'<div class="mainsection" id="poll">'.n;
		$html .= t.t.FeedbackHtml::hed(3,'<a href="'.JRoute::_('index.php?option='.$option.a.'task=poll').'">'.JText::_('Take a Poll').'</a>').n;
		$html .= t.t.'<p>'.JText::_('Cast a vote in the latest poll and check what others in the community think. We want to hear from you!').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= '</div>'.n;
		}
		$html .= '<div class="'.$numcol.' columns ';
		if ($xpoll && $wishlist) {
		$html .=  'forth';
		}
		else if(($xpoll && !$wishlist) or ($wishlist && !$xpoll)) {
		$html .=  'third';
		}
		else {
		$html .=  'second';
		}		
		$html .= '">'.n;
		$html .= t.'<div class="mainsection" id="problem">'.n;
		$html .= t.t.FeedbackHtml::hed(3,'<a href="'.JRoute::_('index.php?option='.$option.a.'task=report_problems').'">'.JText::_('Report a Problem').'</a>').n;
		$html .= t.t.'<p>'.JText::_('FEEDBACK_TROUBLE_INTRO').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= '</div>'.n;
		
		/*$html .= t.'<div class="aside">'.n;
		$html .= t.t.'<p>'.JText::sprintf('FEEDBACK_INTRO', $hubShortName).'</p>'.n;
		$html .= t.'</div><!-- / .aside -->'.n;
		$html .= t.'<div class="subject">'.n;

		$html .= FeedbackHtml::hed(3,'<a href="'.JRoute::_('index.php?option='.$option.a.'task=success_story').'">'.JText::_('HAVE_A_SUCCESS_STORY').' <span>'.JText::_('LET_US_HEAR_IT').'</span></a>').n;
		$html .= '<p>'.JText::_('FEEDBACK_STORY_OTHER_OPTIONS').'</p>'.n;

		$html .= FeedbackHtml::hed(3,'<a href="'.JRoute::_('index.php?option='.$option.a.'task=report_problems').'">'.JText::_('HAVE_A_PROBLEM').' <span>'.JText::_('LET_US_FIX_IT').'</span></a>').n;
		$html .= '<p>'.JText::_('FEEDBACK_TROUBLE_INTRO').'</p>'.n;

		$html .= FeedbackHtml::hed(3,'<a href="'.JRoute::_('index.php?option='.$option.a.'task=suggestions').'">'.JText::_('CAN_WE_DO_BETTER').' <span>'.JText::_('TELL_US_HOW').'</span></a>').n;
		$html .= '<p>'.JText::_('FEEDBACK_SUGGESTION_INTRO').'</p>'.n;

		if (count(JModuleHelper::getModules('poll')) > 0) {
			$html .= FeedbackHtml::hed(3,'<a href="'.JRoute::_('index.php?option='.$option.a.'task=poll').'">'.JText::_('HAVE_AN_OPINION').' <span>'.JText::_('CAST_A_VOTE').'</span></a>').n;
		}
		
		$html .= t.'</div><!-- / .subject -->'.n;
		*/
		$html .= t.'<div class="clear"></div>'.n;
		$html .= '</div><!-- / .main seciton -->'.n;
		
		return $html;
	}

	//-----------

	public function report( $option, $task, $title, $reporter, $problem, $err=0, $verified=0 ) 
	{
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');
		$hubShortURL = $xhub->getCfg('hubShortURL');

		$browsers = array('[unspecified]' => JText::_('FEEDBACK_TROUBLE_SELECT_BROWSER'),
						  'Internet Explorer' => 'Internet Explorer',
						  'Safari' => 'Safari',
						  'Firefox' => 'Firefox',
						  'Opera' => 'Opera',
						  'Mozilla' => 'Mozilla',
						  'Netscape' => 'Netscape',
						  'Camino' => 'Camino',
						  'Omniweb' => 'Omniweb',
						  'Shiira' => 'Shiira',
						  'iCab' => 'iCab',
						  'Avant' => 'Avant Browser',
						  'Other' => 'Other');
						  
		$oses = array('[unspecified]' => JText::_('FEEDBACK_TROUBLE_SELECT_OS'),
					  'Windows' => 'Windows',
					  'Mac OS' => 'Mac OS',
					  'Linux' => 'Linux',
					  'Unix' => 'Unix',
					  'Other' => 'Other');

		$topics = array('???' => 'Unsure/Don\'t know',
					  'Access Denied' => 'Access Denied',
					  'Account/Login' => 'Account/Login',
					  'Content' => 'Content',
					  'Contributions' => 'Contributions',
					  'Online Meetings' => 'Online Meetings',
					  'Tools' => 'Tools',
					  'other' => 'other');

		if (!$reporter) {
			$reporter = array('login' => '', 
					  'name' => '', 
					  'org' => '', 
					  'email' => '');
		}

		
		$html  = FeedbackHtml::div(FeedbackHtml::hed(2, $title), 'full', 'content-header').n;
		$html .= '<div class="main section">'.n;
		//$html .= FeedbackHtml::breadcrumbs($option, 'report_problems');
		
		$html .= '<p class="information">'.JText::_('FEEDBACK_TROUBLE_TICKET_TIMES').'</p>'.n;

		if ($err != 0) {
			$html .= FeedbackHtml::error( JText::_('ERROR_MISSING_FIELDS') ).n;
		}
	
		$html .= FeedbackHtml::formStart('hubForm',JRoute::_('index.php?option='.$option.a.'task=report_problems')).n;
		$html .= '<div class="explaination">'.n;
		$html .= t.'<p>'.JText::_('FEEDBACK_TROUBLE_OTHER_OPTIONS').'</p>'.n;
		$html .= '</div>'.n;
		$html .= '<fieldset>'.n;
		$html .= FeedbackHtml::formInput('hidden','option',$option).n;
		$html .= FeedbackHtml::formInput('hidden','task','sendreport').n;
		$html .= FeedbackHtml::formInput('hidden','problem[referer]',$problem['referer']).n;
		$html .= FeedbackHtml::formInput('hidden','problem[tool]',$problem['tool']).n;
		$html .= FeedbackHtml::formInput('hidden','problem[osver]',$problem['osver']).n;
		$html .= FeedbackHtml::formInput('hidden','problem[browserver]',$problem['browserver']).n;
		$html .= FeedbackHtml::formInput('hidden','problem[short]','').n;
		$html .= FeedbackHtml::formInput('hidden','krhash',$problem['key']).n;
		//$html .= FeedbackHtml::formInput('hidden','verified',$verified).n;
		if ($verified == 1) {
			$html .= FeedbackHtml::formInput('hidden','answer',$problem['sum']).n;
		}
		
		$html .= t.t.FeedbackHtml::hed(3,JText::_('FEEDBACK_TROUBLE_USER_INFORMATION')).n;
		
		// Login
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('USERNAME').':'.n;
		$html .= t.t.t.FeedbackHtml::formInput('text','reporter[login]',$reporter['login'],'','reporter_login').n;
		$html .= t.t.'</label>'.n;
		
		// Name
		$html .= t.t.'<label';
		$html .= ($err != 0 && $reporter['name'] == '') ? ' class="fieldWithErrors">'.n : '>'.n;
		$html .= t.t.t.JText::_('NAME').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.t.t.FeedbackHtml::formInput('text','reporter[name]',$reporter['name'],'','reporter_name').n;
		$html .= t.t.'</label>'.n;
		if ($err != 0 && $reporter['name'] == '') {
			$html .= t.t.FeedbackHtml::error( JText::_('ERROR_MISSING_NAME') ).n;
		}
		
		// School / Organization
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('ORGANIZATION').':'.n;
		//if ($reporter['org'] != '' && $verified == 1) {
		//	$html .= t.t.t.$reporter['org'].n;
		//	$html .= t.t.t.FeedbackHtml::formInput('hidden','reporter[org]',$reporter['org'],'','reporter_org').n;
		//} else {
			$html .= t.t.t.FeedbackHtml::formInput('text','reporter[org]',$reporter['org'],'','reporter_org').n;
		//}
		$html .= t.t.'</label>'.n;
		
		// E-mail
		$html .= t.t.'<label';
		$html .= ($err != 0 && $reporter['email'] == '' || $err == 2) ? ' class="fieldWithErrors">'.n : '>'.n;
		$html .= t.t.t.JText::_('EMAIL').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.t.t.FeedbackHtml::formInput('text','reporter[email]',$reporter['email'],'','reporter_email').n;
		$html .= t.t.'</label>'.n;
		if ($err != 0 && $reporter['email'] == '') {
			$html .= t.t.FeedbackHtml::error( JText::_('ERROR_MISSING_EMAIL') ).n;
		}
		
		$html .= t.t.'<div class="group">'.n;
		$html .= t.t.t.'<label';
		$html .= ($err != 0 && $problem['os'] == '') ? ' class="fieldWithErrors">'.n : '>'.n;
		$html .= t.t.t.t.JText::_('OS').':'.n;
		$html .= t.t.t.t.FeedbackHtml::formSelect('problem[os]', $oses, $problem['os'],'problem_os','').n;
		$html .= t.t.t.'</label>'.n;

		$html .= t.t.t.'<label';
		$html .= ($err != 0 && $problem['browser'] == '') ? ' class="fieldWithErrors">'.n : '>'.n;
		$html .= t.t.t.t.JText::_('BROWSER').':'.n;
		$html .= t.t.t.t.FeedbackHtml::formSelect('problem[browser]', $browsers, $problem['browser'],'problem_browser','').n;
		$html .= t.t.t.'</label>'.n;
		$html .= t.t.'</div>'.n;
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		
		if ($verified != 1) {
			$html .= t.'<div class="explaination">'.n;
			$html .= t.t.FeedbackHtml::hed(4,JText::_('FEEDBACK_WHY_THE_MATH_QUESTION')).n;
			$html .= t.t.'<p>'.JText::_('FEEDBACK_MATH_EXPLANATION').'</p>'.n;
			$html .= t.'</div>'.n;
		}
		$html .= t.'<fieldset>'.n;
		$html .= t.t.FeedbackHtml::hed(3, JText::_('FEEDBACK_TROUBLE_YOUR_PROBLEM')).n;
		//$html .= t.t.'<label>'.n;
		//$html .= t.t.t.JText::_('Concerning').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		//$html .= t.t.t.FeedbackHtml::formSelect('problem[topic]', $topics, $problem['topic'],'problem_topic','').n;
		//$html .= t.t.'</label>'.n;

		//$html .= t.t.'<label>'.n;
		$html .= t.t.t.'<label';
		$html .= ($err != 0 && $problem['long'] == '') ? ' class="fieldWithErrors">'.n : '>'.n;
		$html .= t.t.t.JText::_('FEEDBACK_TROUBLE_DESCRIPTION').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.t.t.FeedbackHtml::formTextarea('problem[long]',stripslashes($problem['long']),10,40,'problem_long').n;
		$html .= t.t.'</label>'.n;
		if ($err != 0 && $problem['long'] == '') {
			$html .= t.t.FeedbackHtml::error( JText::_('ERROR_MISSING_DESCRIPTION') ).n;
		}

		if ($verified != 1) {
			$html .= t.t.t.'<label';
			$html .= ($err == 3) ? ' class="fieldWithErrors">'.n : '>'.n;
			$html .= t.t.t.JText::sprintf('FEEDBACK_TROUBLE_MATH', $problem['operand1'], $problem['operand2']).' '.n;
			$html .= t.t.t.FeedbackHtml::formInput('text','answer','',3,'answer','option','','').' <span class="required">'.JText::_('REQUIRED').'</span>'.n;
			$html .= t.t.'</label>'.n;
			if ($err == 3) {
				$html .= t.t.FeedbackHtml::error( JText::_('ERROR_BAD_CAPTCHA_ANSWER') ).n;
			}
		}
		
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<p class="submit">'.FeedbackHtml::formInput('submit','submit',JText::_('SUBMIT')).'</p>'.n;
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main section -->'.n;
	
		return $html;
	}
	
	//-----------
	
	public function poll( $title, $option )
	{
		$html  = FeedbackHtml::div(FeedbackHtml::hed(2, $title), 'full', 'content-header').n;
		$html .= '<div class="main section">'.n;
		//$html .= FeedbackHtml::breadcrumbs($option, 'poll');

   		$html .= FeedbackHtml::hed(3,JText::_('HAVE_AN_OPINION').' <span>'.JText::_('CAST_A_VOTE').'</span>').n;
		
		jimport('joomla.application.module.helper');
		if (count(JModuleHelper::getModules('poll')) > 0) {
			$module = JModuleHelper::getModule( 'mod_xpoll' );
			
			$html .= '<div class="introtext">'.n;
			$html .= JModuleHelper::renderModule( $module );
			$html .= '</div>'.n;
		} else {
			$html .= FeedbackHtml::warning( JText::_('NO_ACTIVE_POLLS') ).n;
		}
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}
	
	//-----------
	
	public function story( $title, $option, $user, $quote, $captcha, $err=0, $verified=0 )
	{
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');

		$html  = FeedbackHtml::div(FeedbackHtml::hed(2, $title), 'full', 'content-header').n;
		$html .= '<div class="main section">'.n;
		//$html .= FeedbackHtml::breadcrumbs($option, 'success_story');
		
		if ($verified == 1) {
			if ($err != 0) {
				$html .= FeedbackHtml::error( JText::_('ERROR_MISSING_FIELDS') ).n;
			}
    		$html .= FeedbackHtml::formStart('hubForm',JRoute::_('index.php?option='.$option.'&task=success_story')).n;
    		$html .= t.'<div class="explaination">'.n;
			$html .= t.t.'<p>'.JText::_('FEEDBACK_STORY_OTHER_OPTIONS').'</p>'.n;
			$html .= t.'</div>'.n;
			$html .= t.'<fieldset>'.n;
			$html .= t.t.FeedbackHtml::formInput('hidden','option',$option).n;
			$html .= t.t.FeedbackHtml::formInput('hidden','task','sendstory').n;
			$html .= t.t.FeedbackHtml::formInput('hidden','verified',$verified).n;
			$html .= t.t.FeedbackHtml::formInput('hidden','userid',$user['uid'],'','userid').n;
			$html .= t.t.FeedbackHtml::formInput('hidden','useremail',$user['email'],'','useremail').n;

			$html .= t.t.FeedbackHtml::hed(3,JText::_('FEEDBACK_STORY_YOUR_STORY')).n;

			$html .= t.t.'<input type="hidden" name="picture" id="picture" value="" />'.n;
			$html .= t.t.'<iframe width="100%" height="130" scrolling="no" name="filer" frameborder="0" id="filer" src="index2.php?option='.$option.a.'task=img'.a.'no_html=1'.a.'id='.$user['uid'].'"></iframe>'.n;

			$html .= t.t.'<label>'.n;
			$html .= t.t.t.JText::_('NAME').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
			$html .= t.t.t.FeedbackHtml::formInput('text','fullname',$user['name'],30,'fullname').n;
			$html .= t.t.'</label>'.n;

			$html .= t.t.'<label>'.n;
			$html .= t.t.t.JText::_('ORGANIZATION').':'.n;	
			$html .= t.t.t.FeedbackHtml::formInput('text','org',$user['org'],30,'org').n;
			$html .= t.t.'</label>'.n;

			$html .= t.t.'<label';
			$html .= ($err != 0 && $quote == '') ? ' class="fieldWithErrors">'.n : '>'.n;
			$html .= t.t.t.JText::_('FEEDBACK_STORY_DESCRIPTION').':'.n;
			$html .= t.t.t.FeedbackHtml::formTextarea('quote',$quote['long'],15,50,'quote').n;
			$html .= t.t.'</label>'.n;
			if ($err != 0 && $quote == '') {
				$hmtl .= t.t.FeedbackHtml::error( JText::_('FEEDBACK_STORY_MISSING_DESCRIPTION') ).n;
			}

			$html .= t.t.'<label>'.n;
			$html .= t.t.t.'<input type="checkbox" name="publish_ok" value="1" class="option" />'.n;
			$html .= t.t.t.JText::sprintf('FEEDBACK_STORY_AUTHORIZE_QUOTE', $hubShortName, $hubShortName).n;
			$html .= t.t.'</label>'.n;
			
			$html .= t.t.'<label>'.n;
			$html .= t.t.t.'<input type="checkbox" name="contact_ok" value="1" class="option" />'.n;
			$html .= t.t.t.JText::sprintf('FEEDBACK_STORY_AUTHORIZE_CONTACT', $hubShortName).n;
			$html .= t.t.'</label>'.n;
			
			$html .= t.'</fieldset><div class="clear"></div>'.n;
			$html .= t.'<p class="submit">'.FeedbackHtml::formInput('submit','submit',JText::_('SUBMIT')).'</p>'.n;
			$html .= '</form>'.n;
		} else {
			$html .= FeedbackHtml::warning( JText::_('FEEDBACK_STORY_LOGIN') ).n;
			$html .= JModuleHelper::renderModule( JModuleHelper::getModule( 'mod_xlogin' ) );
		}
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}
	
	//-----------
	
	public function suggestions( $title, $option, $user, $sug, $err=0, $verified=0) 
	{
		$xhub =& XFactory::getHub();
		$hubShortName = $xhub->getCfg('hubShortName');

		$selects = array('general' => 'General',
						  'tool' => 'Simulation Tools',
						  'learningmodule' => 'Learning Modules',
						  'lecture' => 'Lectures',
						  'workshop' => 'Workshops'
						  );
		
		$html  = FeedbackHtml::div(FeedbackHtml::hed(2, $title), 'full', 'content-header').n;
		$html .= '<div class="main section">'.n;
		//$html .= FeedbackHtml::breadcrumbs($option, 'suggestions');

		if ($err) {
			$html .= FeedbackHtml::error( JText::_('ERROR_MISSING_FIELDS') ).n;
		}
		$html .= FeedbackHtml::formStart('hubForm',JRoute::_('index.php?option='.$option.'&task=suggestions')).n;
		$html .= t.'<div class="explaination">'.n;
		$html .= t.t.'<p>'.JText::_('FEEDBACK_SUGGESTION_EXPLANATION').'</p>'.n;
		$html .= t.'</div>'.n;
		$html .= t.'<fieldset>'.n;
		$html .= t.t.FeedbackHtml::formInput('hidden','option',$option).n;
		$html .= t.t.FeedbackHtml::formInput('hidden','task','sendsuggestions').n;
		$html .= t.t.FeedbackHtml::formInput('hidden','verified',$verified).n;
		$html .= t.t.FeedbackHtml::formInput('hidden','krhash',$sug['key']).n;
		if ($verified == 1) {
			$html .= FeedbackHtml::formInput('hidden','answer',$sug['sum']).n;
		}
		$html .= t.t.FeedbackHtml::hed(3,JText::_('FEEDBACK_SUGGESTION_USER_INFORMATION')).n;
		$html .= t.t.'<label>'.n;
		$html .= t.t.t.JText::_('USERNAME').': '.n;
		$html .= t.t.t.FeedbackHtml::formInput('text','suggester[login]',$user['login'],30,'suggester_login').n;
		$html .= t.t.'</label>'.n;
		
		$html .= t.t.'<label';
		$html .= ($err != 0 && $user['name'] == '') ? ' class="fieldWithErrors">'.n : '>'.n;
		$html .= t.t.t.JText::_('NAME').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.t.t.FeedbackHtml::formInput('text','suggester[name]',$user['name'],30,'suggester_name').n;
		$html .= t.t.'</label>'.n;
		if ($err != 0 && $user['name'] == '') {
			$html .= FeedbackHtml::error( JText::_('ERROR_MISSING_NAME') ).n;
		}
		
		$html .= t.t.'<label';
		$html .= ($err != 0 && $user['org'] == '') ? ' class="fieldWithErrors">'.n : '>'.n;
		$html .= t.t.t.JText::_('ORGANIZATION').':'.n;
		//if ($user['org'] != '' && $verified == 1) {
		//	$html .= t.t.t.$user['org'].FeedbackHtml::formInput('hidden','suggester[org]',$user['org'],'','suggester_org').n;
		//} else {
			$html .= t.t.t.FeedbackHtml::formInput('text','suggester[org]',$user['org'],40,'suggester_org').n;
		//}
		$html .= t.t.'</label>'.n;
		/*if ($err != 0 && $user['org'] == '') {
			$html .= FeedbackHtml::error( JText::_('ERROR_MISSING_ORGANIZATION') ).n;
		}*/

		$html .= t.t.'<label';
		$html .= ($err != 0 && $user['email'] == '' || $err == 2) ? ' class="fieldWithErrors">'.n : '>'.n;
		$html .= t.t.t.JText::_('EMAIL').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.t.t.FeedbackHtml::formInput('text','suggester[email]',$user['email'],40,'suggester_email').n;
		$html .= t.t.'</label>'.n;
		if ($err != 0 && $user['email'] == '') {
			$html .= FeedbackHtml::error( JText::_('ERROR_MISSING_EMAIL') ).n;
		}
		
		$html .= t.'</fieldset><div class="clear"></div>'.n;
		
		if ($verified != 1) {
			$html .= t.'<div class="explaination">'.n;
			$html .= t.t.'<h4>'.JText::_('FEEDBACK_WHY_THE_MATH_QUESTION').'</h4>'.n;
			$html .= t.t.'<p>'.JText::_('FEEDBACK_MATH_EXPLANATION').'</p>'.n;
			$html .= t.'</div>'.n;
		}
		$html .= t.'<fieldset>'.n;
		$html .= t.t.FeedbackHtml::hed(3,JText::_('FEEDBACK_SUGGESTION_YOUR_COMMENTS')).n;
		
		$html .= t.t.'<label';
		$html .= ($err != 0 && $sug['for'] == '') ? ' class="fieldWithErrors">'.n : '>'.n;
		$html .= t.t.t.JText::_('FEEDBACK_SUGGESTION_TOPIC').':'.n;
		$html .= t.t.t.FeedbackHtml::formSelect('suggestion[for]', $selects, $sug['for'], 'suggestion_for','').n;
		$html .= t.t.'</label>'.n;

		$html .= t.t.'<label';
		$html .= ($err != 0 && $sug['idea'] == '') ? ' class="fieldWithErrors">'.n : '>'.n;
		$html .= t.t.t.JText::_('FEEDBACK_SUGGESTION_DESCRIPTION').': <span class="required">'.JText::_('REQUIRED').'</span>'.n;
		$html .= t.t.t.FeedbackHtml::formTextarea('suggestion[idea]',$sug['idea'],10,40,'suggestion_idea').n;
		$html .= t.t.'</label>'.n;
		if ($err != 0 && $sug['idea'] == '') {
			$html .= FeedbackHtml::error( JText::_('FEEDBACK_SUGGESTION_MISSING_DESCRIPTION') ).n;
		}

		if ($verified != 1) {
			$html .= t.t.t.'<label';
			$html .= ($err == 3) ? ' class="fieldWithErrors">'.n : '>'.n;
			$html .= t.t.t.JText::sprintf('FEEDBACK_TROUBLE_MATH', $sug['operand1'], $sug['operand2']).' '.n;
			$html .= t.t.t.FeedbackHtml::formInput('text','answer','',3,'answer','option','','').' <span class="required">'.JText::_('REQUIRED').'</span>'.n;
			$html .= t.t.'</label>'.n;
			if ($err == 3) {
				$html .= t.t.FeedbackHtml::error( JText::_('ERROR_BAD_CAPTCHA_ANSWER') ).n;
			}
		}

		$html .= t.'</fieldset><div class="clear"></div>'.n;
		$html .= t.'<p class="submit">'.FeedbackHtml::formInput('submit','submit',JText::_('SUBMIT')).'</p>';
		$html .= '</form>'.n;
		$html .= '</div><!-- / .main section -->'.n;

		return $html;
	}

	//-------------------------------------------------------------
	// Post-submission views
	//-------------------------------------------------------------

	public function reportThanks($msg, $ticket, $no_html=0, $option)
	{
		$html = '';
        if ($no_html) {
			$html .= '<div>'.n;
			$html .= t.'<p>'.JText::_('Your ticket').' # <span>'.$ticket.'</span></p>'.n;
			$html .= t.'<p><a href="javascript:HUB.ReportProblem.resetForm();" title="'.JText::_('New report').'">'.JText::_('New report').'</a></p>'.n;
			$html .= '</div>'.n;
			$html .= stripslashes($msg);
		} else {
			$html .= FeedbackHtml::div(FeedbackHtml::hed(2, JText::_('FEEDBACK').': '.JText::_('REPORT_PROBLEMS')), 'full', 'content-header').n;
			$html .= '<div class="main section">'.n;
			//$html .= FeedbackHtml::breadcrumbs($option, 'report_problems');

			$html .= '<p>'.JText::_('FEEDBACK_TROUBLE_THANKS').'</p>'.n;
			if ($ticket != '') {
				$html .= '<p>'.JText::sprintf('FEEDBACK_TROUBLE_TICKET_REFERENCE',$ticket).'</p>'.n;
			}
			$html .= '</div><!-- / .main section -->'.n;
		}
		
		return $html;
	}
	
	//-----------
	
	public function suggestionsThanks($title, $option, $for)
	{
		$xhub =& XFactory::getHub();
		$hubShortURL = $xhub->getCfg('hubShortURL');
		
		$html  = FeedbackHtml::div(FeedbackHtml::hed(2, $title), 'full', 'content-header').n;
		$html .= '<div class="main section">'.n;
		//$html .= FeedbackHtml::breadcrumbs($option, 'suggestions');

		$html .= t.'<p><strong>'.JText::_('FEEDBACK_SUGGESTION_THANKS').'</strong>'.n;
		$html .= t.JText::sprintf('FEEDBACK_SUGGESTION_THANKS_MORE',$hubShortURL).'</p>'.n;	
		$html .= '</div><!-- / .main section -->'.n;
		
		return $html;
	}
	
	//-----------
	
	public function storyThanks( $config, $title, $option, $storyteller, $quote, $picture )
	{
		$xhub =& XFactory::getHub();
		
		$html  = FeedbackHtml::div(FeedbackHtml::hed(2, $title), 'full', 'content-header').n;
		$html .= '<div class="main section">'.n;
		//$html .= FeedbackHtml::breadcrumbs($option, 'success_story');

		$html .= '<p>'.JText::_('FEEDBACK_STORY_THANKS').'</p>'.n;
		if ($picture!= ''  && file_exists( $picture )) {
			$file = $picture;
		} else {  
			$file = $config->get('defaultpic');
		}
		if ($file && file_exists( $file )) {
			list($ow, $oh) = getimagesize($file);
		}
		
		//scale if image is bigger than 120w x120h
		$num = max($ow/120, $oh/120);
		if ($num > 1) {
			$mw = round($ow/$num);
			$mh = round($oh/$num);
		} else {
			$mw = $ow;
			$mh = $oh;
		}
		
		$html .= t.'<table class="storybox">'.n;
		$html .= t.t.'<tbody>'.n;
		$html .= t.t.t.'<tr>'.n;
		$html .= t.t.t.t.'<td><img src='.$xhub->getCfg('hubLongURL').'/'.$file.' width="'.$mw.'" height="'.$mh.'" alt="" /></td>'.n;
		$html .= t.t.t.t.'<td>'.n;
		$html .= t.t.t.t.t.'<div class="quote">'.stripslashes($quote).'</div>'.n;
		$html .= t.t.t.t.t.'<div class="quote"><strong>'.$storyteller['name'].'</strong><br />'.n;
		$html .= t.t.t.t.t.'<i>'.$storyteller['org'].'</i></div>'.n;
		$html .= t.t.t.t.'</td>'.n;
		$html .= t.t.t.'</tr>'.n;
		$html .= t.t.'</tbody>'.n;
		$html .= t.'</table>'.n;
		$html .= '</div><!-- / .main section -->'.n;

		return $html;
	}

	//-------------------------------------------------------------
	// Image handling
	//-------------------------------------------------------------
	
	public function writeImage( $app, $option, $webpath, $default_picture, $path, $file, $file_path, $id, $errors=array() )
	{
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
	<title><?php echo JText::_('MEMBER_PICTURE'); ?></title>

	<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />

	<style type="text/css" media="screen">@import url(templates/<?php echo $app->getTemplate(); ?>/css/main.css);</style>
<?php
	if (is_file(JPATH_ROOT.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'feedback.css')) {
		echo '<link rel="stylesheet" href="'.DS.'templates'.DS. $app->getTemplate() .DS.'html'.DS.$option.DS.'feedback.css" type="text/css" />'.n;
	} else {
		echo '<link rel="stylesheet" href="'.DS.'components'.DS.$option.DS.'feedback.css" type="text/css" />'.n;
	}
?>
<script type="text/javascript">
<!--
function validate() 
{
	var apuf = document.getElementById('file');
	return apuf.value ? true : false;
}

function passparam()
{
	parent.document.getElementById('hubForm').picture.value = this.document.forms[0].conimg.value;
}

window.onload = passparam;
//-->
</script>
 </head>
 <body id="member-picture">
 	<form action="index.php" method="post" enctype="multipart/form-data" name="filelist" id="filelist" onsubmit="return validate();">

<?php
	if (count($errors) > 0) {
		echo MembersHtml::error( implode('<br />',$errors) ).n;
	}
?>

		<table>
			<tbody>
<?php
	$k = 0;

	if ($file && file_exists( JPATH_ROOT.$file_path.DS.$file )) {
		$this_size = filesize(JPATH_ROOT.$file_path.DS.$file);
		list($ow, $oh, $type, $attr) = getimagesize(JPATH_ROOT.$file_path.DS.$file);

		// scale if image is bigger than 120w x120h
		$num = max($ow/120, $oh/120);
		if($num > 1) {
			$mw = round($ow/$num);
			$mh = round($oh/$num);
		} else {
			$mw = $ow;
			$mh = $oh;
		}
?>
				<tr>
					<td>
						<img src="<?php echo $webpath.DS.$path.DS.$file; ?>" alt="" id="conimage" height="<?php echo $mh; ?>" width="<?php echo $mw; ?>" /> 
					</td>
					<td>
						<input type="hidden" name="conimg" value="<?php echo $webpath.DS.$path.DS.$file; ?>" />
						<input type="hidden" name="task" value="delete" />
						<input type="hidden" name="file" id="file" value="<?php echo $file; ?>" />
						<input type="submit" name="submit" value="<?php echo JText::_('DELETE'); ?>" />
					</td>
				</tr>
<?php } else { ?>
				<tr>
					<td>
						<img src="<?php echo $default_picture; ?>" alt="No photo available" id="oimage" name="oimage" />
					</td>
					<td>
						<p><?php echo JText::_('FEEDBACK_STORY_ADD_PICTURE'); ?><br /><small>(gif/jpg/jpeg/png - 200K max)</small></p>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="hidden" name="conimg" value="" />
						<input type="hidden" name="task" value="upload" />
						<input type="hidden" name="currentfile" value="<?php $file; ?>" />
						<input type="file" name="upload" id="upload" size="10" /> <input type="submit" value="<?php echo JText::_('UPLOAD'); ?>" />
					</td>
				</tr>
<?php } ?>
			</tbody>
		</table>
		
		<input type="hidden" name="option" value="<?php echo $option; ?>" />
		<input type="hidden" name="no_html" value="1" />
		<input type="hidden" name="id" value="<?php echo $id; ?>" />
	</form>
 </body>
</html>
<?php
	}
}
?>