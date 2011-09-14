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
JToolBarHelper::title( JText::_('HUB Configuration').': '.JText::_('Components'), 'addedit.png' );
JToolBarHelper::save('savecom');
JToolBarHelper::cancel();

$document =& JFactory::getDocument();
$document->setTitle( JText::_('Edit Preferences') );
JHTML::_('behavior.tooltip');
?>
<?php if ($this->msg) { ?>
<dl id="system-message">
<dt class="message">Message</dt>
<dd class="message message fade">
	<ul>
		<li><?php echo $this->msg; ?></li>
	</ul>
</dd>
</dl>
<?php } ?>
<form action="index.php" method="post" name="adminForm" autocomplete="off">
	<div class="col width-30">
		<fieldset class="adminform">
			<h3><?php echo JText::_('HUB Components'); ?></h3>
			<ul>
				<?php
				foreach ($this->components as $com)
				{
					echo '<li><a href="index.php?option='.$this->option.'&amp;task=components&amp;component='.$com.'">'.$com.'</a></li>'."\n";
				}
				?>
			</ul>
		</fieldset>
	</div>
	<div class="col width-70">
		<fieldset class="adminform">
			<div class="configuration">
				<?php echo JText::_($this->component->name) ?>
			</div>
		</fieldset>

		<fieldset class="adminform">
			<legend>
				<?php echo JText::_( 'Configuration' );?>
			</legend>
			<?php
			$path = JPATH_ADMINISTRATOR.DS.'components'.DS.$this->component->option.DS.'config.xml';
			if (is_file($path)) {
				$params = new JParameter( $this->component->params, $path );
				echo $params->render();
			} else {
				echo '<p>'.JText::_('No parameters to render').'</p>';
			}
			?>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->component->id; ?>" />
	<input type="hidden" name="component" value="<?php echo $this->component->option; ?>" />

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="savecom" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
