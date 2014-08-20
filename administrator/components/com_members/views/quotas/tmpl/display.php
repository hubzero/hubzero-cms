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

// Menu
JToolBarHelper::title(JText::_('COM_MEMBERS_QUOTAS'), 'user.png');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::custom('restoreDefault', 'restore', 'restore', 'Default');

$this->css('quotas.css');
?>

<script type="text/javascript">
	jQuery(document).ready(function ( $ ) {
		setTimeout(doWork, 10);

		function doWork() {
			var rows = $('.quota-row');

			rows.each(function ( i, el ) {
				var id = $(el).find('.row-id').val();
				var usage = $(el).find('.usage-outer');

				$.ajax({
					url      : 'index.php?option=com_members&controller=quotas&task=getQuotaUsage',
					dataType : 'JSON',
					type     : 'GET',
					data     : {"id":id},
					success  : function ( data, textStatus, jqXHR ) {
						if (data.percent > 100) {
							data.percent = 100;
							usage.find('.usage-inner').addClass('max');
						}
						usage.prev('.usage-calculating').hide();
						usage.fadeIn();
						usage.find('.usage-inner').css('width', data.percent+"%");
					},
					error : function ( ) {
						usage.prev('.usage-calculating').hide();
						usage.next('.usage-unavailable').show();
					}
				});
			});
		};
	});
</script>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_USER_ID'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_USERNAME'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_NAME'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_CLASS'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_DISK_USAGE'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];
?>
			<tr class="<?php echo "row$k quota-row"; ?>">
				<td>
					<input class="row-id" type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
						<?php echo $this->escape($row->user_id); ?>
					</a>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
						<?php echo $this->escape($row->username); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape($row->name); ?>
				</td>
				<td>
					<?php echo ($row->class_alias) ? $this->escape($row->class_alias) : 'custom'; ?>
				</td>
				<td>
					<div class="usage-calculating">[calculating...]</div>
					<div class="usage-outer">
						<div class="usage-inner"></div>
					</div>
					<div class="usage-unavailable">unavailable</div>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>