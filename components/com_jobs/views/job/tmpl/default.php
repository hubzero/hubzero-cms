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

$tz = 0;
$dateFormat = '%d %b %Y';
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M Y';
	$tz = true;
}
	/* Job Posting */

	// load some classes
	$jconfig = JFactory::getConfig();
	$sitename = $jconfig->getValue('config.sitename');
	$juser =& JFactory::getUser();

	$job = $this->job;

	$job->cat = $job->cat ? $job->cat : 'unspecified';
	$job->type = $job->type ? $job->type : 'unspecified';

	$startdate = ($job->startdate && $job->startdate !='0000-00-00 00:00:00') ? JHTML::_('date',$job->startdate, '%d %b %Y',0) : 'N/A';
	$closedate = ($job->closedate && $job->closedate !='0000-00-00 00:00:00') ? JHTML::_('date',$job->closedate, '%d %b %Y',0) : 'N/A';

	// Transform the wikitext to HTML
	$wikiconfig = array(
		'option'   => $this->option,
		'scope'    => 'job'.DS.$job->code,
		'pagename' => 'jobs',
		'pageid'   => $job->code,
		'filepath' => '',
		'domain'   => ''
	);
	ximport('Hubzero_Wiki_Parser');
	$p =& Hubzero_Wiki_Parser::getInstance();
	$maintext = $p->parse(stripslashes($job->description), $wikiconfig);

	$owner = ($juser->get('id') == $job->employerid or $this->admin) ? 1 : 0;

?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?>
    <?php if($owner) { echo '<a class="edit button" href="'. JRoute::_('index.php?option='.$this->option.'&task=editjob&code='.$job->code) .'" title="'. JText::_('COM_JOBS_ACTION_EDIT_JOB') .'">'. JText::_('COM_JOBS_ACTION_EDIT_JOB') .'</a>'; } ?>
    </h2>
</div><!-- / #content-header -->

