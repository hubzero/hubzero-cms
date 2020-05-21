<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->row->item();

$content = $this->row->description('parsed');
$content = ($content ?: $item->description('parsed'));

$path = $item->filespace() . DS . $item->get('id');
$asset = $item->assets()[0];

if ($asset)
{
	$imgPath = $path . DS . $asset->file('thumbnail');

	list($originalWidth, $originalHeight) = getimagesize($imgPath);
	$ratio = $originalWidth / $originalHeight;

	$height = (!isset($this->actual) || !$this->actual)
			? round($this->params->get('maxWidth', 290) / $ratio, 0, PHP_ROUND_HALF_UP)
			: $originalHeight;

	$alt = $this->escape(stripslashes($asset->get('description', '')));
}
?>
<p class="publication">
	<a href="<?php echo stripslashes($item->get('url')); ?>" rel="external nofollow noreferrer">
		<?php echo $this->escape(stripslashes($item->get('title', $item->get('url')))); ?>
	</a>
</p>

<?php if ($asset): ?>
<div class="holder">
	<a class="img-link"
		href="<?php echo $asset->link('original'); ?>"
		data-rel="post<?php echo $this->row->get('id'); ?>"
		data-download="<?php echo $asset->link('original'); ?>"
		data-downloadtext="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_DOWNLOAD'); ?>">
		<img src="<?php echo $asset->link('thumb'); ?>" alt="<?php echo $alt; ?>" class="img" height="<?php echo $height; ?>" />
	</a>
</div>
<?php endif; ?>

<?php if ($content): ?>
		<div class="description">
			<?php echo $content; ?>
		</div>
<?php endif;
