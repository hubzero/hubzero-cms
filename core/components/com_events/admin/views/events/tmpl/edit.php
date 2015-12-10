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

$text = ($this->task == 'edit') ? Lang::txt('COM_EVENTS_EDIT') : Lang::txt('COM_EVENTS_NEW');

Toolbar::title(Lang::txt('COM_EVENTS_EVENT') . ': '. $text, 'event.png');
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('event');

$xprofilec = \Hubzero\User\Profile::getInstance($this->row->created_by);
$xprofilem = \Hubzero\User\Profile::getInstance($this->row->modified_by);
$userm = is_object($xprofilem) ? $xprofilem->get('name') : '';
$userc = is_object($xprofilec) ? $xprofilec->get('name') : '';

$params = new \Hubzero\Html\Parameter($this->row->params, PATH_CORE . DS . 'components' . DS . $this->option . DS . 'events.xml');
?>
<script type="text/javascript" src="../core/components/<?php echo $this->option; ?>/site/assets/js/calendar.rc4.js"></script>
<script type="text/javascript">
var HUB = {};

/*window.addEvent('domready', function() {
	myCal1 = new Calendar({ publish_up: 'Y-m-d' }, { direction: 1, tweak: {x: 6, y: 0} });
	myCal2 = new Calendar({ publish_down: 'Y-m-d' }, { direction: 1, tweak: {x: 6, y: 0} });
});*/
</script>

<script type="text/javascript" src="../core/components/<?php echo $this->option; ?>/site/assets/js/events.js"></script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_EVENTS_EVENT'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-title"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<input type="text" name="title" id="field-title" maxlength="250" value="<?php echo $this->escape(html_entity_decode(stripslashes($this->row->title))); ?>" /></td>
			</div>

			<div class="input-wrap">
				<label for="catid"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CATEGORY'); ?>:<span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
				<?php echo \Components\Events\Helpers\Html::buildCategorySelect($this->row->catid, '', 0, $this->option);?></td>
			</div>

			<div class="input-wrap">
				<label for="field-econtent"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ACTIVITY'); ?>:</label><br />
				<?php echo $this->editor('econtent', $this->row->content, '45', '10', 'field-econtent'); ?></td>
			</div>

			<div class="input-wrap">
				<label for="field-adresse_info"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ADRESSE'); ?>:</label><br />
				<input type="text" name="adresse_info" id="field-adresse_info" maxlength="120" value="<?php echo $this->escape(stripslashes($this->row->adresse_info)); ?>" /></td>
			</div>

			<div class="input-wrap">
				<label for="field-contact_info"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CONTACT'); ?>:</label><br />
				<input type="text" name="contact_info" id="field-contact_info" maxlength="120" value="<?php echo $this->escape(stripslashes($this->row->contact_info)); ?>" /></td>
			</div>

			<div class="input-wrap">
				<label for="field-extra_info"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_EXTRA'); ?>:</label><br />
				<input type="text" name="extra_info" id="field-extra_info" maxlength="240" value="<?php echo $this->escape(stripslashes($this->row->extra_info)); ?>" /></td>
			</div>
				<?php
				foreach ($this->fields as $field)
				{
				?>
					<div class="input-wrap">
						<label for="field-<?php echo $field[0]; ?>"><?php echo $field[1]; ?>: <?php echo ($field[3]) ? '<span class="required">' . Lang::txt('JOPTION_REQUIRED') . '</span>' : ''; ?></label><br />
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
				<label for="field-tags"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TAGS'); ?>:</label><br />
				<input type="text" name="tags" id="field-tags" value="<?php echo (isset($this->tags) ? $this->escape($this->tags) : ''); ?>" />
			</div>
		</fieldset>
		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_EVENTS_PUBLISHING'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-publish_up"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_STARTDATE'); ?></label><br />
				<?php echo Html::input('calendar', 'publish_up', $this->row->publish_up, array('id' => 'field-publish_up')); ?>
			</div>

			<div class="input-wrap">
				<label for="field-publish_down"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ENDDATE'); ?></label><br />
				<?php echo Html::input('calendar', 'publish_down', $this->row->publish_down, array('id' => 'field-publish_down')); ?>
			</div>
		</fieldset>

		<?php if ($this->row->scope == 'group') : ?>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_RECURRENCE'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-repeating_rule"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_RECURRENCE'); ?></label><br />
					<input type="text" name="repeating_rule" value="<?php echo stripslashes($this->row->repeating_rule); ?>" />
					<span class="block-hint"><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_RECURRENCE_HINT', 'http://www.kanzaki.com/docs/ical/rrule.html'); ?></span>
				</div>
			</fieldset>
		<?php endif; ?>

		<fieldset class="adminform">
			<legend><span><?php echo Lang::txt('COM_EVENTS_REGISTRATION'); ?></span></legend>

			<div class="input-wrap">
				<label for="field-registerby"><?php echo Lang::txt('COM_EVENTS_REGISTER_BY'); ?>:</label><br />
				<?php echo Html::input('calendar', 'registerby', $this->row->registerby, array('id' => 'field-registerby')); ?>
			</div>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_EVENTS_EMAIL_HINT'); ?>">
				<label for="field-email"><?php echo Lang::txt('COM_EVENTS_EMAIL'); ?>:</label><br />
				<input type="text" name="email" id="field-email" value="<?php echo $this->escape($this->row->email); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_EVENTS_EMAIL_HINT'); ?></span>
			</div>

			<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_EVENTS_RESTRICTED_HINT'); ?>">
				<label for="field-restricted"><?php echo Lang::txt('COM_EVENTS_RESTRICTED'); ?>:</label><br />
				<input type="text" name="restricted" id="field-restricted" value="<?php echo $this->escape($this->row->restricted); ?>" />
				<span class="hint"><?php echo Lang::txt('COM_EVENTS_RESTRICTED_HINT'); ?></span>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_STATE'); ?></th>
					<td><?php echo $this->row->state > 0 ? Lang::txt('COM_EVENTS_EVENT_PUBLISHED') : ($this->row->state < 0 ? Lang::txt('COM_EVENTS_EVENT_ARCHIVED') : Lang::txt('COM_EVENTS_EVENT_UNPUBLISHED')); ?></td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CREATED'); ?></th>
					<td><?php echo $this->row->created ? Date::of($this->row->created)->toLocal('F d, Y @ g:ia').'</td></tr><tr><th>'.Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CREATED_BY').'</th><td>'.$userc : Lang::txt('COM_EVENTS_CAL_LANG_EVENT_NEWEVENT'); ?></td>
				</tr>
			<?php if ($this->row->modified && $this->row->modified != '0000-00-00 00:00:00') { ?>
				<tr>
					<th><?php echo Lang::txt('COM_EVENTS_CAL_LANG_EVENT_MODIFIED'); ?></th>
					<td><?php echo $this->row->modified ? Date::of($this->row->modified)->toLocal('F d, Y @ g:ia').'</td></tr><tr><th>'.Lang::txt('COM_EVENTS_CAL_LANG_EVENT_MODIFIED_BY').'</th><td>'.$userm : Lang::txt('COM_EVENTS_CAL_LANG_EVENT_NOTMODIFIED'); ?></td>
				</tr>
			<?php } ?>
			</tbody>
		</table>

		<fieldset class="adminform paramlist">
			<legend><span><?php echo Lang::txt('COM_EVENTS_REGISTRATION_FIELDS'); ?></span></legend>
			<?php echo $params->render(); ?>
		</fieldset>
	</div><div class="clr"></div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="time_zone" value="<?php echo $this->row->time_zone; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="images" value="" />

	<?php echo Html::input('token'); ?>
</form>
