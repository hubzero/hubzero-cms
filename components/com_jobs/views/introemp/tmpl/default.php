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
	
	/* Mini-login screen for employers */
	
	$xhub =& Hubzero_Factory::getHub();
	$hubShortName = $xhub->getCfg('hubShortName');
	// get some configs
	$promoline = $this->config->get('promoline') ? $this->config->get('promoline') : '';
	$infolink = $this->config->get('infolink') ? $this->config->get('infolink') : '';	
	$maxads = $this->config->get('maxads') ? $this->config->get('maxads') : 3;
	
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->
<?php	
	$html  = '';	
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
	$html .= ($this->task=='addjob') ? JText::_('ACTION_POST_AND_BROWSE') : JText::_('ACTION_BROWSE_AND_POST');
	$html .='</h3>'.n;
	$html .= t.t.'</div>'.n;
	$html .= t.'</div><div class="clear"></div>'.n;	
		
	$html .= '<div class="three columns first">'.n;				
	ximport('Hubzero_Module_Helper');
	$html .= Hubzero_Module_Helper::renderModules('force_mod_mini');
	$html .= t.'<p>'.JText::_('LOGIN_NO_ACCOUNT').' <a href="/register">'.JText::_('LOGIN_REGISTER_NOW').'</a>. '.JText::_('LOGIN_IT_IS_FREE').'</p>';
	$html .= '</div>'.n;
		
	$html .= '<div class="three columns second">'.n;
	$html .= t.t.'<div>'.n;
	$html .= t.t.t.'<p>';
	$html .= JText::_('INTRO_TO_ACCESS').' ';
	$html .= JText::_('EMPLOYER_SERVICES').' ';
	$html .= JText::_('INTRO_SUBSCRIPTION_REQUIRED').' '.JText::_('INTRO_HOW_TO_SUBSCRIBE') ;
	$html .= '</p>';
	$html .= $promoline ? '<p class="promo">'.$promoline.'</p>'.n : '';
	$html .= t.t.'</div>'.n;
	$html .= '</div>'.n;
		
	$html .= '<div class="three columns third">'.n;
	$html .= t.t.'<div>'.n;
	$html .= t.t.t.'<p>';
	$html .= ($this->task=='addjob') 
			? JText::_('INTRO_POST_UP_TO').' '.$maxads.' '.JText::_('INTRO_POST_DETAILS') 
			: JText::_('INTRO_BROWSE_INFO').' '.JText::_('INTRO_BROWSE_DETAILS');
	$html .= ($this->task=='addjob') 
			? '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'helper_job_search.gif" alt="'.JText::_('ACTION_POST_JOB').'" />' 
			: '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'helper_browse_resumes.gif" alt="'.JText::_('ACTION_BROWSE_RESUMES').'" />';
	$html .= '</p>';
	$html .= t.t.'</div>'.n;
	$html .= '</div>'.n;
	$html .= '</div><!-- / .main section -->'.n;
		
	echo $html;
?>