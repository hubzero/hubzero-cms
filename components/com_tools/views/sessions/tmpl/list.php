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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser = JFactory::getUser();

$newSession = JRequest::getVar('REQUEST_URI', JRoute::_('index.php?option=' . $this->option . '&task=invoke&app=' . $this->app->toolname . '&version='. $this->app->version), 'server');
if (strstr($newSession, '?'))
{
	$newSession .= '&amp;newinstance=1';
}
else 
{
	$newSession .= '?newinstance=1';
}
?>
<header id="content-header">
	<h2><?php echo JText::_('My Sessions'); ?></h2>
</header><!-- / #content-header -->

<section class="main section" id="mysessions-section">
	<p class="info">
		<?php echo JText::_('You already have an instance of this tool running. Do you wish to start a new one or view an existing session?'); ?>
	</p>
	<table class="sessions">
		<thead>
			<tr>
				<th><?php echo JText::_('Session'); ?></th>
				<th><?php echo JText::_('Started'); ?></th>
				<th><?php echo JText::_('Last accessed'); ?></th>
				<th><?php echo JText::_('Option'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<a href="<?php echo $newSession; ?>">
						<?php echo JText::_('Start new session'); ?>
					</a>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		if ($this->sessions) {
			$cls = 'even';
			foreach ($this->sessions as $session)
			{
				$cls = ($cls == 'odd') ? 'even' : 'odd';
		?>
			<tr class="<?php echo $cls; ?>">
				<td>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=session&app=' . $session->appname . '&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_RESUME_TITLE'); ?>">
						<?php echo $session->sessname; ?>
					</a>
				</td>
				<td>
					<?php echo $session->start; ?>
				</td>
				<td>
					<?php echo $session->accesstime; ?>
				</td>
			<?php if ($juser->get('username') == $session->username) { ?>
				<td>
					<a class="closetool" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=stop&app=' . $session->appname . '&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_TERMINATE_TITLE'); ?>">
						<?php echo JText::_('COM_TOOLS_TERMINATE'); ?>
					</a>
				</td>
			<?php } else { ?>
				<td>
					<a class="disconnect" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=unshare&app=' . $session->appname . '&sess='.$session->sessnum); ?>" title="<?php echo JText::_('COM_TOOLS_DISCONNECT_TITLE'); ?>">
						<?php echo JText::_('COM_TOOLS_DISCONNECT'); ?>
					</a> 
					<span class="owner"><?php echo JText::_('COM_TOOLS_MY_SESSIONS_OWNER').': '.$session->username; ?></span>
				</td>
			<?php } ?>
			</tr>
		<?php
			}
		}
		?>
		</tbody>
	</table>
</section><!-- / .section -->