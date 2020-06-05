<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

if (!isset($this->folderDepth)):
	$this->folderDepth = 1;
endif;
?>
<ul <?php echo $this->folders_id; ?> class="<?php echo 'depth' . $this->folderDepth; ?>">
	<?php foreach ($this->folderTree as $folder) : ?>
		<?php
		$cls = '';

		$icon = Html::asset('image', 'assets/filetypes/folder.svg', '', null, true, true);

		$open = 0;
		$p = array();
		if ($this->folderDepth == 1):
			$cls = ' class="open"';
			$icon = Html::asset('image', 'assets/filetypes/folder-open.svg', '', null, true, true);
		else:
			$fld = trim($this->folder, '/');
			$trail = explode('/', $fld);

			$p = explode('/', trim($folder['path'], '/'));

			foreach ($p as $i => $f):
				if (!isset($trail[$i])):
					break;
				endif;

				if ($p[$i] == $trail[$i]):
					$open++;
				endif;
			endforeach;

			if ($open && $open == count($p)):
				$cls = ' class="open"';
			endif;
		endif;
		?>
		<li id="<?php echo $this->escape($folder['name']); ?>"<?php echo $cls; ?>>
			<a class="folder" data-folder="<?php echo $this->escape('/' . $folder['path']); ?>" href="<?php echo Route::url('index.php?option=com_media&controller=medialist&tmpl=component&tmpl=' . Request::getCmd('tmpl') . '&' . Session::getFormToken() . '=1&folder=/' . urlencode($folder['path'])); ?>">
				<span class="folder-icon">
					<img src="<?php echo $icon; ?>" alt="<?php echo $this->escape($folder['name']); ?>" />
				</span>
				<?php echo $this->escape($folder['name']); ?>
			</a>
			<?php
			if (isset($folder['children']) && count($folder['children'])):
				$temp = $this->folderTree;

				$this->folderTree = $folder['children'];
				$this->folders_id = 'id="folder-' . $folder['name'] . '"';
				$this->folderDepth++;

				echo $this->loadTemplate('folders');

				$this->folderTree = $temp;
				$this->folderDepth--;
			endif;
			?>
		</li>
	<?php endforeach; ?>
</ul>
