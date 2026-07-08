<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$item = $this->row->item();

include_once \Component::path('com_publications') . DS . 'models' . DS . 'publication.php';
$resource = new \Components\Publications\Models\Publication(null, null, $item->get('object_id'));
$imgPath = $resource->hasImage('master');

$url = Route::url($resource->link('version'));

$content = $this->row->description('parsed');
$content = ($content ?: '<p>' . $resource->get('abstract') . '</p>');

if ($imgPath)
{
	list($originalWidth, $originalHeight) = getimagesize($imgPath);
	$ratio = $originalWidth / $originalHeight;

	$height = (!isset($this->actual) || !$this->actual)
			? round($this->params->get('maxWidth', 290) / $ratio, 0, PHP_ROUND_HALF_UP)
			: $originalHeight;

	$alt = $this->escape(stripslashes($resource->get('title', '')));
}
?>
<h4>
	<a href="<?php echo $url; ?>" rel="external nofollow noreferrer">
		<?php echo $this->escape(stripslashes($resource->get('title', $url))); ?>
	</a>
</h4>

<?php if ($imgPath): ?>
<div class="holder">
	<a href="<?php echo $url; ?>" rel="external nofollow noreferrer">
		<img src="<?php echo Route::url($resource->link('masterimage')); ?>" alt="<?php echo $alt; ?>" class="img" height="<?php echo $height; ?>" />
	</a>
</div>
<?php endif; ?>

<?php if ($content): ?>
		<div class="description">
			<?php echo $content; ?>
		</div>
<?php endif;
