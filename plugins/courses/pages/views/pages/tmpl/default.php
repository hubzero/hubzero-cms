<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

defined('_JEXEC') or die('Restricted access');

$this->css()
     ->js();

$base = $this->offering->link() . '&active=pages';

$this->view('default_menu')
     ->set('option', $this->option)
     ->set('controller', $this->controller)
     ->set('course', $this->course)
     ->set('offering', $this->offering)
     ->set('page', $this->page)
     ->set('pages', $this->pages)
     ->display();
?>
<div class="pages-wrap">
	<div class="pages-content">
<?php
if (!$this->page)
{
	?>
	<div id="pages-introduction">
		<div class="instructions">
			<p><?php echo JText::_('PLG_COURSES_PAGES_NONE_FOUND'); ?></p>
		</div>
	</div>
	<?php
}
else
{
	//$layout = 'page';
	$pathway = JFactory::getApplication()->getPathway();
	$pathway->addItem(
		stripslashes($this->page->get('title')),
		$base . '&unit=' . $this->page->get('url')
	);

	$authorized = false;
	if ($this->page->get('offering_id'))
	{
		// If they're a course level manager
		if ($this->offering->access('manage'))
		{
			$authorized = true;
		}
		// If they're a section manager and the page is a section page
		else if ($this->offering->access('manage', 'section') && $this->page->get('section_id'))
		{
			$authorized = true;
		}
	}
?>
<?php if ($authorized) { ?>
		<ul class="manager-options">
			<li>
				<a class="icon-delete delete" href="<?php echo JRoute::_($base . '&unit=' . $this->page->get('url') . '&b=delete'); ?>" title="<?php echo JText::_('PLG_COURSES_PAGES_DELETE'); ?>">
					<?php echo JText::_('PLG_COURSES_PAGES_DELETE'); ?>
				</a>
			</li>
			<li>
				<a class="icon-edit edit" href="<?php echo JRoute::_($base . '&unit=' . $this->page->get('url') . '&b=edit'); ?>" title="<?php echo JText::_('PLG_COURSES_PAGES_EDIT'); ?>">
					<?php echo JText::_('PLG_COURSES_PAGES_EDIT'); ?>
				</a>
			</li>
		</ul>
<?php } ?>
<?php echo $this->page->content('parsed'); ?>
<?php
}
?>
	</div><!-- / .pages-content -->
</div><!-- / .pages-wrap -->