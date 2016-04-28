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

$this->css('jobs', 'com_jobs');
?>

<section class="main section">
	<div class="subject">
		<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>

	<?php if ($this->self && $this->file) { ?>
		<div id="prefs" class="<?php echo $this->js->active ? 'yes_search' : 'no_search'; ?>">
			<p>
				<?php if ($this->js->active && $this->file) { ?>
					<?php echo Lang::txt('PLG_MEMBERS_RESUME_PROFILE_INCLUDED'); ?>
				<?php } else if ($this->file) { ?>
					<?php echo Lang::txt('PLG_MEMBERS_RESUME_PROFILE_NOT_INCLUDED'); ?>
				<?php } ?>

		<?php if (!$this->editpref) { ?>
				<span class="includeme">
					<a href="<?php echo Route::url($this->member->link() . '&active=resume&action=activate' . '&on=' . ($this->js->active && $this->file ? 0 : 1)); ?>">
				<?php if ($this->js->active && $this->file) { ?>
					[-] <?php echo Lang::txt('PLG_MEMBERS_RESUME_ACTION_HIDE'); ?>
				<?php } else if ($this->file) { ?>
					[+] <?php echo Lang::txt('PLG_MEMBERS_RESUME_ACTION_INCLUDE'); ?>
				<?php } ?>
					</a>.
				</span>
		<?php } else { ?>
			</p>

			<form id="prefsForm" method="post" action="<?php echo Route::url($this->member->link() . '&active=resume'); ?>">
				<fieldset>
					<legend>
						<?php echo $this->editpref==1 ? Lang::txt('PLG_MEMBERS_RESUME_ACTION_INCLUDE_WITH_INFO') :  Lang::txt('PLG_MEMBERS_RESUME_ACTION_EDIT_PREFS'); ?>
					</legend>

					<label class="spacious">
						<?php echo Lang::txt('PLG_MEMBERS_RESUME_PERSONAL_TAGLINE'); ?>
						<span class="selectgroup">
							<textarea name="tagline" id="tagline-men" rows="6" cols="35"><?php echo stripslashes($this->js->tagline); ?></textarea>
							<span class="counter"><span id="counter_number_tagline"></span> <?php echo Lang::txt('PLG_MEMBERS_RESUME_CHARS_LEFT'); ?></span>
						</span>
					</label>
					<label class="spacious">
						<?php echo Lang::txt('PLG_MEMBERS_RESUME_LOOKING_FOR'); ?>
						<span class="selectgroup">
							<textarea name="lookingfor" id="lookingfor-men" rows="6" cols="35"><?php echo stripslashes($this->js->lookingfor); ?></textarea>
							<span class="counter"><span id="counter_number_lookingfor"></span> <?php echo Lang::txt('PLG_MEMBERS_RESUME_CHARS_LEFT'); ?></span>
						</span>
					</label>
					<label>
						<?php echo Lang::txt('PLG_MEMBERS_RESUME_WEBSITE');?>
						<span class="selectgroup">
							<input type="text" class="inputtxt" maxlength="190" name="url" value="<?php echo ($this->js->url ? $this->js->url : $this->member->get('url')); ?>" placeholder="http://" />
						</span>
					</label>
					<label>
						<?php echo Lang::txt('PLG_MEMBERS_RESUME_LINKEDIN'); ?>
						<span class="selectgroup">
							<input type="text" class="inputtxt" maxlength="190" name="linkedin" value="<?php echo $this->js->linkedin; ?>" placeholder="http://" />
						</span>
					</label>
					<label class="cats">
						<?php echo Lang::txt('PLG_MEMBERS_RESUME_POSITION_SOUGHT'); ?>:
					</label>

					<?php
					// get job types
					$types = $this->jt->getTypes();
					$types[0] = Lang::txt('PLG_MEMBERS_RESUME_TYPE_ANY');

					// get job categories
					$cats = $this->jc->getCats();
					$cats[0] = Lang::txt('PLG_MEMBERS_RESUME_CATEGORY_ANY');
					?>

					<div class="selectgroup catssel">
						<label>
							<select name="sought_type" id="sought_type">
								<?php
								foreach ($types as $avalue => $alabel)
								{
									$selected = ($avalue == $this->js->sought_type || $alabel == $this->js->sought_type)
											  ? ' selected="selected"'
											  : '';
									echo ' <option value="' . $this->escape($avalue) . '"' . $selected . '>' . $this->escape($alabel) . '</option>' . "\n";
								}
								?>
							</select>
						</label>
						<label>
							<select name="sought_cid" id="sought_cid">
								<?php
								foreach ($cats as $avalue => $alabel)
								{
									$selected = ($avalue == $this->js->sought_cid || $alabel == $this->js->sought_cid)
											  ? ' selected="selected"'
											  : '';
									echo ' <option value="' . $this->escape($avalue) . '"' . $selected . '>' . $this->escape($alabel) . '</option>' . "\n";
								}
								?>
							</select>
						</label>
					</div>
					<div class="clear"></div>

					<div class="submitblock">
						<span class="selectgroup">
							<input type="submit" class="btn" value="<?php echo $this->editpref == 1 ? Lang::txt('PLG_MEMBERS_RESUME_ACTION_SAVE_AND_INCLUDE') : Lang::txt('PLG_MEMBERS_RESUME_ACTION_SAVE'); ?>" />
							<span>
								<a href="<?php echo Route::url($this->member->link() . '&active=resume'); ?>" class="btn"><?php echo Lang::txt('PLG_MEMBERS_RESUME_CANCEL'); ?></a>
							</span>
						</span>
					</div>
					<input type="hidden" name="activeres" value="<?php echo $this->editpref == 1 ? 1 : $this->js->active; ?>" />
					<input type="hidden" name="action" value="saveprefs" />
				</fieldset>
			</form>
		<?php } ?>
		</div>
	<?php } ?>

		<?php
		// seeker details block
		if ($this->js->active && $this->file)
		{
			// get seeker info
			$seeker = $this->js->getSeeker($this->member->get('id'), User::get('id'));

			if (!$seeker or count($seeker)==0)
			{
				echo '<p class="error">'.Lang::txt('PLG_MEMBERS_RESUME_ERROR_RETRIEVING_PROFILE').'</p>';
			}
			else
			{
				$this->view('seeker')
				     ->set('seeker', $seeker[0])
				     ->set('emp', $this->emp)
				     ->set('admin', 0)
				     ->set('option', $this->option)
				     ->set('params', $this->params)
				     ->set('list', 0)
				     ->display();
			}
		}
		?>

		<?php if ($this->resume->id  && $this->file && $this->self) { ?>
			<table class="list">
				<thead>
					<tr>
						<th class="col halfwidth"><?php echo ucfirst(Lang::txt('PLG_MEMBERS_RESUME_RESUME')); ?></th>
						<th class="col"><?php echo Lang::txt('PLG_MEMBERS_RESUME_LAST_UPDATED'); ?></th>
						<?php echo $this->self ? '<th scope="col">' . Lang::txt('PLG_MEMBERS_RESUME_OPTIONS') . '</th>' : ''; ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
						<?php
						$title = $this->resume->title ?  stripslashes($this->resume->title) : $this->resume->filename;
						$default_title = $this->member->get('firstname')
									? $this->member->get('firstname').' '.$this->member->get('lastname').' '.Lang::txt('PLG_MEMBERS_RESUME')
									: $this->member->get('name').' '.Lang::txt('PLG_MEMBERS_RESUME');
						?>
						<?php if ($this->edittitle && $this->self) { ?>
							<form id="editTitleForm" method="post" action="<?php echo Route::url($this->member->link() . '&active=resume&action=savetitle'); ?>">
								<fieldset>
									<label class="resume">
										<input type="text" name="title" value="<?php echo $this->escape($title); ?>" class="gettitle" maxlength="40" />
										<input type="hidden" name="author" value="<?php echo $this->member->get('id'); ?>" />
										<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_RESUME_ACTION_SAVE'); ?>" />
									</label>
								</fieldset>
							</form>
						<?php } else { ?>
							<a class="resume" href="<?php echo Route::url($this->member->link() . '&active=resume&action=download'); ?>">
								<?php echo $this->escape($title); ?>
							</a>
						<?php } ?>
						</td>
						<td>
							<time datetime="<?php echo $this->resume->created; ?>"><?php echo Date::of($this->resume->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
						</td>
						<td>
							<a class="trash" href="<?php echo Route::url($this->member->link() . '&active=resume&action=deleteresume'); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_RESUME_ACTION_DELETE_THIS_RESUME'); ?>">
								<?php echo Lang::txt('PLG_MEMBERS_RESUME_ACTION_DELETE'); ?>
							</a>
						</td>
					</tr>
				</tbody>
			</table>
		<?php } else if (!$this->js->active) { ?>
			<p class="no_resume">
				<?php echo (!$this->self) ? Lang::txt('PLG_MEMBERS_RESUME_USER_HAS_NO_RESUME') : Lang::txt('PLG_MEMBERS_RESUME_YOU_HAVE_NO_RESUME'); ?>
			</p>
		<?php } ?>

		<?php if ($this->self) { ?>
			<form class="addResumeForm" method="post" action="<?php echo Route::url($this->member->link() . '&active=resume'); ?>" enctype="multipart/form-data">
				<fieldset>
					<legend>
						<?php echo ($this->resume->id && $this->file)
									? Lang::txt('PLG_MEMBERS_RESUME_ACTION_UPLOAD_NEW_RESUME') . ' <span>(' . Lang::txt('PLG_MEMBERS_RESUME_WILL_BE_REPLACED') . ')</span>' . "\n"
									:  Lang::txt('PLG_MEMBERS_RESUME_ACTION_UPLOAD_A_RESUME') . "\n"; ?>
					</legend>
					<div>
						<label>
							<?php echo Lang::txt('PLG_MEMBERS_RESUME_ACTION_ATTACH_FILE'); ?>
							<input type="file" name="uploadres" id="uploadres" />
						</label>
					</div>

					<?php echo Html::input('token'); ?>

					<input type="hidden" name="action" value="uploadresume" />
					<input type="hidden" name="path" value="<?php echo $this->escape($this->path); ?>" />
					<input type="hidden" name="emp" value="<?php echo $this->escape($this->emp); ?>" />
					<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_MEMBERS_RESUME_ACTION_UPLOAD'); ?>" />
				</fieldset>
			</form>
		<?php } ?>

	</div><!-- / .subject -->
	<div class="aside">
		<div class="container">
			<?php if ($this->self) { ?>
				<p><?php echo Lang::txt('PLG_MEMBERS_RESUME_HUB_OFFERS'); ?></p>
			<?php } else { ?>
				<p><?php echo Lang::txt('PLG_MEMBERS_RESUME_NOTICE_YOU_ARE_EMPLOYER'); ?></p>
			<?php } ?>

			<p>
				<a class="icon-next btn" href="<?php echo Route::url('index.php?option=com_jobs'); ?>">
					<?php echo ($this->config->get('industry') ? Lang::txt('PLG_MEMBERS_RESUME_VIEW_JOBS_IN', $this->config->get('industry')) : Lang::txt('PLG_MEMBERS_RESUME_VIEW_JOBS')); ?>
				</a>
			</p>
		</div><!-- / .container -->

		<?php if ($this->self && $this->js->active) { ?>
		<div class="container">
			<table class="jobstats">
				<caption><?php echo Lang::txt('PLG_MEMBERS_RESUME_YOUR_STATS'); ?></caption>
				<tbody>
					<tr>
						<th><?php echo Lang::txt('PLG_MEMBERS_RESUME_TOTAL_VIEWED'); ?></th>
						<td><?php echo $this->stats['totalviewed']; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_MEMBERS_RESUME_VIEWED_PAST_30_DAYS'); ?></th>
						<td><?php echo $this->stats['viewed_thismonth']; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_MEMBERS_RESUME_VIEWED_PAST_7_DAYS'); ?></th>
						<td><?php echo $this->stats['viewed_thisweek']; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_MEMBERS_RESUME_VIEWED_PAST_24_HOURS'); ?></th>
						<td><?php echo $this->stats['viewed_today']; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('PLG_MEMBERS_RESUME_PROFILE_SHORTLISTED'); ?></th>
						<td><?php echo $this->stats['shortlisted']; ?></td>
					</tr>
				</tbody>
			</table>
		</div><!-- / .container -->
		<?php } ?>
	</div><!-- / .aside -->
</section>
