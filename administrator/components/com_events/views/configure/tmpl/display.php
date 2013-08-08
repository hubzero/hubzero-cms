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
JToolBarHelper::title( JText::_( 'COM_EVENTS_MANAGER' ).': <small><small>[ '.JText::_( 'COM_EVENTS_CONFIGURATION' ).' ]</small></small>', 'event.png' );
JToolBarHelper::save('saveconfig');
JToolBarHelper::cancel('cancelconfig');
?>

<form action="index.php" method="post" name="adminForm">
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG'); ?></span></legend>
		
		<table class="admintable">
			<tbody>
				<tr>
					<td class="key" style="width:265px;"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_ADMINMAIL'); ?></td>
					<td><input type="text" name="config[adminmail]" size="30" maxlength="50" value="<?php echo $this->config->adminmail; ?>" /></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_ADMINLEVEL'); ?></td>
					<td><?php
					$level[] = JHTML::_('select.option', '0', JText::_('All registered users'), 'value', 'text' );
					$level[] = JHTML::_('select.option', '1', JText::_('Only special rights and admins'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $level, 'config[adminlevel]', '', 'value', 'text', $this->config->adminlevel, false, false );
					?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_FIRSTDAY'); ?></td>
					<td><?php
					$first[] = JHTML::_('select.option', '0', JText::_('Sunday first'), 'value', 'text' );
					$first[] = JHTML::_('select.option', '1', JText::_('Monday first'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $first, 'config[starday]', '', 'value', 'text', $this->config->starday, false, false );
					?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_VIEWMAIL'); ?></td>
					<td><?php
					$viewm[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
					$viewm[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $viewm, 'config[mailview]', '', 'value', 'text', $this->config->mailview, false, false );
					?></td>
				</tr>      
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_VIEWBY'); ?></td>
					<td><?php
					$viewb[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
					$viewb[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $viewb, 'config[byview]', '', 'value', 'text', $this->config->byview, false, false );
					?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_VIEWHITS'); ?></td>
					<td><?php
					$viewh[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
					$viewh[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $viewh, 'config[hitsview]', '', 'value', 'text', $this->config->hitsview, false, false );
					?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_VIEWREPEAT'); ?></td>
					<td><?php
					$viewr[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
					$viewr[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $viewr, 'config[repeatview]', '', 'value', 'text', $this->config->repeatview, false, false );
					?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_DATEFORMAT'); ?></td>
					<td><?php
					$datef[] = JHTML::_('select.option', '0', JText::_('French-English'), 'value', 'text' );
					$datef[] = JHTML::_('select.option', '1', JText::_('US'), 'value', 'text' );
        			$datef[] = JHTML::_('select.option', '2', JText::_('Deutsch'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $datef, 'config[dateformat]', '', 'value', 'text', $this->config->dateformat, false, false );
					?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_TIMEFORMAT'); ?></td>
					<td><?php
					$stdTime[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
					$stdTime[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $stdTime, 'config[calUseStdTime]', '', 'value', 'text', $this->config->calUseStdTime, false, false );
					?></td>
				</tr>
				<!-- <tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_NAVCOLOR'); ?></td>
					<td><?php
					$navcol[] = JHTML::_('select.option', 'green', JText::_('Green'), 'value', 'text' );
					$navcol[] = JHTML::_('select.option','orange', JText::_('Orange'), 'value', 'text' );
					$navcol[] = JHTML::_('select.option', 'blue', JText::_('Blue'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $navcol, 'config[navbarcolor]', '', 'value', 'text', $this->config->navbarcolor, false, false );
					?></td>
				</tr> -->
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_STARTPAGE'); ?></td>
					<td><?php
					$startpg[] = JHTML::_('select.option', 'day', JText::_('COM_EVENTS_CAL_LANG_REP_DAY'), 'value', 'text' );
					$startpg[] = JHTML::_('select.option', 'week', JText::_('COM_EVENTS_CAL_LANG_REP_WEEK'), 'value', 'text' );
					$startpg[] = JHTML::_('select.option', 'month', JText::_('COM_EVENTS_CAL_LANG_REP_MONTH'), 'value', 'text' );
					$startpg[] = JHTML::_('select.option', 'year', JText::_('COM_EVENTS_CAL_LANG_REP_YEAR'), 'value', 'text' );
					$startpg[] = JHTML::_('select.option', 'categories', JText::_('COM_EVENTS_CAL_LANG_EVENT_CATEGORIES'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $startpg, 'config[startview]', '', 'value', 'text', $this->config->startview, false, false );
					?></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_NUMEVENTS'); ?></td>
					<td><input type="text" size="3" name="config[calEventListRowsPpg]" value="<?php echo $this->config->calEventListRowsPpg; ?>" /></td>
				</tr>
				<tr>
					<td class="key"><?php echo JText::_('COM_EVENTS_CAL_LANG_CONFIG_SIMPLEFORM'); ?></td>
					<td><?php
					$formOpt[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
					$formOpt[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $formOpt, 'config[calSimpleEventForm]', '', 'value', 'text', $this->config->calSimpleEventForm, false, false );
					?></td>
				</tr>
				<!-- <tr>
					<td class="key">Default Event Color ?</td>
					<td><?php
					$defColor[] = JHTML::_('select.option', 'random', JText::_('Random'), 'value', 'text' );
					$defColor[] = JHTML::_('select.option', 'none', JText::_('None'), 'value', 'text' );
					$defColor[] = JHTML::_('select.option', 'category', JText::_('Category'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $defColor, 'config[defColor]', '', 'value', 'text', $this->config->defColor, false, false );
					?></td>
				</tr>
				<tr>
					<td class="key">Hide Event Color Selection in Event Form, and force Event Color to Category Color<br/>(front end only, back end event entry unaffected)</td>
					<td><?php
					$colCatOpt[] = JHTML::_('select.option', 'NO', JText::_('NO'), 'value', 'text' );
					$colCatOpt[] = JHTML::_('select.option', 'YES', JText::_('YES'), 'value', 'text' );
					echo JHTML::_('select.genericlist', $colCatOpt, 'config[calForceCatColorEventForm]', '', 'value', 'text', $this->config->calForceCatColorEventForm, false, false );
					?></td>
				</tr> -->
			</tbody>
		</table>
	</fieldset>
	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_EVENTS_CAL_LANG_CUSTOM_FIELDS'); ?></span></legend>
		
		<table class="admintable">
			<thead>
				<tr>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_FIELD'); ?></th>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_TYPE'); ?></th>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_REQUIRED'); ?></th>
					<th><?php echo JText::_('COM_EVENTS_CAL_LANG_SHOW'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php 
			$fields = $this->config->fields;
			$r = count($fields);
			if ($r > 10) {
				$n = $r;
			} else {
				$n = 10;
			}
			for ($i=0; $i < $n; $i++)
			{
				if ($r == 0 || !isset($fields[$i])) {
					$fields[$i] = array();
					$fields[$i][0] = NULL;
					$fields[$i][1] = NULL;
					$fields[$i][2] = NULL;
					$fields[$i][3] = NULL;
					$fields[$i][4] = NULL;
				}
				?>
				<tr>
					<td><input type="text" name="fields[<?php echo $i; ?>][title]" value="<?php echo $fields[$i][1]; ?>" maxlength="255" /></td>
					<td><select name="fields[<?php echo $i; ?>][type]">
						<option value="text"<?php echo ($fields[$i][2]=='text') ? ' selected="selected"':''; ?>><?php echo JText::_('COM_EVENTS_CAL_LANG_TEXT'); ?></option>
						<option value="checkbox"<?php echo ($fields[$i][2]=='checkbox') ? ' selected="selected"':''; ?>><?php echo JText::_('COM_EVENTS_CAL_LANG_CHECKBOX'); ?></option>
					</select></td>
					<td><input type="checkbox" name="fields[<?php echo $i; ?>][required]" value="1"<?php echo ($fields[$i][3]) ? ' checked="checked"':''; ?> /></td>
					<td><input type="checkbox" name="fields[<?php echo $i; ?>][show]" value="1"<?php echo ($fields[$i][4]) ? ' checked="checked"':''; ?> /></td>
				</tr>
				<?php
			}
			?>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
