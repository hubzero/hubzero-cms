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

$tmpl = Request::getWord('tmpl');

if (!isset($this->controller))
{
	$this->controller = Request::getWord('controller', 'page');
}

if ($tmpl != 'component') { ?>
	<div id="<?php echo ($this->sub) ? 'sub-content-header-extra' : 'content-header-extra'; ?>">
		<ul id="<?php echo ($this->sub) ? 'page_options' : 'useroptions'; ?>">
			<?php if (!User::isGuest() && $this->page->access('create')) { ?>
				<li class="page-new" data-title="<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>">
					<a class="icon-add add btn" href="<?php echo Route::url($this->page->link('base') . '&' . ($this->sub ? 'action' : 'task') . '=new'); ?>">
						<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>
					</a>
				</li>
			<?php } ?>
			<li class="page-index" data-title="<?php echo Lang::txt('COM_WIKI_PAGE_INDEX'); ?>">
				<a class="icon-index index btn" href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:AllPages'); ?>" title="<?php echo Lang::txt('COM_WIKI_INDEX'); ?>">
					<span><?php echo Lang::txt('COM_WIKI_INDEX'); ?></span>
				</a>
			</li>
			<li class="page-search" data-title="<?php echo Lang::txt('COM_WIKI_SEARCH'); ?>">
				<a class="icon-search search btn" href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:Search'); ?>">
					<?php echo Lang::txt('COM_WIKI_SEARCH'); ?>
				</a>
				<div class="page-search-form">
					<form action="<?php echo Route::url($this->page->link('base') . '&pagename=Special:Search'); ?>" method="get">
						<fieldset>
							<legend><?php echo Lang::txt('COM_WIKI_SEARCH_LEGEND'); ?></legend>
							<label for="page-search-q">
								<span><?php echo Lang::txt('COM_WIKI_SEARCH'); ?></span>
								<input type="text" name="q" id="page-search-q" value="" placeholder="<?php echo Lang::txt('COM_WIKI_SEARCH_PLACEHOLDER'); ?>" />
							</label>
							<input type="submit" class="page-search-submit" value="<?php echo Lang::txt('COM_WIKI_GO'); ?>" />
						</fieldset>
					</form>
				</div>
			</li>
		</ul>
	</div><!-- / #content-header-extra -->
<?php } ?>

<ul class="sub-menu">
	<li class="page-text<?php if ($this->controller == 'pages' && ($this->task == 'display' || !$this->task)) { echo ' active'; } ?>">
		<a href="<?php echo Route::url($this->page->link()); ?>" title="<?php echo Lang::txt('COM_WIKI_TAB_ARTICLE'); ?>">
			<span><?php echo Lang::txt('COM_WIKI_TAB_ARTICLE'); ?></span>
		</a>
	</li>
	<?php if ($tmpl != 'component') { ?>
		<?php if ($this->page->exists() && !$this->page->isDeleted() && $this->page->get('namespace') != 'Special') { ?>
			<?php if (($this->page->isLocked() && $this->page->access('manage')) || (!$this->page->isLocked() && $this->page->access('edit'))) { ?>
				<li class="page-edit<?php if ($this->controller == 'pages' && $this->task == 'edit') { echo ' active'; } ?>">
					<a href="<?php echo Route::url($this->page->link('edit')); ?>" title="<?php echo Lang::txt('COM_WIKI_TAB_EDIT'); ?>">
						<span><?php echo Lang::txt('COM_WIKI_TAB_EDIT'); ?></span>
					</a>
				</li>
			<?php } ?>
			<?php if ($this->page->access('view', 'comment')) { ?>
				<li class="page-comments<?php if ($this->controller == 'comments') { echo ' active'; } ?>">
					<a href="<?php echo Route::url($this->page->link('comments')); ?>" title="<?php echo Lang::txt('COM_WIKI_TAB_COMMENTS'); ?>">
						<span><?php echo Lang::txt('COM_WIKI_TAB_COMMENTS'); ?></span>
					</a>
				</li>
			<?php } ?>
				<li class="page-history<?php if ($this->controller == 'history') { echo ' active'; } ?>">
					<a href="<?php echo Route::url($this->page->link('history')); ?>" title="<?php echo Lang::txt('COM_WIKI_TAB_HISTORY'); ?>">
						<span><?php echo Lang::txt('COM_WIKI_TAB_HISTORY'); ?></span>
					</a>
				</li>
				<li class="page-pdf">
					<a href="<?php echo Route::url($this->page->link('pdf')); ?>" title="<?php echo Lang::txt('COM_WIKI_TAB_PDF'); ?>">
						<span><?php echo Lang::txt('COM_WIKI_TAB_PDF'); ?></span>
					</a>
				</li>
			<?php
				if (($this->page->isLocked() && $this->page->access('manage', 'page'))
					|| (!$this->page->isLocked() && $this->page->access('delete', 'page'))) { ?>
				<li class="page-delete<?php if ($this->controller == 'pages' && $this->task == 'delete') { echo ' active'; } ?>">
					<a href="<?php echo Route::url($this->page->link('delete')); ?>" title="<?php echo Lang::txt('COM_WIKI_DELETE_PAGE'); ?>">
						<span><?php echo Lang::txt('COM_WIKI_DELETE_PAGE'); ?></span>
					</a>
				</li>
			<?php } ?>
		<?php } ?>
	<?php } ?>
</ul>
