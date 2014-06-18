<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$jconfig = JFactory::getConfig();
$juri = JURI::getInstance();

$this->css('introduction.css', 'system')
     ->css();
?>

<header id="content-header">
	<h2><?php echo $this->forgeName;?></h2>
</header>

<section id="introduction" class="section">
	<div class="grid">
		<div class="col span9">
			<h3>Tool Development</h3>
			<p>
				Welcome to <?php echo $this->escape($this->forgeName); ?>, the tool
				development area of <a href="<?php echo $juri->base(); ?>"><?php echo $this->escape($jconfig->getValue('config.sitename')); ?></a>.
				The following pages are maintained by the various owners of each
				tool.  Many of these tools are available as Open Source, and
				you can download the code via Subversion from this site.  Some
				tools are closed source at the request of the authors, and only
				a restricted development team has access to the code.  See each
				tool page for details.
			</p>
		</div>
		<div class="col span3 omega">
			<h3>Help</h3>
			<ul>
			<?php
			$juser = JFactory::getUser();
			if ($juser->get('guest')) {
			?>
				<li><a href="<?php echo JRoute::_('index.php?option=com_members&controller=register'); ?>">Sign up for free!</a></li>
			<?php } ?>
				<li><a href="http://subversion.tigris.org/" rel="external">Learn about Subversion</a></li>
				<li><a class="popup" href="<?php echo JRoute::_('index.php?option=com_help&component=tools&page=index'); ?>">Need Help?</a></li>
			</ul>
		</div>
	</div>
</section><!-- / #introduction.section -->

<section class="section">
	<div class="grid">
		<div class="col span3">
			<h2><?php echo JText::_('Available Tools'); ?></h2>
		</div><!-- / .col span3 -->
		<div class="col span9 omega">
			<table>
				<thead>
					<tr>
						<th scope="col"><?php echo JText::_('Title'); ?></th>
						<th scope="col"><?php echo JText::_('Alias'); ?></th>
						<th scope="col"><?php echo JText::_('Status'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				$cls = 'even';
				if (count($this->apps) > 0) {
					foreach ($this->apps as $project)
					{
						//if ($project->state == 1 || $project->state == 3) {
						if ($project->tool_state != 8) {
							if ($project->codeaccess == '@OPEN') {
								$status = JText::_('open source');
							} else {
								$status = JText::_('closed source');
							}
				?>
					<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
						<td><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&app=' . $project->toolname . '&task=wiki'); ?>"><?php echo \Hubzero\Utility\String::truncate(stripslashes($project->title), 50); ?></a></td>
						<td><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&app=' . $project->toolname . '&task=wiki'); ?>"><?php echo $this->escape($project->toolname); ?></a></td>
						<td><span class="<?php echo $status; ?>-code"><?php echo $status; ?></span></td>
					</tr>
				<?php
						}
					}
				} else {
				?>
					<tr class="<?php $cls = ($cls == 'even') ? 'odd' : 'even'; echo $cls; ?>">
						<td colspan="3"><?php echo JText::_('No tools found.'); ?></td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		</div><!-- / .col span9 omega -->
	</div>
</div><!-- / .section -->