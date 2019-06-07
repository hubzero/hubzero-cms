<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<ul id="page_options">
	<li>
		<a class="icon-prev btn back" title="" href="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=calendars'); ?>">
			<?php echo Lang::txt('Back to Manage Calendars'); ?>
		</a>
	</li>
</ul>

<form action="<?php echo Route::url('index.php?option='.$this->option.'&cn='.$this->group->cn.'&active=calendar&action=savecalendar'); ?>" id="hubForm" method="post" class="full">

	<fieldset>
		<legend><?php echo Lang::txt('Group Calendar'); ?></legend>

		<label for="field-title">
			<?php echo Lang::txt('Title:'); ?> <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
			<input type="text" name="calendar[title]" id="field-title" value="<?php echo $this->calendar->get('title'); ?>" />
		</label>

		<label for="field-url">
			<?php echo Lang::txt('URL:'); ?> <span class="optional"><?php echo Lang::txt('Optional'); ?></span>
			<input type="text" name="calendar[url]" id="field-url" value="<?php echo $this->calendar->get('url'); ?>" />
			<span class="hint"><?php echo Lang::txt('This is used to fetch remote calendar events from other services such as a Google Calendar.'); ?></span>
		</label>

		<label for="field-color">
			<?php echo Lang::txt('Color:'); ?> <span class="optional"><?php echo Lang::txt('Optional'); ?></span>
			<?php $colors = array('red','orange','yellow','green','blue','purple','brown'); ?>
			<select name="calendar[color]" id="field-color">
				<option value=""><?php echo Lang::txt('&mdash; Select Color &mdash;'); ?></option>
				<?php foreach ($colors as $color) : ?>
					<?php $sel = ($this->calendar->get('color') == $color) ? 'selected="selected"' : ''; ?>
					<option <?php echo $sel; ?> value="<?php echo $color; ?>"><?php echo ucfirst($color); ?></option>
				<?php endforeach; ?>
			</select>
		</label>

		<label for="field-published">
			<?php echo Lang::txt('Publish Events to Subscribers?:'); ?>
			<select name="calendar[published]" id="field-published">
				<option <?php echo ($this->calendar->get('published') == 1) ? 'selected="selected"' : ''; ?>value="1"><?php echo Lang::txt('JYes'); ?></option>
				<option value="0"><?php echo Lang::txt('JNo'); ?></option>
			</select>
		</label>
	</fieldset>
	<br class="clear" />
	<p class="submit">
		<input type="submit" value="<?php echo Lang::txt('Submit'); ?>" />
	</p>

	<input type="hidden" name="option" value="com_groups" />
	<input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
	<input type="hidden" name="active" value="calendar" />
	<input type="hidden" name="action" value="savecalendar" />
	<input type="hidden" name="calendar[id]" value="<?php echo $this->calendar->get('id'); ?>" />
	<?php echo Html::input('token'); ?>
</form>