<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'edit') ? JText::_('COM_EVENTS_EDIT') : JText::_('COM_EVENTS_NEW');
JToolBarHelper::title(JText::_('COM_EVENTS_EVENT').': <small><small>[ '. $text.' ]</small></small>', 'event.png');
JToolBarHelper::save();
JToolBarHelper::cancel();

$editor =& JFactory::getEditor();

ximport('Hubzero_User_Profile');
$xprofilec =& Hubzero_User_Profile::getInstance($this->row->created_by);
$xprofilem =& Hubzero_User_Profile::getInstance($this->row->modified_by);
$userm = is_object($xprofilem) ? $xprofilem->get('name') : '';
$userc = is_object($xprofilec) ? $xprofilec->get('name') : '';

$params = new JParameter($this->row->params, JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->option . DS . 'events.xml');
?>
<script type="text/javascript" src="../components/<?php echo $option; ?>/js/calendar.rc4.js"></script>
<script type="text/javascript">
var HUB = {};

/*window.addEvent('domready', function() {
	myCal1 = new Calendar({ publish_up: 'Y-m-d' }, { direction: 1, tweak: {x: 6, y: 0} });
	myCal2 = new Calendar({ publish_down: 'Y-m-d' }, { direction: 1, tweak: {x: 6, y: 0} });
});*/
</script>

