<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
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

$juser =& JFactory::getUser();
?>
<div class="<?php echo $modmysessions->moduleclass_sfx; ?>sessionlist">
<?php if ($modmysessions->error) { ?>
	<p class="error"><?php echo JText::_('MOD_MYSESSIONS_NOT_CONFIGURED'); ?></p>
<?php } else { ?>
<?php if ($modmysessions->authorized) { ?>
	<div id="mySessionsTabs">
		<ul class="session_tab_titles">
			<li title="mysessions" class="active">My Sessions</li>
			<li title="allsessions">All Sessions</li>
		</ul>
		<div id="mysessions" class="session_tab_panel active">
<?php } ?>
	<ul class="expandedlist">
<?php
	// Iterate through the session list and create links for each.
	$is_even  = 1;
	$appcount = 0;
	$sessions = $modmysessions->sessions;
	if (is_array($sessions)) {
		foreach ($sessions as $app)
		{
			// If we're on a specific tool page, show sessions for that tool ONLY
			if ($modmysessions->specapp && $app->appname != $modmysessions->specapp) {
				continue;
			}
			$bits = explode('_',$app->appname);
			$bit = (count($bits) > 1) ? array_pop($bits) : '';
			$appname = implode('_',$bits);
			
			$cls = ($is_even) ? '' : 'even ';
			
			if ($modmysessions->supportedtag) {
				if ($modmysessions->rt->checkTagUsage( $modmysessions->supportedtag, 0, $appname )) {
					$cls .= 'supported';
				} else {
					$cls .= 'session';
				}
			} else {
				$cls .= 'session';
			}
?>
		<li class="<?php echo $cls; ?>">
			<a href="<?php echo JRoute::_('index.php?option=com_tools&task=session&sess='.$app->sessnum.'&app='.$appname); ?>" title="<?php echo JText::_('MOD_MYSESSIONS_RESUME_TITLE'); ?>">
				<?php
				echo $app->sessname;
				if ($modmysessions->authorized === 'admin') {
					echo '<br />('.$app->username.')';
				}
				?>
			</a> 
<?php if ($juser->get('username') == $app->username || $modmysessions->authorized === 'admin') { ?>
			<a class="closetool" href="<?php echo JRoute::_('index.php?option=com_tools&task=stop&sess='.$app->sessnum.'&app='.$appname); ?>" title="<?php echo JText::_('MOD_MYSESSIONS_TERMINATE_TITLE'); ?>"><?php echo JText::_('MOD_MYSESSIONS_TERMINATE'); ?></a>
<?php } else { ?>
			<a class="disconnect" href="<?php echo JRoute::_('index.php?option=com_tools&task=unshare&sess='.$app->sessnum.'&app='.$appname); ?>" title="<?php echo JText::_('MOD_MYSESSIONS_DISCONNECT_TITLE'); ?>"><?php echo JText::_('MOD_MYSESSIONS_DISCONNECT'); ?></a> <br /><?php echo JText::_('MOD_MYSESSIONS_OWNER').': '.$app->username; ?>
<?php } ?>
		</li>
<?php
			$appcount++;
			$is_even ^= 1;
		}
	}
	if ($appcount == 0) {
		if (is_array($sessions)) {
?>
			<li class="session"><?php echo JText::_('MOD_MYSESSIONS_NONE'); ?></li>
<?php
		} else {
?>
			<li class="session"><?php echo JText::_('MOD_MYSESSIONS_MISSING_TABLE'); ?></li>
<?php
		}
	}
?>
	</ul>
<?php if ($modmysessions->authorized) { ?>
		</div><!-- / .mysessions -->
		<div id="allsessions" class="session_tab_panel">
	<ul class="expandedlist">
<?php
	// Iterate through the session list and create links for each.
	$is_even  = 1;
	$appcount = 0;
	$sessions = $modmysessions->allsessions;
	if (is_array($sessions)) {
		foreach ($sessions as $app)
		{
			// If we're on a specific tool page, show sessions for that tool ONLY
			if ($modmysessions->specapp && $app->appname != $modmysessions->specapp) {
				continue;
			}
			
			$bits = explode('_',$app->appname);
			$bit = (count($bits) > 1) ? array_pop($bits) : '';
			$appname = implode('_',$bits);
?>
		<li class="<?php echo ($is_even) ? '' : 'even '; ?>session">
			<a href="<?php echo JRoute::_('index.php?option=com_tools&task=session&sess='.$app->sessnum.'&app='.$appname); ?>" title="<?php echo JText::_('MOD_MYSESSIONS_RESUME_TITLE'); ?>">
				<?php
				echo $app->sessname;
				if ($modmysessions->authorized === 'admin') {
					echo '<br />('.$app->username.')';
				}
				?>
			</a> 
<?php if ($juser->get('username') == $app->username || $modmysessions->authorized === 'admin') { ?>
			<a class="closetool" href="<?php echo JRoute::_('index.php?option=com_tools&task=stop&sess='.$app->sessnum.'&app='.$appname); ?>" title="<?php echo JText::_('MOD_MYSESSIONS_TERMINATE_TITLE'); ?>"><?php echo JText::_('MOD_MYSESSIONS_TERMINATE'); ?></a>
<?php } else { ?>
			<a class="disconnect" href="<?php echo JRoute::_('index.php?option=com_tools&task=unshare&sess='.$app->sessnum.'&app='.$appname); ?>" title="<?php echo JText::_('MOD_MYSESSIONS_DISCONNECT_TITLE'); ?>"><?php echo JText::_('MOD_MYSESSIONS_DISCONNECT'); ?></a> <br /><?php echo JText::_('MOD_MYSESSIONS_OWNER').': '.$app->username; ?>
<?php } ?>
		</li>
<?php
			$appcount++;
			$is_even ^= 1;
		}
	}
	if ($appcount == 0) {
		if (is_array($sessions)) {
?>
			<li class="session"><?php echo JText::_('MOD_MYSESSIONS_NONE'); ?></li>
<?php
		} else {
?>
			<li class="session"><?php echo JText::_('MOD_MYSESSIONS_MISSING_TABLE'); ?></li>
<?php
		}
	}
?>
	</ul>
		</div><!-- / .allsessions -->
	</div><!-- / #mySessionsTabs -->
<?php } ?>
</div><!-- / .sessionlist -->
<?php
	// Get the disk usage
	if ($modmysessions->show_storage) {
		$du = MwUtils::getDiskUsage($juser->get('username'));
		if (count($du) <=1) {
			// Error
			$config = JFactory::getConfig();
			if ($config->getValue('config.debug')) {
?>
<p class="error"><?php echo JText::_('MOD_MYSESSIONS_ERROR_RETRIEVING_STORAGE'); ?></p>
<?php
			}
		} else {
			// Calculate the percentage of spaced used
			bcscale(6);
			$val = ($du['softspace'] > 0) ? bcdiv($du['space'], $du['softspace']) : 0;
			$percent = round( $val * 100 );

			// Amount can only have a max of 100 due to some display restrictions
			$amount  = ($percent > 100) ? 100 : $percent;

			// Add the JavaScript file that will do the AJAX magic
			//$document =& JFactory::getDocument();
			//$document->addScript('modules/mod_mysessions/mod_mysessions.js');
?>
<dl id="diskusage">
	<dt><?php echo JText::_('MOD_MYSESSIONS_STORAGE'); ?> (<a href="<?php echo JRoute::_('index.php?option=com_tools&task=storage'); ?>"><?php echo JText::_('MOD_MYSESSIONS_MANAGE'); ?></a>)</dt>
	<dd id="du-amount"><div style="width:<?php echo $amount; ?>%;"><strong>&nbsp;</strong><span><?php echo $amount; ?>%</span></div></dd>
<?php if ($percent == 100) { ?>
	<dd id="du-msg"><p class="warning"><?php echo JText::_('MOD_MYSESSIONS_MAXIMUM_STORAGE'); ?></p></dd>
<?php } ?>
<?php if ($percent > 100) { ?>
	<dd id="du-msg"><p class="warning"><?php echo JText::_('MOD_MYSESSIONS_EXCEEDING_STORAGE'); ?></p></dd>
<?php } ?>
</dl>
<?php
		}
	}
}
?>
