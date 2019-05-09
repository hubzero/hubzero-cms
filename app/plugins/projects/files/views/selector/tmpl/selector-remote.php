<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$connection = \Components\Projects\Models\Orm\Connection::oneOrFail(Request::getInt('cid'));
?>

<?php if (!isset($this->noUl) || !$this->noUl) : ?>
	<ul class="file-selector" id="file-selector">
<?php endif; ?>

<?php if (count($this->items) > 0) : ?>
	<?php $a = 1; ?>
	<?php foreach ($this->items as $item)
	{
		$levelCss = 'level-' . $item->getDirLevel();

		// Get element ID
		$liId  = $item->isDir()
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
		$selected = !empty($this->selected) && in_array($item->getPath(), $this->selected) ? 1 : 0;

		// Is file type allowed?
		$allowed = $item->isFile() && !empty($this->allowed)
				&& !in_array($item->getExtension(), $this->allowed)
				? ' notallowed' : ' allowed';

		$used = !empty($this->used)
				&& in_array($item->getPath(), $this->used) ? true : false;

		// Do not allow files used in other elements
		$allowed = $used ? ' notallowed' : $allowed;

		// No selection for folders
		$allowed = $item->isDir() ? ' freeze' : $allowed;

		// Do not allow to delete previously selected items
		$allowed = $selected ? ' freeze' : $allowed;

		?>
		<li class="<?php echo $item->isDir() ? 'type-folder collapsed' : 'type-file'; ?><?php echo $parentCss; ?><?php if ($selected) { echo ' selectedfilter preselected'; } ?><?php echo $allowed; ?>" id="<?php echo $liId; ?>" data-path="<?php echo $item->getPath(); ?>" data-connection="<?php echo $connection->id; ?>">
			<span class="item-info"><?php echo $item->isFile() ? $item->getSize() : ''; ?></span>
			<span class="item-wrap <?php echo ($item->isDir() ? 'collapsor ' : ' ') . $levelCss; ?>" id="<?php echo urlencode($connection->id . '://' . $item->getPath()); ?>">
				<?php if ($item->isDir()) { ?><span class="collapsor-indicator">&nbsp;</span><?php } ?>
				<?php echo \Components\Projects\Models\File::drawIcon($item->getExtension()); ?>
				<span title="<?php echo $item->getPath(); ?>"><?php echo \Components\Projects\Helpers\Html::shortenFileName($item->getDisplayName(), 50); ?></span>
			</span>

		</li>
	<?php } ?>
<?php else : ?>
	<li class="noresults <?php echo ($parent = Request::getString('parent', '')) ? 'parent-' . $parent : ''; ?>"><?php echo $this->model->isProvisioned() ? Lang::txt('PLG_PROJECTS_FILES_SELECTOR_NO_FILES_FOUND_PROV') : Lang::txt('PLG_PROJECTS_FILES_SELECTOR_NO_FILES_FOUND'); ?></li>
<?php endif; ?>

<?php if (!isset($this->noUl) || !$this->noUl) : ?>
	</ul>
<?php endif;
