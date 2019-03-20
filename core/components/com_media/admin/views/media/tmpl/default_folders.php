<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$icon = 'folder.svg';
		$open = 0;
		$p = array();
		if ($this->folderDepth == 1):
			$cls = ' class="open"';
			$icon = 'folder-open.svg';
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
			<a class="folder" data-folder="<?php echo $this->escape('/' . $folder['path']); ?>" href="<?php echo Route::url('index.php?option=com_media&controller=medialist&tmpl=component&' . Session::getFormToken() . '=1&folder=/' . urlencode($folder['path'])); ?>">
				<span class="folder-icon">
					<img src="<?php echo $this->img($icon); ?>" alt="<?php echo $this->escape($folder['name']); ?>" />
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
