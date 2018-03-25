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

?>
<li>
	<span class="mypub-options">
		<a href="<?php echo $this->row->link('version'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_TITLE'); ?>"><?php echo strtolower(Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW')); ?></a> |
		<a href="<?php echo $this->row->link('editversion'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE_TITLE'); ?>"><?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE'); ?></a>
	</span>
	<span class="pub-thumb"><img src="<?php echo Route::url($this->row->link('thumb')); ?>" alt=""/></span>
	<span class="pub-details">
		<?php echo $this->row->get('title'); ?>
		<span class="block faded mini">
			<?php echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION') . ' ' . $this->row->get('version_label'); ?>
			<span class="<?php echo $this->row->getStatusCss(); ?> major_status"><?php echo $this->row->getStatusName(); ?></span>
			<span class="block">
				<?php echo ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CREATED')) . ' ' . $this->row->created('date') . ' ' . Lang::txt('PLG_PROJECTS_PUBLICATIONS_BY') . ' ' . $this->row->creator('name') ; ?>
				<?php if (!$this->row->project()->isProvisioned()) {
				echo Lang::txt('PLG_PROJECTS_PUBLICATIONS_IN_PROJECT') . ' <a href="' . $this->row->project()->link() . '">' . \Hubzero\Utility\Str::truncate(stripslashes($this->row->project()->get('title')), 80) . '</a>';
			} ?></span>
		</span>
	</span>
</li>