<div id="content-header-extra">
    <ul id="useroptions">
    <?php if($juser->get('guest')) { ?> 
    	<li><?php echo JText::_('COM_JOBS_PLEASE').' <a href="'.JRoute::_('index.php?option='.$this->option.'&task=view').'?action=login">'.JText::_('COM_JOBS_ACTION_LOGIN').'</a> '.JText::_('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
    <?php } else if($this->emp && $this->allowsubscriptions) {  ?>
    	<li><a class="myjobs btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
        <li><a class="shortlist btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('COM_JOBS_SHORTLIST'); ?></a></li>
		<li><a class="add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=addjob'); ?>"><?php echo JText::_('COM_JOBS_ADD_ANOTHER_JOB'); ?></a></li>
    <?php } else if($this->admin) { ?>
    	<li>
        	<a class="myjobs btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_ADMIN_DASHBOARD'); ?></a></li>
		<li><a class="add btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=addjob'); ?>"><?php echo JText::_('COM_JOBS_ADD_ANOTHER_JOB'); ?></a></li>
	<?php } else { ?>  
    	<li><a class="myresume btn" href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=addresume'); ?>"><?php echo JText::_('COM_JOBS_MY_RESUME'); ?></a></li>
    <?php } ?>  		
	</ul>
</div><!-- / #content-header-extra -->
<?php

		$html = '';

		$job->title = trim(stripslashes($job->title));
		$job->description = trim(stripslashes($job->description));
		$job->description = preg_replace('/<br\\s*?\/??>/i', "", $job->description);
		$job->description = JobsHtml::txt_unpee($job->description);

		$html .= '<div class="clear"></div>'."\n";
		$html .= '<div class="main section">'."\n";

	if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
	<?php }
	if ($this->msg_warning) { ?>
	<p class="warning"><?php echo $this->msg_warning; ?></p>
	<?php }
	if ($this->msg_passed) { ?>
	<p class="passed"><?php echo $this->msg_passed; ?></p>
	<?php }
		if($owner) {
			// admin status message
			switch( $job->status )
			{
				case 1:
				$html .= '<p class="statusmsg activead">'.JText::_('COM_JOBS_JOB_AD_PUBLISHED').' '.JText::_('You can').' <a href="'.JRoute::_('index.php?option='.$this->option.'&task=editjob&code='.$job->code).'">'.strtolower(JText::_('COM_JOBS_ACTION_EDIT_JOB')).'</a> '.JText::_('COM_JOBS_JOB_AD_PUBLISHED_TIPS').'</p>';
				break;
				case 3:
				$html .= '<p class="statusmsg inactive">'.JText::_('COM_JOBS_JOB_AD_UNPUBLISHED').' '.JText::_('COM_JOBS_JOB_REOPEN_NOTICE').'</p>';
				break;
				case 4:
				$html .= '<p class="statusmsg inactive">'.JText::_('COM_JOBS_JOB_AD_DRAFT_NOTICE').'</p>';
				break;
				case 0:
				$html .= '<p class="statusmsg inactive">'.JText::_('COM_JOBS_JOB_AD_PENDING_NOTICE').'</p>';
				break;
			}
		}

		$html .= t.'<div id="jobinfo">'."\n";
		$html .= t.t.'<h3><span>'.JText::_('COM_JOBS_JOB_REFERENCE_CODE').': '.$job->code.'</span>'.$job->title.' - ';
		$html .= preg_match('/(.*)http/i', $job->companyWebsite) ? '<a href="'.$job->companyWebsite.'">'.$job->companyName.'</a>' : $job->companyName;
		$html .= ', '.$job->companyLocation.', '.$job->companyLocationCountry.'</h3>'."\n";
		$html .= '<div class="clear"></div>'."\n";
		$html .= t.t.'<div class="apply"><p>'."\n";
		if($job->applied) {
			$html .= t.t.'<span class="alreadyapplied">'.JText::_('COM_JOBS_JOB_APPLIED_ON').' '.JHTML::_('date', $job->applied, $dateFormat, $tz).'<span>'."\n";
			$html .= t.t.'<span><a href="'.JRoute::_('index.php?option='.$this->option.'&task=editapp&code='.$job->code).'">'.JText::_('COM_JOBS_ACTION_EDIT_APPLICATION').'</a> | <a href="'.JRoute::_('index.php?option='.$this->option.'&task=withdraw&code='.$job->code).'" id="showconfirm">'.JText::_('COM_JOBS_ACTION_WITHDRAW_APPLICATION').'</a><span>'."\n";
		}
		else if($job->withdrawn) {
			$html .= t.t.'<span class="withdrawn">'.JText::_('COM_JOBS_JOB_WITHDREW_ON').' '.JHTML::_('date', $job->withdrawn, $dateFormat, $tz).'<span>'."\n";
			$html .= t.t.'<span><a href="'.JRoute::_('index.php?option='.$this->option.'&task=apply&code='.$job->code).'">'.JText::_('COM_JOBS_ACTION_REAPPLY').'</a><span>'."\n";
		}
		else {
			if($job->applyExternalUrl) {
				
				if(strpos($job->applyExternalUrl, "http://") !== false || strpos($job->applyExternalUrl, "https://") !== false)
				{
					$url = $job->applyExternalUrl;
				}
				else {
					$url = "http://" .  $job->applyExternalUrl;
				}
				$html .= t.t.'<a class="extlink" href="'.$url.'" rel="external">'.JText::_('COM_JOBS_ACTION_APPLY_EXTERNALLY').'</a>'."\n";
				$html .= $job->applyInternal ? '<span class="or">'.strtolower(JText::_('COM_JOBS_OR')).'</span>'."\n" : ''."\n";
			}
			if($job->applyInternal) {
				$html .= t.t.'<span class="applybtn"><a href="'.JRoute::_('index.php?option='.$this->option.'&task=apply&code='.$job->code).'">'.JText::_('COM_JOBS_ACTION_APPLY_THROUGH_HUB').' '.$sitename.'</a></span>'."\n";
			}
		}
		$html .= t.t.'</p>';
		$html .= ($job->applied) ? JobsHtml::confirmscreen(JRoute::_('index.php?option='.$this->option.'&task=job&code='.$job->code), JRoute::_('index.php?option='.$this->option.'&task=withdraw&code='.$job->code), $action = "withdrawapp")."\n" : '';
		$html .='</div>'."\n";
		$html .= '<div>'."\n";
		$html .= t.t.'<span class="sub-heading">'.JText::_('COM_JOBS_TABLE_CATEGORY').'</span>'."\n";
		$html .= t.t.'<p>'.$job->cat.'</p>'."\n";
		$html .= t.t.'<span class="sub-heading">'.JText::_('COM_JOBS_TABLE_TYPE').'</span>'."\n";
		$html .= t.t.'<p>'.$job->type.'</p>'."\n";
		$html .= t.t.'<span class="sub-heading">'.JText::_('COM_JOBS_TABLE_START_DATE').'</span>'."\n";
		$html .= t.t.'<p>'.$startdate.'</p>'."\n";
		$html .= t.t.'<span class="sub-heading">'.JText::_('COM_JOBS_TABLE_EXPIRES').'</span>'."\n";
		$html .= t.t.'<p>'.$closedate.'</p>'."\n";
		$html .= t.t.'<div class="reg details">'.$maintext.'</div>'."\n";
		if($job->contactName) {
			$html .= t.t.'<p class="reg details">'.JText::_('COM_JOBS_JOB_INFO_CONTACT').':</p>'."\n";
			$html .= t.t.'<p class="reg">'."\n";
			$html .= t.t.'<span class="contactname">'.$job->contactName.'</span>'."\n";
			$html .= $job->contactPhone ? '<span class="contactinfo">'.JText::_('COM_JOBS_JOB_TABLE_TEL').': '.$job->contactPhone.'</span>'."\n" : '';
			$html .= $job->contactEmail ? '<span class="contactinfo">'.JText::_('COM_JOBS_JOB_TABLE_EMAIL').': '.$job->contactEmail.'</span>'."\n" : '';
			$html .= t.t.'</p>'."\n";
		}
		$html .= t.'</div>'."\n";
		$html .= '</div>'."\n";

		if($owner) {
			// admin options
			switch( $job->status )
			{
				case 1:
					$html .= '<p class="manageroptions"><span><a href="'.JRoute::_('index.php?option='.$this->option.'&task=unpublish&code='.$job->code).'">'.JText::_('COM_JOBS_ACTION_UNPUBLISH_THIS_JOB').'</a> '.JText::_('COM_JOBS_NOTICE_ACCESS_PRESERVED').'</span> </p>';
				break;
				case 4:
					$html .= '<p class="confirmPublish"><span><a href="'.JRoute::_('index.php?option='.$this->option.'&task=confirmjob&code='.$job->code).'">'.JText::_('COM_JOBS_ACTION_PUBLISH_AD').'</a></span> <span class="alternative">'.JText::_('or').'</span> <span class="makechanges"><a href="'.JRoute::_('index.php?option='.$this->option.'&task=editjob&code='.$job->code).'">'.JText::_('COM_JOBS_ACTION_MAKE_CHANGES').'</a></span> <span class="alternative">'.JText::_('COM_JOBS_OR').'</span> <span class="makechanges"><a href="'.JRoute::_('index.php?option='.$this->option.'&task=remove&code='.$job->code).'">'.JText::_('COM_JOBS_ACTION_REMOVE_AD').'</a></span> </p>';
				break;
				case 3:
					$html .= '<p class="manageroptions"><span><a href="'.JRoute::_('index.php?option='.$this->option.'&task=reopen&code='.$job->code).'">'.JText::_('COM_JOBS_ACTION_REOPEN_THIS').'</a> '.JText::_('COM_JOBS_ACTION_INCLUDE_INPUBLIC_LISTING').'</span> <span class="alternative">|</span> <span><a href="'.JRoute::_('index.php?option='.$this->option.'&task=remove&code='.$job->code).'">'.JText::_('COM_JOBS_ACTION_DELETE_THIS_JOB').'</a> ('.JText::_('COM_JOBS_ACTION_DELETE_ALL_RECORDS').')</span> </p>';
				break;
			}
		}
		echo $html;
