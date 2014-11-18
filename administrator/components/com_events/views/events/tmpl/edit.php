<?php
// No direct access
defined('_JEXEC') or die('Restricted access');

$text = ($this->task == 'edit') ? JText::_('COM_EVENTS_EDIT') : JText::_('COM_EVENTS_NEW');
JToolBarHelper::title(JText::_('COM_EVENTS_EVENT').': '. $text, 'event.png');
JToolBarHelper::save();
JToolBarHelper::cancel();
JToolBarHelper::spacer();
JToolBarHelper::help('event');

$editor = JFactory::getEditor();

$xprofilec = \Hubzero\User\Profile::getInstance($this->row->created_by);
$xprofilem = \Hubzero\User\Profile::getInstance($this->row->modified_by);
$userm = is_object($xprofilem) ? $xprofilem->get('name') : '';
$userc = is_object($xprofilec) ? $xprofilec->get('name') : '';

$params = new JParameter($this->row->params, JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . $this->option . DS . 'events.xml');
?>
<script type="text/javascript" src="../components/<?php echo $this->option; ?>/js/calendar.rc4.js"></script>
<script type="text/javascript">
var HUB = {};

/*window.addEvent('domready', function() {
	myCal1 = new Calendar({ publish_up: 'Y-m-d' }, { direction: 1, tweak: {x: 6, y: 0} });
	myCal2 = new Calendar({ publish_down: 'Y-m-d' }, { direction: 1, tweak: {x: 6, y: 0} });
});*/
</script>

