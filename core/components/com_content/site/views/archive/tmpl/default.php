<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

Html::addIncludePath(PATH_COMPONENT . '/helpers');
?>
<?php if ($this->params->get('show_page_heading')) : ?>
	<header id="content-header">
		<h2>
			<?php echo $this->escape($this->params->get('page_heading')); ?>
		</h2>
	</header>
<?php endif; ?>

<section class="main section">
	<div class="archive<?php echo $this->pageclass_sfx; ?>">
		<form id="adminForm" action="<?php echo Route::url('index.php?option=com_content'); ?>" method="post">
			<fieldset class="filters">
				<legend class="hidelabeltxt"><?php echo Lang::txt('JGLOBAL_FILTER_LABEL'); ?></legend>

				<div class="filter-search">
					<?php if ($this->params->get('filter_field') != 'hide') : ?>
						<label class="filter-search-lbl" for="filter-search"><?php echo Lang::txt('COM_CONTENT_' . $this->params->get('filter_field') . '_FILTER_LABEL') . '&#160;'; ?></label>
						<input type="text" name="filter-search" id="filter-search" value="<?php echo $this->escape($this->filter); ?>" class="inputbox" onchange="document.getElementById('adminForm').submit();" />
					<?php endif; ?>

					<?php echo $this->form->monthField; ?>
					<?php echo $this->form->yearField; ?>
					<?php echo $this->form->limitField; ?>
					<button type="submit" class="button"><?php echo Lang::txt('JGLOBAL_FILTER_BUTTON'); ?></button>
				</div>

				<input type="hidden" name="view" value="archive" />
				<input type="hidden" name="option" value="com_content" />
				<input type="hidden" name="limitstart" value="0" />
			</fieldset>

			<?php echo $this->loadTemplate('items'); ?>
		</form>
	</div>
</section>
