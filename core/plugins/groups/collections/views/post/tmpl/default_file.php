<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
$href = 'index.php?option=com_collections&controller=media&task=download&post=';
$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;

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
		//$assets->rewind();
		$first = array_shift($images);

		$isLocal = (filter_var($first->file('original'), FILTER_VALIDATE_URL)) ? false : true;
		$imgPath = $isLocal ? $path . DS . $first->file('thumbnail') : $first->file('original');

		if (file_exists($imgPath))
		{
			list($originalWidth, $originalHeight) = getimagesize($imgPath);
			$ratio = $originalWidth / $originalHeight;

			$alt = $this->escape(stripslashes($first->get('description', '')));
			$height = (!isset($this->actual) || !$this->actual) ? round($this->params->get('maxWidth', 290) / $ratio, 0, PHP_ROUND_HALF_UP) : $originalHeight;

			if ($isLocal) : ?>
				<div class="holder">
					<a class="img-link"
						href="<?php echo $first->link('medium'); ?>"
						data-rel="post<?php echo $this->row->get('id'); ?>"
						data-download="<?php echo $first->link('original'); ?>"
						data-downloadtext="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_DOWNLOAD'); ?>">
						<img src="<?php echo $first->link('thumb'); ?>" alt="<?php echo $alt; ?>" class="img" height="<?php echo $height; ?>" />
					</a>
				</div>
			<?php else : ?>
				<div class="holder">
					<a class="img-link" rel="nofollow" download="download"
						href="<?php echo $imgPath; ?>"
						data-rel="post<?php echo $this->row->get('id'); ?>"
						data-download="<?php echo $imgPath; ?>"
						data-downloadtext="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_DOWNLOAD'); ?>">
						<img src="<?php echo $imgPath; ?>" alt="<?php echo $alt; ?>" class="img" height="<?php echo $height; ?>" />
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
					?>
					<a class="img-link"
						href="<?php echo $asset->link('medium'); ?>"
						data-rel="post<?php echo $this->row->get('id'); ?>"
						data-download="<?php echo $asset->link('original'); ?>"
						data-downloadtext="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_DOWNLOAD'); ?>">
						<img src="<?php echo $asset->link('thumb'); ?>" alt="<?php echo $this->escape(stripslashes($asset->get('description', ''))); ?>" class="img" width="50" height="50" />
					</a>
					<?php
				}
				?>
				<div class="clearfix"></div>
			</div>
			<?php
		}
	}
	//$assets->rewind();
	if (count($files) > 0)
	{
?>
		<ul class="file-list">
			<?php foreach ($files as $asset) {
				$assetHref = $asset->isLink() ? (string) $asset->get('filename') : $asset->link();
				// A link asset's filename is typed freely in the edit form, so reject
				// by scheme rather than by naming schemes: a denylist of
				// javascript|vbscript|data still admits blob:, filesystem:,
				// view-source: and protocol-relative //evil.com.
				if ($asset->isLink())
				{
					$probe = preg_replace('/[\x00-\x20]+/', '', $assetHref);

					if (preg_match('#^/[/\\\\]#', $probe)
					 || (preg_match('#^[a-z][a-z0-9+.\-]*:#i', $probe) && !preg_match('#^https?://#i', $probe)))
					{
						$assetHref = '';
					}
				}
			?>
				<li class="type-<?php echo $asset->get('type'); ?>">
					<a href="<?php echo $this->escape($assetHref); ?>" <?php echo ($asset->isLink()) ? ' rel="external nofollow noreferrer"' : ''; ?>>
						<?php echo $this->escape($asset->get('filename')); ?>
					</a>
					<span class="file-meta">
						<span class="file-size">
							<?php if (!$asset->isLink() && $asset->exists()) { ?>
								<?php echo \Hubzero\Utility\Number::formatBytes($asset->size()); ?>
							<?php } else { ?>
								<?php
								if ($asset->isExternalLink())
								{
									echo Lang::txt('external link');
								}
								else
								{
									echo Lang::txt('internal link');
								}
								?>
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
