<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>
<div class="container" id="recommendations">
	<h3><?php echo Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_HEADER'); ?></h3>

	<?php if ($this->results) { ?>
		<ul>
		<?php foreach ($this->results as $line) { ?>
			<li>
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&' . ($line->alias ? 'alias=' . $line->alias : 'id=' . $line->id) . '&rec_ref=' . $this->resource->id); ?>"><?php echo $this->escape(stripslashes($line->title)); ?></a>
			</li>
		<?php } ?>
		</ul>
	<?php } else { ?>
		<p><?php echo Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_NO_RESULTS_FOUND'); ?></p>
	<?php } ?>

	<p id="credits">
		<a href="<?php echo Request::base(true); ?>/about/hubzero#recommendations"><?php echo Lang::txt('PLG_RESOURCES_RECOMMENDATIONS_POWERED_BY'); ?></a>
	</p>
</div>
