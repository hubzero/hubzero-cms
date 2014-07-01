<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (!$this->sub)
{
	// Include any CSS
	if ($this->page->get('pagename') == 'MainPage')
	{
		$this->css('introduction.css', 'system'); // component, stylesheet name, look in media system dir
	}
	$this->css();
}
// Include any Scripts
$this->js();
?>
	<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
		<h2><?php echo $this->title; ?></h2>
		<?php
		if (!$this->page->isStatic())
		{
			$this->view('authors', 'page')
			     ->setBasePath($this->base_path)
			     ->set('page', $this->page)
			     ->display();
		}
		?>

	<?php echo $this->page->event->afterDisplayTitle; ?>

	<?php if ($this->page->isStatic() && $this->page->access('admin') && $this->controller == 'page' && $this->task == 'display') { ?>
		<div id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>-extra">
			<ul>
				<li><a class="icon-edit edit btn" href="<?php echo JRoute::_($this->page->link('edit')); ?>"><?php echo JText::_('JACTION_EDIT'); ?></a></li>
				<li><a class="icon-history history btn" href="<?php echo JRoute::_($this->page->link('history')); ?>"><?php echo JText::_('COM_WIKI_HISTORY'); ?></a></li>
			</ul>
		</div><!-- /#content-header-extra -->
	<?php } ?>
	</header><!-- /#content-header -->

<?php if ($this->getError()) { ?>
	<p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

<?php if ($this->message) { ?>
	<p class="passed"><?php echo $this->message; ?></p>
<?php } ?>

<?php
echo $this->page->event->beforeDisplayContent;

if (!$this->page->isStatic()) {
	$this->view('submenu', 'page')
	     ->setBasePath($this->base_path)
	     ->set('option', $this->option)
	     ->set('controller', $this->controller)
	     ->set('page', $this->page)
	     ->set('task', $this->task)
	     ->set('sub', $this->sub)
	     ->display();
?>
	<section class="main section">
		<article class="wikipage">
			<?php echo $this->revision->get('pagehtml'); ?>

			<p class="timestamp">
				<?php echo JText::_('COM_WIKI_PAGE_CREATED') . ' <time datetime="' . $this->page->created() . '">'.$this->page->created('date') . '</time>, ' . JText::_('COM_WIKI_PAGE_LAST_MODIFIED') . ' <time datetime="' . $this->revision->created() . '">' . $this->revision->created('date') . '</time>'; ?>
				<?php /*if ($stats = $this->page->getMetrics()) { ?>
				<span class="article-usage">
					<?php echo JText::sprintf('COM_WIKI_PAGE_METRICS', $stats['visitors'], $stats['visits']); ?>
				</span>
				<?php }*/ ?>
			</p>
		<?php if ($this->page->tags('cloud')) { ?>
			<div class="article-tags">
				<h3><?php echo JText::_('COM_WIKI_PAGE_TAGS'); ?></h3>
				<?php echo $this->page->tags('cloud'); ?>
			</div>
		<?php } ?>
		</article>
	</section><!-- / .main section -->
<?php
} else {
	echo $this->revision->get('pagehtml');
}

echo $this->page->event->afterDisplayContent;
