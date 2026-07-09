<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

use Components\Wiki\Helpers\Parser;

// Build the sidebar table of contents for the current page (display view only).
// Only one TOC renders per page: an explicit [[TableOfContents]] macro decides
// (a "sidebar" macro puts it here; here/inline/off keep it out of the sidebar);
// without a macro we render it in the sidebar by default.
$showtoc = ($this->controller == 'pages' && $this->task == 'display'
	&& $this->page->getNamespace() != 'special');
$toc = '';

if ($showtoc)
{
	$directive = Parser::tocDirective($this->page->version->get('pagetext'));

	$render = false;
	$depth  = 0;
	if ($directive === null)
	{
		// No macro: honor the automatic-TOC setting. Only 'sidebar' renders here,
		// and only once the page meets the configured heading threshold.
		$settings = Parser::tocSettings($this->page->get('scope'), $this->page->get('scope_id'));
		if ($settings['mode'] == 'sidebar')
		{
			$headings = preg_match_all('/<h[1-6][\s>]/i', (string) $this->page->version->get('pagehtml'));
			$render = ($headings >= $settings['threshold']);
		}
	}
	elseif ($directive['mode'] == 'sidebar')
	{
		// Macro explicitly asks for the sidebar (forced, no threshold)
		$render = true;
		$depth  = $directive['depth'];
	}
	// automatic inline/off, or macro here/inline/off => the sidebar stays empty

	if ($render)
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
		), $depth);
	}
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

		<?php if (trim($toc) != '') { ?>
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
