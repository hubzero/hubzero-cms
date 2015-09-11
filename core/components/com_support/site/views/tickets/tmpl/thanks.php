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
?>
<?php if ($this->no_html) { ?>
	<div id="report-response">
		<div>
			<p><?php echo Lang::txt('COM_FEEDBACK_YOUR_TICKET'); ?> # <span><a href="<?php echo Route::url('index.php?option=com_support&task=ticket&id='.$this->ticket); ?>" title="View ticket"><?php echo $this->ticket; ?></a></span></p>
			<p><button onclick="javascript:HUB.ReportProblem.resetForm();" title="<?php echo Lang::txt('COM_FEEDBACK_NEW_REPORT'); ?>"><?php echo Lang::txt('COM_FEEDBACK_NEW_REPORT'); ?></button></p>
		</div>
		<p>
			<?php echo Lang::txt('COM_FEEDBACK_TROUBLE_THANKS'); ?><br /><br />
			<?php echo Lang::txt('COM_FEEDBACK_TROUBLE_TICKET_TIMES'); ?>
		</p>
	</div>
	<script type="text/javascript">window.top.window.HUB.ReportProblem.hideTimer();</script>
<?php } else { ?>
	<header id="content-header">
		<h2><?php echo $this->title; ?></h2>
	</header><!-- / #content-header -->

	<section class="main section">
		<?php if ($this->getError()) { ?>
			<p class="error"><?php echo $this->getError(); ?></p>
		<?php } ?>
		<p><?php echo Lang::txt('COM_FEEDBACK_TROUBLE_THANKS'); ?></p>
		<p class="information"><?php echo Lang::txt('COM_FEEDBACK_TROUBLE_TICKET_TIMES'); ?></p>
		<?php if ($this->ticket) { ?>
			<p><?php echo Lang::txt('COM_FEEDBACK_TROUBLE_TICKET_REFERENCE',$this->ticket); ?></p>
		<?php } ?>
	</section><!-- / .main section -->
<?php } ?>