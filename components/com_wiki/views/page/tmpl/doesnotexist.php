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
defined('_JEXEC') or die( 'Restricted access' );

if (!$this->sub)
{
	$this->css();
}
$this->js();
?>
<header id="<?php echo ($this->sub) ? 'sub-content-header' : 'content-header'; ?>">
	<h2><?php echo $this->escape($this->title); ?></h2>
</header><!-- /#content-header -->

<?php
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
	<p class="warning">
		<?php echo JText::sprintf('COM_WIKI_WARNING_PAGE_DOES_NOT_EXIST', JRoute::_($this->page->link('new'))); ?>
	</p>
<?php if (count($this->book->templates('list', array(), 'true'))) { ?>
	<p>
		<?php echo JText::_('COM_WIKI_CHOOSE_TEMPLATE'); ?>
	</p>
	<ul>
	<?php foreach ($this->book->templates() as $template) { ?>
		<li>
			<a href="<?php echo JRoute::_($this->page->link('new') . '&tplate=' . stripslashes($template->get('pagename'))); ?>">
				<?php echo $this->escape(stripslashes($template->get('title'))); ?>
			</a>
		</li>
	<?php } ?>
	</ul>
<?php } ?>
</section><!-- / .main section -->
