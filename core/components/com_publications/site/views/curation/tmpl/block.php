<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get block properties
$complete = $this->pub->curation('blocks', $this->step, 'complete');
$props    = $this->pub->curation('blocks', $this->step, 'props');
$required = $this->pub->curation('blocks', $this->step, 'required');

// Side text from block manifest
$about = $this->manifest->adminTips ? $this->manifest->adminTips : $this->manifest->about;

// Get curator status
$curatorStatus = $this->pub->curation()->getCurationStatus($this->pub, $this->step, 0, 'curator');

$cls = array();
$cls[] = $required ? ' el-required' : ' el-optional';
$cls[] = $complete ? ' el-complete' : ' el-incomplete';
$cls[] = $curatorStatus->status == 1 ? ' el-passed' : '';
$cls[] = $curatorStatus->status == 0 ? ' el-failed' : '';
$cls[] = $curatorStatus->updated && $curatorStatus->status != 2 ? ' el-updated' : '';

$cls = implode(' ', $cls);
$cls = trim($cls);
?>
<div class="curation-block">
	<h4><?php echo $this->manifest->title; ?></h4>

	<?php if (!$this->pub->curation('blocks', $this->step, 'hasElements')): ?>
		<div id="<?php echo 'element' . $this->active; ?>" class="blockelement <?php echo $cls; ?>">
			<div class="element_overview">
				<div class="block-aside">
					<div class="block-info"><?php echo $about; ?></div>
				</div>

				<?php echo $this->pub->curation()->drawChecker($props, $curatorStatus, Route::url($this->pub->link('edit')), $this->manifest->title); ?>

				<div class="block-subject">
					<h5 class="element-title"><?php echo $this->manifest->label; ?></h5>

					<?php echo $this->pub->curation()->drawCurationNotice($curatorStatus, $props, 'curator', 'element' . $this->active); ?>

					<?php echo $this->content; ?>
				</div>
			</div>
		</div>
	<?php else: ?>
		<div class="curation-item">
			<?php echo $this->content; ?>
		</div>
	<?php endif; ?>
</div>
