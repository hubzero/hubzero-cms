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
JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Databases'), 'addedit.png' );
JToolBarHelper::save('savedb');
JToolBarHelper::cancel();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm">
	<div class="col width-50">
		<fieldset class="adminform">
			<legend>IP Database</legend>
			<table class="admintable">
				<tbody>
					<?php
					foreach ($this->arr as $field => $value) 
					{
						if (substr($field, 0, strlen('ipDB')) == 'ipDB') {
							?>
					<tr>
						<td class="key"><?php echo str_replace('ipDB', '', $field); ?></td>
						<td>
							<input class="text_area" type="text" name="settings[<?php echo $field; ?>]" size="30" value="<?php echo $value; ?>" />
						</td>
					</tr>
					<?php
						}	
					}
					?>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="option" value="com_hub" />
	<input type="hidden" name="task" value="savedb" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
