<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

use Components\Wiki\Helpers\Parser;

// Build a table of contents for the current page (display view only)
$showtoc = ($this->controller == 'pages' && $this->task == 'display');
$toc = '';

if ($showtoc)
{
	// Make sure the base URL is correct for the TOC so anchor links don't reload the page
	$url = $this->page->link();
	if ($this->page->get('pagename') == 'MainPage')
	{
		$path = explode('/', rtrim($_SERVER['REQUEST_URI'], '/'));
		if (end($path) != 'MainPage')
		{
			$url = $this->page->link('base');
		}
	}

	$parser = Parser::getInstance();
	$toc = $parser->toc($this->page->version->get('pagehtml'), array(
		'option'    => ($this->option ?: \Request::getCmd('option')),
		'scope'     => $this->page->get('path'),
		'domain'    => $this->page->get('scope'),
		'domain_id' => $this->page->get('scope_id'),
		'url'       => $url,
	));
}

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

		<?php if ($showtoc && trim($toc) != '' && $this->page->getNamespace() != 'special') { ?>
		<div class="container">
			<?php echo $toc; ?>
		</div>
		<?php } ?>

		<div class="container">
			<h3><?php echo Lang::txt('COM_WIKI'); ?></h3>
			<ul id="useroptions">
				<li class="page-main">
					<a href="<?php echo Route::url($this->page->link('base')); ?>" aria-label="<?php echo $this->escape(Lang::txt('COM_WIKI_MAIN_PAGE')) . ' — ' . $this->escape(Lang::txt('COM_WIKI')); ?>">
						<?php echo Lang::txt('COM_WIKI_MAIN_PAGE'); ?>
					</a>
				</li>
				<li class="page-help">
					<a href="<?php echo Route::url($this->page->link('base') . '&pagename=Help:Index'); ?>" aria-label="<?php echo $this->escape(Lang::txt('COM_WIKI_HELP')) . ' — ' . $this->escape(Lang::txt('COM_WIKI')); ?>">
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
						<a href="<?php echo Route::url($this->page->link('base') . '&' . ($this->page->get('scope') != 'site' ? 'action' : 'task') . '=new'); ?>">
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
						<a href="<?php echo Route::url($this->page->link('base') . '&' . ($this->page->get('scope') != 'site' ? 'action' : 'task') . '=new'); ?>">
							<?php echo Lang::txt('COM_WIKI_NEW_PAGE'); ?>
						</a>
					</li>
				</ul>
			</div>
		<?php } ?>
	<?php }
