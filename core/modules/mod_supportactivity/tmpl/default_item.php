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

defined('_HZEXEC_') or die();
?>
				<li data-parent="activity-list<?php echo $this->module->id; ?>" data-time="<?php echo $result->created; ?>" class="<?php echo $this->escape($result->category); ?>">
					<a href="<?php echo Route::url('index.php?option=com_support&controller=tickets&task=edit&id=' . $result->ticket . ($result->id ? '#c' . $result->id : '')); ?>">
						<span class="activity-event">
							<?php echo Lang::txt('MOD_SUPPORTACTIVITY_' . strtoupper($result->category), $result->ticket); ?>
						</span>
						<span class="activity-details">
							<span class="activity-time"><time datetime="<?php echo $result->created; ?>"><?php echo Date::of($result->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1')); ?></time></span>
							<span class="activity-date"><time datetime="<?php echo $result->created; ?>"><?php echo Date::of($result->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time></span>
						</span>
					</a>
				</li>