<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// No direct access
defined('_HZEXEC_') or die();

$database = App::get('db');
$jt = new Components\Jobs\Tables\JobType ( $database );
$jc = new Components\Jobs\Tables\JobCategory ( $database );

$sortbyDir = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$maxscore  = $this->filters['search'] && $this->jobs[0]->keywords > 0 ? $this->jobs[0]->keywords : 1;

?>
<table class="postings">
	<thead>
		<tr class="headings">
			<th><?php echo Lang::txt('COM_JOBS_TABLE_JOB_TITLE'); ?></th>
		<?php if ($this->admin && !$this->emp && !$this->mini) { ?>
			<th><?php echo Lang::txt('COM_JOBS_TABLE_STATUS'); ?></th>
		<?php } ?>
			<th><?php echo Lang::txt('COM_JOBS_TABLE_COMPANY'); ?></th>
			<th><?php echo Lang::txt('COM_JOBS_TABLE_LOCATION'); ?></th>
			<th><?php echo Lang::txt('COM_JOBS_TABLE_CATEGORY'); ?></th>
			<th><?php echo Lang::txt('COM_JOBS_TABLE_TYPE'); ?></th>
			<th><?php echo Lang::txt('COM_JOBS_TABLE_POSTED'); ?></th>
			<th>
				<?php echo Lang::txt('COM_JOBS_TABLE_APPLY_BY'); ?>
			</th>
		<?php if ($this->filters['search']) { ?>
			<th><?php echo Lang::txt('COM_JOBS_TABLE_RELEVANCE'); ?></th>
		<?php } ?>
		</tr>
	</thead>
	<tbody>
	<?php
	for ($i=0, $n=count( $this->jobs ); $i < $n; $i++)
	{
		$model = new Components\Jobs\Models\Job($this->jobs[$i]);

		$closedate = ($this->jobs[$i]->closedate && $this->jobs[$i]->closedate !='0000-00-00 00:00:00') ? Date::of($this->jobs[$i]->closedate)->toLocal('d&\nb\sp;M&\nb\sp;y') : 'ASAP';
		if ($this->jobs[$i]->closedate !='0000-00-00 00:00:00' && $this->jobs[$i]->closedate < Date::toSql())
		{
			$closedate = 'closed';
		}
		$curtype = $jt->getType($this->jobs[$i]->type);
		$curcat = $jc->getCat($this->jobs[$i]->cid);

		// compute relevance to search keywords
		if ($this->filters['search'])
		{
			$relscore = $this->jobs[$i]->keywords > 0 ? floor(($this->jobs[$i]->keywords * 100) / $maxscore) : 0;
		}

		// what's the job status?
		if ($this->admin && !$this->emp && !$this->mini)
		{
			$status = '';
			$class =  '';
			switch ( $this->jobs[$i]->status )
			{
				case 0:     $status =  Lang::txt('COM_JOBS_JOB_STATUS_PENDING');
							$class  = 'post_pending';
							break;
				case 1:     $status =  $this->jobs[$i]->inactive &&  $this->jobs[$i]->inactive < Date::toSql()
							? Lang::txt('COM_JOBS_JOB_STATUS_INVALID')
							: Lang::txt('COM_JOBS_JOB_STATUS_ACTIVE');
							$class  = $this->jobs[$i]->inactive &&  $this->jobs[$i]->inactive < Date::toSql()
							? 'post_invalidsub'
							: 'post_active';
							break;
				case 3:     $status =  Lang::txt('COM_JOBS_JOB_STATUS_INACTIVE');
							$class  = 'post_inactive';
							break;
				case 4:     $status =  Lang::txt('COM_JOBS_JOB_STATUS_DRAFT');
							$class  = 'post_draft';
							break;
			}
		}
		?>
		<tr>
			<td class="jobtitle">
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=job&code=' . $this->jobs[$i]->code); ?>" title="<?php echo $model->content('clean', 250); ?>">
					<?php echo $this->jobs[$i]->title; ?>
				</a>
			</td>
		<?php if ($this->admin && !$this->emp && !$this->mini) { ?>
			<td <?php echo $class ? 'class="' . $class . '"' : ''; ?>>
				<?php echo $status; ?>
			</td>
		<?php } ?>
			<td>
				<?php echo $this->jobs[$i]->companyName; ?>
			</td>
			<td>
				<?php echo $this->jobs[$i]->companyLocation . ', ' . $this->jobs[$i]->companyLocationCountry; ?>
			</td>
			<td class="secondary"><?php echo $curcat; ?></td>
			<td class="secondary"><?php echo $curtype; ?></td>
			<td class="secondary">
				<span class="datedisplay"><?php echo Date::of($this->jobs[$i]->added)->toLocal('d&\nb\sp;M&\nb\sp;y'); ?></span>
			</td>
			<td>
				<?php if ($this->jobs[$i]->applied) { ?>
					<span class="alreadyapplied">
						<?php echo Lang::txt('COM_JOBS_JOB_APPLIED_ON'); ?> <span class="datedisplay"><?php echo Date::of($this->jobs[$i]->applied)->toLocal('d&\nb\sp;M&\nb\sp;y'); ?></span>
					</span>
				<?php } else if ($this->jobs[$i]->withdrawn) { ?>
					<span class="withdrawn">
						<?php echo Lang::txt('COM_JOBS_JOB_WITHDREW_ON'); ?> <span class="datedisplay"><?php echo Date::of($this->jobs[$i]->withdrawn)->toLocal('d&\nb\sp;M&\nb\sp;y'); ?></span>
					</span>
				<?php } else { ?>
					<?php echo $closedate ? '<span class="datedisplay">' . $closedate . '</span>' : ''; ?>
				<?php } ?>
			</td>
		<?php if ($this->filters['search']) { ?>
			<td class="relevancescore <?php echo $relscore > 0 ? 'yes' : 'no'; ?>"><?php echo $relscore; ?> %</td>
		<?php } ?>
		</tr>
	<?php } ?>
	</tbody>
</table>