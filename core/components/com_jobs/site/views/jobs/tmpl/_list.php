<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
				<span class="datedisplay"><?php echo Date::of($this->jobs[$i]->added)->toLocal('d&\nb\sp;M,&\nb\sp;20y'); ?></span>
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
