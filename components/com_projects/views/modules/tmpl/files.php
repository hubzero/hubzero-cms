<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
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

$dateFormat = '%m/%d/%Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'm/d/Y';
	$tz = false;
}

?>
<div class="sidebox">
		<h4 class="assets"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$this->goto.a.'active=files'); ?>" class="hlink"><?php echo ucfirst(JText::_('COM_PROJECTS_FILES')); ?></a>
<?php if (count($this->files) > 0) { ?>
	<span><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$this->goto.'&active=files'); ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_SEE_ALL')); ?> </a></span>
<?php } ?>
</h4>
<?php if (count($this->files) == 0) { ?>
	<p class="mini"><?php echo JText::_('COM_PROJECTS_FILES_NONE'); ?></p>
<?php } else { ?>
	<ul>
		<?php foreach($this->files as $file) { 		
		?>
			<li>
				<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'active=files'.a.$this->goto).'/?action=download'.a.'file='.urlencode($file['fpath']); ?>" title="<?php echo $file['name']; ?>"><?php echo ProjectsHtml::shortenFileName($file['name']); ?></a>
				<span class="block faded mini">
					<?php echo $file['size']; ?> | <?php echo JText::_('COM_PROJECTS_FILES_REV'); ?> <?php echo $file['revisions']; ?> &middot; <?php echo JHTML::_('date', strtotime($file['date']), $dateFormat, $tz); ?> &middot; <?php echo ProjectsHtml::shortenName($file['author']); ?>
				</span>
			</li>
		<?php } ?>
	</ul><?php } ?>
</div>
