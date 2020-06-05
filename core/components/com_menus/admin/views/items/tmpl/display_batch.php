<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$options = array(
	Html::select('option', 'c', Lang::txt('JLIB_HTML_BATCH_COPY')),
	Html::select('option', 'm', Lang::txt('JLIB_HTML_BATCH_MOVE'))
);
$published = $this->filters['published'];
?>
<fieldset class="batch">
	<legend><?php echo Lang::txt('COM_MENUS_BATCH_OPTIONS');?></legend>

	<p><?php echo Lang::txt('COM_MENUS_BATCH_TIP'); ?></p>

	<div class="grid">
		<div class="col span6">
			<div class="input-wrap">
				<?php echo Html::batch('access');?>
			</div>

			<div class="input-wrap">
				<?php echo Html::batch('language'); ?>
			</div>
		</div>
		<div class="col span6">
			<?php if ($published >= 0) : ?>
				<div class="input-wrap combo" id="batch-choose-action">
					<label id="batch-choose-action-lbl" for="batch-choose-action">
						<?php echo Lang::txt('COM_MENUS_BATCH_MENU_LABEL'); ?>
					</label><br />
					<div class="grid">
						<div class="col span6">
							<select name="batch[menu_id]" class="inputbox" id="batch-menu-id">
								<option value=""><?php echo Lang::txt('JSELECT') ?></option>
								<?php echo Html::select('options', Html::menu('menuitems', array('published' => $published))); ?>
							</select>
						</div>
						<div class="col span6">
							<?php echo Html::select('radiolist', $options, 'batch[move_copy]', '', 'value', 'text', 'm'); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<div class="input-wrap">
				<button type="submit" id="btn-batch-submit">
					<?php echo Lang::txt('JGLOBAL_BATCH_PROCESS'); ?>
				</button>
				<button type="button" id="btn-batch-clear">
					<?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>
		</div>
	</div>
</fieldset>
