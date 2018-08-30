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

// No direct access.
defined('_HZEXEC_') or die();

?>
		<div class="container">
			<h3><?php echo Lang::txt('COM_WIKI_SEARCH'); ?></h3>

			<form action="<?php echo Route::url($this->page->link('base') . '&pagename=Special:Search'); ?>" method="get">
				<fieldset>
					<legend><?php echo Lang::txt('COM_WIKI_SEARCH_LEGEND'); ?></legend>
					<label for="page-search-q">
						<span><?php echo Lang::txt('COM_WIKI_SEARCH'); ?></span>
						<input type="text" name="q" id="page-search-q" value="" placeholder="<?php echo Lang::txt('COM_WIKI_SEARCH_PLACEHOLDER'); ?>" />
					</label>
					<input type="submit" class="btn page-search-submit" value="<?php echo Lang::txt('COM_WIKI_GO'); ?>" />
				</fieldset>
			</form>
		</div>

		<div class="container">
			<h3><?php echo Lang::txt('COM_WIKI'); ?></h3>
			<ul id="<?php echo ($this->sub) ? 'page_options' : 'useroptions'; ?>">
				<li class="page-main">
					<a href="<?php echo Route::url($this->page->link('base')); ?>">
						<?php echo Lang::txt('COM_WIKI_MAIN_PAGE'); ?>
					</a>
				</li>
				<li class="page-help">
					<a href="<?php echo Route::url($this->page->link('base') . '&pagename=Help:Index'); ?>">
						<?php echo Lang::txt('COM_WIKI_HELP'); ?>
					</a>
				</li>
				<li class="page-index">
					<a href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:AllPages'); ?>">
						<?php echo Lang::txt('COM_WIKI_PAGE_INDEX'); ?>
					</a>
				</li>
				<li class="page-recent">
					<a href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:RecentChanges'); ?>">
						<?php echo Lang::txt('COM_WIKI_SPECIAL_RECENT_CHANGES'); ?>
					</a>
				</li>
			</ul>
		</div>

	<?php if ($this->page->getNamespace() != 'special') { ?>
		<div class="container">
			<h3><?php echo Lang::txt('COM_WIKI_TOOLS'); ?></h3>
			<ul>
				<li class="page-links">
					<a href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:Links&page=' . $this->page->get('pagename') . '&version=' . $this->page->get('version_id')); ?>">
						<?php echo Lang::txt('COM_WIKI_SPECIAL_LINKS'); ?>
					</a>
				</li>
				<li class="page-cite">
					<a href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:Cite&page=' . $this->page->get('pagename') . '&version=' . $this->page->get('version_id')); ?>">
						<?php echo Lang::txt('COM_WIKI_SPECIAL_CITE'); ?>
					</a>
				</li>
				<li class="page-pdf">
					<a href="<?php echo Route::url($this->page->link('pdf')); ?>">
						<?php echo Lang::txt('COM_WIKI_TAB_PDF'); ?>
					</a>
				</li>
				<?php if (!User::isGuest() && $this->page->access('create')) { ?>
					<li class="page-new" data-title="<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>">
						<a href="<?php echo Route::url($this->page->link('base') . '&' . ($this->sub ? 'action' : 'task') . '=new'); ?>">
							<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>
						</a>
					</li>
				<?php } ?>
			</ul>
		</div>
	<?php } else { ?>
		<?php if (!User::isGuest() && $this->page->access('create')) { ?>
			<div class="container">
				<h3><?php echo Lang::txt('COM_WIKI_TOOLS'); ?></h3>
				<ul>
					<li class="page-new" data-title="<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>">
						<a href="<?php echo Route::url($this->page->link('base') . '&' . ($this->sub ? 'action' : 'task') . '=new'); ?>">
							<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>
						</a>
					</li>
				</ul>
			</div>
		<?php } ?>
	<?php } ?>
