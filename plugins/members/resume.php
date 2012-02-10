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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

//-----------
jimport( 'joomla.plugin.plugin' );
JPlugin::loadLanguage( 'plg_members_resume' );
JPlugin::loadLanguage( 'com_jobs' );

/**
 * Short description for 'plgMembersResume'
 * 
 * Long description (if any) ...
 */
class plgMembersResume extends JPlugin
{

	/**
	 * Short description for 'plgMembersResume'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$subject Parameter description (if any) ...
	 * @param      unknown $config Parameter description (if any) ...
	 * @return     void
	 */
	public function plgMembersResume(&$subject, $config)
	{
		parent::__construct($subject, $config);

		// Load plugin parameters
		$this->_plugin = JPluginHelper::getPlugin( 'members', 'resume' );
		$this->_params = new JParameter( $this->_plugin->params );

		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'admin.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'application.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'category.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'employer.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'job.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'prefs.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'resume.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'seeker.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'shortlist.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'stats.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_jobs'.DS.'tables'.DS.'type.php' );

		$config =& JComponentHelper::getParams( 'com_jobs' );
		$this->config = $config;
	}

	/**
	 * Short description for 'onMembersAreas'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function &onMembersAreas( $user, $member )
	{
		//default areas returned to nothing
		$areas = array();
		
		//if this is the logged in user show them
		if($user->get("id") == $member->get("uidNumber") || $this->isEmployer())
		{
			$areas['resume'] = ucfirst(JText::_('Resume'));
		}
		
		return $areas;
	}

	/**
	 * Short description for 'isEmployer'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $member Parameter description (if any) ...
	 * @param      string $authorized Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function isEmployer( $user='', $member='')
	{
		$juser 	  =& JFactory::getUser();
		$database =& JFactory::getDBO();
		$employer = new Employer( $database );

		// determine who is veiwing the page
		$emp = 0;
		$emp = $employer->isEmployer($juser->get('id'));

		// check if they belong to a dedicated admin group
		if ($this->config->get('admingroup')) {
			ximport('Hubzero_User_Profile');

			//$ugs = Hubzero_User_Helper::getGroups( $juser->get('id') );
			$profile = Hubzero_User_Profile::getInstance($juser->get('id'));
			$ugs = $profile->getGroups('all');
			if ($ugs && count($ugs) > 0) {
				foreach ($ugs as $ug)
				{
					if ($ug->cn == $this->config->get('admingroup')) {
						$emp = 1;
					}
				}
			}
		}

		//if ($authorized) {
		//	$emp = 1;
		//}

		if ($member) {
			$my =  $member->get('uidNumber') == $juser->get('id') ? 1 : 0;
			$emp = $my && $emp ? 0 : $emp;
		}

		return $emp;
	}

	/**
	 * Short description for 'isAdmin'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $admin Parameter description (if any) ...
	 * @return     integer Return description (if any) ...
	 */
	public function isAdmin($admin = 0)
	{
		$juser =& JFactory::getUser();

		// check if they belong to a dedicated admin group
		if ($this->config->get('admingroup')) {
			ximport('Hubzero_User_Profile');

			//$ugs = Hubzero_User_Helper::getGroups( $juser->get('id') );
			$profile = Hubzero_User_Profile::getInstance($juser->get('id'));
			$ugs = $profile->getGroups('all');
			if ($ugs && count($ugs) > 0) {
				foreach ($ugs as $ug) {
					if ($ug->cn == $this->config->get('admingroup')) {
						$admin = 1;
					}
				}
			}
		}

		return $admin;
	}

