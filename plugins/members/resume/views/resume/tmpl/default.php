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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$dateformat = '%d %b %Y';
$timeformat = '%I:%M %p';
$tz = 0;
if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateformat = 'd M Y';
	$timeformat = 'H:i p';
	$tz = true;
}

$juser = JFactory::getUser();
?>

	<div class="aside">
		<div class="container">
		<?php if ($this->self) { ?>
			<p><?php echo JText::_('PLG_RESUME_HUB_OFFERS'); ?></p>
		<?php } else { ?>
			<p><?php echo JText::_('PLG_RESUME_NOTICE_YOU_ARE_EMPLOYER'); ?></p>
		<?php } ?>

			<p>
				<a href="<?php echo JRoute::_('index.php?option=com_jobs'); ?>" class="minimenu">
					<?php echo JText::_('View Jobs') . ($this->config->get('industry') ? ' ' . JText::_('IN') . ' ' . $this->config->get('industry') : ''); ?>
				</a>
			</p>
		</div><!-- / .container -->

		<?php if ($this->self && $this->js->active) { ?>
		<div class="container">
			<ul class="jobstats">
				<li class="statstitle"><?php echo JText::_('PLG_RESUME_YOUR_STATS'); ?></li>
				<li>
					<span><?php echo $this->stats['totalviewed']; ?></span>
					<?php echo JText::_('PLG_RESUME_TOTAL_VIEWED'); ?>
				</li>
				<li>
					<span><?php echo $this->stats['viewed_thismonth']; ?></span>
					<?php echo JText::_('PLG_RESUME_VIEWED_PAST_30_DAYS'); ?>
				</li>
				<li>
					<span><?php echo $this->stats['viewed_thisweek']; ?></span>
					<?php echo JText::_('PLG_RESUME_VIEWED_PAST_7_DAYS'); ?>
				</li>
				<li>
					<span><?php echo $this->stats['viewed_today']; ?></span>
					<?php echo JText::_('PLG_RESUME_VIEWED_PAST_24_HOURS'); ?>
				</li>
				<li>
					<span><?php echo $this->stats['shortlisted']; ?></span>
					<?php echo JText::_('PLG_RESUME_PROFILE_SHORTLISTED'); ?>
				</li>
			</ul>
		</div><!-- / .container -->
		<?php } ?>
	</div><!-- / .aside -->
	<div class="subject">
		<?php if ($this->getError()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>

	<?php if ($this->self && $this->file) { ?>
		<div id="prefs" class="<?php echo $this->js->active ? 'yes_search' : 'no_search'; ?>">
			<p>
				<?php if ($this->js->active && $this->file) { ?>
					<?php echo JText::_('PLG_RESUME_PROFILE_INCLUDED'); ?>
				<?php } else if ($this->file) { ?>
					<?php echo JText::_('PLG_RESUME_PROFILE_NOT_INCLUDED'); ?>
				<?php } ?>

		<?php if (!$this->editpref) { ?>
				<span class="includeme">
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=resume&action=activate') . '&on=' . ($this->js->active && $this->file ? 0 : 1); ?>">
				<?php if ($this->js->active && $this->file) { ?>
					[-] <?php echo JText::_('PLG_RESUME_ACTION_HIDE'); ?>
				<?php } else if ($this->file) { ?>
					[+] <?php echo JText::_('PLG_RESUME_ACTION_INCLUDE'); ?>
				<?php } ?>
					</a>.
				</span>
		<?php } else { ?>
			</p>

			<form id="prefsForm" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=resume'); ?>">
				<fieldset>
					<legend>
						<?php echo $this->editpref==1 ? JText::_('PLG_RESUME_ACTION_INCLUDE_WITH_INFO') :  JText::_('PLG_RESUME_ACTION_EDIT_PREFS'); ?>
					</legend>

					<label class="spacious">
						<?php echo JText::_('PLG_RESUME_PERSONAL_TAGLINE'); ?>
						<span class="selectgroup">
							<textarea name="tagline" id="tagline-men" rows="6" cols="35"><?php echo stripslashes($this->js->tagline); ?></textarea>
							<span class="counter"><span id="counter_number_tagline"></span> <?php echo JText::_('chars left'); ?></span>
						</span>
					</label>
					<label class="spacious">
						<?php echo JText::_('PLG_RESUME_LOOKING_FOR'); ?>
						<span class="selectgroup">
							<textarea name="lookingfor" id="lookingfor-men" rows="6" cols="35"><?php echo stripslashes($this->js->lookingfor); ?></textarea>
							<span class="counter"><span id="counter_number_lookingfor"></span> <?php echo JText::_('PLG_RESUME_CHARS_LEFT'); ?></span>
						</span>
					</label>
					<label>
						<?php echo JText::_('PLG_RESUME_WEBSITE');?>
						<span class="selectgroup">
							<input type="text" class="inputtxt" maxlength="190" name="url" value="<?php echo $this->js->url ? $this->js->url : $this->member->get('url'); ?>" />
						</span>
					</label>
					<label>
						<?php echo JText::_('PLG_RESUME_LINKEDIN'); ?>
						<span class="selectgroup">
							<input type="text" class="inputtxt" maxlength="190" name="linkedin" value="' . $this->js->linkedin . '" />
						</span>
					</label>
					<label class="cats">
						<?php echo JText::_('PLG_RESUME_POSITION_SOUGHT'); ?>: 
					</label>

					<?php
					// get job types
					$types = $this->jt->getTypes();
					$types[0] = JText::_('PLG_RESUME_TYPE_ANY');

					// get job categories
					$cats = $this->jc->getCats();
					$cats[0] = JText::_('PLG_RESUME_CATEGORY_ANY');
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
									echo ' <option value="' . $avalue . '"' . $selected . '>' . $alabel . '</option>' . "\n";
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
									echo ' <option value="' . $avalue . '"' . $selected . '>' . $alabel . '</option>' . "\n";
								}
								?>
							</select>
						</label>
					</div>
					<div class="clear"></div>

					<div class="submitblock">
						<span class="selectgroup">
							<input type="submit" value="<?php echo $this->editpref == 1 ? JText::_('PLG_RESUME_ACTION_SAVE_AND_INCLUDE') : JText::_('PLG_RESUME_ACTION_SAVE'); ?>" /> 
							<span class="cancelaction">
								<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=resume'); ?>"><?php echo JText::_('PLG_RESUME_CANCEL'); ?></a>
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
			$seeker = $this->js->getSeeker($this->member->get('uidNumber'), $juser->get('id'));

			if (!$seeker or count($seeker)==0) 
			{
				echo '<p class="error">'.JText::_('PLG_RESUME_ERROR_RETRIEVING_PROFILE').'</p>';
			}
			else 
			{
				echo $this->showSeeker($seeker[0], $this->emp, 0, $this->option);
			}
		}
		?>

		<?php if ($this->resume->id  && $this->file && $this->self) { ?>
			<table class="list">
				<thead>
					<tr>
						<th class="col halfwidth"><?php echo ucfirst(JText::_('PLG_RESUME_RESUME')); ?></th>
						<th class="col"><?php echo JText::_('PLG_RESUME_LAST_UPDATED'); ?></th>
						<?php echo $this->self ? '<th scope="col">' . JText::_('PLG_RESUME_OPTIONS') . '</th>' : ''; ?>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td>
						<?php 
						$title = $this->resume->title ?  stripslashes($this->resume->title) : $this->resume->filename;
						$default_title = $this->member->get('firstname') 
									? $this->member->get('firstname').' '.$this->member->get('lastname').' '.JText::_('PLG_RESUME') 
									: $this->member->get('name').' '.JText::_('PLG_RESUME');
						?>
						<?php if ($this->edittitle && $this->self) { ?>
							<form id="editTitleForm" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=resume&action=savetitle'); ?>">
								<fieldset>
									<label class="resume">
										<input type="text" name="title" value="<?php echo $this->escape($title); ?>" class="gettitle" maxlength="40" />
										<input type="hidden" name="author" value="<?php echo $this->member->get('uidNumber'); ?>" />
										<input type="submit" value="<?php echo JText::_('PLG_RESUME_ACTION_SAVE'); ?>" />
									</label>
								</fieldset>
							</form>
						<?php } else { ?>
							<a class="resume" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=resume&action=download'); ?>">
								<?php echo $this->escape($title); ?>
							</a>
						<?php } ?>
						</td>
						<td>
							<time datetime="<?php echo $this->resume->created; ?>"><?php echo JHTML::_('date', $this->resume->created, $dateformat, $tz); ?></time>
						</td>
						<td>
							<a class="trash" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=resume&action=deleteresume'); ?>" title="<?php echo JText::_('APLG_RESUME_CTION_DELETE_THIS_RESUME'); ?>">
								<?php echo JText::_('PLG_RESUME_ACTION_DELETE'); ?>
							</a>
						</td>
					</tr>
				</tbody>
			</table>
		<?php } else if (!$this->js->active) { ?>
			<p class="no_resume">
				<?php echo (!$this->self) ? JText::_('PLG_RESUME_USER_HAS_NO_RESUME') : JText::_('PLG_RESUME_YOU_HAVE_NO_RESUME'); ?>
			</p>
		<?php } ?>

		<?php if ($this->self) { ?>
			<form class="addResumeForm" method="post" action="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->member->get('uidNumber') . '&active=resume'); ?>" enctype="multipart/form-data">
				<fieldset>
					<legend>
						<?php echo ($this->resume->id && $this->file) 
									? JText::_('PLG_RESUME_ACTION_UPLOAD_NEW_RESUME') . ' <span>(' . JText::_('PLG_RESUME_WILL_BE_REPLACED') . ')</span>' . "\n" 
									:  JText::_('PLG_RESUME_ACTION_UPLOAD_A_RESUME') . "\n"; ?>
					</legend>
					<div>
						<label>
							<?php echo JText::_('PLG_RESUME_ACTION_ATTACH_FILE'); ?>
							<input type="file" name="uploadres" id="uploadres" />
						</label>
					</div>
					<input type="hidden" name="action" value="uploadresume" />
					<input type="hidden" name="path" value="<?php echo $path; ?>" />
					<input type="hidden" name="emp" value="<?php echo $emp; ?>" />
					<input type="submit" value="<?php echo JText::_('PLG_RESUME_ACTION_UPLOAD'); ?>" />
				</fieldset>
			</form>
		<?php } ?>

	</div><!-- / .subject -->
	<div class="clear"></div>
