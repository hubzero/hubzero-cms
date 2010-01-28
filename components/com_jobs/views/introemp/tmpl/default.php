<?php 
/**
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * @license	GNU General Public License, version 2 (GPLv2) 
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
	
	$xhub =& XFactory::getHub();
	$hubShortName = $xhub->getCfg('hubShortName');
	$infolink = isset($this->config->parameters['infolink']) && $this->config->parameters['infolink']!=''  ? $this->config->parameters['infolink'] : 'kb/jobs';
	$promoline = isset($this->config->parameters['promoline']) ? $this->config->parameters['promoline'] : '';
	$premium_infolink = isset($this->config->parameters['premium_infolink']) && $this->config->parameters['premium_infolink']!=''  ? $this->config->parameters['_premium_infolink'] : '';
	$maxads = isset($this->config->parameters['maxads']) && intval($this->config->parameters['maxads']) > 0  ? $this->config->parameters['maxads'] : 3;
		
		$html  = '';	
		$html .= JobsHtml::div( JobsHtml::hed( 2, $this->title), 'full', 'content-header' );
		$html .= '<div class="main section">'.n;
		$html .= t.'<div class="process_steps threecol">'.n;
		$html .= t.t.'<div class="current">'.n;
		$html .= t.t.t.'<h3><span>1</span> '.JText::_('STEP_LOGIN').' '.JText::_('TO').' '.$hubShortName.'</h3>'.n;
		$html .= t.t.'</div>'.n;
		
		$html .= t.t.'<div>'.n;
		$html .= t.t.t.'<h3><span>2</span> ';
		$html .= JText::_('STEP_SUBSCRIBE');
		$html .= '</h3>'.n;
		$html .= t.t.'</div>'.n;
		
		$html .= t.t.'<div>'.n;
		$html .= t.t.t.'<h3><span>3</span> ';
		$html .= ($this->task=='addjob')  ? JText::_('Post Jobs & Browse Resumes') : JText::_('Browse Resumes & Post Jobs');
		$html .='</h3>'.n;
		$html .= t.t.'</div>'.n;
		$html .= t.'</div><div class="clear"></div>'.n;	
		
		$html .= '<div class="three columns first">'.n;				
		ximport('xmodule');
		$html .= XModuleHelper::renderModules('force_mod_mini');
		$html .= t.'<p>'.JText::_('No account?').' <a href="/register">'.JText::_('Register now').'</a>. '.JText::_('It\'s free!').'</p>';
		$html .= '</div>'.n;
		
		$html .= '<div class="three columns second">'.n;
		$html .= t.t.'<div>'.n;
		$html .= t.t.t.'<p>';
		$html .=  JText::_('To access employer services, a subscription to ');
		$html .= $premium_infolink ? '<a href="'.$premium_infolink.'" class="premium" title="'.JText::_('WHAT_IS_PREMIUM').'">'.JText::_('PREMIUM_SERVICE').'</a> ' : JText::_('Employer Services').' ';
		$html .= JText::_('is required.').' '.JText::_('To subscribe, you will fill out a short form confirming your details as an employer, selecting subscription type and length. If payment is required, your subscription will be approved once the funds are received. You can change/cancel your subscription at any time.') ;
		$html .= '</p>';
		$html .= $promoline ? '<p class="promo">'.$promoline.'</p>'.n : '';
		$html .= t.t.'</div>'.n;
		$html .= '</div>'.n;
		
		$html .= '<div class="three columns third">'.n;
		$html .= t.t.'<div>'.n;
		$html .= t.t.t.'<p>';
		$html .= ($this->task=='addjob') ? JText::_('Post up to').' '.$maxads.' '.JText::_('job ads, depending on your level of service. You can have users apply through this site or an external url. You will also be able to browse user resumes and shortlist those you like. ') : JText::_('Browse resumes of our HUB users. Search for candidates by keywords, positions sought and relevant profile details. You can shortlist candidates and contact them directly. ').' '.JText::_('You can also post job ads and receive user applications to your jobs.');
		$html .= ($this->task=='addjob') ? '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'helper_job_search.gif" />' : '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'helper_browse_resumes.gif" />';
		$html .= '</p>';
		$html .= t.t.'</div>'.n;
		$html .= '</div>'.n;
		$html .= '</div><!-- / .main section -->'.n;
		
		echo $html;
?>