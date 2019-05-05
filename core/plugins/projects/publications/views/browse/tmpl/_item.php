<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<li>
	<span class="mypub-options">
		<a href="<?php echo $this->row->link('version'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_TITLE'); ?>"><?php echo strtolower(Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW')); ?></a> |
		<a href="<?php echo $this->row->link('editversion'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE_TITLE'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE'); ?></a>
	</span>
	<span class="pub-thumb"><img src="<?php echo Route::url($this->row->link('thumb')); ?>" alt=""/></span>
	<span class="pub-details">
		<?php echo $this->row->get('title'); ?>
		<span class="block faded mini">
			<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION') . ' ' . $this->row->get('version_label'); ?>
			<span class="<?php echo $this->row->getStatusCss(); ?> major_status"><?php echo $this->row->getStatusName(); ?></span>
			<span class="block">
				<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CREATED')) . ' ' . $this->row->created('date') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_BY') . ' ' . $this->row->creator('name'); ?>
				<?php if (!$this->row->project()->isProvisioned()) {
				echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_IN_PROJECT') . ' <a href="' . $this->row->project()->link() . '">' . \Hubzero\Utility\Str::truncate(stripslashes($this->row->project()->get('title')), 80) . '</a>';
			} ?></span>
		</span>
	</span>
</li>
