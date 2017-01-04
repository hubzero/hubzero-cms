<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<?php if (!isset($this->noUl) || !$this->noUl) : ?>
	<ul class="file-selector" id="file-selector">
<?php endif; ?>

<?php if (count($this->items) > 0) : ?>
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
				<img src="<?php echo $item->get('icon'); ?>" alt="" /> <span title="<?php echo $item->get('localPath'); ?>"><?php echo \Components\Projects\Helpers\Html::shortenFileName($item->get('name'), 50); ?></span>
			</span>
		</li>
	<?php } ?>
<?php else : ?>
	<li class="noresults <?php echo ($parent = Request::getString('parent', '')) ? 'parent-' . $parent : ''; ?>"><?php echo $this->model->isProvisioned() ? Lang::txt('PLG_PROJECTS_FILES_SELECTOR_NO_FILES_FOUND_PROV') : Lang::txt('PLG_PROJECTS_FILES_SELECTOR_NO_FILES_FOUND'); ?></li>
<?php endif; ?>

<?php if (!isset($this->noUl) || !$this->noUl) : ?>
	</ul>
<?php endif; ?>
