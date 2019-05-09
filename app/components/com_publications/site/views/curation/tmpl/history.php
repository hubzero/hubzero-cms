<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get blocks
$blocks = $this->pub->_curationModel->_blocks;

$history = $this->pub->_curationModel->getHistory();

if (!$this->ajax)
{
	$this->css('curation.css')
		->js('curation.js');
}
?>
<div id="abox-content" class="history-wrap">
	<h3><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_HISTORY_VIEW'); ?></h3>

	<div class="curation-history">
		<div class="pubtitle">
			<p>
				<?php echo \Hubzero\Utility\Str::truncate($this->pub->title, 65); ?> | <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_VERSION') . ' ' . $this->pub->version_label; ?>
			</p>
		</div>
		<?php if ($history) { ?>
			<h5><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_HISTORY_EVENTS'); ?></h5>
			<div class="history-blocks">
				<?php
				$i = 1;
				foreach ($history as $event)
				{
					$author  = User::getInstance($event->created_by);
					$trClass = $i % 2 == 0 ? ' even' : ' odd';
					$i++;
					?>
					<div class="history-block <?php echo $trClass; ?> grid">
						<div class="changelog-time col span3">
							<?php echo Date::of($event->created)->toLocal('M d, Y H:iA'); ?>
							<span class="block"><?php echo $this->escape(stripslashes($author->get('name'))); ?></span>
							<span class="block">(
							<?php echo ($event->curator)
								? Lang::txt('COM_PUBLICATIONS_CURATION_CURATOR')
								: Lang::txt('COM_PUBLICATIONS_CURATION_AUTHOR'); ?>
							)</span>
						</div>
						<div class="changelog-text col span9 omega">
							<?php echo $event->changelog; ?>
							<?php if ($event->comment) { ?>
								<p><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTER_COMMENT') . ' <span class="italic">' . $event->comment . '</span>'; ?></p>
							<?php } ?>
						</div>
						<div class="clear"></div>
					</div>
					<?php
				}
				?>
			</div>
		<?php } else { ?>
			<p class="warning"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_HISTORY_NOTHING'); ?></p>
		<?php } ?>
	</div>
</div>