	/**
	 * Short description for 'onMembers'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $member Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @param      unknown $authorized Parameter description (if any) ...
	 * @param      array $areas Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function onMembers( $user, $member, $option, $areas )
	{
		$return = 'html';
		$active = 'resume';

		$database =& JFactory::getDBO();
		$juser 	  =& JFactory::getUser();

		// Check if our area is in the array of areas we want to return results for
		if (is_array( $areas )) {
			if (!array_intersect( $areas, $this->onMembersAreas($user, $member) )
			&& !array_intersect( $areas, array_keys( $this->onMembersAreas($user, $member) ) )) {
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

		// Jobs component needs to be enabled
		if (!$this->config->get('component_enabled')) {
			$arr['html'] = '<p class="warning">'.JText::_('PLG_RESUME_WARNING_DISABLED').'</p>';
			return $arr;
		}

		// Get authorization
		$emp = $this->isEmployer($user, $member);

		// Are we returning HTML?
		if ($return == 'html'  && $areas[0] == 'resume') {
			$task = JRequest::getVar('action','');

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
		} else if ($emp) {
			//$arr['metadata'] = '<p class="resume"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume').'">'.ucfirst(JText::_('Resume')).'</a></p>'.n;
			$arr['metadata'] = "";
		}

		return $arr;
	}

	/**
	 * Short description for 'save'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $database Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      object $member Parameter description (if any) ...
	 * @param      string $task Parameter description (if any) ...
	 * @param      unknown $emp Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function save($database, $option, $member, $task, $emp)
	{
		$lookingfor = JRequest::getVar('lookingfor','');
		$tagline = JRequest::getVar('tagline','');
		$active = JRequest::getInt('activeres', 0);
		$author = JRequest::getInt('author', 0);
		$title = JRequest::getVar('title','');

		if ($task=='saveprefs') {
			$js = new JobSeeker ( $database );

			if (!$js->load($member->get('uidNumber'))) {
				$this->setError( JText::_('PLG_RESUME_ERROR_PROFILE_NOT_FOUND') );
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
		else if ($task=='savetitle' && $author && $title) {
			$resume = new Resume ( $database );
			if ($resume->load($author)) {
				$resume->title = $title;
				if (!$resume->store()) {
					echo $this->alert( $resume->getError() );
					exit();
				}
			}
		}

		return $this->view($database, $option, $member, $emp);
	}

	/**
	 * Short description for 'activate'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $database Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      object $member Parameter description (if any) ...
	 * @param      unknown $emp Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function activate($database, $option, $member, $emp)
	{
		// are we activating or disactivating?
		$active = JRequest::getInt('on', 0);

		$js = new JobSeeker ( $database );

		if (!$js->load($member->get('uidNumber'))) {
			$this->setError( JText::_('PLG_RESUME_ERROR_PROFILE_NOT_FOUND') );
			return '';
		} else if (!$active) {
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

	/**
	 * Short description for 'getThumb'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getThumb($uid)
	{
		// do we have a thumb image for the user?
		require_once( JPATH_ROOT.DS.'components'.DS.'com_members'.DS.'helpers'.DS.'imghandler.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'profile.php' );
		include_once( JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_members'.DS.'tables'.DS.'association.php' );

		$profile = new Hubzero_User_Profile();
		$profile->load( $uid );
		$thumb = '';

		$config =& JComponentHelper::getParams( 'com_members' );
		$ih = new MembersImgHandler();
		ximport('Hubzero_View_Helper_Html');
		$dir = Hubzero_View_Helper_Html::niceidformat( $uid );
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

	/**
	 * Short description for 'view'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $database Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @param      mixed $member Parameter description (if any) ...
	 * @param      string $emp Parameter description (if any) ...
	 * @param      integer $edittitle Parameter description (if any) ...
	 * @param      mixed $editpref Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function view($database, $option, $member, $emp, $edittitle = 0, $editpref = 0)
	{
		$out = '';
		$juser =& JFactory::getUser();

		// get job seeker info on the user
		$js = new JobSeeker ( $database );
		if (!$js->load($member->get('uidNumber'))) {

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
		ximport('Hubzero_Document');
		Hubzero_Document::addComponentStylesheet('com_jobs');

		$jt = new JobType( $database );
		$jc = new JobCategory( $database );

		// get active resume
		$resume = new Resume($database);
		$file = '';
		$path = $this->build_path( $member->get('uidNumber') );

		if ($resume->load($member->get('uidNumber'))) {
			$file = JPATH_ROOT.$path.DS.$resume->filename;
			if (!is_file($file)) { $file = ''; }
		}

		$class1 = $js->active ? 'yes_search' : 'no_search';  // are we in search?
		$class2 = $file ? 'yes_resume' : 'no_resume'; // do we have resume?

		// get seeker stats
		$jobstats = new JobStats($database);
		$stats = $jobstats->getStats ($member->get('uidNumber'), 'seeker');

		$out = '<div class="aside">'.n;
		if (!$emp) {
			$out .= t.t.'<p>'.JText::_('PLG_RESUME_HUB_OFFERS').'</p>'.n;
		}
		else {
			$out .= t.t.'<p>'.JText::_('PLG_RESUME_NOTICE_YOU_ARE_EMPLOYER').'</p>'.n;
		}
		$hd = JText::_('View Jobs');
		$hd .= $this->config->get('industry') ? ' '.JText::_('IN').' '.$this->config->get('industry') : '';
		$out .= t.t.'<a href="'.JRoute::_('index.php?option=com_jobs').'" class="minimenu">'.$hd.'</a>'.n;
		if (!$emp && $js->active) {
			$out .= '<ul class="jobstats">'.n;
			$out .= '<li class="statstitle">'.JText::_('PLG_RESUME_YOUR_STATS').'</li>'.n;
			$out .= '<li>';
			$out .= '<span>'.$stats['totalviewed'].'</span>'.n;
			$out .= JText::_('PLG_RESUME_TOTAL_VIEWED').n;
			$out .= '</li>'.n;
			$out .= '<li>';
			$out .= '<span>'.$stats['viewed_thismonth'].'</span>'.n;
			$out .= JText::_('PLG_RESUME_VIEWED_PAST_30_DAYS').n;
			$out .= '</li>'.n;
			$out .= '<li>';
			$out .= '<span>'.$stats['viewed_thisweek'].'</span>'.n;
			$out .= JText::_('PLG_RESUME_VIEWED_PAST_7_DAYS').n;
			$out .= '</li>'.n;
			$out .= '<li>';
			$out .= '<span>'.$stats['viewed_today'].'</span>'.n;
			$out .= JText::_('PLG_RESUME_VIEWED_PAST_24_HOURS').n;
			$out .= '</li>'.n;
			$out .= '<li>';
			$out .= '<span>'.$stats['shortlisted'].'</span>'.n;
			$out .= JText::_('PLG_RESUME_PROFILE_SHORTLISTED').n;
			$out .= '</li>'.n;
			$out .= '</ul>'.n;
		}
		$out .= '</div>'.n;
		$out .= '<div class="subject">'.n;
		if (!$emp && $file) {
			$out .= '<div id="prefs" class="'.$class1.'">'.n;
			$out .= ' <p>'.n;
			if ($js->active && $file )  {
				$out .= JText::_('PLG_RESUME_PROFILE_INCLUDED');
			}
			else if ($file) {
				$out .= JText::_('PLG_RESUME_PROFILE_NOT_INCLUDED');
			}
			if (!$editpref) {
				$out .= ' <span class="includeme"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume'.a.'action=activate').a.'on=';
				if ($js->active && $file) {
				$out .= '0">[-] '.JText::_('PLG_RESUME_ACTION_HIDE');
				}
				else if ($file) {
				$out .= '1">[+] '.JText::_('PLG_RESUME_ACTION_INCLUDE');
				}
				$out .= '</a>.</span>'.n;
				$out .= ' </p>'.n;
			}
			else {
				$out .= ' </p>'.n;
				$out .= ' <form id="prefsForm" method="post" action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume').'" >'.n;
				$out .= t.t.'<fieldset>'.n;
				$out .= t.t.t.'<legend>'.n;
				$out .= $editpref==1 ? JText::_('PLG_RESUME_ACTION_INCLUDE_WITH_INFO') :  JText::_('PLG_RESUME_ACTION_EDIT_PREFS');
				$out .= t.t.t.'</legend>'.n;
				$out .= t.t.t.t.'<label class="spacious">'.n;
				$out .= t.t.t.t.t.JText::_('PLG_RESUME_PERSONAL_TAGLINE').n;
				$out .= t.t.t.t.t.'<span class="selectgroup">'.n;
				$out .= t.t.t.t.t.'<textarea name="tagline" id="tagline-men" rows="6" cols="35">'. stripslashes($js->tagline).'</textarea>'.n;
        		$out .= t.t.t.t.'<span class="counter"><span id="counter_number_tagline"></span> '.JText::_('chars left').'</span>'.n;
				$out .= t.t.t.t.t.'</span>'.n;
				$out .= t.t.t.t.'</label>'.n;
				$out .= t.t.t.t.'<label class="spacious">'.n;
				$out .= t.t.t.t.t.JText::_('PLG_RESUME_LOOKING_FOR').n;
				$out .= t.t.t.t.t.'<span class="selectgroup">'.n;
				$out .= t.t.t.t.t.'<textarea name="lookingfor" id="lookingfor-men" rows="6" cols="35">'.stripslashes($js->lookingfor).'</textarea>'.n;
				$out .= t.t.t.t.'<span class="counter"><span id="counter_number_lookingfor"></span> '.JText::_('PLG_RESUME_CHARS_LEFT').'</span>'.n;
				$out .= t.t.t.t.t.'</span>'.n;
        		$out .= t.t.t.t.'</label>'.n;
				$out .= t.t.t.t.'<label>'.n;
				$out .= t.t.t.t.t.JText::_('PLG_RESUME_WEBSITE').n;
				$out .= t.t.t.t.t.'<span class="selectgroup">'.n;
				$out .= t.t.t.t.t.'<input type="text" class="inputtxt" maxlength="190" name="url" value="';
				$out .= $js->url ? $js->url : $member->get('url');
				$out .= '" /> ';
				$out .= t.t.t.t.t.'</span>'.n;
        		$out .= t.t.t.t.'</label>'.n;
				$out .= t.t.t.t.'<label>'.n;
				$out .= t.t.t.t.t.JText::_('PLG_RESUME_LINKEDIN').n;
				$out .= t.t.t.t.t.'<span class="selectgroup">'.n;
				$out .= t.t.t.t.t.'<input type="text" class="inputtxt" maxlength="190" name="linkedin" value="'.$js->linkedin.'" /> ';
				$out .= t.t.t.t.t.'</span>'.n;
        		$out .= t.t.t.t.'</label>'.n;
				$out .= t.t.t.'<label class="cats">'.JText::_('PLG_RESUME_POSITION_SOUGHT').': '.n;
				$out .= t.t.t.'</label>'.n;

				// get job types			
				$types = $jt->getTypes();
				$types[0] = JText::_('TYPE_ANY');

				// get job categories
				$cats = $jc->getCats();
				$cats[0] = JText::_('CATEGORY_ANY');

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
				$out .= $editpref==1 ? JText::_('ACTION_SAVE_AND_INCLUDE') : JText::_('ACTION_SAVE') ;
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
		if ($js->active && $file) {
			// get seeker info
			$seeker = $js->getSeeker($member->get('uidNumber'), $juser->get('id'));

			if (!$seeker or count($seeker)==0) {
				$out .= t.t.t.'<p class="error">'.JText::_('PLG_RESUME_ERROR_RETRIEVING_PROFILE').'</p>'.n;

			}
			else {
				$out .= $this->showSeeker( $seeker[0], $emp, 0, $option);
			}
		}

		//if (($resume->id  && $file) && (!$emp or ($emp && $js->active)) ) {	
		if ($resume->id  && $file && !$emp) {
			$out .= t.'<table class="list">'.n;
			$out .= t.t.'<thead>'.n;
			$out .= t.t.t.'<tr>'.n;
			$out .= t.t.t.t.'<th class="col halfwidth">'.ucfirst(JText::_('PLG_RESUME_RESUME')).'</th>'.n;
			$out .= t.t.t.t.'<th class="col">'.JText::_('PLG_RESUME_LAST_UPDATED').'</th>'.n;
			$out .= !$emp ? t.t.t.t.'<th scope="col">'.JText::_('PLG_RESUME_OPTIONS').'</th>'.n : '';
			$out .= t.t.t.'</tr>'.n;
			$out .= t.t.'</thead>'.n;
			$out .= t.t.'<tbody>'.n;
			$out .= t.t.t.'<tr>'.n;
			$out .= t.t.t.t.'<td>';
			$title = $resume->title ?  stripslashes($resume->title) : $resume->filename;
			$default_title = $member->get('firstname') ? $member->get('firstname').' '.$member->get('lastname').' '.ucfirst(JText::_('Resume')) : $member->get('name').' '.ucfirst(JText::_('Resume'));
			if ($edittitle && !$emp) {
				$out .= t.'<form id="editTitleForm" method="post" action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume'.a.'action=savetitle').'" >'.n;
				$out .= t.t.'<fieldset>'.n;
				$out .= t.t.t.t.'<label class="resume">'.n;
				$out .= t.t.t.t.t.' <input type="text" name="title" value="'.$title.'" class="gettitle" maxlength="40" />'.n;
				$out .= t.t.t.t.t.'<input type="hidden" name="author" value="'.$member->get('uidNumber').'" />'.n;
				$out .= t.t.t.t.t.'<input type="submit" value="'.JText::_('ACTION_SAVE').'" />'.n;
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
			$out .= t.t.t.t.'<td><a class="trash" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume'.a.'action=deleteresume').'" title="'.JText::_('ACTION_DELETE_THIS_RESUME').'">'.JText::_('ACTION_DELETE').'</a> ';
			//$out .= '<a class="edittitle" href="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume'.a.'action=edittitle').'" title="'.JText::_('Edit resume title').'">'.JText::_('Edit title').'</a>';
			$out .= '</td>'.n;
			//}
			$out .= t.t.t.'</tr>'.n;
			$out .= t.t.'</tbody>'.n;
			$out .= t.'</table>'.n;
		}
		else if (!$js->active) {
			$out .= '<p class="no_resume">';
			$out .= ($emp) ? JText::_('PLG_RESUME_USER_HAS_NO_RESUME') : JText::_('PLG_RESUME_YOU_HAVE_NO_RESUME');
			$out .='</p>'.n;
		}

		if (!$emp) {
			$out .= ' <form class="addResumeForm" method="post" action="'.JRoute::_('index.php?option='.$option.a.'id='.$member->get('uidNumber').a.'active=resume').'" enctype="multipart/form-data">'.n;
			$out .= t.t.'<fieldset>'.n;
			$out .= t.t.t.'<legend>'.n;
			$out .= ($resume->id && $file) ? JText::_('ACTION_UPLOAD_NEW_RESUME').' <span>('.JText::_('PLS_RESUME_WILL_BE_REPLACED').')</span>'.n :  JText::_('ACTION_UPLOAD_A_RESUME').n;
			$out .= t.t.t.'</legend>'.n;
			$out .= t.t.t.'<div>'.n;
			$out .= t.t.t.t.'<label>'.n;
			$out .= t.t.t.t.t.JText::_('ACTION_ATTACH_FILE').n;
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
			$out .= t.t.t.'<input type="submit" value="'.JText::_('ACTION_UPLOAD').'" />'.n;
			$out .= t.t.'</fieldset>'.n;
			$out .= '</form>'.n;
		}

		$out .= '</div>'.n;
		return $out;
	}

	/**
	 * Short description for 'showSeeker'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $seeker Parameter description (if any) ...
	 * @param      unknown $emp Parameter description (if any) ...
	 * @param      unknown $admin Parameter description (if any) ...
	 * @param      string $option Parameter description (if any) ...
	 * @param      mixed $list Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	function showSeeker( $seeker, $emp, $admin, $option, $list=0)
	{
		$database =& JFactory::getDBO();
		$jt = new JobType( $database );
		$jc = new JobCategory( $database );

		$out = '';

		$thumb = $this->getThumb($seeker->uid);
		$jobtype = $jt->getType($seeker->sought_type, strtolower(JText::_('TYPE_ANY')) );
		$jobcat = $jc->getCat($seeker->sought_cid, strtolower(JText::_('CATEGORY_ANY')) );

		//$title = $seeker->title ?  $seeker->title : $seeker->filename;
		$title = JText::_('ACTION_DOWNLOAD').' '.$seeker->name.' '.ucfirst(JText::_('PLG_RESUME_RESUME'));

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
		$out .= t.'<div class="lookingforlb">'.JText::_('PLG_RESUME_LOOKING_FOR').n;
		$out .= t.'<span class="jobprefs">';
		$out .= $jobtype ? $jobtype : ' ';
		$out .= $jobcat ? ' &bull; '.$jobcat : '';
		$out .= '</span>'.n;
		$out .= t.'<span class="abouttext">'.stripslashes($seeker->lookingfor).'</span></div>'.n;

		if ($seeker->mine) {
			$out .= t.'<span class="editbt"><a href="'.JRoute::_('index.php?option='.$option.a.'id='.$seeker->uid.a.'active=resume'.a.'action=editprefs').'" title="'.JText::_('ACTION_EDIT_MY_PROFILE').'">&nbsp;</a></span>'.n;
		}
		else if ($emp or $admin) {
			$out .= t.'<span id ="o'.$seeker->uid.'"><a href="';
			$out .= JRoute::_('index.php?option=com_jobs'.a.'oid='.$seeker->uid.a.'task=shortlist').'" class="favvit" title="';
			$out .= isset($seeker->shortlisted) && $seeker->shortlisted ? JText::_('ACTION_REMOVE_FROM_SHORTLIST') : JText::_('ACTION_ADD_TO_SHORTLIST');
			$out .='" >';
			$out .= isset($seeker->shortlisted) && $seeker->shortlisted ? JText::_('ACTION_REMOVE_FROM_SHORTLIST') : JText::_('ACTION_ADD_TO_SHORTLIST');
			$out .='</a></span>'.n;
		}
			/*
			$out .= t.'<span class="abouttext sticktobot">';
			$out .= $seeker->url ? '<a href="'.$seeker->url.'" class="web" title="'.JText::_('Member website').'">'.$seeker->url.'</a>' : '';
			$out .= '</span>'.n;
			*/
		$out .= t.'<div class="clear leftclear"></div>'.n;
		$out .= t.'<span class="indented">';
		if ($resume) {
			$out .= '<a href="'.JRoute::_('index.php?option='.$option.a.'id='.$seeker->uid.a.'active=resume'.a.'action=download').'" class="resume getit" title="'.$title.'">'.ucfirst(JText::_('PLG_RESUME_RESUME')).'</a> <span class="mini">'.JText::_('PLG_RESUME_LAST_UPDATE').': '.$this->nicetime($seeker->created).'</span>  '.n;
			//$out .= $seeker->url ? '<a href="'.$seeker->url.'" class="web" title="'.JText::_('Member website').'">'.$seeker->url.'</a>' : '';
			$out .= $seeker->url ? '<span class="mini"> | </span> <span class="mini"><a href="'.$seeker->url.'" class="web" rel="external" title="'.JText::_('PLG_RESUME_MEMBER_WEBSITE').': '.$seeker->url.'">'.JText::_('PLG_RESUME_WEBSITE').'</a></span>' : '';
			$out .= $seeker->linkedin ? '<span class="mini"> | </span> <span class="mini"><a href="'.$seeker->linkedin.'" class="linkedin" rel="external" title="'.JText::_('PLG_RESUME_MEMBER_LINKEDIN').'">'.JText::_('PLG_RESUME_LINKEDIN').'</a></span>' : '';
		}
		else {
			$out .- '<span class="unavail">'.JText::_('ACTION_DOWNLOAD').'</span>'.n;
		}

