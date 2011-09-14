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
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title( JText::_( 'MyHUB' ).': <small><small>[ '. JText::_('Push Module to Users').' ]</small></small>', 'user.png' );
JToolBarHelper::save('push');
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
	// do field validation
	submitform( pressbutton );
}
</script>
<form action="index.php" method="post" name="adminForm">
	<p><strong>Warning!</strong> This can be a resource intensive process and should not be performed frequently.</p>
	<fieldset class="adminform">
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key"><label for="module">Module:</label></td>
					<td>
						<select name="module" id="module">
							<option value="">Select...</option>
							<?php
							foreach ($this->modules as $module)
							{
								echo '<option value="'.$module->id.'">'.stripslashes($module->title).'</option>'."\n";
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key"><label for="column">Column:</label></td>
					<td>
						<select name="column" id="column">
							<option value="0">One</option>
							<option value="1">Two</option>
							<option value="2">Three</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="key"><label for="position">Position:</label></td>
					<td>
						<select name="position" id="position">
							<option value="first">First</option>
							<option value="last">Last</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="push" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

