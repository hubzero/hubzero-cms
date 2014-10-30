<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

$stamp = $this->publicStamp ? $this->publicStamp->stamp : NULL;

if ($stamp) {
	$juri = JURI::getInstance();
?>
	<p class="publink"><?php echo JText::_('COM_PROJECTS_NOTES_PUB_LINK') . ' <a href="' . trim($juri->base(), DS) . JRoute::_('index.php?option=' . $this->option . a . 'action=get') . '?s=' . $stamp .'" rel="external">' . trim($juri->base(), DS) . JRoute::_('index.php?option=' . $this->option . a . 'action=get') . '?s=' . $stamp . '</a>'; ?>
	<?php if ($this->project->private == 0) {
		$act = $this->publicStamp->listed ? 'unlist' : 'publist'; ?>
	<span><?php echo JText::_('COM_PROJECTS_NOTES_THIS_PAGE_IS'); ?>  <strong class="<?php echo $this->publicStamp->listed ? 'green' : 'urgency'; ?>"><?php echo $this->publicStamp->listed ? JText::_('COM_PROJECTS_NOTES_LISTED') : JText::_('COM_PROJECTS_NOTES_UNLISTED'); ?></strong>. <a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes') . '?action=share&p=' . $this->page->get('id'); ?>" class="showinbox"><?php echo JText::_('COM_PROJECTS_NOTES_SHARE_SETTINGS'); ?> &rsaquo;</a></span>
	<?php } ?>
	</p>

<?php } else { ?>
	<p class="publink"><?php echo JText::_('COM_PROJECTS_NOTES_SHARE_GET_LINK'); ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'alias='.$this->project->alias.'&active=notes') . '?action=share&p=' . $this->page->get('id'); ?>" class="showinbox"><?php echo JText::_('COM_PROJECTS_NOTES_SHARE_GENERATE_LINK'); ?></a></p>
<?php } ?>
