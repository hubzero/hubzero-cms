<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Shaun Einolf <einolfs@mail.nih.gov>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

?>
<div<?php echo ($this->params->get('moduleclass')) ? ' class="' . $this->params->get('moduleclass') . '"' : ''; ?>>
	<h4>
		<?php echo JText::_('MOD_MYTODOS_ASSIGNED'); ?>
	</h4>
	<?php if (count($this->rows) <= 0) { ?>
		<p><em><?php echo JText::_('MOD_MYTODOS_NO_TODOS'); ?></em></p>
	<?php } else { ?>
		<ul class="expandedlist">
			<?php
			foreach ($this->rows as $row)
			{
				//$when = JHTML::_('date.relative', $row->proposed);
				?>
				<li class="todos">
					<a href="<?php echo JRoute::_('index.php?option=com_projects&alias=' . $row->alias . '&active=todo/view/?todoid=' . $row->id); ?>"><?php echo $this->escape($row->content); ?></a><br />
					<?php echo JText::_('MOD_MYTODOS_PROJECT'); ?>: <a href="<?php echo JRoute::_('index.php?option=com_projects&alias=' . $row->alias . '&active=todo'); ?>"><?php echo $this->escape($row->title); ?></a>
					<span></span>
				</li>
				<?php
			}
			?>
		</ul>
	<?php } ?>
</div>