?>
 <?php if($owner) {  ?>
 	<a name="applications"></a>
    <h3><?php echo JText::_('COM_JOBS_APPLICATIONS').' ('.count($job->applications).' '.JText::_('COM_JOBS_TOTAL').')'; ?></h3>
    <?php if (count($job->applications) <= 0 ){  ?>
    <p><?php echo JText::_('COM_JOBS_NOTICE_APPLICATIONS_NONE'); ?></p>
    <?php }  else {

		$html = '';
		$html.= '<ul id="candidates">'."\n";

		JPluginHelper::importPlugin( 'members','resume' );
		$dispatcher =& JDispatcher::getInstance();
		$k = 1;
		for ($i=0, $n=count( $job->applications ); $i < $n; $i++) {
			if($job->applications[$i]->seeker && $job->applications[$i]->status != 2) {
				$applied = ($job->applications[$i]->applied !='0000-00-00 00:00:00') ? JHTML::_('date',$job->applications[$i]->applied, $dateFormat, $tz) : JText::_('N/A');
				$html  .= '<li class="applic">'."\n";
				$html  .= '<span class="countc">'.$k.'</span> '.$job->applications[$i]->seeker->name.' '.JText::_('applied on').' '.$applied."\n";
				$html  .= $job->applications[$i]->cover ? '<blockquote>'.trim(stripslashes($job->applications[$i]->cover)).'</blockquote>' : '';
				$html  .= '</li>'."\n";
				$html  .= '<li>'."\n";
				// show seeker info
				$out   = $dispatcher->trigger( 'showSeeker', array($job->applications[$i]->seeker, $this->emp, $this->admin, 'com_members', $list=0) );
				if (count($out) > 0) {
					$html .= $out[0];
				}
				$html  .= '</li>'."\n";
				$html  .= '<li class="applicbot"></li>'."\n";
				$k++;
			}
		}
		if(count($job->withdrawnlist) > 0) {
			for ($i=0, $n=count( $job->withdrawnlist ); $i < $n; $i++) {
				$n = $k;

				$n++;
			}
		}

		$html  .= '</ul>'."\n";
		if(count($job->withdrawnlist) > 0) {
			$html  .= '<p>'.count($job->withdrawnlist).' '.JText::_('COM_JOBS_NOTICE_CANDIDATES_WITHDREW').'</p>'."\n";
		}

		echo $html;
	} ?>
 <?php } ?>
 </div>

