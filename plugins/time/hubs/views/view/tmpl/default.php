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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$dateFormat = '%m/%d/%Y';
$tz = 0;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'm/d/Y';
	$tz = null;
}

// Set some ordering variables
$start   = ($this->filters['start']) ? '&start='.$this->filters['start'] : '';
$imgasc  = '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'sort_asc.png">';
$imgdesc = '<img src="'.DS.'components'.DS.$this->option.DS.'images'.DS.'sort_desc.png">';
$sortcol = $this->mainframe->getUserStateFromRequest("$this->option.$this->active.orderby", 'orderby', 'name');
$dir     = $this->mainframe->getUserStateFromRequest("$this->option.$this->active.orderdir", 'orderdir', 'asc');
$newdir  = ($dir == 'asc') ? 'desc' : 'asc';
?>

<div id="plg_time_hubs">
	<?php if(count($this->notifications) > 0) {
		foreach ($this->notifications as $notification) { ?>
		<p class="<?php echo $notification['type']; ?>"><?php echo $this->escape($notification['message']); ?></p>
		<?php } // close foreach 
	} // close if count ?>
	<div id="content-header-extra">
		<ul id="useroptions">
			<li class="last">
				<a class="add" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs&action=new'); ?>">
					<?php echo JText::_('PLG_TIME_HUBS_NEW'); ?>
				</a>
			</li>
		</ul>
	</div>
	<div class="container">
		<table class="entries">
			<caption><?php echo JText::_('PLG_TIME_HUBS_CAPTION'); ?></caption>
			<thead>
				<tr>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active=hubs&orderby=name&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_HUBS_NAME'); ?>
						</a>
						<?php if($sortcol == 'name') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active=hubs&orderby=liaison&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_HUBS_LIAISON'); ?>
						</a>
						<?php if($sortcol == 'liaison') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active=hubs&orderby=anniversary_date&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_HUBS_ANNIVERSARY_DATE'); ?>
						</a>
						<?php if($sortcol == 'anniversary_date') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
					<td>
						<a href="<?php echo JRoute::_('index.php?option='.$this->option.$start.'&active=hubs&orderby=support_level&orderdir='.$newdir); ?>">
							<?php echo JText::_('PLG_TIME_HUBS_SUPPORT_LEVEL'); ?>
						</a>
						<?php if($sortcol == 'support_level') { echo ($dir == 'asc') ? $imgasc : $imgdesc; } ?>
					</td>
				</tr>
			</thead>
			<tbody>
				<?php if(count($this->hubs) > 0) {
					foreach($this->hubs as $hub) { ?>
					<tr>
						<td>
							<a class="view" id="<?php echo $hub->id; ?>" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs&action=readonly&id='.$hub->id); ?>">
								<?php echo $hub->name; ?>
							</a>
						</td>
						<td><?php echo $hub->liaison; ?></td>
						<td><?php echo ($hub->anniversary_date != '0000-00-00') ? JHTML::_('date', $hub->anniversary_date, $dateFormat, $tz) : ''; ?></td>
						<td><?php echo $hub->support_level; ?></td>
					</tr>
					<?php } // close foreach
				} else { // else count > 0 ?>
					<tr>
						<td colspan="7" class="no_hubs"><?php echo JText::_('PLG_TIME_HUBS_NONE_TO_DISPLAY'); ?></td>
					</tr>
				<?php } // close else ?>
			</tbody>
		</table>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs'); ?>">
			<?php echo $this->pageNav; ?>
		</form>
	</div>
</div>
