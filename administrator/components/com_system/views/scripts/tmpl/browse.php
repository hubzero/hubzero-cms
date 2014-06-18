<?php
/**
 * HUBzero CMS
 *
 * Copyright 2008-2011 Purdue University. All rights reserved.
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
 * @package       hubzero-cms
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright     Copyright 2008-2011 Purdue University. All rights reserved.
 * @license       http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('Scripts'), 'script.png');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<div id="adminForm">
<?php
if ($this->paths) {
?>
	<table class="adminlist" summary="Available scripts">
		<thead>
			<tr>
				<th scope"col">Script</th>
				<th scope"col">Description</th>
				<th scope"col">Last Ran</th>
				<th scope"col">Total Runs</th>
				<th scope"col">Option</th>
			</tr>
		</thead>
		<tbody>
<?php
	$cls = 'even';
	foreach ($this->paths as $path)
	{
		$scriptName = str_replace($this->path . DS, '', $path);
		$scriptName = str_replace('.php', '', $scriptName);

		$job = null;
		$description = '';
		$form = false;
		$lastRun = '-';
		$totalRuns = '0';
		$args = null;

		if (isset($this->log[$scriptName]))
		{
			$lastRun   = $this->log[$scriptName]['lastRun'];
			$totalRuns = $this->log[$scriptName]['totalRuns'];
		}

		include_once($path);

		if (class_exists($scriptName))
		{
			$job = new $scriptName();
			if ($job instanceof SystemHelperScript)
			{
				$description = $job->getDescription();
				$form = true;
				$args = $job->getOptions();
			}
		}

		$cls = ($cls == 'even') ? 'odd' : 'even';
?>
			<tr class="<?php echo $cls; ?>">
				<th scope"row"><?php echo $this->escape($scriptName); ?></th>
				<td><?php echo $this->escape($description); ?></td>
				<td><?php echo $this->escape($lastRun); ?></td>
				<td><?php echo $this->escape($totalRuns); ?></td>
				<td>
<?php if ($form) { ?>
					<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post">
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="task" value="run" />
						<input type="hidden" name="script" value="<?php echo $scriptName; ?>" />
						<input type="submit" value="Run Script" />
					</form>
<?php
	if ($args) {
		foreach ($args as $arg) {
?>
					<form action="<?php echo JRoute::_('index.php?option=' . $this->option); ?>" method="post">
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="task" value="run" />
						<input type="hidden" name="script" value="<?php echo $scriptName; ?>" />
<?php
			$str = array();
			foreach ($arg as $key => $val) {
				$str[] = $key . '=' . $val;
?>
						<input type="hidden" name="<?php echo $key; ?>" value="<?php echo $val; ?>" />
<?php } ?>
						<input type="submit" value="Run Script (<?php echo implode('&amp;', $str); ?>)" />
					</form>
<?php
		}
	}
?>
<?php } ?>
				</td>
			</tr>
<?php
	}
?>
		</tbody>
	</table>
<?php
} else {
	echo '<p class="warning">'. JText::_('No scripts found.') .'</p>';
}
?>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
</div>