<script type="text/javascript" src="../components/<?php echo $this->option; ?>/js/events.js"></script>
<form action="index.php" method="post" name="adminForm" id="hubForm">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('EVENT'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_TITLE'); ?>: *</td>
						<td><input type="text" name="title" size="45" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->title)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_CATEGORY'); ?>:</th>
						<td><?php echo EventsHtml::buildCategorySelect($this->row->catid, '', 0, $this->option);?></td>
					</tr>
					<tr>
						<td class="key" style="vertical-align:top;"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_ACTIVITY'); ?>:</td>
						<td><?php echo $editor->display('econtent', $this->row->content, '100%', 'auto', '45', '10', false); ?></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_ADRESSE'); ?>:</td>
						<td><input type="text" name="adresse_info" size="45" maxlength="120" value="<?php echo $this->escape(stripslashes($this->row->adresse_info)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_CONTACT'); ?>:</td>
						<td><input type="text" name="contact_info" size="45" maxlength="120" value="<?php echo $this->escape(stripslashes($this->row->contact_info)); ?>" /></td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_EXTRA'); ?>:</td>
						<td><input type="text" name="extra_info" size="45" maxlength="240" value="<?php echo $this->escape(stripslashes($this->row->extra_info)); ?>" /></td>
					</tr>
					<?php
					foreach ($this->fields as $field) 
					{
					?>
					<tr>
						<td class="key"><?php echo $field[1]; ?>: <?php echo ($field[3]) ? '<span class="required">*</span>' : ''; ?></td>
						<td><?php
						if ($field[2] == 'checkbox') {
							echo '<input type="checkbox" name="fields['. $field[0] .']" value="1"';
							if (stripslashes(end($field)) == 1) {
								echo ' checked="checked"';
							}
							echo ' />';
						} else {
							echo '<input type="text" name="fields['. $field[0] .']" size="45" maxlength="255" value="'. stripslashes(end($field)) .'" />';
						}
						?></td>
					</tr>
					<?php 
					}
					?>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_TAGS'); ?>:</td>
						<td><input type="text" name="tags" size="45" value="<?php echo $this->tags; ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_EVENTS_PUBLISHING'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_STARTDATE'); ?></td>
						<td>
							<input type="text" name="publish_up" id="publish_up" size="12" maxlength="10" value="<?php echo $this->times['start_publish'];?>" />
							
						</td>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_STARTTIME');?></td>
						<td>
							<input type="text" name="start_time" id="start_time" size="8" maxlength="8" value="<?php echo $this->times['start_time'];?>" />
							<?php if ($this->config->getCfg('calUseStdTime') =='YES') { ?>
							<input id="start_pm0" name="start_pm" type="radio"  value="0" <?php if (!$this->times['start_pm']) echo "checked"; ?> />AM
							<input id="start_pm1" name="start_pm" type="radio"  value="1" <?php if ($this->times['start_pm']) echo "checked"; ?> />PM
							<?php } ?>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_ENDDATE'); ?></td>
						<td>
							<input type="text" name="publish_down" id="publish_down" size="12" maxlength="10" value="<?php echo $this->times['stop_publish'];?>" />
							
						</td>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_ENDTIME');?></td>
						<td>
							<input class="inputbox" type="text" name="end_time" id="end_time" size="8" maxlength="8" value="<?php echo $this->times['end_time'];?>" />
							<?php if ($this->config->getCfg('calUseStdTime') =='YES') { ?>
							<input id="end_pm0" name="end_pm" type="radio"  value="0" <?php if (!$this->times['end_pm']) echo "checked"; ?> />AM
							<input id="end_pm1" name="end_pm" type="radio"  value="1" <?php if ($this->times['end_pm']) echo "checked"; ?> />PM
							<?php } ?>
						</td>
					</tr>
					<!-- REPEAT -->
					<tr>
						<td class="key" style="vertical-align:top;"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_REPEATTYPE'); ?></td>
						<td colspan="3">
							<table>
								<tr>
									<td style="width:60px;"><span style="text-decoration:underline"><?php echo JText::_('COM_EVENTS_CAL_LANG_REP_DAY');?></span></td>
									<td colspan="2" style="background-color:#FFCCCC;">
										<input id="reccurtype0" name="reccurtype" type="radio"  value="0" <?php if ($this->row->reccurtype == 0) { echo 'checked="checked"'; } ?> />
										<?php echo JText::_('COM_EVENTS_CAL_LANG_ALLDAYS'); ?>
									</td>
								</tr>
								<tr> 
									<td rowspan="3" style="vertical-align:top;"><span style="text-decoration:underline;"><?php echo JText::_('COM_EVENTS_CAL_LANG_REP_WEEK'); ?></span></td>
									<td style="width:100px;background-color:#FFCC99;">
										<input id="reccurtype1" name="reccurtype" type="radio" value="1" <?php if ($this->row->reccurtype == 1) { echo 'checked="checked"'; } ?> />
										1 * <?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('COM_EVENTS_CAL_LANG_REP_WEEK'); ?>
									</td>
									<td style="background-color:#FFCC99">
										<?php 
										if ($this->row->reccurtype == 1 || $this->row->reccurtype == 2) {
											$arg = '';
										} else {
											$arg = ' disabled="disabled"';
										}
										echo EventsHtml::buildReccurDaySelect($this->row->reccurday_week,'reccurday_week',$arg); ?>
									</td>
								</tr>
								<tr> 
									<td style="background-color:#FFCC99">
										<input id="reccurtype2" name="reccurtype" type="radio" value="2" <?php if ($this->row->reccurtype == 2) { echo 'checked="checked"'; } ?> />
										n * <?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('COM_EVENTS_CAL_LANG_REP_WEEK'); ?>
									</td>
									<td style="background-color:#FFCC99">
										<?php 
										if ($this->row->reccurtype == 1 || $this->row->reccurtype == 2) {
											$arg = '';
										} else {
											$arg = ' disabled="disabled"';
										}
										echo EventsHtml::buildWeekDaysCheck($this->row->reccurweekdays, $arg); ?>
									</td>
								</tr>
								<tr>
									<td style="background-color:#FFCC99; text-align: right;vertical-align:top;">
										<em><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_WEEKOPT');?></em>
									</td>
									<td style="background-color:#FFCC99">
										<?php echo EventsHtml::buildWeeksCheck($this->row->reccurweeks, $arg); ?>
										<input id="cb_wn6" name="reccurweekss" type="radio" value="pair" <?php if ($this->row->reccurweeks == 'pair') { echo 'checked="checked"'; } else { echo 'disabled="disabled"'; } ?> />
										<?php echo JText::_('COM_EVENTS_CAL_LANG_REP_WEEKPAIR'); ?><br />
										<input id="cb_wn7" name="reccurweekss" type="radio" value="impair" <?php if ($this->row->reccurweeks == 'impair') { echo 'checked="checked"'; } else { if ($this->row->reccurtype != 1 && $this->row->reccurtype != 2) { echo 'disabled="disabled"'; } } ?> />
										<?php echo JText::_('COM_EVENTS_CAL_LANG_REP_WEEKIMPAIR'); ?>
									</td>
								</tr>
								<tr>
									<td rowspan="2" style="vertical-align:top;"><span style="text-decoration:underline"><?php echo JText::_('COM_EVENTS_CAL_LANG_REP_MONTH'); ?></span></td>
									<td style="background-color:#99CC66">
										<input id="reccurtype3" name="reccurtype" type="radio" value="3" <?php if ($this->row->reccurtype == 3) { echo 'checked="checked"'; } ?> />
										1 * <?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('COM_EVENTS_CAL_LANG_REP_MONTH'); ?>
									</td>
									<td style="background-color:#99CC66">
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
									<td colspan="2" style="background-color:#99CC66">
										<input id="reccurtype4" name="reccurtype" type="radio"  value="4" <?php if ($this->row->reccurtype == 4) { echo 'checked="checked"'; } ?> />
										<?php echo JText::_('COM_EVENTS_CAL_LANG_EACH').' '.JText::_('COM_EVENTS_CAL_LANG_ENDMONTH'); ?>
									</td>
								</tr>
								<tr>
									<td rowspan="2" style="vertical-align:top;"><span style="text-decoration:underline"><?php echo JText::_('COM_EVENTS_CAL_LANG_REP_YEAR'); ?></span></td>
									<td style="background-color:#FFCCCC">
										<input id="reccurtype5" name="reccurtype" type="radio" value="5" <?php if ($this->row->reccurtype == 5) { echo 'checked="checked"'; } ?> />
										1 * <?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_PER').' '.JText::_('COM_EVENTS_CAL_LANG_REP_YEAR'); ?>
									</td>
									<td style="background-color:#FFCCCC">
										<?php 
										if ($this->row->reccurtype == 5) {
											$arg = '';
										} else {
											$arg = ' disabled="disabled"';
										}
										echo EventsHtml::buildReccurDaySelect($this->row->reccurday_year,'reccurday_year',$arg); ?>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				<!-- END REPEAT -->
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_EVENTS_REGISTRATION'); ?></span></legend>
			
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label for="registerby"><?php echo JText::_('COM_EVENTS_REGISTER_BY'); ?>:</label></td>
						<td>
							<?php echo JHTML::_('calendar', $this->row->registerby, 'registerby', 'registerby', "%Y-%m-%d", array('class' => 'inputbox')); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="email"><?php echo JText::_('COM_EVENTS_EMAIL'); ?>:</label></td>
						<td>
							<input type="text" name="email" id="email" value="<?php echo stripslashes($this->row->email); ?>" size="50" />
							<br /><span>The email registrations will be sent to.</span>
						</td>
					</tr>
					<tr>
						<td class="key"><label for="restricted"><?php echo JText::_('COM_EVENTS_RESTRICTED'); ?>:</label></td>
						<td>
							<input type="text" name="restricted" id="restricted" value="<?php echo stripslashes($this->row->restricted); ?>" size="50" />
							<br /><span>If you want registration to be restricted (invite only), enter the password users must enter to gain access to the registration form.</span>
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_STATE'); ?></th>
					<td><?php echo $this->row->state > 0 ? JText::_('Published') : ($this->row->state < 0 ? JText::_('Archived') : JText::_('Draft Unpublished'));?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_HITS'); ?></th>
					<td><?php echo $this->row->hits;?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_CREATED'); ?></th>
					<td><?php echo $this->row->created ? $this->row->created.'</td></tr><tr><th>'.JText::_('COM_EVENTS_CAL_LANG_EVENT_CREATED_BY').'</th><td>'.$userc : JText::_('COM_EVENTS_CAL_LANG_EVENT_NEWEVENT');?></td>
				</tr>
