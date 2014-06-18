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

// is the dashboard customizable?
$customizable = true;
if ($this->params->get('allow_customization', 1) == 0)
{
	$customizable = false;
}
?>

<h3 class="section-header">
	<?php echo JText::_('PLG_MEMBERS_DASHBOARD'); ?>
</h3>

<?php if ($customizable) : ?>
<ul id="page_options">
	<li>
		<a class="icon-add btn add-module" href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->juser->get('id') . '&active=dashboard&action=add' ); ?>">
			<?php echo JText::_('PLG_MEMBERS_DASHBOARD_ADD_MODULES'); ?>
		</a>
	</li>
</ul>
<?php endif; ?>

<noscript>
	<p class="warning"><?php echo JText::_('PLG_MEMBERS_DASHBOARD_NO_JAVASCRIPT'); ?></p>
</noscript>

<div class="modules <?php echo ($customizable) ? 'customizable' : ''; ?>" data-userid="<?php echo $this->juser->get('id'); ?>">
	<?php
		foreach ($this->modules as $module)
		{
			// create view object
			$this->view('module')
			     ->set('admin', $this->admin)
			     ->set('module', $module)
			     ->display();
		}
	?>
</div>

<div class="modules-empty">
	<h3><?php echo JText::_('PLG_MEMBERS_DASHBOARD_EMPTY_TITLE'); ?></h3>
	<p><?php echo JText::_('PLG_MEMBERS_DASHBOARD_EMPTY_DESC'); ?></p>
</div>