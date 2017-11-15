<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_newsfeeds
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();

$published = $this->state->get('filter.published');
?>
<fieldset class="batch">
	<legend><?php echo Lang::txt('COM_NEWSFEEDS_BATCH_OPTIONS');?></legend>
	<p><?php echo Lang::txt('COM_NEWSFEEDS_BATCH_TIP'); ?></p>

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
				<?php echo Html::batch('item', 'com_newsfeeds');?>
			<?php endif; ?>
			<div class="input-wrap">
				<button type="submit" onclick="Joomla.submitbutton('newsfeed.batch');">
					<?php echo Lang::txt('JGLOBAL_BATCH_PROCESS'); ?>
				</button>
				<button type="button" onclick="$('#batch-category-id').val('');$('#batch-access').val('');$('#batch-language-id').val('');">
					<?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?>
				</button>
			</div>
		</div>
	</div>
</fieldset>
