<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<?php if (!isset($this->noUl) || !$this->noUl) : ?>
	<ul class="file-selector" id="file-selector">
<?php endif; ?>

<?php if (is_array($this->items) && count($this->items) > 0) : ?>
	<?php $a = 1; ?>
	<?php foreach ($this->items as $item)
	{
		$level = $item->getDirLevel($item->get('dirname'));
		if (Request::getInt('cid', false) !== false)
		{
			$level++;
		}
		$levelCss = 'level-' . $level;

		// Get element ID
		$liId  = $item->get('type') == 'folder'
				? 'dir-' . strtolower(\Components\Projects\Helpers\Html::generateCode(5, 5, 0, 1, 1))
				: 'item-' . $a;

		// Assign parent classes (for collapsing)
		$parentCss = '';
		if ($parent = Request::getString('parent', false))
		{
			$parentCss = ' parent-' . $parent;
		}

		$a++;

		// Is file already attached?
		$selected = !empty($this->selected) && in_array($item->get('localPath'), $this->selected) ? 1 : 0;

		// Is file type allowed?
		$allowed = $item->get('type') == 'file' && !empty($this->allowed)
				&& !in_array($item->get('ext'), $this->allowed)
				? ' notallowed' : ' allowed';

		$used = !empty($this->used)
				&& in_array($item->get('localPath'), $this->used) ? true : false;

		// Do not allow files used in other elements
		$allowed = $used ? ' notallowed' : $allowed;

		// No selection for folders
		$allowed = $item->get('type') == 'folder' ? ' freeze' : $allowed;

		// Do not allow to delete previously selected items
		$allowed = $selected ? ' freeze' : $allowed;

		?>
		<li class="<?php echo $item->get('type') == 'folder' ? 'type-folder collapsed' : 'type-file'; ?><?php echo $parentCss; ?><?php if ($selected) { echo ' selectedfilter preselected'; } ?><?php echo $allowed; ?>" id="<?php echo $liId; ?>" data-path="<?php echo $item->get('localPath'); ?>" data-connection="<?php echo Request::getInt('cid', 0); ?>">
			<span class="item-info"><?php echo $item->get('type') == 'file' ? $item->getSize('formatted') : ''; ?></span>
			<span class="item-wrap <?php echo ($item->get('type') == 'folder' ? 'collapsor ' : '') . $levelCss; ?>" id="<?php echo urlencode($item->get('localPath')); ?>">
				<?php if ($item->get('type') == 'folder') { ?><span class="collapsor-indicator">&nbsp;</span><?php } ?>
				<img class="file-type file-type-<?php echo $item->get('ext'); ?>" src="<?php echo $item->get('icon'); ?>" alt="" /> <span title="<?php echo $item->get('localPath'); ?>"><?php echo \Components\Projects\Helpers\Html::shortenFileName($item->get('name'), 50); ?></span>
			</span>
		</li>
	<?php } ?>
<?php else : ?>
	<li class="noresults <?php echo ($parent = Request::getString('parent', '')) ? 'parent-' . $parent : ''; ?>"><?php echo $this->model->isProvisioned() ? Lang::txt('PLG_PROJECTS_FILES_SELECTOR_NO_FILES_FOUND_PROV') : Lang::txt('PLG_PROJECTS_FILES_SELECTOR_NO_FILES_FOUND'); ?></li>
<?php endif; ?>

<?php if (!isset($this->noUl) || !$this->noUl) : ?>
	</ul>
<?php endif;
