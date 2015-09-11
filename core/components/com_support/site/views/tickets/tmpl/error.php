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
<div id="report-response">
	<div>
		<p><?php echo Lang::txt('COM_SUPPORT_ERROR_PROCESSING_FORM'); ?></p>
		<p><a href="javascript:HUB.Modules.ReportProblems.reshowForm();" title="<?php echo Lang::txt('COM_SUPPORT_EDIT_REPORT'); ?>"><?php echo Lang::txt('COM_SUPPORT_EDIT_REPORT'); ?></a></p>
	</div>
	<h3><?php echo Lang::txt('COM_SUPPORT_ERROR'); ?></h3>
	<p><?php echo Lang::txt('COM_SUPPORT_ERROR_PROCESSING_DESCRIPTION'); ?></p>
<?php if ($this->getError()) { echo '<p>' . $this->getError() . '</p>'; } ?>
</div>

<script type="text/javascript">window.top.window.HUB.Modules.ReportProblems.hideTimer();</script>