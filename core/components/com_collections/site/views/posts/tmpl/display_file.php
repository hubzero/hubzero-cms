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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->row->item();

$content = $this->row->description('parsed');
$content = ($content ?: $item->description('parsed'));

if ($item->get('title')) { ?>
		<h4>
			<?php echo $this->escape(stripslashes($item->get('title'))); ?>
		</h4>
<?php }

$path = $item->filespace() . DS . $item->get('id');
$href = 'index.php?option=com_collections&controller=media&post=';
$base = 'index.php?option=' . $this->option;

$assets = $item->assets();

if ($assets->total() > 0)
{
	$images = array();
	$files = array();

	foreach ($assets as $asset)
	{
		if ($asset->image())
		{
			$images[] = $asset;
		}
		else
		{
			$files[] = $asset;
		}
	}

	if (count($images) > 0)
	{
		$first = array_shift($images);

		$isLocal = (filter_var($first->file('original'), FILTER_VALIDATE_URL)) ? false : true;
		$imgPath = $isLocal ? $path . DS . $first->file('thumbnail') : $first->file('original');

		if (file_exists($imgPath))
		{
			list($originalWidth, $originalHeight) = getimagesize($imgPath);
			$ratio = $originalWidth / $originalHeight;

			$height = (!isset($this->actual) || !$this->actual)
					? round($this->params->get('maxWidth', 260) / $ratio, 0, PHP_ROUND_HALF_UP)
					: ($originalHeight > 500 ? 500 : $originalHeight);

			$alt = ($first->get('description'))
					? stripslashes($first->get('description'))
					: Lang::txt('COM_COLLECTIONS_IMAGE_ALT', ltrim($first->get('filename'), DS));

			if ($isLocal) : ?>
				<div class="holder">
					<a class="img-link"
						href="<?php echo $first->link('medium'); ?>"
						data-rel="post<?php echo $this->row->get('id'); ?>"
						data-download="<?php echo $first->link('original'); ?>"
						data-downloadtext="<?php echo Lang::txt('COM_COLLECTIONS_DOWNLOAD'); ?>">
						<img src="<?php echo $first->link('thumb'); ?>" alt="<?php echo ($first->get('description')) ? $this->escape(stripslashes($first->get('description'))) : Lang::txt('COM_COLLECTIONS_IMAGE_ALT', ltrim($first->get('filename'), DS)); ?>" class="img" height="<?php echo $height; ?>" />
					</a>
				</div>
			<?php else : ?>
				<div class="holder">
					<a rel="nofollow" download="download" class="img-link"
						href="<?php echo $imgPath; ?>"
						data-rel="post<?php echo $this->row->get('id'); ?>"
						data-download="<?php echo $imgPath; ?>"
						data-downloadtext="<?php echo Lang::txt('COM_COLLECTIONS_DOWNLOAD'); ?>">
						<img src="<?php echo $imgPath; ?>" alt="<?php echo $this->escape($alt); ?>" class="img" height="<?php echo $height; ?>" />
					</a>
				</div>
			<?php endif;
		}
		else
		{
			?>
			<div class="holder notfound">
				<p class="warning"><?php echo Lang::txt('Image not found.'); ?></p>
			</div>
			<?php
		}

		if (count($images) > 0)
		{
			?>
			<div class="gallery">
				<?php
				foreach ($images as $asset)
				{
					$alt = ($asset->get('description')) ? stripslashes($asset->get('description')) : Lang::txt('COM_COLLECTIONS_IMAGE_ALT', ltrim($asset->get('filename'), DS));
					?>
					<a class="img-link"
						href="<?php echo $asset->link('medium'); ?>"
						data-rel="post<?php echo $this->row->get('id'); ?>"
						data-download="<?php echo $asset->link('original'); ?>"
						data-downloadtext="<?php echo Lang::txt('COM_COLLECTIONS_DOWNLOAD'); ?>">
						<img src="<?php echo $asset->link('thumb'); ?>" alt="<?php echo $this->escape($alt); ?>" class="img" width="50" height="50" />
					</a>
					<?php
				}
				?>
				<div class="clearfix"></div>
			</div>
			<?php
		}
	}

	if (count($files) > 0)
	{
?>
		<ul class="file-list">
			<?php foreach ($files as $asset) { ?>
				<li class="type-<?php echo $asset->get('type'); ?>">
					<a href="<?php echo ($asset->isLink()) ? $asset->get('filename') : $asset->link('original'); ?>" <?php echo ($asset->isLink()) ? ' rel="external"' : ''; ?>>
						<?php echo $asset->get('filename'); ?>
					</a>
					<span class="file-meta">
						<span class="file-size">
							<?php if (!$asset->isLink()) { ?>
								<?php
								if ($asset->exists())
								{
									echo \Hubzero\Utility\Number::formatBytes($asset->size());
								}
								else
								{
									echo '--';
								}
								?>
							<?php } else { ?>
								<?php echo Lang::txt('COM_COLLECTIONS_ASSET_TYPE_LINK'); ?>
							<?php } ?>
						</span>
						<?php if ($desc = $asset->get('description')) { ?>
							<span class="file-description">
								<?php echo $this->escape($desc); ?>
							</span>
						<?php } ?>
					</span>
				</li>
			<?php } ?>
		</ul>
<?php
	}
}
?>
<?php if ($content) { ?>
		<div class="description">
			<?php echo $content; ?>
		</div>
<?php }
