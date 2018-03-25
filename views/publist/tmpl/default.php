<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
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
 */

// No direct access
defined('_HZEXEC_') or die();

if (count($this->items) > 0) { ?>
<div class="public-list-header">
	<h3><?php echo Lang::txt('COM_PROJECTS_PUBLICATIONS'); ?></h3>
</div>
<div class="public-list-wrap">
	<ul class="public-list">
		<?php foreach ($this->items as $pub) { ?>
			<li>
				<span class="pub-thumb"><img src="<?php echo Route::url($pub->link('thumb')); ?>" alt="" /></span>
				<span class="pub-details">
					<a href="<?php echo Route::url($pub->link('version')); ?>" title="<?php echo $this->escape($pub->get('title')); ?>"><?php echo stripslashes($pub->get('title')) . ' v.' . $pub->get('version_label'); ?></a>
					<span class="public-list-info">
						- <?php echo Lang::txt('COM_PROJECTS_PUBLISHED') . ' ' . $pub->published('date') . ' ' . Lang::txt('COM_PROJECTS_IN') . ' <a href="' . Route::url($pub->link('category')) . '">' . $pub->category()->name . '</a>'; ?>
					</span>
				</span>
			</li>
		<?php } ?>
	</ul>
</div>
<?php }
