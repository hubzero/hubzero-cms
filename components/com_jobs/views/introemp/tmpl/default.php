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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   GNU General Public License, version 2 (GPLv2) 
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

	/* Mini-login screen for employers */

	$jconfig = JFactory::getConfig();
	$sitename = $jconfig->getValue('config.sitename');
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
	$html .= t.t.t.'<h3><span>1</span> '.JText::_('COM_JOBS_STEP_LOGIN').' '.JText::_('COM_JOBS_TO').' '.$sitename.'</h3>'.n;
	$html .= t.t.'</div>'.n;

	$html .= t.t.'<div>'.n;
	$html .= t.t.t.'<h3><span>2</span> ';
	$html .= JText::_('COM_JOBS_STEP_SUBSCRIBE');
	$html .= '</h3>'.n;
	$html .= t.t.'</div>'.n;

	$html .= t.t.'<div>'.n;
	$html .= t.t.t.'<h3><span>3</span> ';
	$html .= ($this->task=='addjob') ? JText::_('COM_JOBS_ACTION_POST_AND_BROWSE') : JText::_('COM_JOBS_ACTION_BROWSE_AND_POST');
	$html .='</h3>'.n;
	$html .= t.t.'</div>'.n;
	$html .= t.'</div><div class="clear"></div>'.n;

	$html .= '<div class="three columns first">'.n;
	ximport('Hubzero_Module_Helper');
	$html .= Hubzero_Module_Helper::renderModules('force_mod_mini');
	$html .= t.'<p>'.JText::_('COM_JOBS_LOGIN_NO_ACCOUNT').' <a href="/register">'.JText::_('COM_JOBS_LOGIN_REGISTER_NOW').'</a>. '.JText::_('COM_JOBS_LOGIN_IT_IS_FREE').'</p>';
	$html .= '</div>'.n;

	$html .= '<div class="three columns second">'.n;
	$html .= t.t.'<div>'.n;
	$html .= t.t.t.'<p>';
	$html .= JText::_('COM_JOBS_INTRO_TO_ACCESS').' ';
	$html .= JText::_('COM_JOBS_EMPLOYER_SERVICES').' ';
	$html .= JText::_('COM_JOBS_INTRO_SUBSCRIPTION_REQUIRED').' '.JText::_('COM_JOBS_INTRO_HOW_TO_SUBSCRIBE') ;
	$html .= '</p>';
	$html .= $promoline ? '<p class="promo">'.$promoline.'</p>'.n : '';
	$html .= t.t.'</div>'.n;
	$html .= '</div>'.n;

	$html .= '<div class="three columns third">'.n;
	$html .= t.t.'<div>'.n;
	$html .= t.t.t.'<p>';
	$html .= ($this->task=='addjob')
			? JText::_('COM_JOBS_INTRO_POST_UP_TO').' '.$maxads.' '.JText::_('COM_JOBS_INTRO_POST_DETAILS')
			: JText::_('COM_JOBS_INTRO_BROWSE_INFO').' '.JText::_('COM_JOBS_INTRO_BROWSE_DETAILS');
	$html .= ($this->task=='addjob')
			? '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'helper_job_search.gif" alt="'.JText::_('COM_JOBS_ACTION_POST_JOB').'" />'
			: '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'helper_browse_resumes.gif" alt="'.JText::_('COM_JOBS_ACTION_BROWSE_RESUMES').'" />';
	$html .= '</p>';
	$html .= t.t.'</div>'.n;
	$html .= '</div>'.n;
	$html .= '</div><!-- / .main section -->'.n;

	echo $html;
?>