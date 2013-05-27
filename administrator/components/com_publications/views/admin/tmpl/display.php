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
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Publications') . '</a>: <small><small>[' . JText::_('Admin Controls') . ']</small></small>', 'addedit.png');
?>
<h5>Administrative project(s):</h5>
<?php if (count($this->projects) > 0) { ?>
<ul class="projectlist">
<?php
	foreach ($this->projects as $project) { ?>
	<li><a href="index.php?option=com_projects&amp;task=edit&amp;id[]=<?php echo $project->id; ?>"><?php echo Hubzero_View_Helper_Html::shortenText($project->title, 50, 0);  ?> (id <?php echo $project->id; ?>)</a></li>	
<?php	} ?>
</ul>
<?php }
else
{ ?>
	<p>No admin projects found</p>
<?php } ?>	

<h5>Available Admin Options:</h5>
<?php if (!$this->workspace) { ?>
<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=workspace">Make Workspace tool a publication</a>
<?php }
else
{ ?>
	<span class="done"></span> Make Workspace tool a publication (done)
	<br />
	View <a href="/publications/workspace">workspace tool publication</a>
<?php } ?>