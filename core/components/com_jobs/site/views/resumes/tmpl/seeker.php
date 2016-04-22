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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$database = App::get('db');

$jt = new \Components\Jobs\Tables\JobType($database);
$jc = new \Components\Jobs\Tables\JobCategory($database);

$profile = Components\Members\Models\Member::oneOrNew($this->seeker->uid);

$jobtype = $jt->getType($this->seeker->sought_type, strtolower(Lang::txt('COM_JOBS_TYPE_ANY')));
$jobcat  = $jc->getCat($this->seeker->sought_cid, strtolower(Lang::txt('COM_JOBS_CATEGORY_ANY')));

$title = Lang::txt('COM_JOBS_ACTION_DOWNLOAD') . ' ' . $this->seeker->name . ' ' . ucfirst(Lang::txt('COM_JOBS_RESUME'));

// Get the configured upload path
$base_path = DS . trim($this->params->get('webpath', '/site/members'), DS);

$path = $base_path . DS . \Hubzero\Utility\String::pad($this->seeker->uid);

if (!is_dir(PATH_APP . $path))
{
	if (!Filesystem::makeDirectory(PATH_APP . $path))
	{
		$path = '';
	}
}

$resume = is_file(PATH_APP . $path . DS . $this->seeker->filename) ? $path . DS . $this->seeker->filename : '';
?>
<div class="aboutme<?php echo $this->seeker->mine && $this->list ? ' mine' : ''; echo isset($this->seeker->shortlisted) && $this->seeker->shortlisted ? ' shortlisted' : ''; ?>">
	<div class="thumb">
		<img src="<?php echo $profile->picture(); ?>" alt="<?php echo $this->seeker->name; ?>" />
	</div>
	<div class="grid">
		<div class="aboutlb col span5">
			<?php echo $this->list ? '<a href="' . Route::url('index.php?option=' . $this->option . '&id=' . $this->seeker->uid . '&active=resume') . '" class="profilelink">' : ''; ?>
			<?php echo $this->seeker->name; ?>
			<?php echo $this->list ? '</a>' : ''; ?>
			<?php if ($this->seeker->countryresident) { ?>
				, <span class="wherefrom"><?php echo $this->escape($this->seeker->countryresident); ?></span>
			<?php } ?>
			<?php if ($this->seeker->tagline) { ?>
				<blockquote>
					<?php echo stripslashes($this->seeker->tagline); ?>
				</blockquote>
			<?php } ?>
		</div>
		<div class="lookingforlb col span5 omega">
			<?php echo Lang::txt('COM_JOBS_LOOKING_FOR'); ?>
			<span class="jobprefs">
				<?php echo $jobtype ? $jobtype : ' '; ?>
				<?php echo $jobcat ? ' &bull; ' . $jobcat : ''; ?>
			</span>
			<span class="abouttext">
				<?php echo stripslashes($this->seeker->lookingfor); ?>
			</span>
		</div>
	</div>

	<?php if ($this->seeker->mine) { ?>
		<span class="editbt">
			<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->seeker->uid . '&active=resume&action=editprefs'); ?>" title="<?php echo Lang::txt('COM_JOBS_ACTION_EDIT_MY_PROFILE'); ?>">&nbsp;</a>
		</span>
	<?php } else if ($this->emp or $this->admin) { ?>
		<span id="o<?php echo $this->seeker->uid; ?>">
			<a href="<?php echo Route::url('index.php?option=com_jobs&oid=' . $this->seeker->uid . '&task=shortlist'); ?>" class="favvit" title="<?php echo isset($this->seeker->shortlisted) && $this->seeker->shortlisted ? Lang::txt('COM_JOBS_ACTION_REMOVE_FROM_SHORTLIST') : Lang::txt('COM_JOBS_ACTION_ADD_TO_SHORTLIST'); ?>">
				<?php echo isset($this->seeker->shortlisted) && $this->seeker->shortlisted ? Lang::txt('COM_JOBS_ACTION_REMOVE_FROM_SHORTLIST') : Lang::txt('COM_JOBS_ACTION_ADD_TO_SHORTLIST'); ?>
			</a>
		</span>
	<?php } ?>

	<div class="clear leftclear"></div>
	<span class="indented">
		<?php if ($resume) { ?>
			<a href="<?php echo Route::url('index.php?option=' . $this->option . '&id=' . $this->seeker->uid . '&active=resume&action=download'); ?>" class="resume getit" title="<?php echo $title; ?>">
				<?php echo ucfirst(Lang::txt('COM_JOBS_RESUME')); ?>
			</a>
			<span class="mini"><?php echo Lang::txt('COM_JOBS_LAST_UPDATE'); ?>: <?php echo $this->seeker->created; ?></span>
			<?php if ($this->seeker->url) {
				$url = (strpos($this->seeker->url, "http://") === false && strpos($this->seeker->url, "https://") === false) ? "http://" . $this->seeker->url : $this->seeker->url;
				?>
				<span class="mini"> | </span>
				<span class="mini">
					<a href="<?php echo $url; ?>" class="web" rel="external" title="<?php echo Lang::txt('COM_JOBS_MEMBER_WEBSITE') . ': ' . $this->seeker->url; ?>"><?php echo Lang::txt('COM_JOBS_WEBSITE'); ?></a>
				</span>
			<?php } ?>
			<?php if ($this->seeker->linkedin) { ?>
				<span class="mini"> | </span>
				<span class="mini">
					<a href="<?php echo $this->seeker->linkedin; ?>" class="linkedin" rel="external" title="<?php echo Lang::txt('COM_JOBS_MEMBER_LINKEDIN'); ?>"><?php echo Lang::txt('COM_JOBS_LINKEDIN'); ?></a>
				</span>
			<?php } ?>
		<?php } else { ?>
			<span class="unavail"><?php echo Lang::txt('COM_JOBS_ACTION_DOWNLOAD'); ?></span>
		<?php } ?>
	</span>
</div>
