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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//-----------
jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_members_resume' );
JPlugin::loadLanguage( 'com_jobs' );
	
//-----------

class plgMembersResume extends JPlugin
{
	function plgMembersResume(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'resume' );
		$this->_params = new JParameter( $this->_plugin->params );
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'jobs.class.php' );
		
		// Get the component parameters
		
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'jobs.config.php' );
		$jconfig = new JobsConfig( 'com_jobs' );
		$this->config = $jconfig;		
	}
	
	//-----------
	
	function &onMembersAreas( $authorized) 
	{	
		$emp = $this->isEmployer();
		
		if ($authorized or $emp) {
			$areas = array(
				'resume' => ucfirst(JText::_('Resume'))
			);
			
		} else {
			$areas = array();
		}
			
		return $areas;
	}
	
	//-----------

	function isEmployer ( $member='', $authorized = '')
	{		
		$juser 	  =& JFactory::getUser();
		$database =& JFactory::getDBO();
		$employer = new Employer ( $database );
		
		// determine who is veiwing the page
		$emp = 0;
		$emp = $employer->isEmployer($juser->get('id'));
		
		// check if they belong to a dedicated admin group
		$admingroup = (isset($this->config->parameters['admingroup']) && $this->config->parameters['admingroup'] != '' ) ? $this->config->parameters['admingroup'] : '' ;
		if($admingroup) {
			ximport('xgroup');
			ximport('xuserhelper');
				
			$ugs = XUserHelper::getGroups( $juser->get('id') );
				if ($ugs && count($ugs) > 0) {
					foreach ($ugs as $ug) 
					{
						if ($ug->cn == $admingroup) {
							$emp = 1;
						}
					}
				}
		}
		
		if($authorized) {
			$emp = 1;
		}
		
		if($member) {		
		$my =  $member->get('uidNumber') == $juser->get('id') ? 1 : 0;
		$emp = $my && $emp ? 0 : $emp;
		}
		
		return $emp;
	}
	
	//-----------

	function isAdmin ($admin = 0)
	{		
		$juser 	  =& JFactory::getUser();
		
		// check if they belong to a dedicated admin group
		$admingroup = (isset($this->config->parameters['admingroup']) && $this->config->parameters['admingroup'] != '' ) ? $this->config->parameters['admingroup'] : '' ;
		if($admingroup) {
			ximport('xgroup');
			ximport('xuserhelper');
				
			$ugs = XUserHelper::getGroups( $juser->get('id') );
				if ($ugs && count($ugs) > 0) {
					foreach ($ugs as $ug) 
					{
						if ($ug->cn == $admingroup) {
							$admin = 1;
						}
					}
				}
		}
		
		return $admin;
	}


	//-----------

	function onMembers( $member, $option, $authorized, $areas )
	{		
		$return = 'html';
		$active = 'resume';
		
		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();
		
		require_once( JPATH_ROOT.DS.'components'.DS.'com_jobs'.DS.'controller.php' );
		require_once( JPATH_ROOT.DS.'components'.DS.'com_jobs'.DS.'jobs.html.php' );
	
		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas( $authorized) ) 
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas( $authorized) ) )) {
				// do nothing						
			}
		}
		
		$document =& JFactory::getDocument();
		if (is_file('components'.DS.'com_jobs'.DS.'jobs.js')) {
			$document->addScript('components'.DS.'com_jobs'.DS.'jobs.js');
		}		
			
		// The output array we're returning
		$arr = array(
			'html'=>'',
			'metadata'=>'',
			'searchresult'=>''
		);
		
		// Do we need to return any data?
		if ($return != 'html' && $return != 'metadata') {			
			return $arr;
		}
		
		$emp 	= $this->isEmployer($member, $authorized);
		/*$admin 	= $this->isAdmin();
		$emp 	= $admin ? $admin : $emp;
		$my =  $member->get('uidNumber') == $juser->get('id') ? 1 : 0;
		$emp = $my && $emp ? 0 : $emp;*/
						
		// Are we returning HTML?
		if ($return == 'html'  && $areas[0] == 'resume') {
			$task = JRequest::getVar('action','');
			//$oid = JRequest::getInt('id', 0);
					
			switch ($task) 
			{
				case 'uploadresume': 	$arr['html'] = $this->upload($database, $option, $member); 		break;
				case 'deleteresume':   	$arr['html'] = $this->deleteresume ($database, $option, $member, $emp);   break;
				case 'edittitle':   	$arr['html'] = $this->view ($database, $option, $member, $emp, 1);   break;
				case 'savetitle':   	$arr['html'] = $this->save ($database, $option, $member, $task, $emp);   break;
				case 'saveprefs':   	$arr['html'] = $this->save ($database, $option, $member, $task, $emp);   break;
				case 'editprefs':   	$arr['html'] = $this->view($database, $option, $member, $emp, 0, $editpref = 2 ); break;
				case 'activate':   		$arr['html'] = $this->activate($database, $option, $member, $emp); break;
				case 'download':   		$arr['html'] = $this->download($member); break;
				case 'view': 
				default: $arr['html'] = $this->view($database, $option, $member, $emp, $edittitle = 0 ); break;
			}
		} else if($authorized or $emp) {
			$arr['metadata'] = '<p class="resume"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume').'">'.ucfirst(JText::_('Resume')).'</a></p>'.n;
		}
			
		return $arr;
	}
	
	//-----------
	
	public function save($database, $option, $member, $task, $emp) 
	{
		$lookingfor = JRequest::getVar('lookingfor','');
		$tagline = JRequest::getVar('tagline','');
		$active = JRequest::getInt('activeres', 0);
		$author = JRequest::getInt('author', 0);
		$title = JRequest::getVar('title','');
		
		/*if($task=='saveprefs' && (!$tagline or !$lookingfor)) {
			echo $this->alert(JText::_('Please describe both yourself and your career goals.'));						
			return $this->view($database, $option, $member, $emp, 0, 1);
		}*/
		if($task=='saveprefs') {
			$js = new JobSeeker ( $database );
			
			if(!$js->load($member->get('uidNumber'))) {
				$this->setError( JText::_('Job seeker profile not found.') );
				return '';
			}
			
			if (!$js->bind( $_POST )) {
			echo $this->alert( $js->getError() );
			exit();
			}
			
			$js->active = $active;
			$js->updated = date( 'Y-m-d H:i:s', time() );	
			
			if (!$js->store()) {
			echo $this->alert( $js->getError() );
			exit();
			}		
		}
		else if($task=='savetitle' && $author && $title) {
			$resume = new Resume ( $database );
			if($resume->load($author)) {
				$resume->title = $title;
				if (!$resume->store()) {
				echo $this->alert( $resume->getError() );
				exit();
				}
			}
		}
		
		return $this->view($database, $option, $member, $emp);
	}
	
	//-----------
	
	public function activate($database, $option, $member, $emp) 
	{		
		// are we activating or disactivating?
		$active = JRequest::getInt('on', 0);
				
		$js = new JobSeeker ( $database );
		
		if(!$js->load($member->get('uidNumber'))) {
			$this->setError( JText::_('Job seeker profile not found.') );
			return '';
		} else if(!$active) {
			$js->active = $active;
			$js->updated = date( 'Y-m-d H:i:s', time() );	
			
			// store new content
			if (!$js->store()) {
				echo $js->getError();
				exit();
			}
			
			return $this->view($database, $option, $member, $emp);
			
		}
		else {
			// ask to confirm/add search preferences
			return $this->view($database, $option, $member, $emp, 0, 1);
		}		
	}
	
	//----------
	
	public function getThumb ($uid) 
	{	
		// do we have a thumb image for the user?
		require_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'members.imghandler.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'members.class.php' );
		ximport('fileuploadutils');
						
		$profile = new XProfile();
		$profile->load( $uid );
		$thumb = '';
		
		$config =& JComponentHelper::getParams( 'com_members' );
		$ih = new MembersImgHandler();
		$dir = FileUploadUtils::niceidformat( $uid );
		$path = JPATH_ROOT.$config->get('webpath').DS.$dir;
		
		if ($profile->get('picture')) {
			$curthumb = $ih->createThumbName($profile->get('picture'));
			if (file_exists($path.DS.$curthumb)) {
				$thumb = $config->get('webpath').DS.$dir.DS.$curthumb;
			}
				
		}
	
		$thumb = $thumb ? $thumb : DS.'components'.DS.'com_members'.DS.'images'.DS.'profile_thumb.gif';
		
		return $thumb;					
	}
	
	//-----------
	
	public function view($database, $option, $member, $emp, $edittitle = 0, $editpref = 0) 
	{
		$out = '';
		$juser 	  =& JFactory::getUser();		
		
		// get job seeker info on the user
		$js = new JobSeeker ( $database );
		if(!$js->load($member->get('uidNumber'))) {
				
			// make a new entry
			$js = new JobSeeker ( $database );
			$js->uid = $member->get('uidNumber');
			$js->active = 0;
					 
			// check content
			if (!$js->check()) {
				echo $js->getError();
				exit();
			}
			
			// store new content
			if (!$js->store()) {
				echo $js->getError();
				exit();
			}
		}
		
		// Add styles and scripts
		ximport('xdocument');
		XDocument::addComponentStylesheet('com_jobs');
		
		$jt = new JobType ( $database );
		$jc = new JobCategory ( $database );
		
		// get active resume
		$resume = new Resume ($database);
		$file = '';
		$path = $this->build_path( $member->get('uidNumber') );
				
		if($resume->load($member->get('uidNumber'))) {
			$file = JPATH_ROOT.$path.DS.$resume->filename;
			if (!is_file($file)) { $file = ''; }		
		}
			
		$class1 = $js->active ? 'yes_search' : 'no_search';  // are we in search?
		$class2 = $file ? 'yes_resume' : 'no_resume'; // do we have resume?
		
		// get seeker stats
		$jobstats = new JobStats($database);
		$stats = $jobstats->getStats ($member->get('uidNumber'), 'seeker');			
	
		$out = '<div class="aside">'.n;
		if(!$emp) {
			$out .= t.t.'<p>'.JText::_('Our HUB offers a great opportunity for you to connect with potential employers. Start by uploading your recent resume and making your profile visible in employee search. ').'</p>'.n;
		}
		else {
			$out .= t.t.'<p>'.JText::_('Your are viewing this profile as an employer.').'</p>'.n;
		}
		$hd = JText::_('View Jobs');
		$hd .= isset($this->config->parameters['industry']) && $this->config->parameters['industry'] != '' ? ' '.JText::_('IN').' '.$this->config->parameters['industry'] : '';
		$out .= t.t.'<a href="'.JRoute::_('index.php?option=com_jobs').'" class="minimenu">'.$hd.'</a>'.n;
		if(!$emp && $js->active) {
			$out .= '<ul class="jobstats">'.n;
			$out .= '<li class="statstitle">'.JText::_('Your Resume Stats').'</li>'.n;
			$out .= '<li>';
			$out .= '<span>'.$stats['totalviewed'].'</span>'.n;
			$out .= JText::_('Total viewed').n;		
			$out .= '</li>'.n;
			$out .= '<li>';
			$out .= '<span>'.$stats['viewed_thismonth'].'</span>'.n;
			$out .= JText::_('Viewed in the past 30 days').n;	
			$out .= '</li>'.n;
			$out .= '<li>';
			$out .= '<span>'.$stats['viewed_thisweek'].'</span>'.n;
			$out .= JText::_('Viewed in the past 7 days').n;
			$out .= '</li>'.n;
			$out .= '<li>';
			$out .= '<span>'.$stats['viewed_today'].'</span>'.n;
			$out .= JText::_('Viewed in the past 24 hours').n;	
			$out .= '</li>'.n;
			$out .= '<li>';
			$out .= '<span>'.$stats['shortlisted'].'</span>'.n;
			$out .= JText::_('Profile shortlisted by').n;
			$out .= '</li>'.n;
			$out .= '</ul>'.n;
		}		
		$out .= '</div>'.n;
		$out .= '<div class="subject">'.n;
		if(!$emp && $file) {
			$out .= '<div id="prefs" class="'.$class1.'">'.n;
			$out .= ' <p>'.n;
			if($js->active && $file )  {
				$out .= JText::_('Your profile and resume are included in employee search.');
			}
			else if($file) {
				$out .= JText::_('Your profile and resume are not included in employee search.');
			}
			if(!$editpref) {
				$out .= ' <span class="includeme"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume'.a.'action=activate').a.'on=';
				if ($js->active && $file) {
				$out .= '0">[-] '.JText::_('Hide my profile');
				}
				else if ($file) { 
				$out .= '1">[+] '.JText::_('Include my profile');
				}
				$out .= '</a>.</span>'.n;
				$out .= ' </p>'.n;
			}
			else {
				$out .= ' </p>'.n;
				$out .= ' <form id="prefsForm" method="post" action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume').'" >'.n;
				$out .= t.t.'<fieldset>'.n;
				$out .= t.t.t.'<legend>'.n;
				$out .= $editpref==1 ? JText::_('Include my profile with the following information:') :  JText::_('Edit your job search preferences');
				$out .= t.t.t.'</legend>'.n;
				$out .= t.t.t.t.'<label class="spacious">'.n;	
				$out .= t.t.t.t.t.JText::_('Personal tagline:').n;
				$out .= t.t.t.t.t.'<span class="selectgroup">'.n;
				$out .= t.t.t.t.t.'<textarea name="tagline" id="tagline-men" rows="6" cols="35">'. stripslashes($js->tagline).'</textarea>'.n;
        		$out .= t.t.t.t.'<span class="counter"><span id="counter_number_tagline"></span> '.JText::_('chars left').'</span>'.n;
				$out .= t.t.t.t.t.'</span>'.n;
				$out .= t.t.t.t.'</label>'.n;	
				$out .= t.t.t.t.'<label class="spacious">'.n;	
				$out .= t.t.t.t.t.JText::_('Looking for...').n;	
				$out .= t.t.t.t.t.'<span class="selectgroup">'.n;
				$out .= t.t.t.t.t.'<textarea name="lookingfor" id="lookingfor-men" rows="6" cols="35">'.stripslashes($js->lookingfor).'</textarea>'.n;
				$out .= t.t.t.t.'<span class="counter"><span id="counter_number_lookingfor"></span> '.JText::_('chars left').'</span>'.n;
				$out .= t.t.t.t.t.'</span>'.n;
        		$out .= t.t.t.t.'</label>'.n;
				$out .= t.t.t.t.'<label>'.n;	
				$out .= t.t.t.t.t.JText::_('Website').n;	
				$out .= t.t.t.t.t.'<span class="selectgroup">'.n;
				$out .= t.t.t.t.t.'<input type="text" class="inputtxt" maxlength="190" name="url" value="';
				$out .= $js->url ? $js->url : $member->get('url');
				$out .= '" /> ';
				$out .= t.t.t.t.t.'</span>'.n;
        		$out .= t.t.t.t.'</label>'.n;
				$out .= t.t.t.t.'<label>'.n;	
				$out .= t.t.t.t.t.JText::_('LinkedIn URL').n;	
				$out .= t.t.t.t.t.'<span class="selectgroup">'.n;
				$out .= t.t.t.t.t.'<input type="text" class="inputtxt" maxlength="190" name="linkedin" value="'.$js->linkedin.'" /> ';
				$out .= t.t.t.t.t.'</span>'.n;
        		$out .= t.t.t.t.'</label>'.n;
				$out .= t.t.t.'<label class="cats">'.JText::_('Position sought').': '.n;
				$out .= t.t.t.'</label>'.n;
				
				// get job types			
				$types = $jt->getTypes();
				$types[0] = JText::_('Any type');
				
				// get job categories
				$cats = $jc->getCats();
				$cats[0] = JText::_('Any category');
				
				$out .= t.t.t.'<div class="selectgroup catssel">'.n;
				$out .= t.t.t.'<label>'.n;
				$out .= $this->formSelect('sought_type', $types, $js->sought_type, '', '');
				$out .= t.t.t.'</label>'.n;
				$out .= t.t.t.'<label>'.n;
				$out .= $this->formSelect('sought_cid', $cats, $js->sought_cid, '', '');
				$out .= t.t.t.'</label>'.n;
				$out .= t.t.t.'</div>'.n;	
				$out .= '<div class="clear"></div>'.n;	
				$out .= t.t.t.t.t.'<div class="submitblock">'.n;
				$out .= t.t.t.t.t.'<span class="selectgroup">'.n;	
				$out .= t.t.t.t.t.'<input type="submit" value="';
				$out .= $editpref==1 ? JText::_('Save and Include me in Employee Search') : JText::_('Save') ;
				$out .= '" /> <span class="cancelaction">';
				$out .= '<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume').'">';
				$out .= JText::_('CANCEL').'</a></span>'.n;
				$out .= t.t.t.t.t.'</span>'.n;
				$out .= t.t.t.t.t.'</div>'.n;
				$out .= t.t.t.'<input type="hidden" name="activeres" value="';
				$out .= $editpref==1 ? 1 : $js->active;
				$out .='" />'.n;
				$out .= t.t.t.'<input type="hidden" name="action" value="saveprefs" />'.n;
				$out .= t.t.' </fieldset>'.n;
				$out .= ' </form>'.n;
			}
				
			$out .='</div>'.n;
		}
		
		// seeker details block
		if($js->active && $file) {
			// get seeker info
			$seeker = $js->getSeeker($member->get('uidNumber'), $juser->get('id'));
			
			if(!$seeker or count($seeker)==0) {
				$out .= t.t.t.'<p class="error">'.JText::_('Error retreaving job seeker information.').'</p>'.n;	
				
			}
			else {
				$out .= $this->showSeeker( $seeker[0], $emp, 0, $option);
				//$out .= t.t.t.'<div class="clear"></div>'.n;
			}	
		}
		
		//if(($resume->id  && $file) && (!$emp or ($emp && $js->active)) ) {	
		if($resume->id  && $file && !$emp) {	
			$out .= t.'<table class="list">'.n;
			$out .= t.t.'<thead>'.n;
			$out .= t.t.t.'<tr>'.n;
			$out .= t.t.t.t.'<th class="col halfwidth">'.ucfirst(JText::_('Resume')).'</th>'.n;
			$out .= t.t.t.t.'<th class="col">'.JText::_('Last Updated').'</th>'.n;
			$out .= !$emp ? t.t.t.t.'<th scope="col">'.JText::_('Options').'</th>'.n : '';
			$out .= t.t.t.'</tr>'.n;
			$out .= t.t.'</thead>'.n;
			$out .= t.t.'<tbody>'.n;
			$out .= t.t.t.'<tr>'.n;
			$out .= t.t.t.t.'<td>';
			$title = $resume->title ?  stripslashes($resume->title) : $resume->filename;
			$default_title = $member->get('firstname') ? $member->get('firstname').' '.$member->get('lastname').' '.ucfirst(JText::_('Resume')) : $member->get('name').' '.ucfirst(JText::_('Resume'));
			if($edittitle && !$emp) {
				$out .= t.'<form id="editTitleForm" method="post" action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume'.a.'action=savetitle').'" >'.n;
				$out .= t.t.'<fieldset>'.n;
				$out .= t.t.t.t.'<label class="resume">'.n;	
				$out .= t.t.t.t.t.' <input type="text" name="title" value="'.$title.'" class="gettitle" maxlength="40" />'.n;
				$out .= t.t.t.t.t.'<input type="hidden" name="author" value="'.$member->get('uidNumber').'" />'.n;
				$out .= t.t.t.t.t.'<input type="submit" value="'.JText::_('Save').'" />'.n;	
				$out .= t.t.t.t.'</label>'.n;	
				$out .= t.t.'</fieldset>'.n;
				$out .= t.'</form>'.n;
			}
			else {
				$out .='<a class="resume" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume'.a.'action=download').'"> ';
				$out .= $title;
				$out .= '</a>';
				//$out .= ' <span class="filename">('.$resume->filename.')</span>';
			}
				
			$out .= '</td>'.n;
			$out .= t.t.t.t.'<td>'.JHTML::_('date',$resume->created, '%d %b %Y').'</td>'.n;
			//if(!$emp) {
			$out .= t.t.t.t.'<td><a class="trash" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume'.a.'action=deleteresume').'" title="'.JText::_('Delete this resume').'">'.JText::_('Delete').'</a> ';
			//$out .= '<a class="edittitle" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume'.a.'action=edittitle').'" title="'.JText::_('Edit resume title').'">'.JText::_('Edit title').'</a>';
			$out .= '</td>'.n;
			//}
			$out .= t.t.t.'</tr>'.n;
			$out .= t.t.'</tbody>'.n;
			$out .= t.'</table>'.n;
		}
		else if(!$js->active) {
			$out .= '<p class="no_resume">';
			$out .= ($emp) ? JText::_('The user has no active resume on file.') : JText::_('You have no resume on file. Please upload one using the form below.');
			$out .='</p>'.n;
		}
		
		if(!$emp) {
			$out .= ' <form class="addResumeForm" method="post" action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume').'" enctype="multipart/form-data">'.n;
			$out .= t.t.'<fieldset>'.n;
			$out .= t.t.t.'<legend>'.n;
			$out .= ($resume->id && $file) ? JText::_('Upload a New Resume').' <span>('.JText::_('existing resume will be replaced').')</span>'.n :  JText::_('Upload a Resume').n;
			$out .= t.t.t.'</legend>'.n;
			$out .= t.t.t.'<div>'.n;			
			$out .= t.t.t.t.'<label>'.n;			
			$out .= t.t.t.t.t.JText::_('Attach file').n;	
			$out .= t.t.t.t.t.'<input type="file" name="uploadres" id="uploadres" />'.n;					
			$out .= t.t.t.t.'</label>'.n;	
			//$out .= t.t.t.t.'<label>'.n;	
			//$out .= t.t.t.t.t.JText::_('Resume Title:').n;	
			//$out .= t.t.t.t.t.' <input type="text" name="title" value="" class="gettitle" />'.n;	
			//$out .= t.t.t.t.'</label>'.n;	
			$out .= t.t.t.'</div>'.n;
			$out .= t.t.t.'<input type="hidden" name="action" value="uploadresume" />'.n;
			$out .= t.t.t.'<input type="hidden" name="path" value="'.$path.'" />'.n;
			$out .= t.t.t.'<input type="hidden" name="emp" value="'.$emp.'" />'.n;
			$out .= t.t.t.'<input type="submit" value="'.JText::_('Upload').'" />'.n;
			$out .= t.t.'</fieldset>'.n;
			$out .= '</form>'.n;
		}
		
		$out .= '</div>'.n;		
		return $out;		
	}
	
	//-----------
	
	function showSeeker( $seeker, $emp, $admin, $option, $list=0) 
	{
		$database =& JFactory::getDBO();
		$jt = new JobType ( $database );
		$jc = new JobCategory ( $database );
		
		$out = '';
			
		$thumb = $this->getThumb($seeker->uid);
		$jobtype = $jt->getType($seeker->sought_type, JText::_('any type') );
		$jobcat = $jc->getCat($seeker->sought_cid, JText::_('any category'));
		
		//$title = $seeker->title ?  $seeker->title : $seeker->filename;
		$title = JText::_('Download').' '.$seeker->name.' '.ucfirst(JText::_('Resume'));
		
		$path = $this->build_path( $seeker->uid );
				
		$resume = is_file(JPATH_ROOT.$path.DS.$seeker->filename) ? $path.DS.$seeker->filename : '';
		
		// write info about job search
		$out .= '<div class="aboutme';
		$out .= $seeker->mine && $list ? ' mine' : '';
		$out .= isset($seeker->shortlisted) && $seeker->shortlisted ? ' shortlisted' : '';
		$out .= '">'.n;
		$out .= t.'<div class="thumb"><img src="'.$thumb.'" alt="'.$seeker->name.'" /></div>'.n;
		$out .= t.'<div class="aboutlb">';
		$out .= $list ? '<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$seeker->uid.a.'active=resume').'" class="profilelink">' : '';
		$out .= $seeker->name;
		$out .= $list ? '</a>' : '';
		//$out .= $seeker->countryresident ? ', '.htmlentities(getcountry($seeker->countryresident)).n : ''.n;
		$out .= $seeker->countryresident ? ', <span class="wherefrom">'.htmlentities($seeker->countryresident).'</span>'.n : ''.n;
		$out .= t.'<blockquote> <p>'.stripslashes($seeker->tagline).'</p></blockquote>'.n;
		//if($emp or $admin) {
			// show resume link & status
			/*
			$out .= t.'<span class="abouttext">';
			$out .= $resume ? '<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$seeker->uid.a.'active=resume'.a.'action=download').'" class="resume" title="'.$title.'">'.JText::_('Download Resume').'</a> <span class="mini">'.JText::_('Last update').': '.$this->nicetime($seeker->created).'</span>' : '<span class="unavail">'.JText::_('Download Resume').'</span>';
			$out .= '</span>'.n;
			*/
		//}
		$out .= t.'</div>'.n;
		$out .= t.'<div class="lookingforlb">'.JText::_('Looking for...').n;
		$out .= t.'<span class="jobprefs">';
		$out .= $jobtype ? $jobtype : ' ';
		$out .= $jobcat ? ' &bull; '.$jobcat : '';
		$out .= '</span>'.n;
		$out .= t.'<span class="abouttext">'.stripslashes($seeker->lookingfor).'</span></div>'.n;
	
		if($seeker->mine) {
			$out .= t.'<span class="editbt"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$seeker->uid.a.'active=resume'.a.'action=editprefs').'" title="'.JText::_('Edit my profile').'">&nbsp;</a></span>'.n;
		}
		else if($emp or $admin) {
			$out .= t.'<span id ="o'.$seeker->uid.'"><a href="';
			$out .= JRoute::_('index.php?option=com_jobs'.a.'oid='.$seeker->uid.a.'task=shortlist').'" class="favvit" title="';
			$out .= isset($seeker->shortlisted) && $seeker->shortlisted ? JText::_('Remove from shortlist') : JText::_('Add to shortlist');
			$out .='" >';
			$out .= isset($seeker->shortlisted) && $seeker->shortlisted ? JText::_('Remove from shortlist') : JText::_('Add to shortlist');
			$out .='</a></span>'.n;
		}
		
			/*
			$out .= t.'<span class="abouttext sticktobot">';
			$out .= $seeker->url ? '<a href="'.$seeker->url.'" class="web" title="'.JText::_('Member website').'">'.$seeker->url.'</a>' : '';
			$out .= '</span>'.n;
			*/
		$out .= t.'<div class="clear leftclear"></div>'.n;	
		$out .= t.'<span class="indented">';
		if($resume) {
			$out .= '<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$seeker->uid.a.'active=resume'.a.'action=download').'" class="resume getit" title="'.$title.'">'.ucfirst(JText::_('Resume')).'</a> <span class="mini">'.JText::_('Last update').': '.$this->nicetime($seeker->created).'</span>  '.n;
			//$out .= $seeker->url ? '<a href="'.$seeker->url.'" class="web" title="'.JText::_('Member website').'">'.$seeker->url.'</a>' : '';
			$out .= $seeker->url ? '<span class="mini"> | </span> <span class="mini"><a href="'.$seeker->url.'" class="web" rel="external" title="'.JText::_('Member website').': '.$seeker->url.'">'.JText::_('Website').'</a></span>' : '';
			$out .= $seeker->linkedin ? '<span class="mini"> | </span> <span class="mini"><a href="'.$seeker->linkedin.'" class="linkedin" rel="external" title="'.JText::_('Member LinkedIn Profile').'">'.JText::_('LinkedIn').'</a></span>' : '';
		}
		else {
			$out .- '<span class="unavail">'.JText::_('Download Resume').'</span>'.n;
		}
		
		$out .= '</span>'.n;
		$out .='</div>'.n;
				
		return $out;	
	}
	
	//-----------
	
	public function build_path( $uid ) 
	{		
		// Get the configured upload path
		$base_path = $this->_params->get('webpath');
		if ($base_path) {
			// Make sure the path doesn't end with a slash
			if (substr($base_path, -1) == DS) { 
				$base_path = substr($base_path, 0, strlen($base_path) - 1);
			}
			// Ensure the path starts with a slash
			if (substr($base_path, 0, 1) != DS) { 
				$base_path = DS.$base_path;
			}
		}
		else {
			$base_path = DS.'site'.DS.'members';
		}
		
		ximport('fileuploadutils');
		$dir  = FileUploadUtils::niceidformat( $uid );
		
		$listdir = $base_path.DS.$dir;
		
		if (!is_dir(JPATH_ROOT.$listdir)) {
				jimport('joomla.filesystem.folder');
				if (!JFolder::create( JPATH_ROOT.$listdir, 0777 )) {
					//$out .= JText::_('ERR_UNABLE_TO_CREATE_PATH');
					return false;
				}
		}
		
		// Build the path
		return $listdir;
	}

	//----------------------------------------------------------
	// media manager
	//----------------------------------------------------------

	public function upload( $database, $option, $member)
	{		
		$path = JRequest::getVar( 'path', '');
		$emp = JRequest::getInt( 'emp', 0);
		
		if (!$path) {
			$this->setError( JText::_('SUPPORT_NO_UPLOAD_DIRECTORY') );
			return '';
		}
		
		// Incoming file
		$file = JRequest::getVar( 'uploadres', '', 'files', 'array' );	
		
		if (!$file['name']) {
			$this->setError( JText::_('SUPPORT_NO_FILE') );
			return '';
		}
		
		// Incoming
		$title = JRequest::getVar( 'title', '' );
		$default_title = $member->get('firstname') ? $member->get('firstname').' '.$member->get('lastname').' '.ucfirst(JText::_('Resume')) : $member->get('name').' '.ucfirst(JText::_('Resume'));
		$path = JPATH_ROOT.$path;
		
		// Replace file title with user name		
		$file_ext      = substr($file['name'], strripos($file['name'], '.'));
		$file['name'] = $member->get('firstname') ? $member->get('firstname').' '.$member->get('lastname').' '.ucfirst(JText::_('Resume')) : $member->get('name').' '.ucfirst(JText::_('Resume'));
		$file['name'] .= $file_ext;
		
		
		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);
		
		$row = new Resume( $database );
			
		if(!$row->load($member->get('uidNumber'))) {
				$row = new Resume( $database );
				$row->id = 0;		
				$row->uid = $member->get('uidNumber');	
				$row->main = 1;	
		}
		else if (file_exists($path.DS.$row->filename)) // remove prev file first
		{
			JFile::delete($path.DS.$row->filename);
			
			// Remove stats for prev resume
			$jobstats = new JobStats($database);
			$jobstats->deleteStats ($member->get('uidNumber'), 'seeker');
		}
		
		// Perform the upload
		if (!JFile::upload($file['tmp_name'], $path.DS.$file['name'])) {
			$this->setError( JText::_('ERROR_UPLOADING') );
			return '';
		} else {
			// File was uploaded
					
			// Create database entry			
			$title = htmlspecialchars($title);
			$row->created = date( 'Y-m-d H:i:s', time() );	
			$row->filename = $file['name'];
			$row->title = $title ? $title : $default_title ;
			
			if (!$row->check()) {
				$this->setError( $row->getError() );
			}
			if (!$row->store()) {
				$this->setError( $row->getError() );
			}
	
			return $this->view($database, $option, $member, $emp);
		}
	}
	
	//-----------

	protected function deleteresume($database, $option, $member, $emp)
	{		
		$row = new Resume( $database );
		if(!$row->load($member->get('uidNumber'))) {
			$this->setError( JText::_('Resume ID not found.') );
			return '';
		}
		
		// Incoming file
		$file = $row->filename;
		
		$path = $this->build_path( $member->get('uidNumber') );
		
		if (!file_exists(JPATH_ROOT.$path.DS.$file) or !$file) { 
			$this->setError( JText::_('FILE_NOT_FOUND') ); 
			return '';
		} else {
			
			// Attempt to delete the file
			jimport('joomla.filesystem.file');
			if (!JFile::delete(JPATH_ROOT.$path.DS.$file)) {
				$this->setError( JText::_('UNABLE_TO_DELETE_FILE') );
				return '';
			}
			
			$row->delete();
			
			// Remove stats for prev resume
			$jobstats = new JobStats($database);
			$jobstats->deleteStats ($member->get('uidNumber'), 'seeker');

			// Do not include profile in search without a resume
			$js = new JobSeeker ( $database );
			$js->load( $member->get('uidNumber') );
			$js->bind( array('active'=>0) );
			if (!$js->store()) {
				$this->setError( $js->getError() );
			}
			else {
				// Push through to the main view
				return $this->view($database, $option, $member, $emp);
			}			
		}	
	}
	//-----------
	function onMembersShortlist( ) 
	{
		$oid = JRequest::getInt( 'oid', 0 );
		
		if ($oid) {
			$this->shortlist( $oid, $ajax=1 );
		}
	}
	//-----------
	
	function shortlist( $oid, $ajax=0 ) 
	{
		$juser =& JFactory::getUser();
		if (!$juser->get('guest')) {

			$database =& JFactory::getDBO();

			$shortlist = new Shortlist( $database );
			$shortlist->loadEntry( $juser->get('id'), $oid, 'resume' );

			if (!$shortlist->id) {
				$shortlist->emp = $juser->get('id');
				$shortlist->seeker = $oid;
				$shortlist->added = date( 'Y-m-d H:i:s');
				$shortlist->category = 'resume';
				$shortlist->check();
				$shortlist->store();
				
				
			} else {
				$shortlist->delete();
				
			}
			
			if($ajax) {
			
				// get seeker info
				$js = new JobSeeker ( $database );
				$seeker = $js->getSeeker($oid, $juser->get('id'));
				//$emp = $this->isEmployer();
				echo $this->showSeeker( $seeker[0], 1, 0, 'com_members', 1) ;
			
			}		
		}
	}
		
	//-----------
	
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}	
	
	//-----------

	public function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="'.$name.'" id="'.$name.'"';
		$out .= ($class) ? ' class="'.$class.'">'.n : '>'.n;
		foreach ($array as $avalue => $alabel) 
		{
		 	$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected" '
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'.n;
		}
		$out .= '</select>'.n;
		return $out;
	}
		
	//-----------
	
	public function nicetime($date)
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
	
	//-----------

	protected function download($member)
	{
		// Get some needed libraries
		ximport('xserver');

		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();

		// Ensure we have a database object
		if (!$database) {
			JError::raiseError( 500, JText::_('DATABASE_NOT_FOUND') );
			return;
		}
		
		// Incoming
		$uid   = $member->get('uidNumber');
		
		// Load the resume
		$resume = new Resume ($database);
		$file = '';
		$path = $this->build_path( $uid );
				
		if($resume->load($uid)) {
			$file = JPATH_ROOT.$path.DS.$resume->filename;	
		}
		
		if(!is_file($file)) {
			JError::raiseError( 404, JText::_('FILE_NOT_FOUND') );
			return;
		}	
		
		// Use user name as file name
		$default_title = $member->get('firstname') ? $member->get('firstname').' '.$member->get('lastname').' '.ucfirst(JText::_('Resume')) : $member->get('name').' '.ucfirst(JText::_('Resume'));
		$default_title .= substr($resume->filename, strripos($resume->filename, '.'));;	
		
		// Initiate a new content server and serve up the file
		$xserver = new XContentServer();
		$xserver->filename($file);
		
		// record view
		$stats = new JobStats($database);
		if($juser->get('id') != $uid ) {
			$stats->saveView ($uid, 'seeker');
		}
		
		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support
		$xserver->saveas(stripslashes($resume->title));
		$xserver->serve_attachment($file, stripslashes($default_title), false); // @TODO fix byte range support
		$xserver->serve();	

		// Should only get here on error
		JError::raiseError( 404, JText::_('SERVER_ERROR') );
		return;
	}
		
	//-------------------
}