<script type="text/javascript" src="../components/<?php echo $this->option; ?>/js/events.js"></script>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_EVENTS_EVENT'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_TITLE'); ?>: <span class="required">required</span></label><br />
				<input type="text" name="title" id="field-title" maxlength="250" value="<?php echo $this->escape(html_entity_decode(stripslashes($this->row->title))); ?>" /></td>
			</div>

			<div class="input-wrap">
				<label for="catid"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_CATEGORY'); ?>:</label><br />
				<?php echo EventsHtml::buildCategorySelect($this->row->catid, '', 0, $this->option);?></td>
			</div>

			<div class="input-wrap">
				<label for="field-econtent"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_ACTIVITY'); ?>:</label><br />
				<?php echo $editor->display('econtent', $this->row->content, '', '', '45', '10', false, 'field-econtent'); ?></td>
			</div>

			<div class="input-wrap">
				<label for="field-adresse_info"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_ADRESSE'); ?>:</label><br />
				<input type="text" name="adresse_info" id="field-adresse_info" maxlength="120" value="<?php echo $this->escape(stripslashes($this->row->adresse_info)); ?>" /></td>
			</div>

			<div class="input-wrap">
				<label for="field-contact_info"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_CONTACT'); ?>:</label><br />
				<input type="text" name="contact_info" id="field-contact_info" maxlength="120" value="<?php echo $this->escape(stripslashes($this->row->contact_info)); ?>" /></td>
			</div>

			<div class="input-wrap">
				<label for="field-extra_info"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_EXTRA'); ?>:</label><br />
				<input type="text" name="extra_info" id="field-extra_info" maxlength="240" value="<?php echo $this->escape(stripslashes($this->row->extra_info)); ?>" /></td>
			</div>
				<?php
				foreach ($this->fields as $field)
				{
				?>
					<div class="input-wrap">
						<label for="field-<?php echo $field[0]; ?>"><?php echo $field[1]; ?>: <?php echo ($field[3]) ? '<span class="required">required</span>' : ''; ?></label><br />
						<?php
						if ($field[2] == 'checkbox') {
							echo '<input type="checkbox" name="fields['. $field[0] .']" id="field-'. $field[0] .'" value="1"';
							if (stripslashes(end($field)) == 1) {
								echo ' checked="checked"';
							}
							echo ' />';
						} else {
							echo '<input type="text" name="fields['. $field[0] .']" id="field-'. $field[0] .'" maxlength="255" value="'. $this->escape(end($field)) .'" />';
						}
						?>
					</div>
				<?php
				}
				?>
			<div class="input-wrap">
				<label for="field-tags"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_TAGS'); ?>:</label><br />
				<input type="text" name="tags" id="field-tags" value="<?php echo $this->escape($this->tags); ?>" />
			</div>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_EVENTS_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-publish_up"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_STARTDATE'); ?></label><br />
				<?php echo JHTML::_('calendar', $this->row->publish_up, 'publish_up', 'field-publish_up'); ?>
			</div>

			<div class="input-wrap">
				<label for="field-publish_down"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_ENDDATE'); ?></label><br />
				<?php echo JHTML::_('calendar', $this->row->publish_down, 'publish_down', 'field-publish_down'); ?>
			</div>
		</fieldset>

		<?php if ($this->row->scope == 'group') : ?>
			<fieldset class="adminform">
				<legend><span><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_RECURRENCE'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-repeating_rule"><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_RECURRENCE'); ?></label><br />
					<input type="text" name="repeating_rule" value="<?php echo stripslashes($this->row->repeating_rule); ?>" />
					<span class="block-hint"><?php echo JText::sprintf('COM_EVENTS_CAL_LANG_EVENT_RECURRENCE_HINT', 'http://www.kanzaki.com/docs/ical/rrule.html'); ?></span>
				</div>
			</fieldset>
		<?php endif; ?>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_EVENTS_REGISTRATION'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-registerby"><?php echo JText::_('COM_EVENTS_REGISTER_BY'); ?>:</label><br />
				<?php echo JHTML::_('calendar', $this->row->registerby, 'registerby', 'field-registerby'); ?>
			</div>

			<div class="input-wrap" data-hint="The email registrations will be sent to.">
				<label for="field-email"><?php echo JText::_('COM_EVENTS_EMAIL'); ?>:</label><br />
				<input type="text" name="email" id="field-email" value="<?php echo $this->escape($this->row->email); ?>" />
				<span class="hint"><?php echo JText::_('COM_EVENTS_EMAIL_HINT'); ?></span>
			</div>

			<div class="input-wrap" data-hint="If you want registration to be restricted (invite only), enter the password users must enter to gain access to the registration form.">
				<label for="field-restricted"><?php echo JText::_('COM_EVENTS_RESTRICTED'); ?>:</label><br />
				<input type="text" name="restricted" id="field-restricted" value="<?php echo $this->escape($this->row->restricted); ?>" />
				<span class="hint"><?php echo JText::_('COM_EVENTS_RESTRICTED_HINT'); ?></span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_STATE'); ?></th>
					<td><?php echo $this->row->state > 0 ? JText::_('COM_EVENTS_EVENT_PUBLISHED') : ($this->row->state < 0 ? JText::_('COM_EVENTS_EVENT_ARCHIVED') : JText::_('COM_EVENTS_EVENT_UNPUBLISHED'));?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_CREATED'); ?></th>
					<td><?php echo $this->row->created ? JHTML::_('date', $this->row->created, 'F d, Y @ g:ia').'</td></tr><tr><th>'.JText::_('COM_EVENTS_CAL_LANG_EVENT_CREATED_BY').'</th><td>'.$userc : JText::_('COM_EVENTS_CAL_LANG_EVENT_NEWEVENT');?></td>
				</tr>
			<?php if ($this->row->modified && $this->row->modified != '0000-00-00 00:00:00') { ?>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_EVENT_MODIFIED'); ?></th>
					<td><?php echo $this->row->modified ? JHTML::_('date', $this->row->modified, 'F d, Y @ g:ia').'</td></tr><tr><th>'.JText::_('COM_EVENTS_CAL_LANG_EVENT_MODIFIED_BY').'</th><td>'.$userm : JText::_('COM_EVENTS_CAL_LANG_EVENT_NOTMODIFIED');?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform paramlist">
			<legend><span><?php echo JText::_('COM_EVENTS_REGISTRATION_FIELDS'); ?></span></legend>
			<?php echo $params->render(); ?>
		</fieldset>
	</div><div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="images" value="" />

	<?php echo JHTML::_('form.token'); ?>
</form>
