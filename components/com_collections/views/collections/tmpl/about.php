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

//import helper class
ximport('Hubzero_View_Helper_Html');
ximport('Hubzero_Document');

$database = JFactory::getDBO();
$this->juser = JFactory::getUser();

$base = 'index.php?option=' . $this->option;
?>
<div id="content-header">
	<h2><?php echo JText::_('Collections'); ?></h2>
</div>

<div id="content-header-extra">
	<ul>
		<li>
			<a class="about btn" href="<?php echo JRoute::_($base . '&task=about'); ?>">
				<span><?php echo JText::_('About'); ?></span>
			</a>
		</li>
	</ul>
</div>

<form method="get" action="<?php echo JRoute::_($base . '&controller=' . $this->controller . '&task=' . $this->task); ?>" id="collections">
	<fieldset class="filters">
		<div class="filters-inner">
		<ul>
			<li>
				<a class="collections count" href="<?php echo JRoute::_($base . '&task=all'); ?>">
					<span><?php echo JText::sprintf('<strong>%s</strong> collections', $this->collections); ?></span>
				</a>
			</li>
			<li>
				<a class="posts count" href="<?php echo JRoute::_($base . '&task=posts'); ?>">
					<span><?php echo JText::sprintf('<strong>%s</strong> posts', $this->total); ?></span>
				</a>
			</li>
		</ul>
		<div class="clear"></div>
		<p>
			<label for="filter-search">
				<span><?php echo JText::_('Search'); ?></span>
				<input type="text" name="search" id="filter-search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('Search for posts'); ?>" />
			</label>
			<input type="submit" class="filter-submit" value="<?php echo JText::_('Go'); ?>" />
		</p>
		</div>
	</fieldset>

	<div class="main section">
		<div class="four columns first">
			<h3>What are collections?</h3>
		</div>
		<div class="four columns second third fourth">
			<p>
				Curabitur porta lacus nunc. Duis a consequat ante. Donec turpis dolor, consectetur vel vehicula sed, facilisis ac nulla. Nullam tincidunt elit eu massa egestas sit amet feugiat nibh volutpat. Proin malesuada interdum egestas. Pellentesque ac pretium nisl. Etiam at velit nulla.
			</p>
		</div>
		<div class="clear"></div>

		<div class="four columns first">
			<h3>What are posts?</h3>
		</div>
		<div class="four columns second third fourth">
			<p>
				Curabitur porta lacus nunc. Duis a consequat ante. Donec turpis dolor, consectetur vel vehicula sed, facilisis ac nulla. Nullam tincidunt elit eu massa egestas sit amet feugiat nibh volutpat. Proin malesuada interdum egestas. Pellentesque ac pretium nisl. Etiam at velit nulla.
			</p>
		</div>
		<div class="clear"></div>

		<div class="four columns first">
			<h3>What is following?</h3>
		</div>
		<div class="four columns second third fourth">
			<p>
				Curabitur porta lacus nunc. Duis a consequat ante. Donec turpis dolor, consectetur vel vehicula sed, facilisis ac nulla. Nullam tincidunt elit eu massa egestas sit amet feugiat nibh volutpat. Proin malesuada interdum egestas. Pellentesque ac pretium nisl. Etiam at velit nulla.
			</p>
		</div>
		<div class="clear"></div>
	</div>
</form>