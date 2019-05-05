<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$width  = '780';
$height = '460';

$source = with(new \Hubzero\Content\Moderator($this->compiled->getAbsolutePath()))->getUrl();
?>

<?php if ($this->getError()) : ?>
	<h3>
		<?php echo Lang::txt('PLG_HANDLERS_LATEX_PREVIEW_FAILED'); ?>
	</h3>
	<p class="witherror">
		<?php echo $this->getError(); ?>
		<pre>
			<?php if (!empty($this->log)) : ?>
				<?php echo $this->log; ?>
			<?php endif; ?>
		</pre>
	</div>
<?php endif; ?>

<div id="compiled-doc" embed-src="<?php echo $source; ?>" embed-width="<?php echo $width; ?>" embed-height="<?php echo $height; ?>">
	<object class="width-container" width="<?php echo $width; ?>" height="<?php echo $height; ?>" type="<?php echo $this->compiled->getMimetype(); ?>" data="<?php echo $source; ?>" id="pdf_content">
		<embed src="<?php echo $source; ?>" type="<?php echo $this->compiled->getMimetype(); ?>" />
	</object>
</div>