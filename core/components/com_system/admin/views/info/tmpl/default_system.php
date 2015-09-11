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
	<legend><?php echo Lang::txt('COM_SYSTEM_INFO_SYSTEM_INFORMATION'); ?></legend>
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
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_PHP_BUILT_ON'); ?>
				</th>
				<td>
					<?php echo $this->info['php'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_DATABASE_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['dbversion'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_DATABASE_COLLATION'); ?>
				</th>
				<td>
					<?php echo $this->info['dbcollation'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_PHP_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['phpversion'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_WEB_SERVER'); ?>
				</th>
				<td>
					<?php echo Html::system('server', $this->info['server']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_WEBSERVER_TO_PHP_INTERFACE'); ?>
				</th>
				<td>
					<?php echo $this->info['sapi_name'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_JOOMLA_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['version'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_PLATFORM_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['platform'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_USER_AGENT'); ?>
				</th>
				<td>
					<?php echo htmlspecialchars($this->info['useragent']);?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>