		$out .= '</span>'.n;
		$out .='</div>'.n;

		return $out;
	}

	/**
	 * Short description for 'build_path'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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
		} else {
			$base_path = DS.'site'.DS.'members';
		}

		ximport('Hubzero_View_Helper_Html');
		$dir = Hubzero_View_Helper_Html::niceidformat( $uid );

		$listdir = $base_path.DS.$dir;

		if (!is_dir(JPATH_ROOT.$listdir)) {
			jimport('joomla.filesystem.folder');
			if (!JFolder::create( JPATH_ROOT.$listdir, 0777 )) {
				return false;
			}
		}

		// Build the path
		return $listdir;
	}

	//----------------------------------------------------------
	// media manager
	//----------------------------------------------------------

	/**
	 * Short description for 'upload'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $database Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      mixed $member Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
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
		$default_title = $member->get('firstname') ? $member->get('firstname').' '.$member->get('lastname').' '.ucfirst(JText::_('PLG_RESUME_RESUME')) : $member->get('name').' '.ucfirst(JText::_('PLG_RESUME_RESUME'));
		$path = JPATH_ROOT.$path;

		// Replace file title with user name		
		$file_ext = substr($file['name'], strripos($file['name'], '.'));
		$file['name'] = $member->get('firstname') ? $member->get('firstname').' '.$member->get('lastname').' '.ucfirst(JText::_('PLG_RESUME_RESUME')) : $member->get('name').' '.ucfirst(JText::_('PLG_RESUME_RESUME'));
		$file['name'] .= $file_ext;

		// Make the filename safe
		jimport('joomla.filesystem.file');
		$file['name'] = JFile::makeSafe($file['name']);
		$file['name'] = str_replace(' ','_',$file['name']);

		$row = new Resume( $database );

		if (!$row->load($member->get('uidNumber'))) {
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
			// File was uploaded, create database entry			
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

	/**
	 * Short description for 'deleteresume'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $database Parameter description (if any) ...
	 * @param      unknown $option Parameter description (if any) ...
	 * @param      object $member Parameter description (if any) ...
	 * @param      unknown $emp Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	protected function deleteresume($database, $option, $member, $emp)
	{
		$row = new Resume( $database );
		if (!$row->load($member->get('uidNumber'))) {
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
			} else {
				// Push through to the main view
				return $this->view($database, $option, $member, $emp);
			}
		}
	}

	/**
	 * Short description for 'onMembersShortlist'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	function onMembersShortlist( )
	{
		$oid = JRequest::getInt( 'oid', 0 );

		if ($oid) {
			$this->shortlist( $oid, $ajax=1 );
		}
	}

	/**
	 * Short description for 'shortlist'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oid Parameter description (if any) ...
	 * @param      integer $ajax Parameter description (if any) ...
	 * @return     void
	 */
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

			if ($ajax) {
				// get seeker info
				$js = new JobSeeker ( $database );
				$seeker = $js->getSeeker($oid, $juser->get('id'));
				echo $this->showSeeker( $seeker[0], 1, 0, 'com_members', 1) ;
			}
		}
	}

	/**
	 * Short description for 'alert'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $msg Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function alert( $msg )
	{
		return "<script type=\"text/javascript\"> alert('".$msg."'); window.history.go(-1); </script>\n";
	}

	/**
	 * Short description for 'formSelect'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $name Parameter description (if any) ...
	 * @param      array $array Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @param      string $class Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function formSelect($name, $array, $value, $class='')
	{
		$out  = '<select name="'.$name.'" id="'.$name.'"';
		$out .= ($class) ? ' class="'.$class.'">'."\n" : '>'."\n";
		foreach ($array as $avalue => $alabel)
		{
		 	$selected = ($avalue == $value || $alabel == $value)
					  ? ' selected="selected" '
					  : '';
			$out .= ' <option value="'.$avalue.'"'.$selected.'>'.$alabel.'</option>'."\n";
		}
		$out .= '</select>'."\n";
		return $out;
	}

	/**
	 * Short description for 'nicetime'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $date Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function nicetime($date)
	{
		if (empty($date)) {
			return "No date provided";
		}

		$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
		$lengths = array("60","60","24","7","4.35","12","10");

		$now = time();
		$unix_date = strtotime($date);

		// check validity of date
		if (empty($unix_date)) {
			return "Bad date";
		}

		// is it future date or past date
		if ($now > $unix_date) {
			$difference = $now - $unix_date;
			$tense = "ago";

		} else {
			$difference = $unix_date - $now;
			//$tense = "from now";
			$tense = "";
		}

		for ($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			$difference /= $lengths[$j];
		}

		$difference = round($difference);

		if ($difference != 1) {
			$periods[$j].= "s";
		}

		return "$difference $periods[$j] {$tense}";
	}

	/**
	 * Short description for 'download'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      mixed $member Parameter description (if any) ...
	 * @return     unknown Return description (if any) ...
	 */
	protected function download($member)
	{
		// Get some needed libraries
		ximport('Hubzero_Content_Server');

		$database =& JFactory::getDBO();
		$juser    =& JFactory::getUser();

		// Ensure we have a database object
		if (!$database) {
			JError::raiseError( 500, JText::_('DATABASE_NOT_FOUND') );
			return;
		}

		// Incoming
		$uid = $member->get('uidNumber');

		// Load the resume
		$resume = new Resume($database);
		$file = '';
		$path = $this->build_path( $uid );

		if ($resume->load($uid)) {
			$file = JPATH_ROOT.$path.DS.$resume->filename;
		}

		if (!is_file($file)) {
			JError::raiseError( 404, JText::_('FILE_NOT_FOUND') );
			return;
		}

		// Use user name as file name
		$default_title = $member->get('firstname') ? $member->get('firstname').' '.$member->get('lastname').' '.ucfirst(JText::_('Resume')) : $member->get('name').' '.ucfirst(JText::_('Resume'));
		$default_title .= substr($resume->filename, strripos($resume->filename, '.'));;

		// Initiate a new content server and serve up the file
		$xserver = new Hubzero_Content_Server();
		$xserver->filename($file);

		// record view
		$stats = new JobStats($database);
		if ($juser->get('id') != $uid ) {
			$stats->saveView ($uid, 'seeker');
		}

		$xserver->disposition('attachment');
		$xserver->acceptranges(false); // @TODO fix byte range support
		$xserver->saveas(stripslashes($resume->title));
		$result = $xserver->serve_attachment($file, stripslashes($default_title), false); // @TODO fix byte range support

		if (!$result) {
			JError::raiseError( 500, JText::_('SERVER_ERROR') );
		}
	}
}

