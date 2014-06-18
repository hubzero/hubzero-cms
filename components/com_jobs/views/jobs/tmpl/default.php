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

	/* Jobs List */
	$jobs = $this->jobs;
	$option = $this->option;
	$jobs = $this->jobs;
	$filters = $this->filters;
	$allowsubscriptions = $this->allowsubscriptions;

	$jobsHtml = new JobsHtml();

	if ($this->subscriptioncode && $this->thisemployer)
	{
		$this->title .= ' '.JText::_('FROM').' '.$this->thisemployer->companyName;
	}

	$html  = '';
	$now = JFactory::getDate()->toSql();

	if (!$this->mini)
	{
		?>
		<header id="content-header">
			<h2><?php echo $this->title; ?></h2>

			<div id="content-header-extra">
				<ul id="useroptions">
				<?php if($this->guest) { ?>
					<li><?php echo JText::_('COM_JOBS_PLEASE').' <a href="'.JRoute::_('index.php?option='.$option.'&task=view').'?action=login">'.JText::_('COM_JOBS_ACTION_LOGIN').'</a> '.JText::_('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS'); ?></li>
				<?php } else if($this->emp && $this->allowsubscriptions) {  ?>
					<li><a class="myjobs btn" href="<?php echo JRoute::_('index.php?option='.$option.'&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_EMPLOYER_DASHBOARD'); ?></a></li>
					<li><a class="shortlist btn" href="<?php echo JRoute::_('index.php?option='.$option.'&task=resumes').'?filterby=shortlisted'; ?>"><?php echo JText::_('COM_JOBS_SHORTLIST'); ?></a></li>
				<?php } else if($this->admin) { ?>
					<li>
						<?php echo JText::_('COM_JOBS_NOTICE_YOU_ARE_ADMIN'); ?>
						<a class="myjobs btn" href="<?php echo JRoute::_('index.php?option='.$option.'&task=dashboard'); ?>"><?php echo JText::_('COM_JOBS_ADMIN_DASHBOARD'); ?></a>
					</li>
				<?php } else { ?>
					<li><a class="myresume btn" href="<?php echo JRoute::_('index.php?option='.$option.'&task=addresume'); ?>"><?php echo JText::_('COM_JOBS_MY_RESUME'); ?></a></li>
				<?php } ?>
				</ul>
			</div><!-- / #content-header-extra -->
		</header><!-- / #content-header -->

		<section class="main section">
		<?php if ($this->allowsubscriptions) { ?>
			<div class="subject">
		<?php } ?>
				<form method="get" action="<?php echo JRoute::_('index.php?option='.$option.'&task=browse'); ?>">
					<?php
					$sortbys = array('category'=>JText::_('COM_JOBS_CATEGORY'),'opendate'=>JText::_('COM_JOBS_POSTED_DATE'),'type'=>JText::_('COM_JOBS_TYPE'));
					$filterbys = array('all'=>JText::_('COM_JOBS_ALL'),'open'=>JText::_('COM_JOBS_ACTIVE'),'closed'=>JText::_('COM_JOBS_EXPIRED'));
					?>
					<div class="jobs_controls">
						<fieldset>
							<label>
								<?php echo JText::_('COM_JOBS_ACTION_SEARCH_BY_KEYWORDS'); ?>:<span class="questionmark tooltips" title="<?php echo JText::_('COM_JOBS_TIP_SEARCH_JOBS_BY_KEYWORDS'); ?>"></span>
								<input type="text" name="q" value="<?php echo $this->escape($filters['search']); ?>" />
							</label>
							<input type="submit" value="<?php echo JText::_('COM_JOBS_GO'); ?>" />
							<input type="hidden" name="limitstart" value="0" />
							<input type="hidden" name="performsearch" value="1" />
						</fieldset>
						<div class="note_total">
							<?php
							// show how many
							$totalnote = JText::_('COM_JOBS_NOTICE_DISPLAYING').' ';
							if ($filters['start'] == 0)
							{
								$totalnote .= ($this->pageNav->total > count($jobs)) ? ' '.JText::_('COM_JOBS_NOTICE_TOP').' '.count($jobs).' '.JText::_('COM_JOBS_NOTICE_OUT_OF').' '.$this->pageNav->total : strtolower(JText::_('COM_JOBS_ALL')).' '.count($jobs);
							}
							else
							{
								$totalnote .= ($filters['start'] + 1);
								$totalnote .= ' - ';
								$totalnote .= $filters['start'] + count($jobs);
								$totalnote .=' out of '.$this->pageNav->total;
							}
							$totalnote .= ' '.JText::_('COM_JOBS_NOTICE_JOB_OPENINGS');
							?>
						</div>
					</div>
	<?php } else { ?>
		<section class="main section">
			<h3><?php echo JText::_('COM_JOBS_LATEST_POSTINGS'); ?></h3>
	<?php } ?>

			<?php if (count($jobs) > 0) { ?>
				<?php
				$jt = new JobType ( $this->database );
				$jc = new JobCategory ( $this->database );

				$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';

				$maxscore = $filters['search'] && $jobs[0]->keywords > 0 ? $jobs[0]->keywords : 1;
				?>
				<table class="postings">
					<thead>
						<tr class="headings">
							<th<?php if ($this->filters['sortby'] == 'title') {  echo ' class="activesort"'; } ?>>
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=' . $this->task).'/?sortby=title&sortdir='.$sortbyDir. '&q=' . $this->filters['search']; ?>" class="re_sort">
									<?php echo JText::_('COM_JOBS_TABLE_JOB_TITLE'); ?>
								</a>
							</th>
						<?php if ($this->admin && !$this->emp && !$this->mini) { ?>
							<th><?php echo JText::_('COM_JOBS_TABLE_STATUS'); ?></th>
						<?php } ?>
							<th><?php echo JText::_('COM_JOBS_TABLE_COMPANY'); ?></th>
							<th><?php echo JText::_('COM_JOBS_TABLE_LOCATION'); ?></th>
							<th<?php if ($this->filters['sortby'] == 'category') { echo ' class="activesort"'; } ?>>
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=' . $this->task).'/?sortby=category&sortdir='.$sortbyDir. '&q=' . $this->filters['search']; ?>" class="re_sort">
									<?php echo JText::_('COM_JOBS_TABLE_CATEGORY'); ?>
								</a>
							</th>
							<th<?php if ($this->filters['sortby'] == 'type') { echo ' class="activesort"'; } ?>>
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=' . $this->task).'/?sortby=type&sortdir='.$sortbyDir. '&q=' . $this->filters['search']; ?>" class="re_sort">
									<?php echo JText::_('COM_JOBS_TABLE_TYPE'); ?>
								</a>
							</th>
							<th<?php if ($this->filters['sortby'] == 'opendate') { echo ' class="activesort"'; } ?>>
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=' . $this->task).'/?sortby=opendate&sortdir='.$sortbyDir. '&q=' . $this->filters['search']; ?>" class="re_sort">
									<?php echo JText::_('COM_JOBS_TABLE_POSTED'); ?>
								</a>
							</th>
							<th>
								<?php echo JText::_('COM_JOBS_TABLE_APPLY_BY'); ?>
							</th>
						<?php if ($filters['search']) { ?>
							<th><?php echo JText::_('COM_JOBS_TABLE_RELEVANCE'); ?></th>
						<?php } ?>
						</tr>
					</thead>
					<tbody>
					<?php
					for ($i=0, $n=count( $jobs ); $i < $n; $i++)
					{
						$model = new JobsModelJob($jobs[$i]);

						//$txt = $model->content('parsed');
						$closedate = ($jobs[$i]->closedate && $jobs[$i]->closedate !='0000-00-00 00:00:00') ? JHTML::_('date',$jobs[$i]->closedate, 'd&\nb\sp;M&\nb\sp;y') : 'ASAP';
						if($jobs[$i]->closedate !='0000-00-00 00:00:00' && $jobs[$i]->closedate < $now)
						{
							$closedate = 'closed';
						}
						$curtype = $jt->getType($jobs[$i]->type);
						$curcat = $jc->getCat($jobs[$i]->cid);

						// compute relevance to search keywords
						if ($filters['search']) {
							$relscore = $jobs[$i]->keywords > 0 ? floor(($jobs[$i]->keywords * 100) / $maxscore) : 0;
						}

						// what's the job status?
						if ($this->admin && !$this->emp && !$this->mini) {
							$status = '';
							$class =  '';
							switch ( $jobs[$i]->status )
							{
								case 0:    		$status =  JText::_('COM_JOBS_JOB_STATUS_PENDING');
												$class  = 'post_pending';
																						break;
								case 1:    		$status =  $jobs[$i]->inactive &&  $jobs[$i]->inactive < $now
												? JText::_('COM_JOBS_JOB_STATUS_INVALID')
												: JText::_('COM_JOBS_JOB_STATUS_ACTIVE');
												$class  = $jobs[$i]->inactive &&  $jobs[$i]->inactive < $now
												? 'post_invalidsub'
												: 'post_active';
																						break;
								case 3:    		$status =  JText::_('COM_JOBS_JOB_STATUS_INACTIVE');
												$class  = 'post_inactive';
												break;
								case 4:    		$status =  JText::_('COM_JOBS_JOB_STATUS_DRAFT');
												$class  = 'post_draft';
												break;
							}
						}
						?>
						<tr>
							<td class="jobtitle">
								<a href="<?php echo JRoute::_('index.php?option='.$option.'&task=job&code='.$jobs[$i]->code); ?>" title="<?php echo $model->content('clean', 250); ?>">
									<?php echo $jobs[$i]->title; ?>
								</a>
							</td>
						<?php if ($this->admin && !$this->emp && !$this->mini) { ?>
							<td <?php echo $class ? 'class="'.$class.'"' : ''; ?>>
								<?php echo $status; ?>
							</td>
						<?php } ?>
							<td>
								<?php echo $jobs[$i]->companyName; ?>
							</td>
							<td>
								<?php echo $jobs[$i]->companyLocation.', '.$jobs[$i]->companyLocationCountry; ?>
							</td>
							<td class="secondary"><?php echo $curcat; ?></td>
							<td class="secondary"><?php echo $curtype; ?></td>
							<td class="secondary">
								<span class="datedisplay"><?php echo JHTML::_('date', $jobs[$i]->added, 'd&\nb\sp;M&\nb\sp;y'); ?></span>
							</td>
							<td>
								<?php if ($jobs[$i]->applied) { ?>
									<span class="alreadyapplied">
										<?php echo JText::_('COM_JOBS_JOB_APPLIED_ON'); ?> <span class="datedisplay"><?php echo JHTML::_('date', $jobs[$i]->applied, 'd&\nb\sp;M&\nb\sp;y'); ?></span>
									</span>
								<?php } else if ($jobs[$i]->withdrawn) { ?>
									<span class="withdrawn">
										<?php echo JText::_('COM_JOBS_JOB_WITHDREW_ON'); ?> <span class="datedisplay"><?php echo JHTML::_('date', $jobs[$i]->withdrawn, 'd&\nb\sp;M&\nb\sp;y'); ?></span>
									</span>
								<?php } else { ?>
									<?php echo $closedate ? '<span class="datedisplay">'.$closedate.'</span>' : ''; ?>
								<?php } ?>
							</td>
						<?php if ($filters['search']) { ?>
							<td class="relevancescore <?php echo $relscore > 0 ? 'yes' : 'no'; ?>"><?php echo $relscore; ?> %</td>
						<?php } ?>
						</tr>
					<?php } ?>
					</tbody>
				</table>
		<?php } else { ?>
			<p>
				<?php
				echo JText::_('COM_JOBS_NO_JOBS_FOUND');
				if ($this->subscriptioncode)
				{
					if ($this->thisemployer)
					{
						echo ' '.JText::_('COM_JOBS_FROM').' '.JText::_('COM_JOBS_EMPLOYER').' '.$this->thisemployer->companyName.' ('.$this->subscriptioncode.')';
					}
					else
					{
						echo ' '.JText::_('COM_JOBS_FROM').' '.JText::_('COM_JOBS_REQUESTED_EMPLOYER').' ('.$this->subscriptioncode.')';
					}
					echo '. <a href="'.JRoute::_('index.php?option='.$option.'&task=browse').'"">'.JText::_('COM_JOBS_ACTION_BROWSE_ALL_JOBS').'</a>';
				}
				?>
			</p>
		<?php } ?>

	<?php if (!$this->mini) { ?>
		<?php
		// Insert page navigation
		$pagenavhtml = $this->pageNav->getListFooter();
		$pagenavhtml = str_replace('jobs/?','jobs/browse/?',$pagenavhtml);
		echo $pagenavhtml;
		if ($allowsubscriptions) { ?>
				</form>
			</div><!-- / .subject -->
			<aside class="aside minimenu">
				<div class="container">
					<h3><?php echo JText::_('COM_JOBS_EMPLOYERS'); ?></h3>
					<ul>
						<li><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=addjob'); ?>"><?php echo JText::_('COM_JOBS_POST_JOB'); ?></a></li>
						<li><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=resumes'); ?>"><?php echo JText::_('COM_JOBS_BROWSE_RESUMES'); ?></a></li>
					</ul>
				</div>
				<div class="container">
					<h3><?php echo JText::_('COM_JOBS_SEEKERS'); ?></h3>
					<ul>
						<li><a href="<?php echo JRoute::_('index.php?option='.$option.'&task=addresume'); ?>"><?php echo JText::_('COM_JOBS_POST_RESUME'); ?></a></li>
					</ul>
					<?php if ($this->config->get('infolink')) { ?>
						<p><a href="<?php echo $this->config->get('infolink'); ?>"><?php echo JText::_('COM_JOBS_LEARN_MORE'); ?></a> <?php echo JText::_('COM_JOBS_ABOUT_THE_PROCESS'); ?>.</p>
					<?php } ?>
				</div>
			</aside><!-- / .aside -->
		<?php } ?>
	<?php } ?>
	</section>
