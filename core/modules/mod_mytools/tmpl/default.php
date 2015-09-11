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

// Push the module CSS to the template
$this->css();
$this->js();

if ($this->fav || $this->no_html) { ?>
	<?php echo $this->buildList($this->favtools, 'fav'); ?>
	<p><?php echo Lang::txt('MOD_MYTOOLS_EXPLANATION'); ?></p>
<?php } else { ?>
	<div id="myToolsTabs" data-api="<?php echo Route::url('index.php?option=com_members&active=dashboard&no_html=1&init=1&action='); ?>">
		<ul class="tab_titles">
			<li title="recenttools" class="active"><?php echo Lang::txt('MOD_MYTOOLS_RECENT'); ?></li>
			<li title="favtools"><?php echo Lang::txt('MOD_MYTOOLS_FAVORITES'); ?></li>
			<li title="alltools"><?php echo Lang::txt('MOD_MYTOOLS_ALL_TOOLS'); ?></li>
		</ul>

		<div id="recenttools" class="tab_panel active">
			<?php echo $this->buildList($this->rectools, 'recent'); ?>
			<p><?php echo Lang::txt('MOD_MYTOOLS_RECENT_EXPLANATION'); ?></p>
		</div>

		<div id="favtools" class="tab_panel">
			<?php echo $this->buildList($this->favtools, 'favs'); ?>
			<p><?php echo Lang::txt('MOD_MYTOOLS_FAVORITES_EXPLANATION'); ?></p>
		</div>

		<div id="alltools" class="tab_panel">
			<div id="filter-mytools">
				<input type="text" placeholder="<?php echo Lang::txt('MOD_MYTOOLS_SEARCH_PLACEHOLDER'); ?>" />
			</div>
			<?php echo $this->buildList($this->alltools, 'all'); ?>
			<p><?php echo Lang::txt('MOD_MYTOOLS_ALL_TOOLS_EXPLANATION'); ?></p>
		</div>
	</div>
	<input type="hidden" class="mytools_favs" value="<?php echo $this->escape(implode(',', $this->favs)); ?>" />
<?php }