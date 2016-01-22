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

// no direct access
defined('_HZEXEC_') or die();
?>
<fieldset class="adminform">
	<legend><?php echo Lang::txt('COM_SYSTEM_INFO_CONFIGURATION_FILE'); ?></legend>
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<?php echo Lang::txt('COM_SYSTEM_INFO_SETTING'); ?>
				</th>
				<th scope="col">
					<?php echo Lang::txt('COM_SYSTEM_INFO_VALUE'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->config as $key => $value):?>
				<tr>
					<td>
						<?php echo $key;?>
					</td>
					<td>
						<?php
						if (is_array($value))
						{
							foreach ($value as $ky => $val)
							{
								if (in_array($ky, array('password','user','smtpuser','smtppass','secret','ftp_user','ftp_pass')))
								{
									$val = str_repeat('x', 6);
								}

								if (is_array($val))
								{
									foreach ($val as $k => $v)
									{
										echo htmlspecialchars($k, ENT_QUOTES) .' = ' . htmlspecialchars($v, ENT_QUOTES) . '<br />';
									}
								}
								else
								{
									echo htmlspecialchars($ky, ENT_QUOTES) .' = ' . htmlspecialchars($val, ENT_QUOTES) . '<br />';
								}
							}
						}
						else
						{
							echo htmlspecialchars($value, ENT_QUOTES);
						}
						?>
					</td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>
</fieldset>
