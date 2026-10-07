<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// The article search form. Expects: option, filters (search, sort).
$search = isset($this->filters['search']) ? $this->filters['search'] : '';
$sort   = isset($this->filters['sort']) ? $this->filters['sort'] : '';
?>
<form class="kb-search" action="<?php echo Route::url('index.php?option=' . $this->option . '&section=all'); ?>" method="get">
	<div class="container data-entry">
		<input class="entry-search-submit" type="submit" value="<?php echo Lang::txt('COM_KB_SEARCH'); ?>" />
		<fieldset class="entry-search">
			<legend><?php echo Lang::txt('COM_KB_SEARCH_LEGEND'); ?></legend>
			<label for="entry-search-field"><?php echo Lang::txt('COM_KB_SEARCH_LABEL'); ?></label>
			<input type="text" name="search" id="entry-search-field" value="<?php echo $this->escape($search); ?>" placeholder="<?php echo Lang::txt('COM_KB_SEARCH_PLACEHOLDER'); ?>" />
			<?php if ($sort) { ?>
				<input type="hidden" name="sort" value="<?php echo $this->escape($sort); ?>" />
			<?php } ?>
		</fieldset>
	</div><!-- / .container -->
</form>
