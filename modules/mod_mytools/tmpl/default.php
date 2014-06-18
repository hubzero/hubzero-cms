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

// Push the module CSS to the template
$this->css();
$this->js();
?>
<?php if ($this->fav || $this->no_html) { ?>
	<?php echo $this->buildList($this->favtools, 'fav'); ?>
	<p><?php echo JText::_('MOD_MYTOOLS_EXPLANATION'); ?></p>
<?php } else { ?>
<div id="myToolsTabs">
	<ul class="tab_titles">
		<li title="recenttools" class="active"><?php echo JText::_('MOD_MYTOOLS_RECENT'); ?></li>
		<li title="favtools"><?php echo JText::_('MOD_MYTOOLS_FAVORITES'); ?></li>
		<li title="alltools"><?php echo JText::_('MOD_MYTOOLS_ALL_TOOLS'); ?></li>
	</ul>

	<div id="recenttools" class="tab_panel active">
		<?php
		$r = $this->rectools;
		echo $this->buildList($r, 'recent'); ?>
		<p><?php echo JText::_('MOD_MYTOOLS_RECENT_EXPLANATION'); ?></p>
	</div>

	<div id="favtools" class="tab_panel">
		<?php
		$f = $this->favtools;
		echo $this->buildList($f, 'favs'); ?>
		<p><?php echo JText::_('MOD_MYTOOLS_FAVORITES_EXPLANATION'); ?></p>
	</div>

	<div id="alltools" class="tab_panel">
		<div id="filter-mytools">
			<input type="text" placeholder="Search Tools" />
		</div>
		<?php
		$a = $this->alltools;
		echo $this->buildList($a, 'all'); ?>
		<p><?php echo JText::_('MOD_MYTOOLS_ALL_TOOLS_EXPLANATION'); ?></p>
	</div>
</div>
<?php } ?>