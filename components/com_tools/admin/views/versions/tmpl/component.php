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

JHTML::_('behavior.modal');

$mwdb = ToolsHelperUtils::getMWDBO();
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('a.edit-asset').on('click', function(e) {
		e.preventDefault();

		window.parent.$.fancybox.open($(this).attr('href'), {type: 'iframe', size: {x: 570, y: 550}});
	});
});
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="4" style="text-align:right;">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=addZone&version=' . $this->version . '&tmpl=component'); ?>" class="button edit-asset" rel="{type: 'iframe', size: {x: 570, y: 550}}"><?php echo Lang::txt('COM_TOOLS_ADD_ZONE'); ?></a>
				</th>
			</tr>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_ZONE_NAME'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_PUBLISH_UP'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_TOOLS_COL_PUBLISH_DOWN'); ?></th>
				<th scope="col">X</th>
			</tr>
		</thead>
		<tbody>
<?php
$i = 0;
$k = 0;
foreach ($this->rows as $row)
{
	// Grab the zone name
	$zone = new MwZones($mwdb);
	$zone->load($row->zone_id);
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<a class="edit-asset" rel="{handler: 'iframe', size: {x: 570, y: 550}}" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=editZone&id=' . $row->id . '&tmpl=component'); ?>">
						<?php echo $this->escape(stripslashes($zone->title)); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->publish_up)); ?>
				</td>
				<td>
					<span><?php echo $this->escape(stripslashes($row->publish_down)); ?></span>
				</td>
				<td>
					<a class="state trash" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=removeZone&id=' . $row->id . '&version=' . $this->version . '&tmpl=component&' . Session::getFormToken() . '=1'); ?>">
						<span><img src="/components/<?php echo $this->option; ?>/admin/assets/img/trash.png" width="15" height="15" alt="<?php echo Lang::txt('[ x ]'); ?>" /></span>
					</a>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
	<input type="hidden" name="task" id="task" value="edit" />

	<?php echo Html::input('token'); ?>
</form>