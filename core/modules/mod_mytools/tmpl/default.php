<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

// Push the module CSS to the template
$this->css();
$this->js();

if ($this->fav || $this->no_html) { ?>
	<?php echo $this->buildList($this->favtools, 'fav'); ?>
	<p><?php echo Lang::txt('MOD_MYTOOLS_EXPLANATION'); ?></p>
<?php } else { ?>
	<div id="myToolsTabs" data-api="<?php echo Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=dashboard&no_html=1&init=1&action=module&moduleid=' . $this->module->id); ?>">
		<ul class="tab_titles">
			<li title="recenttools" class="active"><?php echo Lang::txt('MOD_MYTOOLS_RECENT'); ?></li>
			<li title="favtools"><?php echo Lang::txt('MOD_MYTOOLS_FAVORITES'); ?></li>
			<li title="alltools"><?php echo Lang::txt('MOD_MYTOOLS_ALL_TOOLS'); ?></li>
		</ul>

		<div id="recenttools" class="tab_panel active">
			<?php echo $this->buildList($this->rectools, 'recent'); ?>
			<p><?php echo Lang::txt('MOD_MYTOOLS_RECENT_EXPLANATION'); ?></p>
		</div>

		<div id="favtools" class="tab_panel">
			<?php echo $this->buildList($this->favtools, 'favs'); ?>
			<p><?php echo Lang::txt('MOD_MYTOOLS_FAVORITES_EXPLANATION'); ?></p>
		</div>

		<div id="alltools" class="tab_panel">
			<div id="filter-mytools">
				<input type="text" placeholder="<?php echo Lang::txt('MOD_MYTOOLS_SEARCH_PLACEHOLDER'); ?>" />
			</div>
			<?php echo $this->buildList($this->alltools, 'all'); ?>
			<p><?php echo Lang::txt('MOD_MYTOOLS_ALL_TOOLS_EXPLANATION'); ?></p>
		</div>
	</div>
	<input type="hidden" class="mytools_favs" value="<?php echo $this->escape(implode(',', $this->favs)); ?>" />
<?php }