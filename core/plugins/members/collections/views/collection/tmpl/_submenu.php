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

$base = $this->member->link() . '&active=' . $this->name;
?>
	<nav>
		<ul class="sub-menu">
			<?php if ($this->params->get('access-manage-collection')) { ?>
				<li<?php if ($this->active == 'livefeed') { echo ' class="active"'; } ?>>
					<a class="livefeed tooltips" href="<?php echo Route::url($base); ?>" title="<?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED_TITLE'); ?>">
						<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_FEED'); ?></span>
					</a>
				</li>
			<?php } ?>
			<li<?php if ($this->active == 'collections') { echo ' class="active"'; } ?>>
				<a class="collections count" href="<?php echo Route::url($base . '&task=all'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_COLLECTIONS', $this->collections); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'posts') { echo ' class="active"'; } ?>>
				<a class="posts count" href="<?php echo Route::url($base . '&task=posts'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_POSTS', $this->posts); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'followers') { echo ' class="active"'; } ?>>
				<a class="followers count" href="<?php echo Route::url($base . '&task=followers'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_FOLLOWERS', $this->followers); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'following') { echo ' class="active"'; } ?>>
				<a class="following count" href="<?php echo Route::url($base . '&task=following'); ?>">
					<span><?php echo Lang::txt('PLG_MEMBERS_COLLECTIONS_HEADER_NUM_FOLLOWNG', $this->following); ?></span>
				</a>
			</li>
		</ul>
	</nav>