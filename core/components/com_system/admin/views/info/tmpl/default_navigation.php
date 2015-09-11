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
<div id="submenu-box">
	<div class="submenu-box">
		<div class="submenu-pad">
			<ul id="submenu" class="information">
				<li>
					<a href="#page-site" id="site" class="active"><?php echo Lang::txt('COM_SYSTEM_INFO_SYSTEM_INFORMATION'); ?></a>
				</li>
				<li>
					<a href="#page-phpsettings" id="phpsettings"><?php echo Lang::txt('COM_SYSTEM_INFO_PHP_SETTINGS'); ?></a>
				</li>
				<li>
					<a href="#page-config" id="config"><?php echo Lang::txt('COM_SYSTEM_INFO_CONFIGURATION_FILE'); ?></a>
				</li>
				<li>
					<a href="#page-directory" id="directory"><?php echo Lang::txt('COM_SYSTEM_INFO_DIRECTORY_PERMISSIONS'); ?></a>
				</li>
				<li>
					<a href="#page-phpinfo" id="phpinfo"><?php echo Lang::txt('COM_SYSTEM_INFO_PHP_INFORMATION'); ?></a>
				</li>
			</ul>
			<div class="clr"></div>
		</div>
	</div>
	<div class="clr"></div>
</div>
