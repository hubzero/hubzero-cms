<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
				<?php echo \Hubzero\Utility\String::truncate($this->pub->title, 65); ?> | <?php echo Lang::txt('COM_PUBLICATIONS_CURATION_VERSION') . ' ' . $this->pub->version_label; ?>
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
