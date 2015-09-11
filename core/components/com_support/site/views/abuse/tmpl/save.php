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

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<div class="grid">
		<div class="col span-half">
			<div id="ticket-number">
				<h2>
					<?php echo Lang::txt('COM_SUPPORT_REPORT_NUMBER', $this->report->id); ?>
				</h2>
			</div>
		</div><!-- / .col span-half -->
		<div class="col span-half omega">
			<div id="messagebox">
				<div class="wrap">
					<h3><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_THANKS'); ?></h3>
				<?php if ($this->report) { ?>
					<p><?php echo Lang::txt('COM_SUPPORT_REPORT_NUMBER_REFERENCE', $this->report->id); ?></p>
				<?php } ?>
				<?php if ($this->returnlink) { ?>
					<p><a class="btn" href="<?php echo $this->returnlink; ?>"><?php echo Lang::txt('COM_SUPPORT_REPORT_ABUSE_CONTINUE'); ?></a></p>
				<?php } ?>
				</div>
			</div>
		</div><!-- / .col span-half omega -->
	</div><!-- / .grid -->
</section><!-- / .main section -->
