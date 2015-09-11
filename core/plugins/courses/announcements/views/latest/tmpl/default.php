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

// No direct access
defined('_HZEXEC_') or die();

$this->css();
if ($this->params->get('allowClose', 1))
{
	$this->js();
}

$rows = $this->offering->announcements(array(
	'limit'     => $this->params->get('display_limit', 1),
	'published' => true
));

if ($rows->total() > 0)
{
	$announcements = array();

	foreach ($rows as $row)
	{
		if ($this->params->get('allowClose', 1))
		{
			if (!($hide = Request::getWord('ancmnt' . $row->get('id'), '', 'cookie')))
			{
				$announcements[] = $row;
			}
		}
	}

	if (count($announcements))
	{
		?>
		<div class="announcements">
			<?php foreach ($announcements as $row) { ?>
				<div class="announcement<?php if ($row->get('priority')) { echo ' high'; } ?>">
					<?php echo $row->content('parsed'); ?>
					<dl class="entry-meta">
						<dt class="entry-id"><?php echo $row->get('id'); ?></dt>
						<dd class="time">
							<time datetime="<?php echo $row->published(); ?>">
								<?php echo $row->published('time'); ?>
							</time>
						</dd>
						<dd class="date">
							<time datetime="<?php echo $row->published(); ?>">
								<?php echo $row->published('date'); ?>
							</time>
						</dd>
					</dl>
					<?php
						$page = Request::getVar('REQUEST_URI', '', 'server');
						if ($page && $this->params->get('allowClose', 1))
						{
							$page .= (strstr($page, '?')) ? '&' : '?';
							$page .= 'ancmnt' . $row->get('id') . '=closed';
							?>
							<a class="close" href="<?php echo $page; ?>" data-id="<?php echo $row->get('id'); ?>" data-duration="<?php echo $this->params->get('closeDuration', 30); ?>" title="<?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_CLOSE_THIS'); ?>">
								<span><?php echo Lang::txt('PLG_COURSES_ANNOUNCEMENTS_CLOSE'); ?></span>
							</a>
							<?php
						}
					?>
				</div>
			<?php } ?>
		</div>
		<?php
	}
}