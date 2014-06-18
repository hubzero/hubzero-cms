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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if ($this->getError()) { ?>
<p><?php echo $this->getError(); ?></p>
<?php } else { ?>
<table class="adminform">
	<thead>
		<tr>
			<th colspan="3">User ratings and comments</th>
		</tr>
	</thead>
	<tbody>
<?php
foreach ($this->rows as $row)
{
	if (intval($row->created) <> 0)
	{
		$thedate = JHTML::_('date', $row->created);
	}
	$juser = JUser::getInstance($row->user_id);
?>
	<tr>
		<th>User:</th>
		<td><?php echo $this->escape($juser->get('name')); ?></td>
	</tr>
	<tr>
		<th>Rating:</th>
		<td><?php echo ResourcesHtml::writeRating($row->rating); ?></td>
	</tr>
	<tr>
		<th>Rated:</th>
 		<td><?php echo $thedate; ?></td>
	</tr>
	<tr>
 		<th style="border-bottom: 2px solid #999;vertical-align:top;">Comment:</th>
		<td style="border-bottom: 2px solid #999;" class="aLeft"><?php
			if ($row->comment) {
				echo $this->escape(stripslashes($row->comment));
			} else {
				echo '[ no comment ]';
			}
   			?></td>
		</tr>
<?php } ?>
	</tbody>
</table>
<?php } ?>