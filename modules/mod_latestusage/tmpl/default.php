<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>
<table <?php echo ($this->cls) ? 'class="' . $this->cls . '" ' : ''; ?>>
	<caption><?php echo Lang::txt('MOD_LATESTUSAGE_CAPTION'); ?></caption>
	<tfoot>
		<tr>
			<td><a href="<?php echo Route::url('index.php?option=com_usage&task=maps&type=online'); ?>"><?php echo Lang::txt('MOD_LATESTUSAGE_WHOSONLONE'); ?></a></td>
			<td class="more"><a href="<?php echo Route::url('index.php?option=com_usage'); ?>"><?php echo Lang::txt('MOD_LATESTUSAGE_MORE'); ?></a></td>
		</tr>
	</tfoot>
	<tbody>
		<tr>
			<th scope="row"><?php echo Lang::txt('MOD_LATESTUSAGE_USERS'); ?></th>
			<td class="numerical-data"><?php echo $this->users; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo Lang::txt('MOD_LATESTUSAGE_RESOURCES'); ?></th>
			<td class="numerical-data"><?php echo $this->resources; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo Lang::txt('MOD_LATESTUSAGE_TOOLS'); ?></th>
			<td class="numerical-data"><?php echo $this->tools; ?></td>
		</tr>
		<tr>
			<th scope="row"><?php echo Lang::txt('MOD_LATESTUSAGE_SIMULATIONS'); ?></th>
			<td class="numerical-data"><?php echo $this->sims; ?></td>
		</tr>
	</tbody>
</table>
