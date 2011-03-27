<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>

<form action="index.php" method="post" name="adminForm">
	<table>
		<tr>
			<td align="left" width="100%">
				<?php echo JText::_( 'Filter' ); ?>:
				<input type="text" name="search" id="search" value="<?php echo $this->escape($this->search); ?>" class="text_area" onchange="document.adminForm.submit();" />
				<button onclick="this.form.submit();"><?php echo JText::_( 'Go' ); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();"><?php echo JText::_( 'Reset' ); ?></button>
			</td>
			<td nowrap="nowrap">
				<span class="componentheading"><?php echo JText::_( 'Search Logging' ); ?> :
				<?php echo $this->enabled ? '<b><font color="green">'. JText::_( 'Enabled' ) .'</font></b>' : '<b><font color="red">'. JText::_( 'Disabled' ) .'</font></b>' ?>
				</span>
			</td>
			<td nowrap="nowrap" align="right">
			<?php if ( $this->showResults ) : ?>
				<a href="index.php?option=com_search&amp;search_results=0"><?php echo JText::_( 'Hide Search Results' ); ?></a>
			<?php else : ?>
				<a href="index.php?option=com_search&amp;search_results=1"><?php echo JText::_( 'Show Search Results' ); ?></a>
			<?php endif; ?>
			</td>
		</tr>
	</table>

	<div id="tablecell">
		<table class="adminlist">
			<thead>
				<tr>
					<th width="10">
						<?php echo JText::_( 'NUM' ); ?>
					</th>
					<th class="title">
						<?php echo JHTML::_('grid.sort',   'Search Text', 'search_term', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					</th>
					<th nowrap="nowrap" width="20%">
						<?php echo JHTML::_('grid.sort',   'Times Requested', 'hits', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
					</th>
					<?php
					if ( $this->showResults ) : ?>
						<th nowrap="nowrap" width="20%">
							<?php echo JText::_( 'Results Returned' ); ?>
						</th>
					<?php endif; ?>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4">
						<?php echo $this->pageNav->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for ($i=0, $n = count($this->items); $i < $n; $i++) {
				$row =& $this->items[$i];
				?>
				<tr class="row<?php echo $k;?>">
					<td align="right">
						<?php echo $i+1+$this->pageNav->limitstart; ?>
					</td>
					<td>
						<?php echo htmlspecialchars($row->search_term, ENT_QUOTES, 'UTF-8'); ?>
					</td>
					<td align="center">
						<?php echo $row->hits; ?>
					</td>
					<?php if ( $this->showResults ) : ?>
					<td align="center">
						<?php echo $row->returns; ?>
					</td>
					<?php endif; ?>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
	</div>

	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>
