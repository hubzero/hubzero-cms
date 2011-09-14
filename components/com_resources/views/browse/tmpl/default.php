<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 * All rights reserved.
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

$database =& JFactory::getDBO();

$sortbys = array();
if ($this->config->get('show_ranking')) {
	$sortbys['ranking'] = JText::_('COM_RESOURCES_RANKING');
}
$sortbys['date'] = JText::_('COM_RESOURCES_DATE_PUBLISHED');
$sortbys['date_modified'] = JText::_('COM_RESOURCES_DATE_MODIFIED');
$sortbys['title'] = JText::_('COM_RESOURCES_TITLE');
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option.'&task=browse'); ?>" id="resourcesform" method="post">
		<div class="aside">
			<fieldset>
				<label>
					<?php echo JText::_('COM_RESOURCES_TYPE'); ?>: 
					<select name="type" id="type">
						<option value=""><?php echo JText::_('COM_RESOURCES_ALL'); ?></option>
<?php
			if (count($this->types) > 0) {
				foreach ($this->types as $item)
				{
?>
						<option value="<?php echo $item->id; ?>"<?php echo ($this->filters['type'] == $item->id) ? ' selected="selected"' : ''; ?>><?php echo $item->type; ?></option>
<?php
				}
			}
?>
					</select>
				</label>
				<label>
					<?php echo JText::_('COM_RESOURCES_SORT_BY'); ?>:
					<select name="sortby" id="sortby">
<?php
						foreach ($sortbys as $avalue => $alabel)
						{
?>
						<option value="<?php echo $avalue; ?>"<?php echo ($avalue == $this->filters['sortby'] || $alabel == $this->filters['sortby']) ? ' selected="selected"' : ''; ?>><?php echo $alabel; ?></option>
<?php
						}
?>
					</select>
				</label>
				<input type="submit" value="<?php echo JText::_('COM_RESOURCES_GO'); ?>" />
			</fieldset>
		</div><!-- / .aside -->
		<div class="subject">
<?php
if ($this->results) {
	switch ($this->filters['sortby'])
	{
		case 'date_created': $show_date = 1; break;
		case 'date_modified': $show_date = 2; break;
		case 'date':
		default: $show_date = 3; break;
	}
	echo ResourcesHtml::writeResults( $database, $this->results, $this->authorized, $show_date );
	echo '<div class="clear"></div>';
} else { ?>
			<p class="warning"><?php echo JText::_('COM_RESOURCES_NO_RESULTS'); ?></p>
<?php }

$pn = $this->pageNav->getListFooter();
$pn = str_replace('/?/&amp;','/?',$pn);
$f = 'task=browse';
foreach ($this->filters as $k=>$v)
{
	$f .= ($v && $k != 'authorized' && $k != 'limit' && $k != 'start') ? '&amp;'.$k.'='.$v : '';
}
$pn = str_replace('?','?'.$f.'&amp;',$pn);
echo $pn;
?>
		</div><!-- / .subject -->
		<div class="clear"></div>
	</form>
</div><!-- / .main section -->
