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

	/* Job Posting */

	// load some classes
	$sitename = Config::get('sitename');

	$jobsHtml = new \Components\Jobs\Helpers\Html();

	$job = $this->job;

	$job->cat = $job->cat ? $job->cat : 'unspecified';
	$job->type = $job->type ? $job->type : 'unspecified';

	$startdate = ($job->startdate && $job->startdate !='0000-00-00 00:00:00') ? JHTML::_('date', $job->startdate, Lang::txt('DATE_FORMAT_HZ1')) : 'N/A';
	$closedate = ($job->closedate && $job->closedate !='0000-00-00 00:00:00') ? JHTML::_('date', $job->closedate, Lang::txt('DATE_FORMAT_HZ1')) : 'N/A';

	$model = new \Components\Jobs\Models\Job($job);

	$maintext = $model->content('parsed');

	$owner = (User::get('id') == $job->employerid or $this->admin) ? 1 : 0;

	$html = '';
?>
<header id="content-header">
	<h2><?php echo $this->title; ?>
		<?php if ($owner) { echo '<a class="edit button" href="'. Route::url('index.php?option='.$this->option.'&task=editjob&code='.$job->code) .'" title="'. Lang::txt('COM_JOBS_ACTION_EDIT_JOB') .'">'. Lang::txt('COM_JOBS_ACTION_EDIT_JOB') .'</a>'; } ?>
	</h2>

	<div id="content-header-extra">
		<ul id="useroptions">
		<?php if (User::isGuest()) { ?>
			<li><?php echo Lang::txt('COM_JOBS_PLEASE').' <a href="'.Route::url('index.php?option='.$this->option.'&task=view').'?action=login">'.Lang::txt('COM_JOBS_ACTION_LOGIN').'</a> '.Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
		<?php } else if ($this->emp && $this->allowsubscriptions) {  ?>
			<li><a class="icon-dashboard myjobs btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
			<li><a class="icon-list shortlist btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=resumes').'?filterby=shortlisted'; ?>"><?php echo Lang::txt('COM_JOBS_SHORTLIST'); ?></a></li>
			<li><a class="icon-add add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=addjob'); ?>"><?php echo Lang::txt('COM_JOBS_ADD_ANOTHER_JOB'); ?></a></li>
		<?php } else if ($this->admin) { ?>
			<li>
				<a class="icon-dashboard myjobs btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=dashboard'); ?>"><?php echo Lang::txt('COM_JOBS_ADMIN_DASHBOARD'); ?></a></li>
			<li><a class="icon-add add btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=addjob'); ?>"><?php echo Lang::txt('COM_JOBS_ADD_ANOTHER_JOB'); ?></a></li>
		<?php } else { ?>
			<li><a class="myresume btn" href="<?php echo Route::url('index.php?option='.$this->option.'&task=addresume'); ?>"><?php echo Lang::txt('COM_JOBS_MY_RESUME'); ?></a></li>
		<?php } ?>
		</ul>
	</div><!-- / #content-header-extra -->
</header><!-- / #content-header -->

<?php
	$job->title = trim(stripslashes($job->title));
	$job->description = trim(stripslashes($job->description));
	$job->description = preg_replace('/<br\\s*?\/??>/i', "", $job->description);
	$job->description = $jobsHtml->txt_unpee($job->description);
?>

