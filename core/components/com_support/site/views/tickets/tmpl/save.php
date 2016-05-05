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

// No direct access.
defined('_HZEXEC_') or die();

if (!$this->no_html)
{
	$this->css();
}
?>
<?php if ($this->no_html) { ?>
	<div id="report-response">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>
		<div>
			<p><?php echo Lang::txt('COM_SUPPORT_YOUR_TICKET'); ?> # <span><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=' . $this->ticket); ?>"><?php echo $this->ticket; ?></a></span></p>
			<p><button onclick="javascript:HUB.Modules.ReportProblems.resetForm();" title="<?php echo Lang::txt('COM_SUPPORT_NEW_REPORT'); ?>"><?php echo Lang::txt('COM_SUPPORT_NEW_REPORT'); ?></button></p>
		</div>
		<p>
			<?php echo Lang::txt('COM_SUPPORT_TROUBLE_THANKS'); ?><br /><br />
			<?php echo Lang::txt('COM_SUPPORT_TROUBLE_TICKET_TIMES'); ?>
		</p>
	</div>
	<script type="text/javascript">window.top.window.HUB.Modules.ReportProblems.hideTimer();</script>
<?php } else { ?>
	<header id="content-header">
		<h2><?php echo Lang::txt('COM_SUPPORT'); ?></h2>
	</header><!-- / #content-header -->
	<section class="main section">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
		<?php } ?>
		<div class="grid">
			<div class="col span-half">
				<div id="ticket-number">
					<h2>
						<span><?php echo Lang::txt('COM_SUPPORT_TICKET_NUMBER', ' '); ?></span><strong><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=ticket&id=' . $this->ticket); ?>"><?php echo $this->ticket; ?></a></strong>
					</h2>
				</div>
			</div>
			<div class="col span-half omega">
				<div id="messagebox">
					<div class="wrap">
						<h3><?php echo Lang::txt('COM_SUPPORT_TROUBLE_THANKS'); ?></h3>
						<p><?php echo Lang::txt('COM_SUPPORT_TROUBLE_TICKET_TIMES'); ?></p>
					<?php if ($this->ticket) { ?>
						<p><?php echo Lang::txt('COM_SUPPORT_TROUBLE_TICKET_REFERENCE', $this->ticket); ?></p>
					<?php } ?>
					</div>
				</div>
			</div><!-- / .col span-half omega -->
		</div><!-- / .grid -->
	</section><!-- / .main section -->
<?php } ?>