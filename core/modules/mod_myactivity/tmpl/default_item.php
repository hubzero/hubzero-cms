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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

$status = '';
if (!$row->wasViewed())
{
	$status = 'new';

	$row->markAsViewed();
}

$name = $this->escape(stripslashes($row->log->creator()->get('name')));

?>
<li
	data-time="<?php echo $row->get('created'); ?>"
	data-id="<?php echo $row->get('id'); ?>"
	data-log_id="<?php echo $row->get('log_id'); ?>"
	class="activity <?php echo $this->escape($row->get('scope') . '.' . $row->get('scope_id') . ' ' . $row->log->get('action')) . ' ' . $status; ?>">

	<div class="activity <?php echo $this->escape($row->log->get('component')); ?>">
		<span class="activity-details">
			<span class="activity-actor">
				<?php if ($row->log->creator()->get('public')) { ?>
					<a href="<?php echo Route::url($row->log->creator()->getLink()); ?>">
						<?php echo $name; ?>
					</a>
				<?php } else { ?>
					<?php echo $name; ?>
				<?php } ?>
			</span>
			<span class="activity-time"><time datetime="<?php echo $row->get('created'); ?>"><?php echo Date::of($row->get('created'))->relative(); ?></time></span>
			<!-- <span class="activity-channel"><?php echo $this->escape($row->get('scope') . '.' . $row->get('scope_id')); ?></span> -->
		</span>
		<span class="activity-event">
			<?php echo $row->log->get('description'); ?>
		</span>
	</div>

</li>
