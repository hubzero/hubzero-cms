<?php
/**
 * @package     hubzero-cms
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2008-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<div id="content-header" class="full">
	<h2><?php echo JText::_('XImport'); ?></h2>
</div><!-- / #content-header -->

<div class="main section">
<?php
if ($this->paths) {
?>
	<table summary="Available scripts">
		<caption>Available scripts</caption>
		<thead>
			<tr>
				<th>Script</th>
				<th>Description</th>
				<th>Last Ran</th>
				<th>Total Runs</th>
				<th>Option</th>
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
		
		if (isset($this->log[$scriptName])) {
			$lastRun = $this->log[$scriptName]['lastRun'];
			$totalRuns = $this->log[$scriptName]['totalRuns'];
		}
		
		include_once($path);
		if (class_exists($scriptName)) {
			$job = new $scriptName();
			if ($job instanceof XImportHelperScript) {
				$description = $job->getDescription();
				$form = true;
				$args = $job->getOptions();
			}
		}
		
		$cls = ($cls == 'even') ? 'odd' : 'even';
?>
			<tr class="<?php echo $cls; ?>">
				<th><?php echo $scriptName; ?></th>
				<td><?php echo $description; ?></td>
				<td><?php echo $lastRun; ?></td>
				<td><?php echo $totalRuns; ?></td>
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
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->
