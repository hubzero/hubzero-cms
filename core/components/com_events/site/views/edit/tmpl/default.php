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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->css('calendar.css')
     ->css('jquery.ui.css', 'system')
     ->js();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>

	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="icon-browse browse btn" href="<?php echo Route::url('index.php?option=' . $this->option); ?>"><?php echo Lang::txt('EVENTS_BROWSE'); ?></a>
			</li>
		</ul>
	</div>
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=save'); ?>" method="post" id="hubForm">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
		<div class="explaination">
			<p><?php echo Lang::txt('EVENTS_CAL_LANG_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo ($this->row->id) ? Lang::txt('EVENTS_UPDATE_EVENT') : Lang::txt('EVENTS_NEW_EVENT');?></legend>

			<label>
				<?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?>: <span class="required"><?php echo Lang::txt('EVENTS_CAL_LANG_REQUIRED'); ?></span>
				<?php echo \Components\Events\Helpers\Html::buildCategorySelect($this->row->catid, '', $this->gid, $this->option); ?>
			</label>

			<label>
				<?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_TITLE'); ?>: <span class="required"><?php echo Lang::txt('EVENTS_CAL_LANG_REQUIRED'); ?></span>
				<input type="text" name="title" maxlength="250" value="<?php echo $this->escape(html_entity_decode(stripslashes($this->row->title))); ?>" />
			</label>

			<label>
				<?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?>:
				<textarea name="econtent" id="econtent" rows="15" cols="10"><?php echo $this->escape(stripslashes($this->row->content)); ?></textarea>
			</label>

			<label>
				<?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_ADRESSE'); ?>
				<input type="text" name="adresse_info" maxlength="120" value="<?php echo $this->escape(stripslashes($this->row->adresse_info)); ?>" />
			</label>

			<label>
				<?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_EXTRA'); ?>
				<input type="text" name="extra_info" maxlength="240" value="<?php echo $this->escape(stripslashes($this->row->extra_info)); ?>" />
			</label>
			<?php
				if ($this->fields) {
					foreach ($this->fields as $field)
					{
						?>
						<label>
							<?php echo $field[1]; ?>: <?php echo ($field[3]) ? '<span class="required">required</span>' : ''; ?>
							<?php
							if ($field[2] == 'checkbox') {
								echo '<input class="option" type="checkbox" name="fields['. $field[0] .']" value="1"';
								if (stripslashes(end($field)) == 1) {
									echo ' checked="checked"';
								}
								echo ' />';
							} else {
								echo '<input type="text" name="fields['. $field[0] .']" size="45" maxlength="255" value="'. $this->escape(stripslashes(end($field))) .'" />';
							}
							?>
						</label>
			<?php
					}
				}
			?>
			<label>
				<?php echo Lang::txt('EVENTS_E_TAGS'); ?>
				<?php
				$tf = Event::trigger('hubzero.onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->lists['tags'])));
				if (count($tf) > 0) {
					echo $tf[0];
				} else {
					echo '<input type="text" name="tags" value="'. $this->escape($this->lists['tags']) .'" size="38" />';
				}
				?>
			</label>
			<fieldset>
				<legend><?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_TIME'); ?></legend>
				<label for="publish_up">
				<?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_STARTDATE').' &amp; '.Lang::txt('EVENTS_CAL_LANG_EVENT_STARTTIME'); ?></label>
				<p>
					<?php //echo Html::input('calendar', 'publish_up', $start_publish, array('class'=>'option inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
					<input class="option" type="text" name="publish_up" id="publish_up" size="10" maxlength="10" value="<?php echo $this->times['start_publish']; ?>" />
					<input class="option" type="text" name="start_time" id="start_time" size="5" maxlength="6" value="<?php echo $this->times['start_time']; ?>" />
					<?php if ($this->config->getCfg('calUseStdTime') =='YES') { ?>
					<input class="option" id="start_pm0" name="start_pm" type="radio"  value="0" <?php if (!$this->times['start_pm']) echo 'checked="checked"'; ?> /><small>AM</small>
					<input class="option" id="start_pm1" name="start_pm" type="radio"  value="1" <?php if ($this->times['start_pm']) echo 'checked="checked"'; ?> /><small>PM</small>
					<?php } ?>
				</p>

				<label for="publish_down">
				<?php echo Lang::txt('EVENTS_CAL_LANG_EVENT_ENDDATE').' &amp; '.Lang::txt('EVENTS_CAL_LANG_EVENT_ENDTIME'); ?></label>
				<p>
					<?php //echo Html::input('calendar', 'publish_down', $stop_publish, array('class'=>'option inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
					<input class="option" type="text" name="publish_down" id="publish_down" size="10" maxlength="10" value="<?php echo $this->times['stop_publish']; ?>" />
					<input class="option" type="text" name="end_time" id="end_time" size="5" maxlength="6" value="<?php echo $this->times['end_time']; ?>" />
					<?php if ($this->config->getCfg('calUseStdTime') =='YES') { ?>
					<input class="option" id="end_pm0" name="end_pm" type="radio"  value="0" <?php if (!$this->times['end_pm']) echo 'checked="checked"'; ?> /><small>AM</small>
					<input class="option" id="end_pm1" name="end_pm" type="radio"  value="1" <?php if ($this->times['end_pm']) echo 'checked="checked"'; ?> /><small>PM</small>
					<?php } ?>
				</p>

				<label>
					<?php echo Lang::txt('EVENTS_CAL_TIME_ZONE'); ?>
					<?php echo \Components\Events\Helpers\Html::buildTimeZoneSelect($this->times['time_zone'], ''); ?>
				</label>
			</fieldset>
			<input type="hidden" name="state" value="<?php echo $this->escape($this->row->state); ?>" />
		</fieldset><div class="clear"></div>

		<input type="hidden" name="email" value="<?php echo $this->escape(stripslashes($this->row->email)); ?>" />
		<input type="hidden" name="restricted" value="<?php echo $this->escape(stripslashes($this->row->restricted)); ?>" />
		<p class="submit">
			<input class="btn btn-success" type="submit" value="<?php echo Lang::txt('EVENTS_SAVE'); ?>" />

			<a class="btn btn-secondary" href="<?php echo $this->row->id ? Route::url('index.php?option=' . $this->option . '&task=details&id=' . $this->row->id) : Route::url('index.php?option=' . $this->option); ?>">
				<?php echo Lang::txt('EVENTS_CANCEL'); ?>
			</a>
		</p>

		<?php echo Html::input('token'); ?>
		<?php echo Html::input('honeypot'); ?>
		<input type="hidden" name="created_by" value="<?php echo $this->row->created_by; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="id" id="event-id" value="<?php echo $this->row->id; ?>" />
	</form>
</section>
