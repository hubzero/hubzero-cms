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

$cls    = '';
$params = '';
if ($this->level == 0)
{
	$cls    = 'item-list pages';
	$params = 'data-url="' . JRoute::_('index.php?option=com_groups&cn='.$this->group->get('cn').'&controller=pages&task=reorder&no_html=1') . '"';
	$params .= ' data-max-depth="' . ($this->config->get('page_depth', 5) + 1) . '"';
}
?>

<ul class="<?php echo $cls; ?>" <?php echo $params; ?>>
	<?php if (count($this->pages) > 0) : ?>
		<?php foreach ($this->pages as $page) : ?>
			<?php
				// get page details
				$category = $this->categories->fetch('id', $page->get('category'));
				$version  = $page->versions()->first();

				// page class
				$cls = '';
				if ($page->get('home') == 1)
				{
					$cls .= ' root';
				}

				//get file check outs
				$checkout = GroupsHelperPages::getCheckout($page->get('id'));
			?>
			<li id="<?php echo $page->get('id'); ?>" class="<?php echo $cls; ?>">
				<?php
					$this->view('item')
						 ->set('page', $page)
						 ->set('category', $category)
						 ->set('group', $this->group)
						 ->set('version', $version)
						 ->set('checkout', $checkout)
						 ->display();

					// display page children
					if ($children = $page->get('children'))
					{
						$this->view('list')
							 ->set('level', 10)
							 ->set('pages', $children)
							 ->set('categories', $this->categories)
							 ->set('group', $this->group)
							 ->display();
					}
				?>
			</li>
		<?php endforeach; ?>
		<?php if ($this->level == 0) : ?>
			<div class="item-list-loader"></div>
		<?php endif; ?>
	<?php elseif ($this->level == 0) : ?>
		<li class="no-results">
			<p><?php echo JText::_('COM_GROUPS_PAGES_NO_PAGES'); ?></p>
		</li>
	<?php endif; ?>
</ul>