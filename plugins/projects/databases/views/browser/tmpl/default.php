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

// Get databases to choose from
$objPD = new ProjectDatabase($this->database);
$items = $objPD->getItems($this->project->id, array());

$missing = array();
$shown = array();

// Attached item
$selected = NULL;
if ($this->primary && !empty($this->attachments)) 
{
	$selected = $this->attachments[0]->object_name;
}

// Build url
$route = $this->project->provisioned 
	? 'index.php?option=com_publications' . a . 'task=submit'
	: 'index.php?option=com_projects' . a . 'alias=' . $this->project->alias;
$p_url = JRoute::_($route . a . 'active=databases');

?>
<ul id="c-browser" <?php if (count($items) == 0 && isset($this->attachments) && count($this->attachments) == 0) { echo 'class="hidden"'; } ?> >
<?php
	if (count($items) > 0)
	{ ?>
	<?php
		foreach ($items as $item)
		{
			if ($item->revision == NULL || ($selected && $selected == $item->database_name) )
			{
			?>
		<li class="c-click databases" id="data::<?php echo $item->database_name; ?>"><?php echo $item->title; ?></li>	
	<?php 
			$shown[] = $item->database_name;
			}
		} ?>
		
		<?php

		// Check for missing items
		// Primary content / Supporting docs
		if (isset($this->attachments)) 
		{
			if (count($this->attachments) > 0) 
			{
				foreach ($this->attachments as $attachment) 
				{
					if (!in_array($attachment->object_name, $shown)) 
					{
						// Found missing
						$miss = array();
						$miss['id'] = $attachment->object_name;
						$miss['title'] = $attachment->title;
						$missing[] = $miss;
					}
				}
			}
		}
		
		// Add missing items
		if (count($missing) > 0) 
		{
			foreach ($missing as $miss) 
			{ ?>
				<li class="c-click databases i-missing" id="data::<?php echo $miss['id']; ?>"><?php echo $miss['title']; ?><span class="c-missing"><?php echo JText::_('PLG_PROJECTS_DATA_MISSING_DATABASE'); ?></span></li>
		<?php	}
		}		
	}
?>
</ul>

<?php if ((count($shown) + count($missing)) == 0) { ?>
	<p class="noresults"><?php echo JText::_('PLG_PROJECTS_DATA_NO_SELECTION_ITEMS_FOUND_DATA'); ?></p>
<?php } ?>

<?php if (!$this->project->provisioned) { ?>
	<p class="addnew">Go to <a href="<?php echo JRoute::_($route).'?active=databases'; ?>">Databases</a> to create a new database</p>
<?php } ?>