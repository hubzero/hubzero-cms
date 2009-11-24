<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<table <?php echo ($modlatestusage->cls) ? 'class="'.$modlatestusage->cls.'" ' : ''; ?> summary="<?php echo JText::_('MOD_LATESTUSAGE'); ?>">
	<caption><?php echo JText::_('MOD_LATESTUSAGE_CAPTION'); ?></caption>
	<tfoot>
		<tr>
			<td><a href="<?php echo JRoute::_('index.php?option=com_usage&task=maps&type=online'); ?>"><?php echo JText::_('MOD_LATESTUSAGE_WHOSONLONE'); ?></a></td>
			<td class="more"><a href="<?php echo JRoute::_('index.php?option=com_usage'); ?>"><?php echo JText::_('MOD_LATESTUSAGE_MORE'); ?></a></td>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<th scope="row"><?php echo JText::_('MOD_LATESTUSAGE_USERS'); ?></th>
			<td class="numerical-data"><?php echo $modlatestusage->users; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo JText::_('MOD_LATESTUSAGE_RESOURCES'); ?></th>
			<td class="numerical-data"><?php echo $modlatestusage->resources; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo JText::_('MOD_LATESTUSAGE_TOOLS'); ?></th>
			<td class="numerical-data"><?php echo $modlatestusage->tools; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo JText::_('MOD_LATESTUSAGE_SIMULATIONS'); ?></th>
			<td class="numerical-data"><?php echo $modlatestusage->sims; ?></td>
		</tr>
	</tbody>
</table>