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

$col1 = (isset($this->usermods[0])) ? $this->usermods[0] : '';
$col2 = (isset($this->usermods[1])) ? $this->usermods[1] : '';
$col3 = (isset($this->usermods[2])) ? $this->usermods[2] : '';
$serials = "{$col1};{$col2};{$col3}";
?>
<h3 class="section-header">
	<?php echo JText::_('PLG_MEMBERS_DASHBOARD'); ?>
</h3>

<?php if ($this->config->get('allow_customization', 0) != 1) { ?>
<div class="section-header-extra">
	<ul id="page_options">
		<li class="last hide" id="personalize">
			<a class="btn icon-config dashboard" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&id=' . $this->juser->get('id') . '&active=dashboard'); ?>">
				<?php echo JText::_('PLG_MEMBERS_DASHBOARD_PERSONALIZE'); ?>
			</a>
			<div id="modules-dock">
				<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=view&id='.$this->juser->get('id').'&active=dashboard'); ?>" method="post" name="mysettings" id="cpnlc">
					<input type="hidden" name="uid" id="uid" value="<?php echo $this->juser->get('id'); ?>" />
					<input type="hidden" name="serials" id="serials" value="<?php echo $this->escape($serials); ?>" />
					<fieldset id="available">
						<?php
						// Instantiate a view
						$view = new \Hubzero\Plugin\View(
							array(
								'folder'  => 'members',
								'element' => 'dashboard',
								'name'    => 'list'
							)
						);
						$view->modules = $this->availmods;
						$view->display();
						?>
					</fieldset>
				</form>
			</div>
		</li>
	</ul>
</div><!-- / .section-header-extra -->
<?php } ?>

<div class="main section">
	<noscript>
		<p class="warning"><?php echo JText::_('PLG_MEMBERS_DASHBOARD_NO_JAVASCRIPT'); ?></p>
	</noscript>
	<table id="droppables" data-site="<?php echo rtrim(JURI::getInstance()->base(true), '/'); ?>">
		<tbody>
			<tr>
			<?php for ($c = 0; $c < count($this->columns); $c++) { ?>
				<td class="sortable" id="sortcol_<?php echo $c; ?>">
					<?php echo $this->columns[$c]; ?>
				</td>
			<?php } ?>
			</tr>
		</tbody>
	</table>
</div><!-- / .main section -->