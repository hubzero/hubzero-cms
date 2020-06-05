<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get blocks
$blocks = $this->pub->_curationModel->_blocks;

$history = $this->pub->_curationModel->getHistory();

if (!$this->ajax):
	$this->css('curation.css')
		->js('curation.js');
endif;
?>
<div id="abox-content" class="history-wrap">
	<h3><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_HISTORY_VIEW'); ?></h3>

	<div class="curation-history">
		<div class="pubtitle">
			<p>
				<?php echo \Hubzero\Utility\Str::truncate($this->pub->title, 65); ?> | <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_VERSION') . ' ' . $this->pub->version_label; ?>
			</p>
		</div>
		<?php if ($history): ?>
			<h4><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_HISTORY_EVENTS'); ?></h4>
			<div class="history-blocks">
				<?php
				$i = 1;
				foreach ($history as $event):
					$trClass = $i % 2 == 0 ? ' even' : ' odd';
					$i++;

					$author = User::getInstance($event->created_by);
					$author = stripslashes($author->get('name'));
					$author = $author ?: Lang::txt('JUNKNOWN');
					?>
					<div class="history-block <?php echo $trClass; ?> grid">
						<div class="changelog-time col span3">
							<time datetime="<?php echo Date::of($event->created)->format('Y-m-d\TH:i:s\Z'); ?>"><?php echo Date::of($event->created)->toLocal('M d, Y H:iA'); ?></time>
							<span class="block"><?php echo $this->escape($author); ?></span>
							<span class="block">(
							<?php echo ($event->curator)
								? Lang::txt('COM_PUBLICATIONS_CURATION_CURATOR')
								: Lang::txt('COM_PUBLICATIONS_CURATION_AUTHOR'); ?>
							)</span>
						</div>
						<div class="changelog-text col span9 omega">
							<div class="changelog-contents">
								<?php echo $event->changelog; ?>
							</div>
							<?php if ($event->comment): ?>
								<div class="changelog-comment">
									<?php echo Lang::txt('COM_PUBLICATIONS_CURATION_SUBMITTER_COMMENT') . ' <span class="italic">' . $event->comment . '</span>'; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<?php
				endforeach;
				?>
			</div>
		<?php else: ?>
			<p class="warning"><?php echo Lang::txt('COM_PUBLICATIONS_CURATION_HISTORY_NOTHING'); ?></p>
		<?php endif; ?>
	</div>
</div>
