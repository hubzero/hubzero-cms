<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
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
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css()
     ->js();
?>
<div class="hub-mail">
	<table class="hub-message">
		<thead>
			<tr>
				<th colspan="2"><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_DETAILS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_DATE_RECEIVED'); ?></th>
				<td><?php echo Date::of($this->xmessage->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_FROM'); ?></th>
				<td><?php echo $this->from; ?></td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_SUBJECT'); ?></th>
				<td>
					<?php
						$subject = stripslashes($this->xmessage->subject);
						if ($this->xmessage->component == 'support')
						{
							$fg = explode(' ', $subject);
							$fh = array_pop($fg);
							echo implode(' ', $fg);
						}
						else
						{
							echo $this->escape($subject);
						}
					?>
				</td>
			</tr>
			<tr>
				<th><?php echo Lang::txt('PLG_MEMBERS_MESSAGES_MESSAGE'); ?></th>
				<td><?php echo $this->xmessage->message; ?></td>
			</tr>
		</tbody>
	</table>
</div>
