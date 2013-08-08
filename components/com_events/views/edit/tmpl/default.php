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
defined('_JEXEC') or die( 'Restricted access' );
?>
<div id="content-header">
    <h2><?php echo $this->title; ?></h2>
</div>
<div id="content-header-extra">
	<ul id="useroptions">
		<li class="last">
			<a class="icon-browse browse btn" href="<?php echo JRoute::_('index.php?option=' . $this->option); ?>"><?php echo JText::_('Browse Events'); ?></a>
		</li>
	</ul>
</div>

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=save'); ?>" method="post" id="hubForm">
<?php if ($this->getError()) { ?>
		<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>
		<div class="explaination">
			<p><?php echo JText::_('EVENTS_CAL_LANG_EXPLANATION'); ?></p>
		</div>
		<fieldset>
			<legend><?php echo ($this->row->id) ? JText::_('EVENTS_UPDATE_EVENT') : JText::_('EVENTS_NEW_EVENT');?></legend>
			
			<label>
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_CATEGORY'); ?>: <span class="required"><?php echo JText::_('EVENTS_CAL_LANG_REQUIRED'); ?></span>
				<?php echo EventsHtml::buildCategorySelect($this->row->catid, '', $this->gid, $this->option); ?>
			</label>
			
			<label>
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_TITLE'); ?>: <span class="required"><?php echo JText::_('EVENTS_CAL_LANG_REQUIRED'); ?></span>
				<input type="text" name="title" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" />
			</label>

			<label>
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_DESCRIPTION'); ?>: <span class="required"><?php echo JText::_('EVENTS_CAL_LANG_REQUIRED'); ?></span>
				<textarea name="econtent" id="econtent" rows="15" cols="10"><?php echo $this->escape(stripslashes($this->row->content)); ?></textarea>
			</label>

			<label>
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_ADRESSE'); ?>
				<input type="text" name="adresse_info" maxlength="120" value="<?php echo $this->escape(stripslashes($this->row->adresse_info)); ?>" />
			</label>

			<label>
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_EXTRA'); ?>
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
				<?php echo JText::_('EVENTS_E_TAGS'); ?>
<?php
			JPluginHelper::importPlugin( 'hubzero' );
			$dispatcher =& JDispatcher::getInstance();
			$tf = $dispatcher->trigger( 'onGetMultiEntry', array(array('tags', 'tags', 'actags','',$this->lists['tags'])) );
			if (count($tf) > 0) {
				echo $tf[0];
			} else {
				echo '<input type="text" name="tags" value="'. $this->escape($this->lists['tags']) .'" size="38" />';
			}
?>
			</label>
			<fieldset>
				<legend><?php echo JText::_('EVENTS_CAL_LANG_EVENT_TIME'); ?></legend>
				<label for="publish_up">
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_STARTDATE').' &amp; '.JText::_('EVENTS_CAL_LANG_EVENT_STARTTIME'); ?></label>
				<p>
					<?php //echo JHTML::_('calendar', $start_publish, 'publish_up', 'publish_up', '%Y-%m-%d', array('class'=>'option inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
                    <input class="option" type="text" name="publish_up" id="publish_up" size="10" maxlength="10" value="<?php echo $this->times['start_publish']; ?>" />
					<input class="option" type="text" name="start_time" id="start_time" size="5" maxlength="6" value="<?php echo $this->times['start_time']; ?>" />
					<?php if ($this->config->getCfg('calUseStdTime') =='YES') { ?>
					<input class="option" id="start_pm0" name="start_pm" type="radio"  value="0" <?php if (!$this->times['start_pm']) echo 'checked="checked"'; ?> /><small>AM</small>
					<input class="option" id="start_pm1" name="start_pm" type="radio"  value="1" <?php if ($this->times['start_pm']) echo 'checked="checked"'; ?> /><small>PM</small>
					<?php } ?>
				</p>
				
				<label for="publish_down">
				<?php echo JText::_('EVENTS_CAL_LANG_EVENT_ENDDATE').' &amp; '.JText::_('EVENTS_CAL_LANG_EVENT_ENDTIME'); ?></label>
				<p>
					<?php //echo JHTML::_('calendar', $stop_publish, 'publish_down', 'publish_down', '%Y-%m-%d', array('class'=>'option inputbox', 'size'=>'10',  'maxlength'=>'10')); ?>
					<input class="option" type="text" name="publish_down" id="publish_down" size="10" maxlength="10" value="<?php echo $this->times['stop_publish']; ?>" />
					<input class="option" type="text" name="end_time" id="end_time" size="5" maxlength="6" value="<?php echo $this->times['end_time']; ?>" />
					<?php if ($this->config->getCfg('calUseStdTime') =='YES') { ?>
					<input class="option" id="end_pm0" name="end_pm" type="radio"  value="0" <?php if (!$this->times['end_pm']) echo 'checked="checked"'; ?> /><small>AM</small>
					<input class="option" id="end_pm1" name="end_pm" type="radio"  value="1" <?php if ($this->times['end_pm']) echo 'checked="checked"'; ?> /><small>PM</small>
					<?php } ?>
				</p>
				
				<label>
					<?php echo JText::_('EVENTS_CAL_TIME_ZONE'); ?>
					<?php echo EventsHtml::buildTimeZoneSelect($this->times['time_zone'], ''); ?>
				</label>
			</fieldset>
			<?php if ($this->row->id) { ?>
			<label>
				<?php echo JText::_('EVENTS_E_PUBLISHING'); ?>
				<?php echo $this->lists['state']; ?>
			</label>
			<?php } else { ?>
			<input type="hidden" name="state" value="<?php echo $this->escape($this->row->state); ?>" />
			<?php } ?>
		</fieldset><div class="clear"></div>
<?php if ($this->config->getCfg('calSimpleEventForm') != 'YES') { ?>
		<div class="explaination">
			<p><?php echo JText::_('EVENTS_CAL_LANG_EVENT_REPEAT_INFO'); ?></p>
		</div>
		<fieldset>
    		<legend><?php echo JText::_('EVENTS_CAL_LANG_EVENT_REPEATTYPE'); ?></legend>

			<label>
				<input class="option" id="reccurtype-no" name="reccurtype" type="radio" value="0" <?php if ($this->row->reccurtype == 0) { echo 'checked="checked"'; } ?> /> 
				<strong><?php echo JText::_('Do not repeat'); ?></strong>
			</label>
			
			<fieldset>
				<legend><strong><?php echo JText::_('Repeat:'); ?></strong></legend>
			<table summary="<?php echo JText::_('Repeat type'); ?>">
				<tbody>
					<tr>
						<th><?php echo JText::_('EVENTS_CAL_LANG_REP_DAY'); ?></th>
						<td colspan="2" class="frm_td_bydays">
							<label class="option"><input class="option" id="reccurtype0" name="reccurtype" type="radio" value="0" /> <?php echo JText::_('EVENTS_CAL_LANG_ALLDAYS'); ?></label>
						</td>
					</tr>
					<tr>
						<th rowspan="3"><?php echo JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?></th>
						<td class="frm_td_byweeks">
							<label class="option"><input class="option" id="reccurtype1" name="reccurtype" type="radio" value="1" <?php if ($this->row->reccurtype == 1) { echo 'checked="checked"'; } ?> /> 1 * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?></label>
						</td>
						<td class="frm_td_byweeks">
							<?php 
							if ($this->row->reccurtype == 1 || $this->row->reccurtype == 2) {
								$arg = '';
							} else {
								$arg = ' disabled="disabled"';
							}
							echo EventsHtml::buildReccurDaySelect($this->row->reccurday,'reccurday_week',$arg); ?>
						</td>
					</tr>
					<tr>
						<td class="frm_td_byweeks">
							<label class="option"><input class="option" id="reccurtype2" name="reccurtype" type="radio" value="2" <?php if ($this->row->reccurtype == 2) { echo 'checked="checked"'; } ?> /> n * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_WEEK'); ?></label>
						</td>
						<td class="frm_td_byweeks">
							<?php 
							if ($this->row->reccurtype == 1 || $this->row->reccurtype == 2) {
								$arg = '';
							} else {
								$arg = ' disabled="disabled"';
							}
							echo EventsHtml::buildWeekDaysCheck($this->row->reccurweekdays, 'class="option"'.$arg); ?>
						</td>
					</tr>
					<tr>
						<td class="frm_td_byweeks"><em><?php echo JText::_('EVENTS_CAL_LANG_EVENT_WEEKOPT'); ?></em></td>
						<td class="frm_td_byweeks">
							<?php echo EventsHtml::buildWeeksCheck($this->row->reccurweeks, $arg); ?>
							<label class="option"><input class="option" id="cb_wn6" name="reccurweekss" type="radio" value="pair" <?php if ($this->row->reccurweeks == 'pair') { echo 'checked="checked"'; } else { echo 'disabled="disabled"'; } ?> /> <?php echo JText::_('EVENTS_CAL_LANG_REP_WEEKPAIR'); ?></label><br />
							<label class="option"><input class="option" id="cb_wn7" name="reccurweekss" type="radio" value="impair" <?php if ($this->row->reccurweeks == 'impair') { echo 'checked="checked"'; } else { if ($this->row->reccurtype != 1 && $this->row->reccurtype != 2) { echo 'disabled="disabled"'; } } ?> /> <?php echo JText::_('EVENTS_CAL_LANG_REP_WEEKIMPAIR'); ?></label>
						</td>
					</tr>
					<tr>
						<th rowspan="2"><?php echo JText::_('EVENTS_CAL_LANG_REP_MONTH'); ?></th>
						<td class="frm_td_bymonth">
							<label class="option"><input class="option" id="reccurtype3" name="reccurtype" type="radio" value="3" <?php if ($this->row->reccurtype == 3) { echo 'checked="checked"'; } ?> /> 1 * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_MONTH'); ?></label>
						</td>
						<td class="frm_td_bymonth">
							<?php 
							if ($this->row->reccurtype == 3) {
								$arg = '';
							} else {
								$arg = ' disabled="disabled"';
							}
							echo EventsHtml::buildReccurDaySelect($this->row->reccurday_month,'reccurday_month',$arg); ?>
						</td>
					</tr>
					<tr>
						<td class="frm_td_bymonth">
							<label class="option"><input class="option" id="reccurtype4" name="reccurtype" type="radio" value="4" <?php if ($this->row->reccurtype == 4) { echo 'checked="checked"'; } ?> /><?php echo JText::_('EVENTS_CAL_LANG_EACH').' '.JText::_('EVENTS_CAL_LANG_ENDMONTH'); ?></label>
						</td>
					</tr>
					<tr>
						<th rowspan="2"><?php echo JText::_('EVENTS_CAL_LANG_REP_YEAR'); ?></th>
						<td class="frm_td_byyear">
							<label class="option"><input class="option" id="reccurtype5" name="reccurtype" type="radio" value="5" <?php if ($this->row->reccurtype == 5) { echo 'checked="checked"'; } ?> /> 1 * <?php echo JText::_('EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('EVENTS_CAL_LANG_REP_YEAR'); ?></label>
						</td>
						<td class="frm_td_byyear">
							<?php 
							if ($this->row->reccurtype == 5) {
								$arg = '';
							} else {
								$arg = ' disabled="disabled"';
							}
							echo EventsHtml::buildReccurDaySelect($this->row->reccurday_year,'reccurday_year',$arg); ?>
						</td>
					</tr>
				</tbody>
            </table>
			</fieldset>
		</fieldset><div class="clear"></div>
<?php } ?>
		<input type="hidden" name="email" value="<?php echo $this->escape(stripslashes($this->row->email)); ?>" />
		<input type="hidden" name="restricted" value="<?php echo $this->escape(stripslashes($this->row->restricted)); ?>" />
		<p class="submit"><input type="submit" value="<?php echo JText::_('EVENTS_SAVE'); ?>" /></p>
      
		<input type="hidden" name="created_by" value="<?php echo $this->row->created_by; ?>" />
		<input type="hidden" name="created_by_alias" value="<?php echo $this->escape($this->row->created_by_alias); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="save" />
		<input type="hidden" name="id" id="event-id" value="<?php echo $this->row->id; ?>" />
	</form>
</div>