<?php if ($this->row->modified && $this->row->modified != '0000-00-00 00:00:00') { ?>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_MODIFIED'); ?></th>
					<td><?php echo $this->row->modified ? $this->row->modified.'</td></tr><tr><th>'.JText::_('COM_EVENTS_CAL_LANG_EVENT_MODIFIED_BY').'</th><td>'.$userm : JText::_('COM_EVENTS_CAL_LANG_EVENT_NOTMODIFIED');?></td>
				</tr>
<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_STATUS'); ?></span></legend>

			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_TYPE'); ?></td>
						<td>
							<input type="radio" name="announcement" value="0" <?php if ($this->row->announcement == 0) { echo 'checked="checked"'; } ?> /> <?php echo JText::_('COM_EVENTS_EVENT'); ?></label><br />
							<input type="radio" name="announcement" value="1" <?php if ($this->row->announcement == 1) { echo 'checked="checked"'; } ?> /> <?php echo JText::_('COM_EVENTS_ANNOUNCEMENT'); ?></label>
						</td>
					</tr>
					<tr>
						<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_ACCESSLEVEL'); ?></td>
						<td><?php echo $this->glist; ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset class="adminform paramlist">
			<legend><span><?php echo JText::_('COM_EVENTS_REGISTRATION_FIELDS'); ?></span></legend>
			<?php echo $params->render(); ?>
		</fieldset>
	</div><div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="sid" value="<?php echo $this->row->sid; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="images" value="" />

	<?php echo JHTML::_('form.token'); ?>
</form>
