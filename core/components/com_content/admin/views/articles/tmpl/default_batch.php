<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$published = $this->filters['published'];
?>
<fieldset class="batch">
	<legend><?php echo Lang::txt('COM_CONTENT_BATCH_OPTIONS');?></legend>
	<p><?php echo Lang::txt('COM_CONTENT_BATCH_TIP'); ?></p>

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
				<?php echo Html::batch('item', 'com_content');?>
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
