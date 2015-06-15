<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

<table class="listing" id="projectlist">
	<thead>
		<tr>
			<th class="th_image" colspan="2"></th>
			<th><?php echo Lang::txt('COM_PROJECTS_TITLE'); ?></th>
			<th><?php echo Lang::txt('COM_PROJECTS_OWNER'); ?></th>
	<?php if (in_array($this->filters['reviewer'], array('sponsored', 'sensitive'))) { ?>
		<?php if ($this->filters['reviewer'] == 'sensitive') {  ?>
			<th><?php echo Lang::txt('COM_PROJECTS_TYPE_OF_DATA'); ?></th>
			<th><?php echo Lang::txt('COM_PROJECTS_SPS_APPROVAL_STATUS'); ?></th>
		<?php } ?>
		<?php if ($this->filters['reviewer'] == 'sponsored') {  ?>
			<th><?php echo Lang::txt('COM_PROJECTS_SPS_INFO'); ?></th>
			<th><?php echo Lang::txt('COM_PROJECTS_SPS_APPROVAL_STATUS'); ?></th>
		<?php } ?>
			<th></th>
	<?php } ?>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($this->rows as $row)
{
	if ($row->get('owned_by_group') && !$row->groupOwner())
	{
		continue; // owner group has been deleted
	}

	// Display List of items
	$this->view('_item')
	     ->set('option', $this->option)
	     ->set('filters', $this->filters)
	     ->set('model', $this->model)
	     ->set('row', $row)
	     ->display();
}
?>
	</tbody>
</table>