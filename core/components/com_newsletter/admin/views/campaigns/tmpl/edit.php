<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('campaign');

// If current record has a secret, then we are editing, otherwise we are new.
$hasSecret = strlen($this->campaign->secret) > 0;

// Language to match whether we are adding or editing:
$text = ($hasSecret ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));

Toolbar::title(Lang::txt('COM_NEWSLETTER_CAMPAIGN') . ': ' . $text, 'campaign');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('campaign');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<fieldset class="adminform">
		<legend><span><?php echo $text; ?> <?php echo Lang::txt('COM_NEWSLETTER_CAMPAIGN'); ?></span></legend>

		<!-- Display campaign id, but only if it already exists (we are editing): -->
		<?php if ($hasSecret) { ?>
			<div class="input-wrap">
				<label for="campaign-title"><?php echo Lang::txt('COM_NEWSLETTER_CAMPAIGN_ID'); ?></label><br />
				<?php echo $this->campaign->id; ?>
			</div>
		<?php } ?>

		<div class="input-wrap">
			<label for="campaign-title"><?php echo Lang::txt('COM_NEWSLETTER_CAMPAIGN_NAME'); ?></label><br />
			<input type="text" name="campaign[title]" id="campaign-title" value="<?php echo $this->escape($this->campaign->title); ?>" /></td>
		</div>

		<!-- Campaign expiration date: adapted from com_events/admin/views/events/tmpl/edit.php -->
		<!-- If a new record, default 90 days -->
		<?php if (!$this->campaign->expire_date) {
			$exDate  = Date::of('+90 days');
		} else {
			$exDate  = Date::of($this->campaign->expire_date);
		} ?>
		<div class="input-wrap">
			<label for="campaign-expire_date"><?php echo Lang::txt('COM_NEWSLETTER_CAMPAIGN_EXPIRE_DATE'); ?></label><br />
			<?php echo Html::input('calendar', 'campaign[expire_date_display]', Date::of($exDate)->toLocal(), array('id' => 'campaign-expire_date')); ?>
		</div>

		<div class="input-wrap">
			<label for="campaign-description"><?php echo Lang::txt('COM_NEWSLETTER_MAILINGLIST_DESC'); ?></label><br />
			<textarea name="campaign[description]" id="campaign-description" rows="5"><?php echo $this->escape($this->campaign->description); ?></textarea>
		</div>

		<!-- Display Reset Secret only if editing the campaign -->
		<?php if ($hasSecret) { ?>
			<div class="input-wrap">
				<input type="checkbox" name="params[reset_secret]" id="cb-reset-secret" value="1" class="checkbox-toggle" />
				<label for="cb-reset-secret">Reset Campaign Secret</label>
			</div>
		<?php } ?>
	</fieldset>

	<input type="hidden" name="campaign[id]" value="<?php echo $this->campaign->id; ?>" />
	<input type="hidden" name="campaign[expire_date_gmt]" value="<?php echo Date::of($exDate, 'GMT'); ?>" />
	<input type="hidden" name="campaign[expire_date_local]" value="<?php echo Date::of($exDate)->toLocal(); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>