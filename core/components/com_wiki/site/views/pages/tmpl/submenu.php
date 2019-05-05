<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$tmpl = Request::getWord('tmpl');

if (!isset($this->controller))
{
	$this->controller = Request::getWord('controller', 'page');
}

if ($tmpl != 'component' && $this->sub) { ?>
	<div id="sub-content-header-extra">
		<ul id="page_options">
			<?php if (!User::isGuest() && $this->page->access('create')) { ?>
				<li class="page-new">
					<a class="icon-add add btn tooltips" title="<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>" href="<?php echo Route::url($this->page->link('base') . '&action=new'); ?>">
						<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>
					</a>
				</li>
			<?php } ?>
			<li class="page-index">
				<a class="icon-list-ul index btn tooltips" title="<?php echo Lang::txt('COM_WIKI_PAGE_INDEX'); ?>" href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:AllPages'); ?>" title="<?php echo Lang::txt('COM_WIKI_INDEX'); ?>">
					<span><?php echo Lang::txt('COM_WIKI_INDEX'); ?></span>
				</a>
			</li>
			<li class="page-search">
				<a class="icon-search search btn tooltips" title="<?php echo Lang::txt('COM_WIKI_SEARCH'); ?>" href="<?php echo Route::url($this->page->link('base') . '&pagename=Special:Search'); ?>">
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
			<span class="icon-align-left"><?php echo Lang::txt('COM_WIKI_TAB_ARTICLE'); ?></span>
		</a>
	</li>
	<?php if ($tmpl != 'component') { ?>
		<?php if ($this->page->exists() && !$this->page->isDeleted() && $this->page->getNamespace() != 'special') { ?>
			<?php if (($this->page->isLocked() && $this->page->access('manage')) || (!$this->page->isLocked() && $this->page->access('edit'))) { ?>
				<li class="page-edit<?php if ($this->controller == 'pages' && in_array($this->task, array('edit', 'preview', 'save'))) { echo ' active'; } ?>">
					<a href="<?php echo Route::url($this->page->link('edit')); ?>" title="<?php echo Lang::txt('COM_WIKI_TAB_EDIT'); ?>">
						<span class="icon-pencil"><?php echo Lang::txt('COM_WIKI_TAB_EDIT'); ?></span>
					</a>
				</li>
			<?php } ?>
			<?php if ($this->page->config('comments', 1) && $this->page->access('view', 'comment')) { ?>
				<li class="page-comments<?php if ($this->controller == 'comments') { echo ' active'; } ?>">
					<a href="<?php echo Route::url($this->page->link('comments')); ?>" title="<?php echo Lang::txt('COM_WIKI_TAB_COMMENTS'); ?>">
						<span class="icon-comments"><?php echo Lang::txt('COM_WIKI_TAB_COMMENTS'); ?></span>
					</a>
				</li>
			<?php } ?>
				<li class="page-history<?php if ($this->controller == 'history') { echo ' active'; } ?>">
					<a href="<?php echo Route::url($this->page->link('history')); ?>" title="<?php echo Lang::txt('COM_WIKI_TAB_HISTORY'); ?>">
						<span class="icon-clock"><?php echo Lang::txt('COM_WIKI_TAB_HISTORY'); ?></span>
					</a>
				</li>
			<?php if ($this->page->get('scope') != 'site') { ?>
				<li class="page-pdf">
					<a href="<?php echo Route::url($this->page->link('pdf')); ?>" title="<?php echo Lang::txt('COM_WIKI_TAB_PDF'); ?>">
						<span class="icon-download-alt"><?php echo Lang::txt('COM_WIKI_TAB_PDF'); ?></span>
					</a>
				</li>
			<?php } ?>
			<?php
				if (($this->page->isLocked() && $this->page->access('manage', 'page'))
					|| (!$this->page->isLocked() && $this->page->access('delete', 'page'))) { ?>
				<li class="page-delete<?php if ($this->controller == 'pages' && $this->task == 'delete') { echo ' active'; } ?>">
					<a href="<?php echo Route::url($this->page->link('delete')); ?>" title="<?php echo Lang::txt('COM_WIKI_DELETE_PAGE'); ?>">
						<span class="icon-remove-sign"><?php echo Lang::txt('COM_WIKI_DELETE_PAGE'); ?></span>
					</a>
				</li>
			<?php } ?>
		<?php } ?>
	<?php } ?>
</ul>