<section class="main section">
	<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
	<?php }
	if ($this->msg_warning) { ?>
		<p class="warning"><?php echo $this->msg_warning; ?></p>
	<?php }
	if ($this->msg_passed) { ?>
		<p class="passed"><?php echo $this->msg_passed; ?></p>
	<?php }
		if ($owner)
		{
			// admin status message
			switch ( $job->status )
			{
				case 1:
				$html .= '<p class="statusmsg activead">'.Lang::txt('COM_JOBS_JOB_AD_PUBLISHED').' '.Lang::txt('You can').' <a href="'.Route::url('index.php?option='.$this->option.'&task=editjob&code='.$job->code).'">'.strtolower(Lang::txt('COM_JOBS_ACTION_EDIT_JOB')).'</a> '.Lang::txt('COM_JOBS_JOB_AD_PUBLISHED_TIPS').'</p>';
				break;
				case 3:
				$html .= '<p class="statusmsg inactive">'.Lang::txt('COM_JOBS_JOB_AD_UNPUBLISHED').' '.Lang::txt('COM_JOBS_JOB_REOPEN_NOTICE').'</p>';
				break;
				case 4:
				$html .= '<p class="statusmsg inactive">'.Lang::txt('COM_JOBS_JOB_AD_DRAFT_NOTICE').'</p>';
				break;
				case 0:
				$html .= '<p class="statusmsg inactive">'.Lang::txt('COM_JOBS_JOB_AD_PENDING_NOTICE').'</p>';
				break;
			}
		}

		$html = '';
		$html .= '<div id="jobinfo">'."\n";
		$html .= '<h3><span>'.Lang::txt('COM_JOBS_JOB_REFERENCE_CODE').': '.$job->code.'</span>'.$job->title.' - ';
		$html .= preg_match('/(.*)http/i', $job->companyWebsite) ? '<a href="'.$job->companyWebsite.'">'.$job->companyName.'</a>' : $job->companyName;
		$html .= ', '.$job->companyLocation;
		$html .= $job->companyLocationCountry ? ', ' . strtoupper($job->companyLocationCountry) : '';
		$html .= '</h3>'."\n";
		$html .= '<div class="clear"></div>'."\n";
		$html .= '<div class="apply"><p>'."\n";
		if ($job->applied) {
			$html .= '<span class="alreadyapplied">'.Lang::txt('COM_JOBS_JOB_APPLIED_ON').' '.JHTML::_('date', $job->applied, Lang::txt('DATE_FORMAT_HZ1')).'<span>'."\n";
			$html .= '<span><a href="'.Route::url('index.php?option='.$this->option.'&task=editapp&code='.$job->code).'">'.Lang::txt('COM_JOBS_ACTION_EDIT_APPLICATION').'</a> | <a href="'.Route::url('index.php?option='.$this->option.'&task=withdraw&code='.$job->code).'" id="showconfirm">'.Lang::txt('COM_JOBS_ACTION_WITHDRAW_APPLICATION').'</a><span>'."\n";
		}
		else if ($job->withdrawn) {
			$html .= '<span class="withdrawn">'.Lang::txt('COM_JOBS_JOB_WITHDREW_ON').' '.JHTML::_('date', $job->withdrawn, Lang::txt('DATE_FORMAT_HZ1')).'<span>'."\n";
			$html .= '<span><a href="'.Route::url('index.php?option='.$this->option.'&task=apply&code='.$job->code).'">'.Lang::txt('COM_JOBS_ACTION_REAPPLY').'</a><span>'."\n";
		}
		else {
			if ($job->applyExternalUrl) {

				if (strpos($job->applyExternalUrl, "http://") !== false || strpos($job->applyExternalUrl, "https://") !== false)
				{
					$url = $job->applyExternalUrl;
				}
				else {
					$url = "http://" .  $job->applyExternalUrl;
				}
				$html .= '<a class="extlink" href="'.$url.'" rel="external">'.Lang::txt('COM_JOBS_ACTION_APPLY_EXTERNALLY').'</a>'."\n";
				$html .= $job->applyInternal ? '<span class="or">'.strtolower(Lang::txt('COM_JOBS_OR')).'</span>'."\n" : ''."\n";
			}
			if ($job->applyInternal) {
				$html .= '<span class="applybtn"><a href="'.Route::url('index.php?option='.$this->option.'&task=apply&code='.$job->code).'">'.Lang::txt('COM_JOBS_ACTION_APPLY_THROUGH_HUB').' '.$sitename.'</a></span>'."\n";
			}
		}
		$html .= '</p>';
		$html .= ($job->applied) ? $jobsHtml->confirmscreen(Route::url('index.php?option='.$this->option.'&task=job&code='.$job->code), Route::url('index.php?option='.$this->option.'&task=withdraw&code='.$job->code), $action = "withdrawapp")."\n" : '';
		$html .='</div>'."\n";
		$html .= '<div>'."\n";
		$html .= '<span class="sub-heading">'.Lang::txt('COM_JOBS_TABLE_CATEGORY').'</span>'."\n";
		$html .= '<p>'.$job->cat.'</p>'."\n";
		$html .= '<span class="sub-heading">'.Lang::txt('COM_JOBS_TABLE_TYPE').'</span>'."\n";
		$html .= '<p>'.$job->type.'</p>'."\n";
		$html .= '<span class="sub-heading">'.Lang::txt('COM_JOBS_TABLE_START_DATE').'</span>'."\n";
		$html .= '<p>'.$startdate.'</p>'."\n";
		$html .= '<span class="sub-heading">'.Lang::txt('COM_JOBS_TABLE_EXPIRES').'</span>'."\n";
		$html .= '<p>'.$closedate.'</p>'."\n";
		$html .= '<div class="reg details">'.$maintext.'</div>'."\n";
		if ($job->contactName) {
			$html .= '<p class="reg details">'.Lang::txt('COM_JOBS_JOB_INFO_CONTACT').':</p>'."\n";
			$html .= '<p class="reg">'."\n";
			$html .= '<span class="contactname">'.$job->contactName.'</span>'."\n";
			$html .= $job->contactPhone ? '<span class="contactinfo">'.Lang::txt('COM_JOBS_JOB_TABLE_TEL').': '.$job->contactPhone.'</span>'."\n" : '';
			$html .= $job->contactEmail ? '<span class="contactinfo">'.Lang::txt('COM_JOBS_JOB_TABLE_EMAIL').': '.$job->contactEmail.'</span>'."\n" : '';
			$html .= '</p>'."\n";
		}
		$html .= '</div>'."\n";
		$html .= '</div>'."\n";

		if ($owner)
		{
			// admin options
			switch ($job->status)
			{
				case 1:
					$html .= '<p class="manageroptions"><span><a href="'.Route::url('index.php?option='.$this->option.'&task=unpublish&code='.$job->code).'">'.Lang::txt('COM_JOBS_ACTION_UNPUBLISH_THIS_JOB').'</a> '.Lang::txt('COM_JOBS_NOTICE_ACCESS_PRESERVED').'</span> </p>';
				break;
				case 4:
					$html .= '<p class="confirmPublish"><span><a href="'.Route::url('index.php?option='.$this->option.'&task=confirmjob&code='.$job->code).'">'.Lang::txt('COM_JOBS_ACTION_PUBLISH_AD').'</a></span> <span class="alternative">'.Lang::txt('or').'</span> <span class="makechanges"><a href="'.Route::url('index.php?option='.$this->option.'&task=editjob&code='.$job->code).'">'.Lang::txt('COM_JOBS_ACTION_MAKE_CHANGES').'</a></span> <span class="alternative">'.Lang::txt('COM_JOBS_OR').'</span> <span class="makechanges"><a href="'.Route::url('index.php?option='.$this->option.'&task=remove&code='.$job->code).'">'.Lang::txt('COM_JOBS_ACTION_REMOVE_AD').'</a></span> </p>';
				break;
				case 3:
					$html .= '<p class="manageroptions"><span><a href="'.Route::url('index.php?option='.$this->option.'&task=reopen&code='.$job->code).'">'.Lang::txt('COM_JOBS_ACTION_REOPEN_THIS').'</a> '.Lang::txt('COM_JOBS_ACTION_INCLUDE_INPUBLIC_LISTING').'</span> <span class="alternative">|</span> <span><a href="'.Route::url('index.php?option='.$this->option.'&task=remove&code='.$job->code).'">'.Lang::txt('COM_JOBS_ACTION_DELETE_THIS_JOB').'</a> ('.Lang::txt('COM_JOBS_ACTION_DELETE_ALL_RECORDS').')</span> </p>';
				break;
			}
		}
		echo $html;
	?>
	<?php if ($owner) { ?>
		<h3><?php echo Lang::txt('COM_JOBS_APPLICATIONS').' ('.count($job->applications).' '.Lang::txt('COM_JOBS_TOTAL').')'; ?></h3>
		<?php if (count($job->applications) <= 0 ){  ?>
		<p><?php echo Lang::txt('COM_JOBS_NOTICE_APPLICATIONS_NONE'); ?></p>
		<?php }  else {

			$html  = '';
			$html .= '<ul id="candidates">'."\n";

			JPluginHelper::importPlugin( 'members','resume' );
			$dispatcher = JDispatcher::getInstance();
			$k = 1;
			for ($i=0, $n=count( $job->applications ); $i < $n; $i++) {
				if ($job->applications[$i]->seeker && $job->applications[$i]->status != 2) {
					$applied = ($job->applications[$i]->applied !='0000-00-00 00:00:00') ? JHTML::_('date',$job->applications[$i]->applied, Lang::txt('DATE_FORMAT_HZ1')) : Lang::txt('N/A');
					$html  .= '<li class="applic">'."\n";
					$html  .= '<span class="countc">'.$k.'</span> '.$job->applications[$i]->seeker->name.' '.Lang::txt('applied on').' '.$applied."\n";
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
			if (count($job->withdrawnlist) > 0) {
				for ($i=0, $n=count( $job->withdrawnlist ); $i < $n; $i++) {
					$n = $k;

					$n++;
				}
			}

			$html  .= '</ul>'."\n";
			if (count($job->withdrawnlist) > 0) {
				$html  .= '<p>'.count($job->withdrawnlist).' '.Lang::txt('COM_JOBS_NOTICE_CANDIDATES_WITHDREW').'</p>'."\n";
			}

			echo $html;
		} ?>
	<?php } ?>
</section>
